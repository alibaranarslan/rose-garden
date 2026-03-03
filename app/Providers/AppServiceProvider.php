<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Setting;
use App\Observers\OrderItemObserver;
use App\Observers\OrderObserver;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
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
        RateLimiter::for('login', fn (Request $request) => Limit::perMinute(5)->by($request->ip()));
        RateLimiter::for('register', fn (Request $request) => Limit::perMinute(3)->by($request->ip()));
        RateLimiter::for('api', fn (Request $request) => Limit::perMinute(60)->by($request->ip()));
        RateLimiter::for('search', fn (Request $request) => Limit::perMinute(20)->by($request->ip()));
        RateLimiter::for('contact', fn (Request $request) => Limit::perMinute(3)->by($request->ip()));

        // Register model observers
        Order::observe(OrderObserver::class);
        OrderItem::observe(OrderItemObserver::class);

        View::share('navCategories', Cache::remember('rg_nav_categories', 300, fn () => Category::active()
            ->roots()
            ->orderBy('sort_order')
            ->get()));

        View::share('siteSettings', Cache::remember('rg_site_settings', 3600, fn () => Setting::query()
            ->get()
            ->groupBy('group')
            ->map(fn ($items) => $items->pluck('value', 'key'))));

        URL::defaults(['locale' => app()->getLocale()]);
    }
}
