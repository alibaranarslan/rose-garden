# Surface Trust Micro-Fix

## Amaç
Üç müşteri-facing yüzeyde güven hissini artırmak: guest loyalty popup’ı daha ortalı ve daha zengin hale getirmek, cookie consent davranışının gerçekten kırık olup olmadığını netleştirmek ve browser tab title encoding/render sorununu kapatmak.

## Popup İçin Yapılan Görsel Düzenleme
- Guest loyalty prompt sağ-alt toast yerine merkezli mini modal yapısına taşındı.
- Popup artık iki kolonlu, daha dengeli bir kart olarak görünüyor.
- Sol tarafta mevcut ürün görseli kullanılıyor; ürün yoksa güvenli placeholder fallback’e düşüyor.
- CTA ve dismiss davranışı korundu.
- Cooldown mantığı değişmedi.

## Cookie Consent İnceleme Sonucu
- Cookie consent broken değil.
- Banner, fresh browser session’da mount oluyor ve `localStorage` state yoksa görünür.
- Önceki oturumda consent kaydı olduğu için görünmemesi beklenen davranış.
- Bu turda zorla açılan bir banner eklenmedi.

## Title / Encoding Kök Nedeni
- Browser title zincirinde `site_name` fallback’ı boş durumda `Rose Garden`a düşüyordu.
- Bu da page title’ın `Rose Garden | Rose Garden` gibi eksik bir çıktıya inmesine yol açıyordu.
- Kök neden render tarafında brand fallback’ının yeterince güçlü olmamasıydı; cookie consent ile ilişkili değildi.

## Title / Encoding İçin Yapılan Düzeltme
- `App\Support\SiteBranding` fallback’ı `Rose Garden Çiçek Çikolata` olacak şekilde güçlendirildi.
- Footer/brand fallback metinleri UTF-8 ve okunur hale getirildi.
- Regression testi eklenerek boş settings durumunda title’ın doğru marka adını üretmesi sabitlendi.

## Değiştirilen Dosyalar
- `app/Support/SiteBranding.php`
- `resources/views/components/guest-loyalty-prompt.blade.php`
- `tests/Feature/Storefront/BrandingSettingsTest.php`

## Yapılan Doğrulamalar
- `php artisan test --filter=BrandingSettingsTest`
- `php artisan test --filter=PublicSurfaceSmokeTest`
- `php artisan test --filter=ProductCartCheckoutSurfaceTest`
- `npm run build`
- Browser smoke:
  - title doğrulandı
  - cookie consent fresh session’da göründü
  - guest loyalty popup göründü
  - console/page error oluşmadı
  - popup heading box merkezli kart içinde render oldu

## Kalan Riskler
- Popup görsel alanı ürün fallback’ine düşebilir; bu durumda yine de güvenli placeholder kullanır.
- Cookie consent önceki ziyaret tercihleri varsa görünmez; bu beklenen davranış.
- Browser smoke kısa ve dar kapsamlı; yalnızca bu üç yüzeyin kritik pürüzlerini doğruladı.

## Sonraki Güvenli Adım
- İstenirse guest loyalty popup için ürün görseli seçimini daha deterministik hale getirmek.
- İstenirse cookie consent için sadece “tercihleri yeniden aç” gibi bir geri erişim yüzeyi eklemek, ama banner’ı zorla açmadan.
