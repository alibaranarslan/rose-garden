# RG Beacon Robots Dynamic Settings Fix 2026-05-09

## Amac

Admin SEO settings icindeki `robots_txt_extra` alaninin public `/robots.txt` ciktisina gercekten yansimasini saglamak.

Bu tur yalnizca robots.txt dynamic rendering blocker'ini kapatti. Settings redesign, payment/mail/SMS, storefront redesign, Layout Studio, catalog/content QA veya genis SEO refactor yapilmadi.

## Root Cause

`routes/web.php` icinde `/robots.txt` icin dinamik Laravel route zaten vardi ve `Setting::get('seo', 'robots_txt_extra')` okuyacak sekilde tasarlanmisti.

Canli HTTP istegi ise Laravel route'una ulasmiyordu. Sebep, `public/robots.txt` fiziksel dosyasinin web server tarafinda Laravel front controller'dan once servis edilmesiydi.

Risk sinifi:

- PHP built-in server: public dizinde fiziksel dosya varsa statik dosyayi dondurur.
- Apache `.htaccess`: `RewriteCond %{REQUEST_FILENAME} !-f` nedeniyle fiziksel dosya varsa `index.php` route'una dusmez.
- Tipik Nginx `try_files`: dosya varsa Laravel route'una gecmeden statik dosya dondurur.

Bu nedenle sorun settings persist veya route closure mantigi degildi; statik public file shadowing sorunuydu.

## Yapilan Fix

- `public/robots.txt` kaldirildi.
- Public `/robots.txt` ownership'i tek yerde, Laravel route'unda birakildi.
- Mevcut route'un sitemap URL normalization davranisi korunarak degistirilmedi.
- `PublicSurfaceSmokeTest` robots testine `public/robots.txt` dosyasinin yeniden eklenmesini yakalayacak guard eklendi.

## Degistirilen Dosyalar

- `public/robots.txt` silindi.
- `tests/Feature/Storefront/PublicSurfaceSmokeTest.php`
- `docs/reports/rg-beacon-robots-dynamic-settings-fix-2026-05-09.md`

## Test / HTTP Dogrulamalari

- `php -l tests\Feature\Storefront\PublicSurfaceSmokeTest.php`
  - Passed.
- `php artisan test --filter=PublicSurfaceSmokeTest`
  - Passed: 11 tests, 57 assertions.
- `Test-Path public\robots.txt`
  - `False`.
- HTTP smoke icin local Laravel server gecici olarak `http://127.0.0.1:8001` uzerinde baslatildi.
- Gecici settings marker yazildi:
  - `robots_txt_extra = # ROBOTS-DYNAMIC-SMOKE-20260509`
  - `robots_txt = User-agent: * / Allow: / / Sitemap: https://old.example/sitemap.xml`
  - `canonical_domain = https://example.test/path`
- HTTP `/robots.txt` ciktisinda marker gorundu:
  - `# ROBOTS-DYNAMIC-SMOKE-20260509`
- Sitemap normalization dogrulandi:
  - eski `https://old.example/sitemap.xml` yerine `https://example.test/sitemap.xml` donduruldu.
- Cleanup yapildi:
  - gecici `robots_txt`, `robots_txt_extra`, `canonical_domain` settings satirlari silindi.
  - cleanup sonrasi ilgili SEO key count: `0`.
  - cleanup sonrasi HTTP `/robots.txt` marker icermedi ve default sitemap olarak `http://127.0.0.1:8001/sitemap.xml` dondurdu.
- Gecici local server process'i durduruldu.

## Kalan Riskler

- Deployment ortaminda eski `public/robots.txt` dosyasi release artifact veya sunucu uzerinde stale olarak kalirsa yine Laravel route'unu golgeleyebilir. Deploy paketinde dosya silme isleminin gercekten uygulanmasi gerekir.
- Bu tur sadece robots dynamic rendering blocker'ini kapsadi; diger SEO/admin settings yuzeyleri yeniden QA edilmedi.
- Route closure mevcut haliyle yeterli oldugu icin controller/service refactor yapilmadi.

## Settings Smoke Retry Durumu

Settings robots smoke retry icin hazir. Bir sonraki guvenli adim, `/admin/seo-settings` uzerinden `robots_txt_extra` icin kucuk bir marker kaydedip public `/robots.txt` HTTP ciktisinda gorundugunu ve revert sonrasi marker'in kalktigini tekrar browser-level dogrulamaktir.
