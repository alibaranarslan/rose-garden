<?php

namespace App\Providers;

use App\Models\AbandonedCart;
use App\Models\AdminLoginLog;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Setting;
use App\Models\SpecialOccasion;
use App\Notifications\AbandonedCartNotification;
use App\Notifications\Channels\SmsChannel;
use App\Observers\OrderItemObserver;
use App\Observers\OrderObserver;
use App\Support\CatalogTaxonomy;
use App\Support\DynamicMailConfig;
use App\Support\HeaderThemeResolver;
use App\Support\LocalizedSettings;
use App\Support\SiteBranding;
use App\Support\StorefrontLocale;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Notifications\Events\NotificationFailed;
use Illuminate\Notifications\Events\NotificationSent;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        RateLimiter::for('login', fn (Request $request) => Limit::perMinute(5)->by($request->ip()));
        RateLimiter::for('register', fn (Request $request) => Limit::perMinute(3)->by($request->ip()));
        RateLimiter::for('password-reset', fn (Request $request) => Limit::perMinute(3)->by($request->ip()));
        RateLimiter::for('api', fn (Request $request) => Limit::perMinute(60)->by($request->ip()));
        RateLimiter::for('search', fn (Request $request) => Limit::perMinute(20)->by($request->ip()));
        RateLimiter::for('contact', fn (Request $request) => $request->isMethod('post')
            ? Limit::perMinute(3)->by($request->ip())
            : Limit::perMinute(60)->by($request->ip()));

        ResetPassword::createUrlUsing(function (object $notifiable, string $token): string {
            $locale = StorefrontLocale::normalize(
                data_get($notifiable, 'preferred_language'),
                StorefrontLocale::current()
            );

            return StorefrontLocale::route('password.reset', [
                'token' => $token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ], $locale, true, true);
        });

        if (! $this->app->runningUnitTests()) {
            DynamicMailConfig::apply();
        }

        // Register model observers
        Order::observe(OrderObserver::class);
        OrderItem::observe(OrderItemObserver::class);

        // Admin login audit logging
        Event::listen(Login::class, function (Login $event) {
            try {
                AdminLoginLog::create([
                    'user_id' => $event->user->id,
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'action' => 'login',
                    'created_at' => now(),
                ]);
            } catch (\Throwable) {
                // Non-critical: do not break login flow
            }
        });

        Event::listen(Logout::class, function (Logout $event) {
            if (! $event->user) {
                return;
            }
            try {
                AdminLoginLog::create([
                    'user_id' => $event->user->id,
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'action' => 'logout',
                    'created_at' => now(),
                ]);
            } catch (\Throwable) {
            }
        });

        Event::listen(NotificationSent::class, function (NotificationSent $event): void {
            if (! $event->notification instanceof AbandonedCartNotification) {
                return;
            }

            $cartId = $event->notification->cartId();

            if (! $cartId) {
                return;
            }

            $channel = $this->normalizeReminderChannel($event->channel);

            $cart = AbandonedCart::query()->find($cartId);

            if (! $cart) {
                return;
            }

            $cart->forceFill([
                'last_reminder_status' => 'sent',
                'last_reminder_channel' => $this->mergeReminderChannels($cart->last_reminder_channel, $channel),
                'last_reminder_error' => null,
            ])->save();
        });

        Event::listen(NotificationFailed::class, function (NotificationFailed $event): void {
            if (! $event->notification instanceof AbandonedCartNotification) {
                return;
            }

            $cartId = $event->notification->cartId();

            if (! $cartId) {
                return;
            }

            $cart = AbandonedCart::query()->find($cartId);

            if (! $cart || $cart->last_reminder_status === 'sent') {
                return;
            }

            $cart->forceFill([
                'last_reminder_status' => 'failed',
                'last_reminder_error' => data_get($event->data, 'exception.message', 'Bildirim gönderimi başarısız oldu.'),
            ])->save();
        });

        View::share('navCategories', $this->safeRemember('rg_nav_categories', 300, function () {
            $slugs = CatalogTaxonomy::navigationCategorySlugs();

            return Category::active()
                ->whereIn('slug', $slugs)
                ->whereHas('products', fn ($query) => $query->storefrontReady())
                ->with('parent')
                ->get()
                ->sortBy(fn (Category $c) => array_search($c->slug, $slugs, true))
                ->values();
        }, collect()));

        View::composer('*', function ($view): void {
            $view->with('siteSettings', $this->safeRemember(
                'rg_site_settings_'.app()->getLocale(),
                3600,
                fn () => Setting::query()
                    ->get()
                    ->groupBy('group')
                    ->map(fn ($items) => $items->pluck('value', 'key'))
                    ->pipe(function ($groups) {
                        if ($groups->has('general')) {
                            $groups->put('general', $groups->get('general')->map(function ($value, $key) {
                                return in_array($key, ['site_name', 'site_tagline'], true)
                                    ? LocalizedSettings::resolveText($value, '')
                                    : $value;
                            }));
                        }

                        if ($groups->has('contact')) {
                            $groups->put('contact', $groups->get('contact')->map(function ($value, $key) {
                                return $key === 'address'
                                    ? LocalizedSettings::resolveText($value, '')
                                    : $value;
                            }));
                        }

                        return $groups;
                    }),
                collect()
            ));

            $view->with('siteBranding', $this->safeRemember(
                'rg_site_branding_'.app()->getLocale(),
                3600,
                fn () => SiteBranding::current(),
                []
            ));

            $view->with('activeHeaderTheme', app(HeaderThemeResolver::class)->resolve());
        });

        View::share('navSpecialOccasions', $this->safeRemember('rg_nav_special_occasions', 300, function () {
            return SpecialOccasion::active()
                ->with('category')
                ->get()
                ->sortBy(fn (SpecialOccasion $occasion) => $occasion->daysUntil())
                ->take(6)
                ->values();
        }, collect()));

        View::share('footerPromoVisuals', $this->safeRemember('rg_footer_promo_visuals', 600, function () {
            return \App\Support\StorefrontImage::footerPromoVisualCards(3);
        }, []));

        if ($this->app->runningUnitTests()) {
            View::share('navCategories', collect());
            View::share('navSpecialOccasions', collect());
            View::share('footerPromoVisuals', []);
        }

        URL::defaults(['locale' => app()->getLocale()]);

        Queue::failing(function (JobFailed $event) {
            Log::channel('daily')->error('[QUEUE_FAILURE] Job failed', [
                'job' => $event->job->resolveName(),
                'connection' => $event->connectionName,
                'queue' => $event->job->getQueue(),
                'exception' => $event->exception->getMessage(),
            ]);

            if (app()->bound('sentry')) {
                \Sentry\withScope(function (\Sentry\State\Scope $scope) use ($event): void {
                    $scope->setTag('queue.job', $event->job->resolveName());
                    $scope->setTag('queue.connection', $event->connectionName);
                    \Sentry\captureException($event->exception);
                });
            }
        });

        if (! $this->app->runningUnitTests()) {
            Queue::before(function (): void {
                DynamicMailConfig::apply();
            });
        }
    }

    private function safeRemember(string $key, int $ttl, callable $resolver, mixed $fallback): mixed
    {
        if ($this->app->runningUnitTests()) {
            try {
                return $resolver();
            } catch (\Throwable $e) {
                Log::warning('View share fallback used', [
                    'key' => $key,
                    'message' => $e->getMessage(),
                ]);

                return $fallback;
            }
        }

        try {
            return Cache::remember($key, $ttl, $resolver);
        } catch (\Throwable $e) {
            Log::warning('View share fallback used', [
                'key' => $key,
                'message' => $e->getMessage(),
            ]);

            return $fallback;
        }
    }

    private function normalizeReminderChannel(string $channel): string
    {
        return match ($channel) {
            'mail' => 'email',
            SmsChannel::class => 'sms',
            default => $channel,
        };
    }

    private function mergeReminderChannels(?string $existing, string $incoming): string
    {
        $channels = collect(explode('+', (string) $existing))
            ->filter()
            ->push($incoming)
            ->unique()
            ->values();

        return $channels->implode('+');
    }
}
