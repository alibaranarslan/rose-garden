# RG Beacon Storefront Regressions

Date: 2026-04-17
Workspace: `C:\nwp0203\rose-garden`
Scope: storefront shell, language switcher, search localization, catalog visibility

## Amaç

- Harbor ingest sonrasında storefront shell ve katalog yüzeylerinde kalan regresyonları kapatmak.
- Dil menüsünü header içinde okunur, rahat tıklanır ve clip olmayan hale getirmek.
- `name` ve `short_description` alanlarındaki `tr/en/ku` aramasını gerçek katalog akışında daha dayanıklı hale getirmek.
- Katalog, homepage modülleri, search ve PDP related yüzeylerinde Harbor sonrası ürün görünürlüğünü doğrulamak ve image-gated daralmayı kapatmak.

## Root Cause

- UI sorunu:
  - Header içindeki language switcher, `header.blade.php` tarafındaki `overflow-hidden` kapsayıcı içinde kaldığı için dropdown clipping üretiyordu.
  - Tetikleyici yalnızca dar bir locale badge gibi davranıyor, aktif locale label yeterince görünmüyordu.
- Search query sorunu:
  - Search sorgusu tek bir `%full phrase%` eşleşmesine aşırı bağlıydı.
  - EN/KU query birden fazla kelimeyle ya da `name` ve `short_description` alanlarına dağılmış halde geldiğinde sonuçsuz kalabiliyordu.
- Catalog visibility / scope sorunu:
  - `HomeModuleDataService::attachCategoryCoverPaths()` hâlâ `whereHas('images')` ile ürün seçiyordu.
  - Bu, Harbor sonrası genel `storefrontReady` modeli genişlemiş olsa bile homepage category/discovery tarafında gereksiz image-gating bırakıyordu.
- Yardımcı neden:
  - Merkezi `StorefrontLocale::route()` helper mevcut olmasına rağmen shell/search/catalog/home/PDP yüzeylerindeki bazı linkler hâlâ düz `route()` kullanıyordu; locale prefix korunmuyordu.

## Düzeltilen Regressions

- Language switcher:
  - Header kapsayıcısı dropdown'ı clip etmeyecek şekilde düzeltildi.
  - Switcher butonunda aktif locale label görünür hale getirildi.
  - Dropdown genişletildi; locale label/code ikilisi daha okunur ve rahat tıklanır oldu.
  - Z-index ve escape/outside-close davranışı netleştirildi.
- Search localization:
  - Search sorgusu full-phrase eşleşmesini korurken çok kelimeli query için token-aware fallback eklendi.
  - Her token `name` ve `short_description` içindeki `tr/en/ku` JSON alanlarında aranıyor.
  - Search form ve ilgili storefront CTA/linklerinde locale-aware `StorefrontLocale::route()` kullanıldı.
- Catalog visibility:
  - Homepage category cover seçiminde `whereHas('images')` image-gating'i kaldırıldı.
  - Harbor sonrası `storefrontReady` görünürlük modeli korunarak homepage category/discovery tarafı da aynı mantıkla hizalandı.
  - PDP related ve storefront kart/mini kart/home rail linkleri locale-aware helper ile stabilize edildi.

## Değiştirilen Dosyalar

- `app/Http/Controllers/SearchController.php`
- `app/Services/HomeModuleDataService.php`
- `resources/views/layouts/partials/header.blade.php`
- `resources/views/components/language-switcher.blade.php`
- `resources/views/search/results.blade.php`
- `resources/views/products/index.blade.php`
- `resources/views/products/show.blade.php`
- `resources/views/components/product-list-layout.blade.php`
- `resources/views/components/product-card.blade.php`
- `resources/views/components/product-card-mini.blade.php`
- `resources/views/components/category-card.blade.php`
- `resources/views/components/store-hero.blade.php`
- `resources/views/components/trust-badges.blade.php`
- `resources/views/home/layout-studio.blade.php`
- `resources/views/home/sections/category-showcase.blade.php`
- `tests/Feature/Storefront/LocalizationSurfaceTest.php`
- `tests/Feature/Storefront/SearchLocalizationTest.php`
- `tests/Feature/Storefront/StorefrontVisibilityTest.php`

## Yapılan Doğrulamalar

- Testler:
  - `php artisan test --filter=LocalizationSurfaceTest`
  - `php artisan test --filter=SearchLocalizationTest`
  - `php artisan test --filter=StorefrontVisibilityTest`
- Gerçek katalog verisi doğrulaması:
  - Varsayılan MySQL bağlantısı bu ortamda erişilemediği için Harbor ingest raporundaki yöntemle workspace SQLite kopyası kullanıldı.
  - SQLite doğrulamasında:
    - `products_total = 56`
    - `storefront_ready = 55`
    - `featured = 13`
    - `new = 55`
  - Published homepage data collect sonucunda:
    - `sections = announcement_bar, hero, category_showcase, featured_showcase, occasion_spotlight, new_arrivals, trust_badges, blog_preview`
    - `categories = 6`
    - `featured = 8`
    - `new = 6`
  - Sample PDP related doğrulaması:
    - örnek storefront-ready ürün için `related_count = 30`
  - SQLite JSON arama doğrulaması:
    - `english_purple_matches = 8`
    - `kurdish_gul_matches = 18`

## Kalan Riskler

- Varsayılan `.env` MySQL bağlantısı bu ortamda kapalı olduğu için runtime verification workspace SQLite kopyasıyla yapıldı.
- Repo genelindeki tüm storefront linkleri henüz `StorefrontLocale::route()` helper'a taşınmış değil; bu tur shell/search/catalog/home/PDP yüzeyleriyle sınırlı tutuldu.
- Language switcher için tam browser-level pointer smoke testi bu turda ayrı bir e2e araçla yapılmadı; DOM/CSS düzeltmesi ve feature coverage ile sınırlandı.

## Sonraki Güvenli Adım

- Aynı locale-aware route helper temizliğini storefront içindeki kalan utility/blog/static CTA yüzeylerine dar kapsamda yaymak ve ardından kısa bir manual browser smoke test çalıştırmak.
