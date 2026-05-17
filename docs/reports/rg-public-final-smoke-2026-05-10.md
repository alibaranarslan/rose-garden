# Rose Garden Public Final Smoke - 2026-05-10

## Kapsam

- Public yüzeyler masaüstü ve mobil görünümde yerel Playwright ile tarandı.
- In-app Browser runtime 60 saniyede timeout verdiği için fallback olarak yerel Playwright kullanıldı.
- Hedef: müşteriye gösterimde negatif ilk izlenim yaratabilecek 500/404, console error, görünür kırık görsel, boş ekran, framework overlay, bozuk Türkçe metin ve temel etkileşim sorunlarını yakalamak.

## Taranan Yüzeyler

- `/`, `/tr`
- `/urunler`
- `/kategori/smoke-ops-kategori-ops223119`
- `/urun/smoke-ops-urun-ops223119`
- `/sepet`, `/odeme`
- `/arama?q=smoke`
- `/ozel-gunler`, `/blog`, `/iletisim`, `/sss`, `/teslimat-bilgileri`
- `/robots.txt`, `/sitemap.xml`

## Bulgular

- Tüm taranan rotalar 200 döndü.
- Console error/page error görülmedi.
- Görünür kırık görsel kalmadı.
- Boş ekran veya framework error overlay görülmedi.
- Arama etkileşimi `smoke` sorgusunda sonuç sayfasına geçti ve sonuç metni görüldü.
- Ürün detayda sepete ekleme sinyali görüldü.

## Düzeltme

- Ürün detay lightbox görselinde başlangıçta boş `src` yüzünden mevcut ürün URL'si resim gibi deneniyordu.
- `resources/views/products/show.blade.php` içinde lightbox görseli `lightboxImage || activeImage` fallback'iyle güncellendi.
- `tests/Feature/Storefront/StorefrontVisibilityTest.php` içine lightbox fallback regresyon testi eklendi.

## Görsel Kanıt

- Desktop ana sayfa: `C:\Users\Ali\AppData\Local\Temp\rg-public-smoke-20260510-000223\desktop-home.png`
- Desktop ürün detay: `C:\Users\Ali\AppData\Local\Temp\rg-public-smoke-20260510-000223\desktop-product.png`
- Mobil ana sayfa: `C:\Users\Ali\AppData\Local\Temp\rg-public-smoke-20260510-000223\mobile-home.png`

## Test Kanıtı

- `php artisan test tests\Feature\Storefront\StorefrontVisibilityTest.php tests\Feature\Storefront\PublicSurfaceSmokeTest.php tests\Feature\Storefront\ProductCartCheckoutSurfaceTest.php`
  - 24 test, 124 assertion, başarılı.
- `php artisan test tests\Feature\Admin\AdminLanguageQualityTest.php`
  - 2 test, 51 assertion, başarılı.

## Kalan Not

- Public görünür yüzey final smoke açısından temiz.
- Mevcut fixture verilerinde ürün/kategori adları smoke içerikli olduğu için canlı müşteri demosundan önce gerçek katalog içerikleriyle son görsel tur önerilir.
