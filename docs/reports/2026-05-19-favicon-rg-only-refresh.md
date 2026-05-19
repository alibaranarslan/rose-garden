# Rose Garden RG-Only Favicon Refresh

Tarih: 2026-05-19

## Kapsam

Bir önceki favicon setindeki yaprak detayı kaldırıldı. Yeni kapsam yalnızca favicon görsel dosyalarıyla sınırlıdır; storefront layout, katalog, checkout veya admin davranışına dokunulmadı.

## Değişiklik

- `public/favicon.svg` ve `public/images/branding/favicon.svg` yalnızca `RG` monogramı + rose vurgu kalacak şekilde sadeleştirildi.
- `public/favicon.ico` yeniden üretildi.
- `public/images/branding/favicon.png`, `favicon-32.png`, `favicon-dark.png`, `apple-touch-icon.png`, `nb_rg_favicon_light.png`, `nb_rg_favicon_dark.png` yapraksız `RG` monogram setiyle güncellendi.

## Doğrulama

- `php -l resources\views\layouts\partials\meta.blade.php` geçti.
- `npm run build` geçti.
- `php artisan test tests\Feature\Storefront\PublicSurfaceSmokeTest.php` geçti: 12 test, 62 assertion.
- Yerel görsel kontrol: `favicon.png` ve `favicon-32.png` yapraksız `RG` monogramı olarak görüntülendi.
- Deploy branch: `deploy/rose-garden-main-ae2d00c`.
- Sunucuda `npm run build`, `php artisan optimize:clear`, `config:cache`, `route:cache`, `view:cache` geçti.
- Canlı `curl -I https://rosegardencicekcilik.com.tr/favicon.ico` sonucu: `200 OK`, `Content-Type: image/x-icon`, `Content-Length: 3299`.
- Canlı `curl -I https://rosegardencicekcilik.com.tr/favicon.svg` sonucu: `200 OK`, `Content-Type: image/svg+xml`, `Content-Length: 1258`.
- Canlı `curl -I https://rosegardencicekcilik.com.tr/images/branding/favicon.svg` sonucu: `200 OK`, `Content-Type: image/svg+xml`, `Content-Length: 1258`.
- Playwright canlı smoke: `https://rosegardencicekcilik.com.tr/tr` 200 döndü, favicon linkleri yeni cache-busted assetlere işaret etti, `favicon.svg` içinde `RG` bulundu, yaprak rengi bulunmadı, console error sayısı `0`.
