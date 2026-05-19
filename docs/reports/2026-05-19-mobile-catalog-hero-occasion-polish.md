# Rose Garden Mobil Katalog, Hero ve Özel Gün Polish

Tarih: 2026-05-19

## Kapsam

- Mobil ürün listeleme sayfasının katalog hissi güçlendirildi.
- Mobil ana hero'da metin ve ürün görseli daha kompakt, hizalı ve görseli daha belirgin bir kompozisyona çekildi.
- Ana sayfadaki özel gün vitrini yalnızca yakın gelecekteki aktif özel günler için gösterilecek şekilde daraltıldı.

## Değişiklikler

- `SpecialOccasion::nearestActiveUpcoming()` eklendi. Varsayılan eşik 90 gün.
- `HomeModuleDataService` ve legacy `HomeController`, ana sayfa özel gün seçimi için yeni yakın-gelecek resolver'ını kullanıyor.
- PLP hero'ya `rg-plp-hero--mobile-slim` sınıfı eklendi.
- Mobil CSS'te PLP hero daha kısa, kategori pill'leri daha kompakt, katalog grid'i daha sıkı iki kolon, ürün kartları daha kısa olacak şekilde güncellendi.
- Mobil hero kartında arka plan ürün görseli büyütüldü ve sağdan daha kontrollü konumlandırıldı; gradient metin okunurluğunu koruyacak şekilde ayarlandı.

## Doğrulama

- `php artisan test tests\Feature\Storefront\PublicSurfaceSmokeTest.php` geçti: 18 test, 89 assertion.
- `npm run build` geçti.
- Playwright mobile smoke, 390x844 viewport:
  - `/tr` title `Rose Garden`, hero kartı render oldu, yatay overflow yok.
  - `/tr/urunler` title `Rose Garden Ürünleri | Rose Garden`, `rg-plp-hero--mobile-slim` ve `rg-catalog-grid` render oldu.
  - `/tr/urunler` katalog grid'i 2 kolon verdi: `174.688px 174.688px`.
  - `/tr/urunler` 16 katalog kartı render oldu, yatay overflow yok.
  - Console error/warn yok.
- Canlı deploy: Rose Garden server commit `622075a`.
- Canlı Playwright mobile smoke, 390x844 viewport:
  - `https://rosegardencicekcilik.com.tr/tr` title `Rose Garden`, hero kartı render oldu, yatay overflow yok.
  - `https://rosegardencicekcilik.com.tr/tr/urunler` title `Rose Garden Ürünleri | Rose Garden`, `rg-plp-hero--mobile-slim` ve `rg-catalog-grid` render oldu.
  - Canlı katalog grid'i 2 kolon verdi: `174.688px 174.688px`.
  - Canlı katalogda 16 ürün kartı render oldu, yatay overflow yok.
  - Canlı console error/warn yok.

## Kanıt Dosyaları

- `C:\Users\Ali\AppData\Local\Temp\rg-next-three-mobile-home.png`
- `C:\Users\Ali\AppData\Local\Temp\rg-next-three-mobile-catalog.png`
- `C:\Users\Ali\AppData\Local\Temp\rg-next-three-live-mobile-home.png`
- `C:\Users\Ali\AppData\Local\Temp\rg-next-three-live-mobile-catalog.png`

## Not

- Local DB'de yakın aktif özel gün bulunmadığı smoke'ta ana sayfa özel gün bloğu görünmedi. Bu, yeni davranış açısından doğru kabul edildi: uzak/yıllık rollover tarihleri ana sayfada "yaklaşan" gibi gösterilmeyecek.
