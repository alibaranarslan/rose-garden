# RG Pulse Customer-Ready Micro Fix - 2026-04-18

## Amaç
Hızlı müşteri-facing scope içinde homepage ve shell tarafında ilk bakışta göze batan son kusurları kapatmak: kırık Türkçe shell copy, header search/control hizası ve yarım hisseden özel günler kompozisyonu.

## Kapatılan müşteri-facing kusurlar
- Header ve shell içinde görünen ASCII/transliterasyon copy kırıkları temizlendi.
- Header search bar ile tema, dil ve cart kontrolleri tek yükseklik ritmine yaklaştırıldı.
- Homepage özel günler bloğunda tek ürün kaldığında oluşan boş/yarım his iki ek sinyal kartıyla kapatıldı.

## Shell copy düzeltmeleri
- Header preview badge, search placeholder, search aria, auth CTA, account/logout, contact ve order tracking metinleri doğru Türkçe karakterlerle güncellendi.
- Nav içindeki `Özel Günler`, `Keşfet`, `Yaklaşan Özel Gün`, `Tümünü Gör` gibi görünür metinler doğru Türkçe anahtarlara taşındı.
- Theme toggle ve language switcher içindeki bozuk encoding kaynaklı metinler düzeltildi.
- Homepage category showcase içindeki `Özel Günler` etiketi de aynı müşteri-facing polish kapsamına alındı.

## Header hizalama düzeltmesi
- Header search form yüksekliği `h-11` ile sabitlendi.
- Header control cluster için ortak flex/gap ritmi tanımlandı.
- Theme toggle, language switcher ve cart control ölçüleri `2.75rem` yüksekliğe normalize edildi.
- Amaç yatay ritmi temizlemekti; büyük görsel redesign yapılmadı.

## Özel günler alanında yapılan hızlı iyileştirme
- Mevcut lead product-first kompozisyon korundu.
- Eğer sağ tarafta yeterli mini ürün kartı yoksa, boş not bloğu yerine:
  - seçkiye dönüş sinyali
  - teslimat/tarih ritmi sinyali
  eklendi.
- Böylece blok yarım görünmek yerine hala ticari ve kontrollü kalıyor.

## Değiştirilen dosyalar
- `resources/views/layouts/partials/header.blade.php`
- `resources/views/layouts/partials/nav.blade.php`
- `resources/views/home/sections/occasion-spotlight.blade.php`
- `resources/views/home/sections/category-showcase.blade.php`
- `resources/views/components/language-switcher.blade.php`
- `resources/views/components/theme-toggle.blade.php`
- `resources/css/app.css`

## Yapılan doğrulamalar
- `php artisan test tests/Feature/Storefront/PublicSurfaceSmokeTest.php tests/Feature/Storefront/LayoutPublishingToStorefrontTest.php tests/Feature/Storefront/StorefrontVisibilityTest.php tests/Feature/Storefront/HeaderThemeTest.php tests/Feature/Storefront/BrandingSettingsTest.php`
- `php artisan view:cache`
- `npm run build`

## Kalan riskler
- Bu tur mobile-specific polish değildi; mobile’da sadece mevcut davranışı korumaya çalıştı.
- Bazı merchandising/localization kapsamı dışı metinler repo genelinde hala ASCII anahtarlarla yaşayabilir; bu tur yalnızca müşteri karşısında görünen shell/home kusurlarını hedefledi.
- Header hizası build ve smoke seviyesinde temiz; son piksel seviyesinde tarayıcı görsel kontrol yine faydalı olur.

## Hızlı scope sonrası önerilen karar
Customer-ready hızlı scope bu yüzey için yeterli. Sonraki güvenli adım, geniş localization cleanup veya JS/rail/pdp gibi daha derin işlere dönmeden önce kısa bir manuel visual smoke ile desktop storefront’u onaylamak olur.
