# RG Beacon Encoding Cleanup 2026-04-17

## Amaç

Storefront genelinde kullanıcıya görünen mojibake / encoding bozulmalarını temizlemek, `lang/**` ile Blade yüzeyleri arasındaki UTF-8 bütünlüğünü toparlamak ve encoding kaynaklı storefront test kırıklarını kapatmak.

## Root Cause Sınıfları

- `file encoding issue`
- `corrupted translation payload`
- `mixed-source copy issue`
- `legacy mojibake residue`

## Bozuk Metinlerin Ana Kaynakları

1. Bazı storefront Blade dosyaları CP1252/UTF-8 karışımı bozuk kopyalar taşıyordu.
2. `lang/en.json` ve `lang/ku.json` içinde birden fazla nesilden kalan bozuk translation payload vardı.
3. `lang/tr/*.php` ve `lang/ku/*.php` validation/auth/password dosyalarında password ve validation copy’si kırılmıştı.
4. Storefront testleri de aynı bozuk metni assert ettiği için runtime düzelince testler ayrıca güncellenmek zorunda kaldı.
5. Layout Studio shell içinde birkaç hardcoded / untranslated storefront string locale zincirinin dışında kalmıştı.

## Temizlenen Yüzeyler

- header / footer shell
- language switcher helper copy
- cookie / consent panel
- order tracking
- account auth surfaces
- public FAQ / contact / special occasions copy
- home shell ve Layout Studio preview banner
- checkout public copy ve validation language chain
- storefront compatibility / localization test metinleri

## Temizlenen Translation Dosyaları

- `lang/en.json`
- `lang/ku.json`
- `lang/tr/auth.php`
- `lang/tr/passwords.php`
- `lang/tr/validation.php`
- `lang/ku/auth.php`
- `lang/ku/passwords.php`
- `lang/ku/validation.php`

## Değiştirilen Dosyalar

- `lang/en.json`
- `lang/ku.json`
- `lang/tr/auth.php`
- `lang/tr/passwords.php`
- `lang/tr/validation.php`
- `lang/ku/auth.php`
- `lang/ku/passwords.php`
- `lang/ku/validation.php`
- `resources/views/layouts/app.blade.php`
- `resources/views/layouts/partials/header.blade.php`
- `resources/views/layouts/partials/footer.blade.php`
- `resources/views/layouts/partials/announcement-bar.blade.php`
- `resources/views/layouts/partials/announcement-bar-dynamic.blade.php`
- `resources/views/components/language-switcher.blade.php`
- `resources/views/components/theme-toggle.blade.php`
- `resources/views/components/plp-filter-fields.blade.php`
- `resources/views/cookie-consent.blade.php`
- `resources/views/home/layout-studio.blade.php`
- `resources/views/home/index.blade.php`
- `resources/views/home/sections/blog-preview.blade.php`
- `resources/views/home/sections/featured-showcase.blade.php`
- `resources/views/home/sections/occasion-spotlight.blade.php`
- `resources/views/home/sections/trust-badges.blade.php`
- `resources/views/account/addresses.blade.php`
- `resources/views/account/forgot-password.blade.php`
- `resources/views/account/kvkk.blade.php`
- `resources/views/account/login.blade.php`
- `resources/views/account/profile.blade.php`
- `resources/views/account/register.blade.php`
- `resources/views/account/reset-password.blade.php`
- `resources/views/blog/index.blade.php`
- `resources/views/checkout/fail.blade.php`
- `resources/views/checkout/index.blade.php`
- `resources/views/checkout/success.blade.php`
- `resources/views/errors/404.blade.php`
- `resources/views/errors/500.blade.php`
- `resources/views/livewire/cart-page.blade.php`
- `resources/views/livewire/product-search.blade.php`
- `resources/views/order-tracking/index.blade.php`
- `resources/views/pages/contact.blade.php`
- `resources/views/pages/faq.blade.php`
- `resources/views/special-occasions/index.blade.php`
- `tests/Feature/Storefront/AccountAndContentSurfaceTest.php`
- `tests/Feature/Storefront/BrandingSettingsTest.php`
- `tests/Feature/Storefront/HeaderThemeTest.php`
- `tests/Feature/Storefront/LocalizationSurfaceTest.php`
- `tests/Feature/Storefront/LocalizedStorefrontContentTest.php`
- `tests/Feature/Storefront/ProductCartCheckoutSurfaceTest.php`
- `tests/Feature/Storefront/SearchLocalizationTest.php`
- `tests/Feature/Storefront/StorefrontCompatibilityTest.php`
- `tests/Feature/Storefront/StorefrontVisibilityTest.php`

## Yapılan Doğrulamalar

- `php artisan test --filter=StorefrontCompatibilityTest`
- `php artisan test --filter=LocalizationSurfaceTest`
- `php artisan test --filter=AccountAndContentSurfaceTest`
- `php artisan test --filter=CheckoutFlowTest`
- `php artisan test --filter=HeaderThemeTest`
- UTF-8 scan:
  - storefront views, `lang/**`, `tests/Feature/Storefront/**`, `tests/Feature/Checkout/**` içinde `Ã / Ä / Å / â€™ / U+FFFD` kalıntısı kalmadığı doğrulandı.

## Kalan Riskler

1. Admin tarafında ayrı mojibake temizliği gerektiren legacy dosyalar hâlâ var; bu tur storefront ile sınırlı tutuldu.
2. `lang/en.json` ve `lang/ku.json` geçmişte farklı kaynaklardan büyüdüğü için ileride yeni kopya eklenirken tekrar mixed-encoding taşıma riski var.
3. Dynamic DB content tarafında admin’den bozuk metin girilirse bu tur sadece dosya ve storefront runtime kaynaklarını temizler; giriş tarafında ayrıca koruma faydalı olur.

## Sonraki Güvenli Adım

Admin/content-entry zincirinde import ve edit akışlarına UTF-8 guard eklemek, ardından public + admin için dar kapsamlı encoding smoke testini CI’a almak.
