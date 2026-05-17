# RG Pulse Homepage Fast Track - 2026-04-18

## Amac
Musteriye hizli gosterimde ilk bakista negatif sinyal ureten homepage kusurlarini kisa ve dar bir turda azaltmak. Bu tur derin redesign veya altyapi calismasi degildir; yalnizca homepage merchandising ve gorusel tekrar riskini hizli sekilde toparlar.

## Hizli musteri-facing homepage kusurlari
- Ozel gun blogu ticari ve tasarimsal olarak yeterince guclu hissettirmiyordu.
- Hero cevresinde ve homepage ust yarisinda ayni urun gorselleri fazla tekrar hissi uretiyordu.
- Sayfa genelinde buyuk sorun degil ama musteri gozune batabilecek son merchandising pürüzleri kalmisti.

## Yapilan duzeltmeler
- `app/Services/HomeModuleDataService.php`
  Kategori cover secimlerinde hero urunu ve secili vitrin urunu dislanarak ust yari gorsel tekrarinin azaltilmasi saglandi.
- `resources/views/home/sections/occasion-spotlight.blade.php`
  Ozel gun blogu sag tarafta daha dolu ve daha kabul edilebilir bir kompozisyona cekildi:
  - tek guclu lead urun gorseli
  - net urun yonlendirmesi
  - kalan urunler icin destek kartlari
- Ozel gun blogu, bos/yarim bir ara katman yerine daha ikna edici bir kesif alani gibi davranacak sekilde sade ama daha guclu hale getirildi.

## Degistirilen dosyalar
- `C:\nwp0203\rose-garden\app\Services\HomeModuleDataService.php`
- `C:\nwp0203\rose-garden\resources\views\home\sections\occasion-spotlight.blade.php`

## Yapilan dogrulamalar
- `php artisan test tests/Feature/Storefront/PublicSurfaceSmokeTest.php tests/Feature/Storefront/LayoutPublishingToStorefrontTest.php tests/Feature/Storefront/StorefrontVisibilityTest.php`
- `php artisan view:cache`
- `npm run build`

## Kalan riskler
- Homepage rail Alpine/runtime problemi bu turun disinda bilinclı olarak birakildi.
- Localization overhaul, PDP locale 404 ve admin taraflari bu turda ele alinmadi.
- Gorsel tekrar azaltilsa da katalog derinligi sinirliysa bazi tekrarlar tamamen sifirlanmayabilir; fake asset eklenmedi.

## Sonraki en yuksek ROI adim
Homepage icin sonraki en yuksek ROI adim, ayri bir dar turda rail Alpine/controller kirigini kapatmak olur; bu, musteri demosunda console ve interaction kalitesini en hizli iyilestirecek adimdir.
