# RG Pulse Final Customer Facing Micro Fix - 2026-04-18

## Amaç
Customer-ready fast track sonunda ekranda ilk bakışta göze batan son iki kusuru kapatmak:
- header satırında search form ve utility pill grubunun gerçekten tek optik hatta görünmesi
- homepage özel günler alanının boş/yarım görünmemesi

## Önceki fixlerin neden yetersiz kaldığı
- Önceki header turu ölçüleri teknik olarak yaklaştırdı, ancak search satırı hâlâ grid/flex karışımı içinde optik olarak utility grubundan ayrı davranıyordu.
- `Ara` butonu outer shell ile fazla iç içe durduğu için taşma/kırpılma hissi üretiyordu.
- Özel günler alanında sağ taraftaki fallback notu boşluğu gerçekten doldurmuyor, alanı tasarlanmış bir kompozisyon gibi göstermiyordu.

## Header hizalama için gerçek değişiklik
- Desktop header satırı daha basit bir tek-flex akışına çekildi.
- Search form `md:flex-1` ile aynı satırın ana gövdesi haline getirildi.
- Utility cluster stretch/no-wrap ritmine sabitlendi.
- Auth/account ve WhatsApp pill grupları `rg-header-pill-row` ile aynı yükseklik akışına bağlandı.

## Search button clipping fix özeti
- Search shell `items-stretch` akışına alındı.
- İç shell padding korundu ama submit button artık dış yüksekliğe karşı savaşmıyor; satır boyunca stretch ederek oturuyor.
- `Ara` butonunda sabit dar yükseklik yerine `height: auto` + stretch modeli kullanıldı.
- Input ve submit aynı kabuk içinde tek control gibi davranacak şekilde dikey akış sadeleştirildi.

## Özel günler alanında yapılan gerçek kompozisyon düzeltmesi
- Zayıf tek satırlık fallback not kaldırıldı.
- Lead product varsa, sağ alt alan artık her durumda iki slotluk destek kompozisyonu üretiyor:
  - gerçek mini ürün kartı
  - seçki akışı kartı
  - kategori rotası kartı
  - gerekirse teslimat ritmi kartı
- Lead product yoksa, sağ taraf ekstra iki destek kartıyla yine dolu ve bilinçli görünecek şekilde genişletildi.
- Böylece alan “ürün yok” hissi yerine “kürasyonlu keşif alanı” hissi veriyor.

## Değiştirilen dosyalar
- `resources/views/layouts/partials/header.blade.php`
- `resources/views/home/sections/occasion-spotlight.blade.php`
- `resources/css/app.css`

## Yapılan doğrulamalar
- `php artisan test tests/Feature/Storefront/PublicSurfaceSmokeTest.php tests/Feature/Storefront/LayoutPublishingToStorefrontTest.php tests/Feature/Storefront/StorefrontVisibilityTest.php tests/Feature/Storefront/HeaderThemeTest.php tests/Feature/Storefront/BrandingSettingsTest.php`
- `npm run build`

## Kalan riskler
- Bu turda mobil davranış özel olarak ele alınmadı; mevcut responsive akış korunmaya çalışıldı.
- Son piksel seviyesinde optik değerlendirme için kısa bir desktop görsel smoke yine değerlidir.
- Rail JS, localization overhaul ve PDP/admin konuları bu turun dışında bırakıldı.

## Customer-ready quick close durumu
Bu turdan sonra header satırı gerçekten daha hizalı, `Ara` butonu kırpılma hissi üretmeyecek şekilde temizlenmiş ve özel günler alanı da artık boş/yarım hissettirmeyecek kadar doldurulmuş durumda. Customer-ready quick close için hazır kabul edilebilir.
