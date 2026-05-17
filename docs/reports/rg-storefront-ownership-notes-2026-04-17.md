# RG Storefront Ownership Notes

Date: 2026-04-17
Workspace: `C:\nwp0203\rose-garden`
Audience: implementation agents only

- Live homepage owner: `routes/web.php` named `home` -> `App\Http\Controllers\StorefrontHomeController@index` -> `resources/views/home/layout-studio.blade.php`
- Legacy homepage path: `App\Http\Controllers\HomeController@index` -> `resources/views/home/index.blade.php`
- Legacy homepage path is reference-only in this repo; do not target it for live storefront homepage changes
- Locale-prefixed storefront routes are public URL aliases, not canonical named-route owners
- Canonical named storefront routes live in the non-prefixed `Route::middleware('set.locale')` group in `routes/web.php`
- Cart entry ownership: `/sepet` route shell -> `resources/views/cart/index.blade.php` -> `App\Livewire\CartPage`
- Checkout entry ownership: `/odeme` route shell -> `resources/views/checkout/index.blade.php` -> `App\Livewire\CheckoutWizard`
- Checkout continuation ownership: `App\Http\Controllers\CheckoutController@processPayment`
- Checkout result ownership: `App\Http\Controllers\CheckoutController@success` and `@fail`
- If a task changes live homepage behavior, start from `StorefrontHomeController`, `home/layout-studio.blade.php`, and the Layout Studio services before touching any legacy home file
