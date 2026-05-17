<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AdminGuideProgressController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\Auth\KvkkConsentController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CookieConsentController;
use App\Http\Controllers\OrderTrackingController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SpecialOccasionController;
use App\Http\Controllers\StorefrontHomeController;
use App\Models\Address;
use App\Models\Order;
use App\Models\Setting;
use App\Services\PaytrService;
use App\Support\AdminPrivileges;
use App\Support\StorefrontLocale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

Route::get('/health', function () {
    $checks = [
        'status' => 'ok',
        'app' => 'rose-garden',
        'timestamp' => now()->toIso8601String(),
        'debug' => (bool) config('app.debug'),
        'queue_driver' => (string) config('queue.default'),
    ];

    try {
        DB::connection()->getPdo();
        $checks['database'] = 'ok';
    } catch (\Throwable $e) {
        $checks['database'] = 'fail';
        $checks['status'] = 'degraded';
    }

    try {
        Cache::put('health_probe_rg', 'ok', 10);
        $checks['cache'] = Cache::get('health_probe_rg') === 'ok' ? 'ok' : 'fail';
        if ($checks['cache'] !== 'ok') {
            $checks['status'] = 'degraded';
        }
    } catch (\Throwable $e) {
        $checks['cache'] = 'fail';
        $checks['status'] = 'degraded';
    }

    if ($checks['queue_driver'] === 'database') {
        $checks['queue_table_present'] = Schema::hasTable(config('queue.connections.database.table', 'jobs'));
    }

    $code = $checks['status'] === 'ok' ? 200 : 503;

    return response()->json($checks, $code);
});

Route::get('/sitemap.xml', function () {
    $path = public_path('sitemap.xml');
    abort_unless(file_exists($path), 404);

    return response()->file($path, ['Content-Type' => 'application/xml']);
});

Route::get('/robots.txt', function () {
    $canonicalDomain = trim((string) Setting::get('seo', 'canonical_domain', config('app.url')));

    if ($canonicalDomain !== '') {
        if (! str_starts_with($canonicalDomain, 'http://') && ! str_starts_with($canonicalDomain, 'https://')) {
            $canonicalDomain = 'https://'.$canonicalDomain;
        }

        $parts = parse_url($canonicalDomain);

        if (is_array($parts) && ! empty($parts['host'])) {
            $scheme = $parts['scheme'] ?? 'https';
            $port = isset($parts['port']) ? ':'.$parts['port'] : '';
            $canonicalDomain = "{$scheme}://{$parts['host']}{$port}";
        } else {
            $canonicalDomain = rtrim((string) config('app.url'), '/');
        }
    }

    $sitemapUrl = rtrim($canonicalDomain !== '' ? $canonicalDomain : rtrim((string) config('app.url'), '/'), '/').'/sitemap.xml';
    $stored = trim((string) Setting::get('seo', 'robots_txt', "User-agent: *\nAllow: /\nSitemap: {$sitemapUrl}"));
    $extra = trim((string) Setting::get('seo', 'robots_txt_extra', ''));
    $content = implode("\n", array_filter([$stored, $extra], fn (string $value): bool => trim($value) !== ''));
    $content = preg_replace('#Sitemap:\s*https?://[^\s]+#i', 'Sitemap: '.$sitemapUrl, $content);

    return response($content, 200)->header('Content-Type', 'text/plain');
});

// Canonical storefront ownership notes:
// - Live homepage owner is StorefrontHomeController@index -> resources/views/home/layout-studio.blade.php.
// - Legacy HomeController/home.index intentionally stay outside the active storefront route map.
// - Locale-prefixed storefront routes are URL aliases; canonical named routes live in the non-prefixed group below.
// - Route registration now flows through a shared definition so alias/canonical groups cannot silently drift.
$storefrontLocaleConstraint = StorefrontLocale::routeConstraint();

// Cart entry is a thin route shell. Runtime behavior continues in resources/views/cart/index.blade.php
// and App\Livewire\CartPage.
$storefrontCartEntry = static fn () => view('cart.index');

