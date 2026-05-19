# Rose Garden Favicon Brand Refresh

Tarih: 2026-05-19

## Kapsam

Favicon'da eski tasarımdan kalan kırmızı kutu / yeşil yaprak görseli Rose Garden marka kimliğiyle uyumlu hale getirildi. Kapsam yalnızca public favicon/branding assetleri ve favicon link sıralamasıyla sınırlı tutuldu.

## Değişiklik

- `public/favicon.ico` yeni Rose Garden monogram favicon ile değiştirildi.
- `public/favicon.svg` eklendi.
- `public/images/branding/favicon.svg` eklendi.
- `public/images/branding/favicon.png`, `favicon-32.png`, `favicon-dark.png`, `apple-touch-icon.png`, `nb_rg_favicon_light.png`, `nb_rg_favicon_dark.png` yeni marka setiyle değiştirildi.
- `resources/views/layouts/partials/meta.blade.php` kök favicon ve SVG favicon linklerini cache-busting dosya zamanı ile yayınlayacak şekilde güncellendi.

## Tasarım Notu

Yeni favicon küçük ölçekte okunabilirliği önceleyen `RG` monogramına dayanır. Palet Rose Garden'ın mevcut görsel kimliğiyle uyumludur: krem zemin, koyu plum tipografi, rose vurgu ve yeşil yaprak detayı. Karanlık tema için ayrı koyu zeminli favicon PNG seti korunmuştur.

## Doğrulama

- `php -l resources\views\layouts\partials\meta.blade.php` geçti.
- `npm run build` geçti.
- `php artisan test tests\Feature\Storefront\PublicSurfaceSmokeTest.php` geçti: 12 test, 62 assertion.
- Deploy branch: `deploy/rose-garden-main-49801d4`.
- Sunucuda `npm run build`, `php artisan optimize:clear`, `config:cache`, `route:cache`, `view:cache` geçti.
- Canlı `curl -I https://rosegardencicekcilik.com.tr/favicon.ico` sonucu: `200 OK`, `Content-Type: image/x-icon`, `Content-Length: 3413`.
- Canlı `curl -I https://rosegardencicekcilik.com.tr/favicon.svg` sonucu: `200 OK`, `Content-Type: image/svg+xml`, `Content-Length: 1858`.
- Canlı `curl -I https://rosegardencicekcilik.com.tr/images/branding/favicon.svg` sonucu: `200 OK`, `Content-Type: image/svg+xml`, `Content-Length: 1858`.
- Playwright canlı smoke: `https://rosegardencicekcilik.com.tr/tr` 200 döndü, favicon linkleri yeni cache-busted assetlere işaret etti, console error sayısı `0`.

## Not

In-app Browser aracı bu turda görünür browser namespace olarak yüklenmediği için canlı görsel/head doğrulama Playwright fallback ile yapıldı.
