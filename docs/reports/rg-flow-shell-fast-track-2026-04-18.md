# RG Flow Shell Fast Track - 2026-04-18

## Amaç

Müşterinin ilk bakışta fark edeceği shell ve utility kusurlarını hızlıca kapatmak. Bu tur redesign değildir; yalnızca language switcher okunurluğu, checkout dark mode header/logo uyumu ve header shell micro-fixleri hedeflendi.

## Ele alınan hızlı kusurlar

- Language switcher dropdown fazla saydam ve okunaksız görünüyordu.
- Checkout dark mode header/logo kabuğu yeterince güvenli hissettirmiyordu.
- Header search ve utility control ritminde küçük ama görünür hizalama farkları vardı.

## Yapılan düzeltmeler

- Language switcher button ve dropdown daha opak, daha kontrollü ve daha kontrastlı hale getirildi.
- Language switcher’in aktif/secondary satırları daha net okunacak şekilde sıkıştırıldı.
- Checkout header daha derin dark mode zemini ve logo etrafında daha temiz bir shell ile dengelendi.
- Checkout logo alanı ve güvenli ödeme chip’i daha tutarlı bir ilk izlenim verecek şekilde toparlandı.
- Header search formu, submit button yüksekliği ve utility cluster spacing’i micro-fix seviyesinde hizalandı.
- Theme toggle, header shell ile daha uyumlu bir control formuna çekildi.

## Değiştirilen dosyalar

- `C:\nwp0203\rose-garden\resources\views\components\language-switcher.blade.php`
- `C:\nwp0203\rose-garden\resources\views\layouts\checkout.blade.php`
- `C:\nwp0203\rose-garden\resources\views\layouts\partials\header.blade.php`
- `C:\nwp0203\rose-garden\resources\views\components\theme-toggle.blade.php`
- `C:\nwp0203\rose-garden\resources\css\app.css`
- `C:\nwp0203\rose-garden\tests\Feature\Storefront\PublicSurfaceSmokeTest.php`

## Yapılan doğrulamalar

- `php artisan test --filter=PublicSurfaceSmokeTest`
- `php artisan test --filter=LocalizationSurfaceTest`
- `php artisan test --filter=HeaderThemeTest`
- `php artisan test --filter=StorefrontCompatibilityTest`
- `npm run build`

## Kalan riskler

- Bu tur bilinçli olarak çok dar tutuldu; header içinde hala ikinci derecede görülebilecek küçük hizalama farkları olabilir.
- Localization overhaul, PDP locale 404, homepage merchandising ve admin deep-check bu turun kapsamı dışında kaldı.
- Gerçek cihazda son görsel bakış her zaman faydalıdır.

## Sonraki en yüksek ROI adım

Checkout ve header shell yeterince temiz kabul edilirse, sonraki en yüksek ROI adım homepage rail / special occasion gibi daha görünür merchandising kusurları değil; önce kalan shell regressions için kısa bir manual smoke ve ardından sadece gerçekten göze batan tek bir yüzeye odaklanan mikro tur olur.
