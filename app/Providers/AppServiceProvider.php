<?php

namespace App\Providers;

use App\Models\AdminLoginLog;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Setting;
use App\Observers\OrderItemObserver;
use App\Observers\OrderObserver;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;

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
        RateLimiter::for('api', fn (Request $request) => Limit::perMinute(60)->by($request->ip()));
        RateLimiter::for('search', fn (Request $request) => Limit::perMinute(20)->by($request->ip()));
        RateLimiter::for('contact', fn (Request $request) => Limit::perMinute(3)->by($request->ip()));

        // Register model observers
        Order::observe(OrderObserver::class);
        OrderItem::observe(OrderItemObserver::class);

        // Admin login audit logging
        Event::listen(Login::class, function (Login $event) {
            try {
                AdminLoginLog::create([
                    'user_id'    => $event->user->id,
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'action'     => 'login',
                    'created_at' => now(),
                ]);
            } catch (\Throwable) {
                // Non-critical: do not break login flow
            }
        });

        Event::listen(Logout::class, function (Logout $event) {
            if (!$event->user) {
                return;
            }
            try {
                AdminLoginLog::create([
                    'user_id'    => $event->user->id,
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'action'     => 'logout',
                    'created_at' => now(),
                ]);
            } catch (\Throwable) {
            }
        });

        View::share('navCategories', Cache::remember('rg_nav_categories', 300, fn () => Category::active()
            ->roots()
            ->orderBy('sort_order')
            ->get()));

        View::share('siteSettings', Cache::remember('rg_site_settings', 3600, fn () => Setting::query()
            ->get()
            ->groupBy('group')
            ->map(fn ($items) => $items->pluck('value', 'key'))));

        URL::defaults(['locale' => app()->getLocale()]);

        Queue::failing(function (JobFailed $event) {
            Log::channel('daily')->error('[QUEUE_FAILURE] Job failed', [
                'job'        => $event->job->resolveName(),
                'connection' => $event->connectionName,
                'queue'      => $event->job->getQueue(),
                'exception'  => $event->exception->getMessage(),
            ]);

            if (app()->bound('sentry')) {
                \Sentry\withScope(function (\Sentry\State\Scope $scope) use ($event): void {
                    $scope->setTag('queue.job', $event->job->resolveName());
                    $scope->setTag('queue.connection', $event->connectionName);
                    \Sentry\captureException($event->exception);
                });
            }
        });
    }
}
