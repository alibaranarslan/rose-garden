# Harbor Ingest Report

## Amaç
- `storage/app/product-import/incoming` altındaki gerçek ürün görsellerini güvenli biçimde ürün kataloğuna bağlamak.
- Product storefront yüzeylerinde placeholder bağımlılığını azaltmak.
- Belirsiz dosyaları otomatik bağlamadan raporlamak.

## Root Cause
- Önceki ingest hattı, incoming dosyaları katalog anahtarına çok dar bir eşleme ile bağlıyordu.
- UUID adları ve katalogdaki farklı adlandırma biçimleri tek başına slug bazlı yaklaşımla güvenli yönetilemiyordu.
- Storefront tarafında bazı görsel yolları gereğinden fazla placeholder’a düşebiliyor, bazı ürün yüzeyleri de yanlışlıkla image-gated davranabiliyordu.

## Kullanılan Ingest / Eşleme Stratejisi
- Mevcut `products:import-incoming` hattı düzeltildi ve güvenli eşleşme raporu eklenerek tekrar çalıştırılabilir hale getirildi.
- Eşleşme sırası:
  - katalog anahtarı ile exact basename eşleşmesi
  - normalize edilmiş filename eşleşmesi
  - normalize edilmiş slug / `name.tr` / `name.en` eşleşmesi
- Çakışmalı veya düşük güvenli dosyalar otomatik bağlanmadı.
- Gerçek import, workspace içindeki SQLite veritabanına ve `storage/app/public/products` kopyalarına uygulandı.

## Güvenli Eşleşen Ürünler
- Toplam güvenli eşleşme: `55`
- Eşleşmelerin tamamı güvenli kabul edildi ve import edildi.
- Örnek güvenli kümeler:
  - Buketler: `rustik-kirmizi-gul-pamuk-hediye-buket`, `mor-ruya-karisik-buket`, `premium-kirmizi-gul-cipso-kubbe`, `asil-ask-siyah-kagit-gul-buket`, `pembe-zambak-gul-buket`, `lavanta-bahar-karisik-buket`
  - Saksı bitkileri: `2li-beyaz-orkide`, `2li-mor-orkide-a`, `2li-mor-orkide-b`, `areka-palm-merkez`, `areka-palm-standart`, `benjamin-ficus-saksi`, `guzmanya-saksi`, `patos-sarmaşık-saksi`, `tekli-yukka`

## Eşleşmeyen Ürünler
- Yok.

## Belirsiz Dosyalar
- Yok.

## Storefront Placeholder Cleanup
- Ürün kartlarında başka ürünün görseline kayma yapan alternate fallback kaldırıldı.
- Ürün visual strip yardımcıları artık yalnızca `whereHas('images')` ile daraltılmıyor.
- `storefrontReady` scope’u remote-only ürünleri dışlıyor, fakat image row’u olmayan ürünleri görünür bırakıyor.
- Teknik fallback olarak placeholder asset’leri korunuyor; fiziksel olarak silinmedi.

## Yapılan Doğrulamalar
- `php artisan products:import-incoming --dry-run --force`
  - Sonuç: `55` güvenli eşleşme, `0` belirsiz, `0` eşleşmeyen.
- `php artisan products:import-incoming --force`
  - Sonuç: import tamamlandı ve product image kopyaları oluşturuldu.
- Testler:
  - `tests/Unit/Support/StorefrontIncomingAssetsTest.php`
  - `tests/Feature/Console/ImportIncomingProductsCommandTest.php`
  - `tests/Feature/Storefront/StorefrontVisibilityTest.php`
  - `tests/Feature/Storefront/SearchLocalizationTest.php`
  - `tests/Feature/Storefront/LayoutPublishingToStorefrontTest.php`
  - `tests/Feature/Storefront/ProductCartCheckoutSurfaceTest.php`
  - Toplam: `15` test, `82` assertion geçti.

## Kalan Riskler
- Çalışma ortamındaki varsayılan MySQL bağlantısı erişilebilir değildi; gerçek import workspace içinde SQLite override ile yapıldı.
- Farklı bir runtime veritabanı kullanılıyorsa import komutunun aynı ortamda bir kez daha çalıştırılması gerekir.
- `storage/app/public/products` altında oluşan görseller deployment senkronizasyonuna dahil edilmelidir.

## Sonraki Güvenli Adım
- Aynı ingest komutunu production benzeri veritabanında bir kez daha dry-run ile doğrula, sonra force çalıştır.
- Ardından yalnızca product storefront yüzeylerinde görsel regressions için kısa bir smoke test çalıştır.