// Checkout entry is also a thin route shell. Order creation belongs to resources/views/checkout/index.blade.php
// and App\Livewire\CheckoutWizard, while payment continuation and results stay in CheckoutController.
$storefrontCheckoutEntry = static fn () => view('checkout.index');

$registerGuestStorefrontRoutes = function (bool $named): void {
    $route = Route::get('/giris', [LoginController::class, 'showLogin']);
    if ($named) {
        $route->name('login');
    }

    $route = Route::post('/giris', [LoginController::class, 'login'])->middleware('throttle:login');
    if ($named) {
        $route->name('login.submit');
    }

    $route = Route::get('/kayit', [LoginController::class, 'showRegister']);
    if ($named) {
        $route->name('register');
    }

    $route = Route::post('/kayit', [LoginController::class, 'register'])->middleware('throttle:register');
    if ($named) {
        $route->name('register.submit');
    }

    $route = Route::get('/sifremi-unuttum', [PasswordResetController::class, 'showForgotForm']);
    if ($named) {
        $route->name('password.request');
    }

    $route = Route::post('/sifremi-unuttum', [PasswordResetController::class, 'sendResetLink'])->middleware('throttle:password-reset');
    if ($named) {
        $route->name('password.email');
    }

    $route = Route::get('/sifre-sifirla/{token}', function (Request $request, PasswordResetController $controller) {
        return $controller->showResetForm($request, (string) $request->route('token'));
    });
    if ($named) {
        $route->name('password.reset');
    }

    $route = Route::post('/sifre-sifirla', [PasswordResetController::class, 'reset'])->middleware('throttle:password-reset');
    if ($named) {
        $route->name('password.update');
    }
};

$registerAuthenticatedStorefrontRoutes = function (bool $named): void {
    $route = Route::post('/cikis', [LoginController::class, 'logout']);
    if ($named) {
        $route->name('logout');
    }

    $route = Route::get('/hesabim', [AccountController::class, 'dashboard']);
    if ($named) {
        $route->name('account.dashboard');
    }

    $route = Route::get('/hesabim/kvkk', [AccountController::class, 'kvkkPanel']);
    if ($named) {
        $route->name('account.kvkk');
    }

    $route = Route::post('/hesabim/kvkk/talep', [AccountController::class, 'submitDataRequest']);
    if ($named) {
        $route->name('account.kvkk.request');
    }

    $route = Route::post('/hesabim/kvkk/pazarlama-izni-kaldir', [AccountController::class, 'withdrawMarketingConsent']);
    if ($named) {
        $route->name('account.kvkk.withdraw-marketing');
    }

    $route = Route::get('/hesabim/kvkk/verilerimi-indir', [AccountController::class, 'exportPersonalData']);
    if ($named) {
        $route->name('account.kvkk.export');
    }

    $route = Route::get('/hesabim/siparislerim', [AccountController::class, 'orders']);
    if ($named) {
        $route->name('account.orders');
    }

    $route = Route::get('/hesabim/siparis/{orderNumber}', function (Request $request, AccountController $controller) {
        return $controller->orderShow((string) $request->route('orderNumber'));
    });
    if ($named) {
        $route->name('account.order.show');
    }

    $route = Route::post('/hesabim/siparis/{orderNumber}/tekrar', function (Request $request, AccountController $controller) {
        return $controller->reorder((string) $request->route('orderNumber'));
    });
    if ($named) {
        $route->name('account.order.reorder');
    }

    $route = Route::get('/hesabim/favorilerim', [AccountController::class, 'favorites']);
    if ($named) {
        $route->name('account.favorites');
    }

    $route = Route::get('/hesabim/puanlarim', [AccountController::class, 'loyalty']);
    if ($named) {
        $route->name('account.loyalty');
    }

    $route = Route::get('/hesabim/adreslerim', [AccountController::class, 'addresses']);
    if ($named) {
        $route->name('account.addresses');
    }

    $route = Route::post('/hesabim/adreslerim', [AccountController::class, 'storeAddress']);
    if ($named) {
        $route->name('account.addresses.store');
    }

    $route = Route::put('/hesabim/adreslerim/{address}', function (Request $request, AccountController $controller) {
        $address = Address::query()->findOrFail($request->route('address'));

        return $controller->updateAddress($request, $address);
    });
    if ($named) {
        $route->name('account.addresses.update');
    }

    $route = Route::delete('/hesabim/adreslerim/{address}', function (Request $request, AccountController $controller) {
        $address = Address::query()->findOrFail($request->route('address'));

        return $controller->deleteAddress($address);
    });
    if ($named) {
        $route->name('account.addresses.delete');
    }

    $route = Route::patch('/hesabim/adreslerim/{address}/varsayilan', function (Request $request, AccountController $controller) {
        $address = Address::query()->findOrFail($request->route('address'));

        return $controller->setDefaultAddress($address);
    });
    if ($named) {
        $route->name('account.addresses.default');
    }

    $route = Route::get('/hesabim/profilim', [AccountController::class, 'profile']);
    if ($named) {
        $route->name('account.profile');
    }

    $route = Route::put('/hesabim/profilim', [AccountController::class, 'updateProfile']);
    if ($named) {
        $route->name('account.profile.update');
    }
};

