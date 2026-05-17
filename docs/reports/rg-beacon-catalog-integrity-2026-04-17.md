# RG Beacon Catalog Integrity - 2026-04-17

## Amaç

Harbor ingest sonrası storefront katalog davranışını güvenilir hale getirmek:

- kategori sayfalarının gerçekten seçili kategori ağacına sadık çalışması
- kategori üst bilgisi ve grid sayım mantığının aynı katalog gerçeğini göstermesi
- search -> PDP -> related zincirinde temel katalog semantiğinin çelişmemesi
- ürün/görsel eşleşmesi problemi ile query/route problemi birbirinden net ayrılması

## Root Cause Sınıfları

1. `wrong locale-aware route generation / parameter binding`
   - `ProductController@index(Request $request, ?string $locale = null, ?string $slug = null)` imzası locale alias grup için doğruydu, fakat canonical `/kategori/{slug}` rotası `slug` değerini controller içinde efektif olarak category filtresine taşımıyordu.
   - Sonuç: kategori route’u pratikte tüm katalogu döndürebiliyor, bu da manual QA’de görülen "ilk kartlar doğru, sonra alakasız ürünler karışıyor" semptomunu üretiyordu.

2. `category query leakage / subtree count mismatch`
   - sidebar ve hero link count’ları doğrudan kategori pivot sayımına dayanıyordu.
   - Bu yaklaşım, ürünler yalnızca child kategoriye bağlıysa parent kategori sayısını eksik gösteriyor; kategori başlığı ile grid mantığı arasında güven kaybı üretiyordu.

3. `fallback contamination`
   - PDP related query’si ürünün tüm kategori ID’leri üzerinden geniş eşleşme yapıyordu.
   - Root + child birlikte atanmış ürünlerde related havuzu gereksiz genişliyordu; semantik olarak daha spesifik leaf kategori yerine daha gevşek kategori ortaklığı öne çıkıyordu.

4. `image mismatch`
   - Bu turda sistematik image/product mismatch kanıtı bulunmadı.
   - Mevcut Harbor snapshot’ta problem query/route semantiği tarafında tekrar üretilebildi; image pairing tarafında değil.

5. `data quality issue`
   - Mevcut SQLite katalog örnekleminde çapraz root category attachment saptanmadı.
   - Bu turda ana sorun veri kalitesi değil, runtime route/query ownership oldu.

## Kategori / Listing Query Ownership Özeti

- Kategori ve listing owner: `app/Http/Controllers/ProductController.php`
- Search owner: `app/Http/Controllers/SearchController.php`
- Home storefront product module owner: `app/Services/HomeModuleDataService.php`
- Katalog category tree ve display-category semantiği: `app/Support/StorefrontCatalog.php`
- Kategori shell / count görünümü: `resources/views/products/index.blade.php` ve `resources/views/components/product-list-layout.blade.php`
- Route binding owner: `routes/web.php`

## Düzeltilen Katalog Sorunları

1. Kategori route param binding düzeltildi.
   - `/urunler` ve `/kategori/{slug}` rotaları wrapper closure ile controller’a açık parametre geçecek şekilde güncellendi.
   - Locale alias davranışı korunurken canonical category slug artık filtreye güvenilir biçimde ulaşıyor.

2. Kategori count semantiği subtree bazına taşındı.
   - Yeni `StorefrontCatalog` helper’ı storefront-ready product/category eşleşmelerini distinct product set olarak topluyor.
   - Parent kategoriler artık yalnızca direct pivot sayımı değil, child subtree içindeki gerçek storefront ürün setiyle sayılıyor.

3. Product card category semantiği derin kategoriye sabitlendi.
   - Catalog helper ürün kategorilerini depth-first sıralıyor.
   - Home/search/listing/PDP related yüzeylerinde kartlar artık kök kategori yerine daha spesifik kategori etiketini öne çekiyor.

4. Related products havuzu sıkılaştırıldı.
   - PDP related query’si önce ürünün en derin kategori grubundan ürün topluyor.
   - Yeterli ürün yoksa ancak o zaman ürünün kalan kategori havuzuna kontrollü fallback yapıyor.

