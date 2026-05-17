# RG Final Smoke Readiness

Date: 2026-04-17
Workspace: `C:\nwp0203\rose-garden`

## Amaç

Final kalite kapısı turu olarak storefront, checkout ve admin-fed public yuzeylerde canliya alimi engelleyebilecek son blocker'lari aramak, dusuk riskli olanlari kapatmak ve kalan riskleri net karar ile raporlamak.

## Smoke Kapsami

- Storefront public:
  - homepage
  - listing/category
  - PDP
  - search
  - cart
  - checkout entry / success / fail
  - login / register / forgot / reset
  - blog
  - static pages
  - order tracking
- Admin-fed public dependencies:
  - branding/general settings
  - SEO canonical / robots / sitemap
  - payment settings
  - delivery zones / time slots
  - homepage module/content feed
  - imported product images
- Teknik smoke:
  - route surfaces
  - locale-prefixed surfaces
  - storefront image rendering
  - canonical / hreflang / robots / sitemap
  - deploy verify
  - sitemap generate
  - kritik feature testler

## Dogrulamalar

### Feature testler

- `php artisan test tests/Feature/Storefront/LocalizationSurfaceTest.php tests/Feature/Storefront/SearchLocalizationTest.php tests/Feature/Storefront/StorefrontVisibilityTest.php tests/Feature/Storefront/PublicSurfaceSmokeTest.php tests/Feature/Storefront/ProductCartCheckoutSurfaceTest.php tests/Feature/Checkout/CheckoutFlowTest.php tests/Feature/Storefront/BrandingSettingsTest.php tests/Feature/Storefront/HeaderThemeTest.php tests/Feature/Storefront/LayoutPublishingToStorefrontTest.php tests/Feature/Storefront/StorefrontCompatibilityTest.php tests/Feature/Storefront/LocalizedStorefrontContentTest.php`
  - Sonuc: `42` test gecti, `271` assertion gecti.
- `php artisan test tests/Feature/Storefront/PublicSurfaceSmokeTest.php tests/Feature/Storefront/StorefrontCompatibilityTest.php tests/Feature/Storefront/LocalizationSurfaceTest.php tests/Feature/Checkout/CheckoutFlowTest.php tests/Feature/Storefront/ProductCartCheckoutSurfaceTest.php tests/Feature/Storefront/SearchLocalizationTest.php`
  - Sonuc: `29` test gecti, `205` assertion gecti.

### Artisan smoke

- `php artisan route:list --json`
  - Canonical named routes ile locale-prefixed alias route gruplarinin birlikte ayakta oldugu dogrulandi.
- `php artisan route:list --name=home`
  - `GET /` canonical `home` route'u ve preview route'lar dogrulandi.
- `php artisan sitemap:generate`
  - Sonuc: `80 URL`.
- `php artisan deploy:verify --base-url=http://localhost:8001`
  - Tum kontroller gecti.

## Bu Turda Yapilan Kucuk Duzeltmeler

- Locale-siz ve locale-prefixed public navigation linkleri `StorefrontLocale::route()` ile hizalandi:
  - `resources/views/layouts/partials/header.blade.php`
  - `resources/views/layouts/partials/nav.blade.php`
  - `resources/views/layouts/checkout.blade.php`
  - `resources/views/cart/index.blade.php`
  - `resources/views/checkout/fail.blade.php`
  - `resources/views/checkout/success.blade.php`
- Account yuzeyindeki ana kisa yollar locale-aware hale getirildi:
  - `resources/views/account/partials/sidebar.blade.php`
  - `resources/views/account/dashboard.blade.php`
  - `resources/views/account/favorites.blade.php`
  - `resources/views/account/orders/index.blade.php`
  - `resources/views/account/orders/show.blade.php`
- Editoryal ve sezon yuzeylerinde locale-aware link temizligi yapildi:
  - `resources/views/blog/index.blade.php`
  - `resources/views/special-occasions/index.blade.php`
  - `resources/views/special-occasions/show.blade.php`

## Blocker Listesi

- Yok.

## Minor Issues

- Bazi ikincil utility/auth/content sayfalarinda halen canonical `route()` kullanimlari var; bu durum locale-prefix devamligini bazi derin linklerde zayiflatabilir, ancak public smoke ve checkout akislari calisiyor.

## Accepted Risks

- Canonical named route modeli ile locale-prefixed alias modeli bilerek birlikte yasiyor.
- Repo genelinde tam `StorefrontLocale::route()` migrasyonu bu turda tamamlanmadi.
- Ayri bir cleanup turunda `account/*`, `pages/*`, `blog/show` ve diger utility akislardaki kalan direct `route()` kullanimlari standartlastirilabilir.

## Canliya Alim Karari

Canliya alim icin hazir

## Onerilen Son Rollout Sirasi

1. `php artisan deploy:verify --base-url=http://localhost:8001` sonucunu production-benzeri ortamda bir kez daha calistir.
2. `php artisan sitemap:generate` cikti dosyasini deploy artifact akisine dahil et.
3. Public smoke'ta homepage, PLP, PDP, search, cart ve checkout son bir kez manuel kontrol et.
4. Payment, delivery zone ve delivery time slot operasyonel verilerini canli ortamda son kez dogrula.
5. Sonra deployment'i yayinla ve ilk dakikalarda `/health`, `/robots.txt`, `/sitemap.xml`, `/odeme/basarili` ve `order tracking` yuzeylerini izle.