$registerStorefrontRoutes = function (bool $named) use (
    $storefrontCartEntry,
    $storefrontCheckoutEntry,
    $registerGuestStorefrontRoutes,
    $registerAuthenticatedStorefrontRoutes
): void {
    $route = Route::get('/', [StorefrontHomeController::class, 'index'])->middleware('cache.page:300');
    if ($named) {
        $route->name('home');
    }

    $route = Route::get('/urunler', function (Request $request, ProductController $controller, ?string $locale = null) {
        return $controller->index($request, $locale);
    })->middleware('cache.page:180');
    if ($named) {
        $route->name('products.index');
    }

    $route = Route::get('/kategori/{slug}', function (Request $request, ProductController $controller) {
        return $controller->index(
            $request,
            $request->route('locale'),
            $request->route('slug'),
        );
    })->middleware('cache.page:180');
    if ($named) {
        $route->name('products.category');
    }

    $route = Route::get('/urun/{slug}', function (Request $request, ProductController $controller) {
        return $controller->show((string) $request->route('slug'));
    })->middleware('cache.page:600');
    if ($named) {
        $route->name('products.show');
    }

    $route = Route::get('/arama', [SearchController::class, 'index'])->middleware('throttle:search');
    if ($named) {
        $route->name('search');
    }

    $route = Route::get('/sepet', $storefrontCartEntry);
    if ($named) {
        $route->name('cart');
    }

    $route = Route::get('/odeme', $storefrontCheckoutEntry);
    if ($named) {
        $route->name('checkout');
    }

    $route = Route::get('/blog', [BlogController::class, 'index'])->middleware('cache.page:300');
    if ($named) {
        $route->name('blog.index');
    }

    $route = Route::get('/blog/{slug}', function (Request $request, BlogController $controller) {
        return $controller->show((string) $request->route('slug'));
    })->middleware('cache.page:600');
    if ($named) {
        $route->name('blog.show');
    }

    $route = Route::get('/ozel-gunler', [SpecialOccasionController::class, 'index'])->middleware('cache.page:300');
    if ($named) {
        $route->name('special-occasions.index');
    }

    $route = Route::get('/ozel-gunler/{slug}', function (Request $request, SpecialOccasionController $controller) {
        return $controller->show((string) $request->route('slug'));
    })->middleware('cache.page:600');
    if ($named) {
        $route->name('special-occasions.show');
    }

    $route = Route::get('/iletisim', [PageController::class, 'contact'])->middleware('throttle:contact');
    if ($named) {
        $route->name('contact');
    }

    $route = Route::post('/iletisim', [PageController::class, 'submitContact'])->middleware('throttle:contact');
    if ($named) {
        $route->name('contact.submit');
    }

    $route = Route::get('/sss', [PageController::class, 'faq']);
    if ($named) {
        $route->name('faq');
    }

    $route = Route::get('/teslimat-bilgileri', [PageController::class, 'deliveryInfo']);
    if ($named) {
        $route->name('delivery.info');
    }

    $route = Route::get('/siparis-takip', [OrderTrackingController::class, 'index']);
    if ($named) {
        $route->name('order.track');
    }

    $route = Route::post('/siparis-takip', [OrderTrackingController::class, 'track']);
    if ($named) {
        $route->name('order.track.submit');
    }

    $route = Route::get('/sayfa/{slug}', function (Request $request, PageController $controller) {
        return $controller->show((string) $request->route('slug'));
    });
    if ($named) {
        $route->name('page.show');
    }

    $route = Route::get('/auth/google', [GoogleAuthController::class, 'redirect']);
    if ($named) {
        $route->name('auth.google');
    }

    $route = Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback']);
    if ($named) {
        $route->name('auth.google.callback');
    }

    $route = Route::get('/odeme/basarili', [CheckoutController::class, 'success']);
    if ($named) {
        $route->name('checkout.success');
    }

    $route = Route::get('/odeme/basarisiz', [CheckoutController::class, 'fail']);
    if ($named) {
        $route->name('checkout.fail');
    }

    $route = Route::get('/odeme/{order}', function (Request $request, CheckoutController $controller, PaytrService $paytr) {
        $order = Order::query()->findOrFail($request->route('order'));

        return $controller->processPayment($order, $paytr);
    });
    if ($named) {
        $route->name('checkout.payment');
    }

    if ($named) {
        Route::post('/cookie-consent', [CookieConsentController::class, 'store'])->name('cookie-consent.store');
    }

    Route::middleware('guest')->group(function () use ($named, $registerGuestStorefrontRoutes) {
        $registerGuestStorefrontRoutes($named);
    });

    Route::middleware('auth')->group(function () use ($named, $registerAuthenticatedStorefrontRoutes) {
        $registerAuthenticatedStorefrontRoutes($named);
    });
};