5. Search/catalog category continuity hizalandı.
   - Search sonuçları artık category-parent ve variant verisini aynı katalog helper’ı ile alıyor.
   - Search kartları ile PLP/PDP kartlarında category label davranışı aynı mantığa bağlandı.

6. Category shell count tutarlılığı düzeltildi.
   - Kategori içindeyken "Tüm Ürünler" pill count artık mevcut grid count’u değil, gerçek tüm katalog toplamını gösteriyor.

## Düzeltilen Kategori / Ürün Uyuşmazlıkları

- Canonical category route tüm katalogu döndürdüğü için alakasız ürün sızıntısı oluşuyordu; bu düzeltildi.
- Parent category count’ları child-only ürünleri kaçırabildiği için kategori başlığı ile filtre/count algısı drift ediyordu; bu düzeltildi.
- PDP related alanı root category ortaklığı yüzünden fazla geniş ürün havuzu kullanabiliyordu; bu daraltıldı.

## Image Pairing Değerlendirmesi

SQLite katalog örnekleminde ilk 12 aktif ürün için slug/category/image_count kesiti kontrol edildi:

- bouquet ürünleri `cicek-buketleri` ve ilgili bouquet child kategorileriyle eşleşiyor
- orchid/dracaena ürünleri `saksi-cicekleri` ve ilgili plant child kategorileriyle eşleşiyor
- görsel sayıları beklenen ürünlerde mevcut, Harbor ingest sonrası sistematik "yanlış ürün görseli" paterni görülmedi

Sonuç:

- bu turda gözlenen storefront problem `image mismatch` olarak sınıflandırılmadı
- ana problem `wrong route parameter binding + subtree/count semantics + related fallback scope`

## Değiştirilen Dosyalar

- `app/Support/StorefrontCatalog.php`
- `app/Http/Controllers/ProductController.php`
- `app/Http/Controllers/SearchController.php`
- `app/Services/HomeModuleDataService.php`
- `resources/views/components/product-list-layout.blade.php`
- `resources/views/products/index.blade.php`
- `routes/web.php`
- `tests/Feature/Storefront/CatalogIntegrityTest.php`

## Yapılan Doğrulamalar

### Testler

- `php artisan test --filter=CatalogIntegrityTest`
- `php artisan test --filter=PublicSurfaceSmokeTest`
- `php artisan test --filter=SearchLocalizationTest`
- `php artisan test --filter=StorefrontVisibilityTest`

### Geçen davranışlar

- category slug’a göre listing gerçekten seçili subtree ile filtreleniyor
- category ürün sayısı ve filter/link count’ları subtree mantığıyla hizalanıyor
- locale-prefixed storefront listing rotaları bozulmadan çalışıyor
- search localization regresyon açmadan devam ediyor
- home/related surfaces storefront-ready visibility davranışını koruyor

### Ek veri kontrolü

- aktif ürün/category örneklemi SQLite üzerinden gözden geçirildi
- bouquet root direct count: `31`
- potted root direct count: `24`
- image pairing örnekleminde sistematik yanlış ürün-görsel kanıtı görülmedi

## Kalan Riskler

1. `StorefrontCompatibilityTest` içinde order-tracking yüzeyindeki eski encoding hassasiyetli assertion bu ortamda hâlâ düşüyor.
   - Bu turdaki katalog fix’iyle ilişkili görünmüyor.
   - Ayrı bir text-encoding cleanup turunda ele alınmalı.

2. `StorefrontCatalog` distinct subtree set hesabını PHP tarafında yapıyor.
   - Mevcut kategori hacmi için düşük riskli ve okunur.
   - Kategori ağacı ciddi büyürse DB-side aggregate optimizasyonu düşünülebilir.

3. Harbor ingest snapshot’ında veri kalitesi temiz görünüyor.
   - Gelecek import’larda çapraz root assignment üreten yeni veri gelirse bunun için ingest-time guard eklemek gerekebilir.

## Sonraki Güvenli Adım

Homepage polish turundan önce düşük riskli sonraki adım:

- storefront metin encoding cleanup ve smoke test turu
- özellikle order tracking / static content yüzeylerinde mojibake kalıntılarının temizlenmesi
