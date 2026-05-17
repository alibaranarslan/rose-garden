# RG Beacon Localization Integrity - 2026-04-17

## Amaç

Harbor sonrası storefront genelinde `tr/en/ku` davranışını güvenilir hale getirmek; locale-aware navigation kırıklarını kapatmak; auth/checkout validation mesajlarını seçili dile uydurmak; partial Turkish leakage kalan public yüzeyleri temizlemek; locale geçişi sonrası oluşan 404 ve locale drop davranışlarını düzeltmek.

## Root Cause Sınıfları

1. `wrong locale-aware route generation`
   - Public/account/content yüzeylerinde çok sayıda blade linki hâlâ doğrudan `route()` kullanıyordu.
   - Guest/auth redirect akışları da locale bilgisini korumuyordu.

2. `missing translations`
   - Laravel framework fallback mesajları için `validation.php`, `auth.php`, `passwords.php` dosyaları yoktu.
   - KVKK consent yüzeyi neredeyse tamamen sabit Türkçe metinle yaşıyordu.

3. `dynamic content localization gap`
   - Dynamic içerik modeli doğruydu, fakat shell/legal/auth copy zincirindeki eksik halkalar yüzünden EN/KU yüzeylerde Türkçe sızıntı kalıyordu.

4. `search normalization/query issue`
   - Kürtçe aramalarda diacritic varyasyonları (`gul` / `gûl`) normalize edilmediği için Türkçe storefront yüzeyinde bazı query kombinasyonları kaçıyordu.

5. `locale-scoped route coverage gap`
   - KVKK consent rotaları storefront alias modelinin dışında kaldığı için locale-prefixed erişimlerde 404 üretiyordu.

## Localization Ownership Özeti

- Route ownership: Canonical named route owner hâlâ `App\Support\StorefrontLocale::route()` ve canonical non-prefixed route grubu.
- Locale state ownership: `SetLocale` middleware + `StorefrontLocale::resolveRequestLocale()`.
- Dynamic content ownership: `Product`, `BlogPost`, `SpecialOccasion`, `Page` üzerindeki translatable alanlar korunarak bırakıldı.
- Shell/auth/legal copy ownership: Blade içi `__()` çağrıları + `lang/*.json` + yeni `lang/{locale}/auth.php`, `passwords.php`, `validation.php`.
- Auth redirect continuity: `bootstrap/app.php` içinde guest/user redirect closure'ları locale-aware hale getirildi.

## Düzeltilen Yüzeyler

- Header / mobile nav
- Footer
- Language continuity taşıyan cart/search/product/blog/home links
- Account login/register/forgot/reset/profile/addresses/KVKK yüzeyleri
- Auth KVKK consent surface
- Blog index kart ve detail linkleri
- Special occasions listing linkleri
- Cart, checkout success/fail ve related public navigation zinciri
- Error pages (`404`, `500`) için locale-aware recovery links

## Düzeltilen 404 / Locale Navigation Sorunları

- Guest -> account redirect artık locale-prefixed login sayfasına dönüyor.
- Guest/auth redirect sonrası locale düşmesi kapatıldı.
- KVKK consent rotaları locale alias modeline eklendi; `/en/kvkk-onayi` ve `/ku/kvkk-onayi` artık 404 üretmiyor.
- Header/footer/account/blog/special occasions/cart/home zincirinde plain `route()` kalan linkler `StorefrontLocale::route()` ile hizalandı.
- Product listing filter URL üretimi locale-aware hale getirildi.

## Düzeltilen Validation / Message Sorunları

- Türkçe yüzeyde Laravel default İngilizce validation fallback’i kapatıldı.
- Auth failure, password reset broker ve KVKK consent hata mesajları locale-aware hale getirildi.
- Checkout wizard agreement validation mesajları Türkçe attribute adlarıyla render edilir hale getirildi.
- KVKK consent auth yüzeyi `tr/en/ku` için çeviri dosyalarına taşındı.

## Kürtçe Search Sorununun Kök Nedeni

Kök neden yalnızca stored translation eksikliği değildi. Search katmanı JSON çeviri alanlarında lower-case arama yapıyordu ama Kürtçe diacritic varyasyonlarını normalize etmiyordu. Bu yüzden Türkçe storefront yüzeyinde `gul` ve `gûl` gibi query farkları aynı ürün setine eşlenmiyordu. Çözüm olarak PHP tarafında query normalize edildi ve SQL tarafında `name` ile `short_description` araması diacritic-insensitive hale getirildi.

## Değiştirilen Dosyalar

