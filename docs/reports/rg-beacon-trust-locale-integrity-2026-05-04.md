# RG Beacon Trust & Locale Integrity

Date: 2026-05-04
Workspace: `C:\nwp0203\rose-garden`

## Amaç

Müşteri güvenini doğrudan zedeleyen locale continuity ve karışık dil kusurlarını storefront scope içinde kapatmak.
Bu tur görsel redesign değil; PDP locale switch, locale-prefixed content navigation ve ana customer-facing shell copy güvenilirliği turudur.

## Root Cause Sınıfları

1. `wrong locale-aware route generation`
   - Locale-prefixed closure proxy rotaları `{locale}` ve `{slug}` / `{id}` parametrelerini sırayla bağladığı için detay rotalarında locale değeri slug/id yerine geçebiliyordu.
2. `mixed-source shell copy issue`
   - Live storefront shell içinde birkaç customer-facing label önceki cleanup turlarından sonra da dağınık kaldı.
3. `legacy mojibake residue`
   - Bazı locale/test beklentileri hâlâ eski bozuk metinleri referanslıyordu.

## Düzeltilen Locale Route Sorunları

- PDP locale switch 404 kök nedeni kapatıldı.
  - Önceki davranış:
    - `/{locale}/urun/{slug}` isteğinde closure proxy `slug` yerine `locale` değerini controller'a geçirebiliyordu.
    - Sonuç: `/en/urun/{slug}` ve `/ku/urun/{slug}` için `ProductController@show('en')` benzeri yanlış çağrı ve `404`.
  - Yeni davranış:
    - Route proxy `Request::route()` üzerinden named parametreyi okuyup canonical controller'a doğru slug ile delege ediyor.

- Aynı root cause altındaki diğer customer-facing detail rotaları da aynı modelle düzeltildi:
  - `/{locale}/kategori/{slug}`
  - `/{locale}/blog/{slug}`
  - `/{locale}/sayfa/{slug}`
  - `/{locale}/ozel-gunler/{slug}`
  - `/{locale}/sifre-sifirla/{token}`
  - locale-prefixed account order routes
  - locale-prefixed address actions
  - locale-prefixed checkout payment continuation

## PDP Locale 404 Kök Nedeni ve Çözümü

### Kök neden

`routes/web.php` içindeki locale alias closure'ları bazı detail rotalarda şu deseni kullanıyordu:

- `function (string $slug, ProductController $controller, ?string $locale = null)`

Locale-prefixed route grubunda ilk route parametresi `{locale}` olduğu için Laravel closure'a önce locale değerini enjekte ediyordu.
Bu nedenle:

- `/en/urun/rustik-kirmizi-gul-pamuk-hediye-buket`
- efektif olarak
- `ProductController::show('en')`
- çağrısına dönüşebiliyordu.

### Çözüm

- Locale alias closure'ları request-driven delegasyona çevrildi.
- Slug/id/token/orderNumber artık `Request::route('...')` ile açıkça okunuyor.
- Adres ve order tabanlı utility rotalarda model binding locale ile karışmasın diye model resolve adımı closure içinde açık hale getirildi.

## Düzeltilen Karışık Dil Yüzeyleri

- Language switcher locale label'ları normalize edildi:
  - `Türkçe`
  - `English`
  - `Kurdî`

- Homepage Layout Studio shell'inde İngilizce/Kürtçe yüzeylere sızan birkaç preview/fallback label translation zincirine bağlandı:
  - preview state
  - draft badge
  - rail headings
  - rail CTA labels

- Localization feature test beklentileri canlı customer-facing çıktıya hizalandı; eski mojibake beklentileri kaldırıldı.

## Admin-fed Locale Değerlendirmesi

Bu turda admin live scenario testine girilmedi, ancak gerçek katalog snapshot'ı üzerinde localized field completeness kontrolü yapıldı.

Sonuç:

- `products_total = 70`
- `products_missing_en_name = 0`
- `products_missing_ku_name = 0`
- `pages_total = 9`
- `pages_missing_en_title = 0`
- `pages_missing_ku_title = 0`
- `posts_total = 4`
- `posts_missing_en_title = 0`
- `posts_missing_ku_title = 0`

Değerlendirme:

- Bu snapshot'ta admin-fed ana customer-facing içeriklerde EN/KU title/name boşluğu görünmedi.
- Bu yüzden bu turda admin-fed fallback için ek agresif code path gevşetmesi yapılmadı.
- Route continuity fix'i sonrası admin-fed localized content çözümleme zinciri feature test ile de doğrulandı.

## Değiştirilen Dosyalar

- `C:\nwp0203\rose-garden\routes\web.php`
- `C:\nwp0203\rose-garden\app\Support\StorefrontLocale.php`
- `C:\nwp0203\rose-garden\resources\views\components\language-switcher.blade.php`
- `C:\nwp0203\rose-garden\resources\views\home\layout-studio.blade.php`
- `C:\nwp0203\rose-garden\lang\en.json`
- `C:\nwp0203\rose-garden\lang\ku.json`
- `C:\nwp0203\rose-garden\tests\Feature\Storefront\LocalizationSurfaceTest.php`
- `C:\nwp0203\rose-garden\tests\Feature\Storefront\TrustLocaleIntegrityTest.php`

## Yapılan Doğrulamalar

### Feature testler

- `php artisan test --filter=LocalizationSurfaceTest`
- `php artisan test --filter=TrustLocaleIntegrityTest`
- `php artisan test --filter=StorefrontCompatibilityTest`
- `php artisan test --filter=ProductCartCheckoutSurfaceTest`
- `php artisan test --filter=PublicSurfaceSmokeTest`

### Doğrudan runtime doğrulama

Locale-prefixed detail route smoke:

- `/en/urun/{slug}` => `200`
- `/en/kategori/{slug}` => `200`
- `/en/blog/{slug}` => `200`
- `/en/sayfa/{slug}` => `200`
- `/en/ozel-gunler/{slug}` => `200`

### Admin-fed localized content doğrulaması

`TrustLocaleIntegrityTest` ile şu zincir doğrulandı:

- EN PDP localized product content
- EN category localized title
- EN blog localized title
- EN page localized title
- EN special occasion localized title
- KU PDP/blog/page/special occasion localized content resolution

## Kalan Riskler

- `lang/*.json` içinde eski mojibake key mirası hâlâ geniş kapsamlı biçimde duruyor; bu tur customer-facing trust kusurlarını kapattı ama repo genelinde translation payload normalization tamamlanmış değil.
- Bu tur admin live scenario walkthrough içermiyor; admin panelden yeni içerik girişinin browser-level smoke'u ayrı scope olarak ele alınmalı.
- Daha derin utility/content yüzeylerinde locale continuity şu turda doğrudan regression üretmiyor, ancak route proxy deseninin dışındaki kalan legacy `route()` kullanımları ayrı cleanup turunda yeniden taranabilir.

## Sonraki Güvenli Adım

`Admin live scenario` scope açılıp:

1. admin'den güncellenen localized product/page/blog content'in storefront'a browser-level yansıması doğrulanmalı
2. kalan `lang/*.json` legacy key seti kontrollü normalization turuna alınmalı
3. PDP ve content surfaces için kısa manuel locale-switch walkthrough tekrar çalıştırılmalı
