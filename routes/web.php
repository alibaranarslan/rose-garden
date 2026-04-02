<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\Auth\KvkkConsentController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CookieConsentController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderTrackingController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SearchController;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::get('/health', function () {
    $checks = ['status' => 'ok', 'app' => 'rose-garden'];
    try {
        DB::connection()->getPdo();
        $checks['database'] = 'ok';
    } catch (\Throwable $e) {
        $checks['database'] = 'fail';
        $checks['status'] = 'degraded';
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
    $stored = \App\Models\Setting::query()->where('key', 'robots_txt')->value('value');
    $content = $stored
        ? preg_replace('#Sitemap:\s*https?://[^\s]+#i', 'Sitemap: ' . url('/sitemap.xml'), $stored)
        : "User-agent: *\nAllow: /\nSitemap: " . url('/sitemap.xml');

    return response($content, 200)->header('Content-Type', 'text/plain');
});

// KVKK consent routes (no locale prefix required, auth middleware)
Route::middleware('auth')->group(function () {
    Route::get('/kvkk-onayi', [KvkkConsentController::class, 'show'])->name('kvkk.consent');
    Route::post('/kvkk-onayi', [KvkkConsentController::class, 'store'])->name('kvkk.consent.store');
    Route::get('/kvkk-reddet', [KvkkConsentController::class, 'reject'])->name('kvkk.consent.reject');
});

// Admin order print route (no locale prefix, auth protected)
Route::get('/admin/orders/{order}/print', function (\App\Models\Order $order) {
    return view('admin.orders.print', compact('order'));
})->middleware(['web', 'auth'])->name('orders.print');

Route::prefix('{locale}')
    ->where(['locale' => 'tr|en|ku'])
    ->middleware('set.locale')
    ->group(function () {
        Route::get('/', [HomeController::class, 'index']);
        Route::get('/urunler', [ProductController::class, 'index']);
        Route::get('/kategori/{slug}', [ProductController::class, 'index']);
        Route::get('/urun/{slug}', [ProductController::class, 'show']);
        Route::get('/arama', [SearchController::class, 'index'])->middleware('throttle:search');
        Route::get('/sepet', fn () => view('cart.index'));
        Route::get('/odeme', fn () => view('checkout.index'));
        Route::get('/blog', [BlogController::class, 'index']);
        Route::get('/blog/{slug}', [BlogController::class, 'show']);
        Route::get('/iletisim', [PageController::class, 'contact'])->middleware('throttle:contact');
        Route::post('/iletisim', [PageController::class, 'submitContact'])->middleware('throttle:contact');
        Route::get('/sss', [PageController::class, 'faq']);
        Route::get('/teslimat-bilgileri', [PageController::class, 'deliveryInfo']);
        Route::get('/siparis-takip', [OrderTrackingController::class, 'index']);
        Route::post('/siparis-takip', [OrderTrackingController::class, 'track']);
        Route::get('/sayfa/{slug}', [PageController::class, 'show']);
        Route::get('/auth/google', [GoogleAuthController::class, 'redirect']);
        Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback']);
        Route::get('/odeme/basarili', [CheckoutController::class, 'success']);
        Route::get('/odeme/basarisiz', [CheckoutController::class, 'fail']);
        Route::get('/odeme/{order}', [CheckoutController::class, 'processPayment']);
        Route::middleware('guest')->group(function () {
            Route::get('/giris', [LoginController::class, 'showLogin']);
            Route::post('/giris', [LoginController::class, 'login'])->middleware('throttle:login');
            Route::get('/kayit', [LoginController::class, 'showRegister']);
            Route::post('/kayit', [LoginController::class, 'register'])->middleware('throttle:register');
            Route::get('/sifremi-unuttum', [PasswordResetController::class, 'showForgotForm']);
            Route::post('/sifremi-unuttum', [PasswordResetController::class, 'sendResetLink']);
            Route::get('/sifre-sifirla/{token}', [PasswordResetController::class, 'showResetForm']);
            Route::post('/sifre-sifirla', [PasswordResetController::class, 'reset']);
        });
        Route::middleware('auth')->group(function () {
            Route::post('/cikis', [LoginController::class, 'logout']);
            Route::get('/hesabim', [AccountController::class, 'dashboard']);
            Route::get('/hesabim/kvkk', [AccountController::class, 'kvkkPanel']);
            Route::post('/hesabim/kvkk/talep', [AccountController::class, 'submitDataRequest']);
            Route::post('/hesabim/kvkk/pazarlama-izni-kaldir', [AccountController::class, 'withdrawMarketingConsent']);
            Route::get('/hesabim/kvkk/verilerimi-indir', [AccountController::class, 'exportPersonalData']);
            Route::get('/hesabim/siparislerim', [AccountController::class, 'orders']);
            Route::get('/hesabim/siparis/{orderNumber}', [AccountController::class, 'orderShow']);
            Route::post('/hesabim/siparis/{orderNumber}/tekrar', [AccountController::class, 'reorder']);
            Route::get('/hesabim/favorilerim', [AccountController::class, 'favorites']);
            Route::get('/hesabim/puanlarim', [AccountController::class, 'loyalty']);
            Route::get('/hesabim/adreslerim', [AccountController::class, 'addresses']);
            Route::post('/hesabim/adreslerim', [AccountController::class, 'storeAddress']);
            Route::put('/hesabim/adreslerim/{address}', [AccountController::class, 'updateAddress']);
            Route::delete('/hesabim/adreslerim/{address}', [AccountController::class, 'deleteAddress']);
            Route::patch('/hesabim/adreslerim/{address}/varsayilan', [AccountController::class, 'setDefaultAddress']);
            Route::get('/hesabim/profilim', [AccountController::class, 'profile']);
            Route::put('/hesabim/profilim', [AccountController::class, 'updateProfile']);
        });
    });

Route::middleware('set.locale')->group(function () {
    Route::get('/', [HomeController::class, 'index'])->middleware('cache.page:300')->name('home');
    Route::get('/urunler', [ProductController::class, 'index'])->middleware('cache.page:180')->name('products.index');
    Route::get('/kategori/{slug}', [ProductController::class, 'index'])->middleware('cache.page:180')->name('products.category');
    Route::get('/urun/{slug}', [ProductController::class, 'show'])->middleware('cache.page:600')->name('products.show');
    Route::get('/arama', [SearchController::class, 'index'])->middleware('throttle:search')->name('search');
    Route::get('/sepet', fn () => view('cart.index'))->name('cart');
    Route::get('/odeme', fn () => view('checkout.index'))->name('checkout');
    Route::get('/blog', [BlogController::class, 'index'])->middleware('cache.page:300')->name('blog.index');
    Route::get('/blog/{slug}', [BlogController::class, 'show'])->middleware('cache.page:600')->name('blog.show');
    Route::get('/iletisim', [PageController::class, 'contact'])->middleware('throttle:contact')->name('contact');
    Route::post('/iletisim', [PageController::class, 'submitContact'])->middleware('throttle:contact')->name('contact.submit');
    Route::get('/sss', [PageController::class, 'faq'])->name('faq');
    Route::get('/teslimat-bilgileri', [PageController::class, 'deliveryInfo'])->name('delivery.info');
    Route::get('/siparis-takip', [OrderTrackingController::class, 'index'])->name('order.track');
    Route::post('/siparis-takip', [OrderTrackingController::class, 'track'])->name('order.track.submit');
    Route::get('/sayfa/{slug}', [PageController::class, 'show'])->name('page.show');
    Route::get('/auth/google', [GoogleAuthController::class, 'redirect'])->name('auth.google');
    Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->name('auth.google.callback');
    Route::get('/odeme/basarili', [CheckoutController::class, 'success'])->name('checkout.success');
    Route::get('/odeme/basarisiz', [CheckoutController::class, 'fail'])->name('checkout.fail');
    Route::get('/odeme/{order}', [CheckoutController::class, 'processPayment'])->name('checkout.payment');
    Route::post('/cookie-consent', [CookieConsentController::class, 'store'])->name('cookie-consent.store');
    Route::middleware('guest')->group(function () {
        Route::get('/giris', [LoginController::class, 'showLogin'])->name('login');
        Route::post('/giris', [LoginController::class, 'login'])->middleware('throttle:login')->name('login.submit');
        Route::get('/kayit', [LoginController::class, 'showRegister'])->name('register');
        Route::post('/kayit', [LoginController::class, 'register'])->middleware('throttle:register')->name('register.submit');
        Route::get('/sifremi-unuttum', [PasswordResetController::class, 'showForgotForm'])->name('password.request');
        Route::post('/sifremi-unuttum', [PasswordResetController::class, 'sendResetLink'])->name('password.email');
        Route::get('/sifre-sifirla/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
        Route::post('/sifre-sifirla', [PasswordResetController::class, 'reset'])->name('password.update');
    });
    Route::middleware('auth')->group(function () {
        Route::post('/cikis', [LoginController::class, 'logout'])->name('logout');
        Route::get('/hesabim', [AccountController::class, 'dashboard'])->name('account.dashboard');
        Route::get('/hesabim/kvkk', [AccountController::class, 'kvkkPanel'])->name('account.kvkk');
        Route::post('/hesabim/kvkk/talep', [AccountController::class, 'submitDataRequest'])->name('account.kvkk.request');
        Route::post('/hesabim/kvkk/pazarlama-izni-kaldir', [AccountController::class, 'withdrawMarketingConsent'])->name('account.kvkk.withdraw-marketing');
        Route::get('/hesabim/kvkk/verilerimi-indir', [AccountController::class, 'exportPersonalData'])->name('account.kvkk.export');
        Route::get('/hesabim/siparislerim', [AccountController::class, 'orders'])->name('account.orders');
        Route::get('/hesabim/siparis/{orderNumber}', [AccountController::class, 'orderShow'])->name('account.order.show');
        Route::post('/hesabim/siparis/{orderNumber}/tekrar', [AccountController::class, 'reorder'])->name('account.order.reorder');
        Route::get('/hesabim/favorilerim', [AccountController::class, 'favorites'])->name('account.favorites');
        Route::get('/hesabim/puanlarim', [AccountController::class, 'loyalty'])->name('account.loyalty');
        Route::get('/hesabim/adreslerim', [AccountController::class, 'addresses'])->name('account.addresses');
        Route::post('/hesabim/adreslerim', [AccountController::class, 'storeAddress'])->name('account.addresses.store');
        Route::put('/hesabim/adreslerim/{address}', [AccountController::class, 'updateAddress'])->name('account.addresses.update');
        Route::delete('/hesabim/adreslerim/{address}', [AccountController::class, 'deleteAddress'])->name('account.addresses.delete');
        Route::patch('/hesabim/adreslerim/{address}/varsayilan', [AccountController::class, 'setDefaultAddress'])->name('account.addresses.default');
        Route::get('/hesabim/profilim', [AccountController::class, 'profile'])->name('account.profile');
        Route::put('/hesabim/profilim', [AccountController::class, 'updateProfile'])->name('account.profile.update');
    });
});