$registerKvkkConsentRoutes = function (bool $named): void {
    $route = Route::get('/kvkk-onayi', [KvkkConsentController::class, 'show']);
    if ($named) {
        $route->name('kvkk.consent');
    }

    $route = Route::post('/kvkk-onayi', [KvkkConsentController::class, 'store']);
    if ($named) {
        $route->name('kvkk.consent.store');
    }

    $route = Route::get('/kvkk-reddet', [KvkkConsentController::class, 'reject']);
    if ($named) {
        $route->name('kvkk.consent.reject');
    }
};

// Admin order print route (no locale prefix, auth protected)
Route::get('/admin/orders/{order}/print', function (Order $order) {
    abort_unless(AdminPrivileges::canAccessAdminPanel(auth()->user()), 403);

    return view('admin.orders.print', compact('order'));
})->middleware(['web', 'auth'])->name('orders.print');

Route::post('/admin/guide-progress', [AdminGuideProgressController::class, 'store'])
    ->middleware(['web', 'auth'])
    ->name('admin.guides.progress.store');

// Signed previews reuse the same canonical homepage render chain as the live storefront homepage.
Route::get('/preview/home/{revision}', [StorefrontHomeController::class, 'preview'])
    ->middleware('signed')
    ->name('layout.preview.home');

Route::get('/preview/header-theme/{headerTheme}', [StorefrontHomeController::class, 'themePreview'])
    ->middleware('signed')
    ->name('header-theme.preview.home');

// Locale-prefixed storefront aliases. Keep behavior aligned with the canonical named group below.
// Do not move canonical ownership here unless the locale route model is intentionally redesigned.
Route::prefix('{locale}')
    ->where($storefrontLocaleConstraint)
    ->middleware('set.locale')
    ->group(function () use ($registerStorefrontRoutes) {
        $registerStorefrontRoutes(false);
    });

Route::prefix('{locale}')
    ->where($storefrontLocaleConstraint)
    ->middleware(['set.locale', 'auth'])
    ->group(function () use ($registerKvkkConsentRoutes) {
        $registerKvkkConsentRoutes(false);
    });

// Canonical named storefront routes. Route helpers and active storefront ownership resolve here.
Route::middleware('set.locale')->group(function () use ($registerStorefrontRoutes) {
    $registerStorefrontRoutes(true);
});

Route::middleware(['set.locale', 'auth'])->group(function () use ($registerKvkkConsentRoutes) {
    $registerKvkkConsentRoutes(true);
});