- `C:\nwp0203\rose-garden\app\Http\Controllers\Auth\GoogleAuthController.php`
- `C:\nwp0203\rose-garden\app\Http\Controllers\Auth\KvkkConsentController.php`
- `C:\nwp0203\rose-garden\app\Http\Controllers\Auth\LoginController.php`
- `C:\nwp0203\rose-garden\app\Http\Controllers\Auth\PasswordResetController.php`
- `C:\nwp0203\rose-garden\app\Http\Controllers\AccountController.php`
- `C:\nwp0203\rose-garden\app\Http\Controllers\BlogController.php`
- `C:\nwp0203\rose-garden\app\Http\Controllers\CheckoutController.php`
- `C:\nwp0203\rose-garden\app\Http\Controllers\PageController.php`
- `C:\nwp0203\rose-garden\app\Http\Controllers\SearchController.php`
- `C:\nwp0203\rose-garden\app\Livewire\FavoriteToggle.php`
- `C:\nwp0203\rose-garden\bootstrap\app.php`
- `C:\nwp0203\rose-garden\lang\tr\auth.php`
- `C:\nwp0203\rose-garden\lang\en\auth.php`
- `C:\nwp0203\rose-garden\lang\ku\auth.php`
- `C:\nwp0203\rose-garden\lang\tr\passwords.php`
- `C:\nwp0203\rose-garden\lang\en\passwords.php`
- `C:\nwp0203\rose-garden\lang\ku\passwords.php`
- `C:\nwp0203\rose-garden\lang\tr\validation.php`
- `C:\nwp0203\rose-garden\lang\en\validation.php`
- `C:\nwp0203\rose-garden\lang\ku\validation.php`
- `C:\nwp0203\rose-garden\resources\views\account\addresses.blade.php`
- `C:\nwp0203\rose-garden\resources\views\account\forgot-password.blade.php`
- `C:\nwp0203\rose-garden\resources\views\account\kvkk.blade.php`
- `C:\nwp0203\rose-garden\resources\views\account\profile.blade.php`
- `C:\nwp0203\rose-garden\resources\views\account\register.blade.php`
- `C:\nwp0203\rose-garden\resources\views\account\reset-password.blade.php`
- `C:\nwp0203\rose-garden\resources\views\auth\kvkk-consent.blade.php`
- `C:\nwp0203\rose-garden\resources\views\blog\index.blade.php`
- `C:\nwp0203\rose-garden\resources\views\components\cart-link.blade.php`
- `C:\nwp0203\rose-garden\resources\views\components\plp-filter-fields.blade.php`
- `C:\nwp0203\rose-garden\resources\views\errors\404.blade.php`
- `C:\nwp0203\rose-garden\resources\views\errors\500.blade.php`
- `C:\nwp0203\rose-garden\resources\views\home\index.blade.php`
- `C:\nwp0203\rose-garden\resources\views\home\sections\blog-preview.blade.php`
- `C:\nwp0203\rose-garden\resources\views\home\sections\featured-showcase.blade.php`
- `C:\nwp0203\rose-garden\resources\views\home\sections\occasion-spotlight.blade.php`
- `C:\nwp0203\rose-garden\resources\views\home\sections\trust-badges.blade.php`
- `C:\nwp0203\rose-garden\resources\views\layouts\partials\footer.blade.php`
- `C:\nwp0203\rose-garden\resources\views\layouts\partials\header.blade.php`
- `C:\nwp0203\rose-garden\resources\views\livewire\cart-icon.blade.php`
- `C:\nwp0203\rose-garden\resources\views\livewire\cart-page.blade.php`
- `C:\nwp0203\rose-garden\resources\views\livewire\product-search.blade.php`
- `C:\nwp0203\rose-garden\resources\views\special-occasions\index.blade.php`
- `C:\nwp0203\rose-garden\routes\web.php`
- `C:\nwp0203\rose-garden\tests\Feature\Storefront\AccountAndContentSurfaceTest.php`
- `C:\nwp0203\rose-garden\tests\Feature\Storefront\LocalizationSurfaceTest.php`
- `C:\nwp0203\rose-garden\tests\Feature\Storefront\ProductCartCheckoutSurfaceTest.php`
- `C:\nwp0203\rose-garden\tests\Feature\Storefront\SearchLocalizationTest.php`

## Yapılan Doğrulamalar

Çalıştırılan testler:

- `php artisan test --filter=LocalizationSurfaceTest`
- `php artisan test --filter=SearchLocalizationTest`
- `php artisan test --filter=AccountAndContentSurfaceTest`
- `php artisan test --filter=ProductCartCheckoutSurfaceTest`

Doğrulanan başlıklar:

- locale-prefixed shell link continuity
- guest -> account redirect locale continuity
- KVKK consent locale-prefixed render + link continuity
- Türkçe KVKK validation message localization
- Turkish storefront üzerinde Kurdish query normalization
- blog/static page render continuity
- special occasions locale-prefixed links
- checkout agreement validation mesajlarının Türkçe render edilmesi

## Kalan Riskler

- EN/KU için mevcut `lang/*.json` dosyalarında tarihsel encoding mirası var; bu tur yeni fallback boşluklarını kapattı ama repo genelinde eski anahtarların temizlenmesi ayrı bir kalite turu gerektirir.
- Auth guest/user redirect closure’ları global middleware seviyesinde ayarlandı; storefront için doğru, fakat admin dışı özel başka web akışları varsa ayrı smoke turu ile tekrar bakılmalı.
- Bu tur browser-level manual click smoke yerine feature test + route continuity üzerinden doğrulandı.

## Sonraki Güvenli Adım

Locale integrity tarafı için sonraki güvenli adım, EN/KU `lang/*.json` anahtarlarını encoding-normalized cleanup turundan geçirmek ve ardından browser-level smoke ile header/footer/account/blog/checkout/public content yüzeylerinde tek tek locale switch navigation walkthrough yapmaktır.
