# Rose Garden Cookie, Loyalty Popup ve Footer Domain Polish

Tarih: 2026-05-19

## Kapsam

- Çerez bildirimi ilk ekranda sağdaki ürün/hero görselini kapatan dikey karttan, daha kompakt alt bar düzenine taşındı.
- Guest Paraçiçek üyelik popup'ı çerez kararından hemen sonra açılmayacak şekilde geciktirildi.
- Eski `adiyamancicekcisi.com.tr` fallback'leri yeni canlı domain olan `rosegardencicekcilik.com.tr` ile değiştirildi.

## Değişiklikler

- `resources/views/cookie-consent.blade.php` cookie shell'i geniş ve yatay `rg-cookie-consent-card` yapısına geçirildi.
- `resources/css/app.css` desktop cookie bar için iki kolonlu kompakt yerleşim, düşük yükseklik ve settings paneli grid davranışı eklendi.
- `resources/views/components/guest-loyalty-prompt.blade.php` popup gecikmesi `2200ms` yerine `12000ms` yapıldı.
- `config/mail.php`, settings/customer seedleri, mail/KVKK view fallback'leri ve admin SEO placeholder'ı yeni domainle güncellendi.
- `tests/Feature/Storefront/PublicSurfaceSmokeTest.php` footer domain fallback'i ve kompakt cookie shell için regresyon testi eklendi.

## Doğrulama

- `php artisan test tests\Feature\Storefront\PublicSurfaceSmokeTest.php` geçti: 16 test, 82 assertion.
- `php artisan test tests\Unit\Support\DynamicMailConfigTest.php tests\Feature\Admin\ProductionReadinessTest.php tests\Feature\Admin\AdminSettingsOperationsIntegrationTest.php` geçti: 14 test, 71 assertion.
- `npm run build` geçti.
- Browser QA: `http://localhost:8001/tr` açıldı, title `Rose Garden`, console error/warn yok.
- Browser QA: cookie bar ilk ekranda alt bar olarak göründü; `Tümünü Kabul Et` tıklanınca banner kapandı.
- Browser QA: local optimize clear sonrası HTML'de `info@rosegardencicekcilik.com.tr` göründü, `adiyamancicekcisi.com.tr` görünmedi.
- Canlı deploy: Rose Garden server commit `8ac6ed1`.
- Canlı smoke: `https://rosegardencicekcilik.com.tr/tr` 200 döndü; HTML'de yeni domain, kompakt cookie class'ı ve `promptDelayMs = 12000` görüldü; eski domain görünmedi.
- Canlı Browser QA: `https://rosegardencicekcilik.com.tr/tr` title `Rose Garden`; console error/warn yok; cookie bar kompakt alt bar olarak göründü.

## Not

- Lokal DB'de eski contact email değeri bulunduğu için render doğrulamasından önce yalnızca `contact.contact_email` local setting'i yeni domainle güncellendi.
- Canlı deploy sonrası aynı public setting canlı DB'de de yeni domainle güncellenmeli veya admin panelinden doğrulanmalıdır.
