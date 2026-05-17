# RG Steward Settings Robots Smoke 2026-05-09

**Tarih:** 2026-05-09  
**Kapsam:** Admin SEO settings `robots_txt_extra` safe persist/revert ve public `/robots.txt` dynamic yansima smoke'u.  
**Not:** Bu turda yeni kod yazilmadi. Payment, mail, SMS, genel settings QA, storefront redesign ve mobile QA yapilmadi.

## Amac

Beacon tarafindan kapatildigi raporlanan `robots_txt_extra` public yansima blocker'ini dar browser-level operatör akisi ile dogrulamak:

- Admin SEO settings sayfasinda `robots_txt_extra` alanina marker kaydetmek.
- Admin form tekrar acildiginda marker'in kalici gorundugunu dogrulamak.
- Public `/robots.txt` ciktisinda marker'in gorundugunu dogrulamak.
- Revert sonrasi marker'in hem admin formundan hem public ciktidan kalktigini dogrulamak.
- SQLite settings icinde test marker kalmadigini dogrulamak.

## Baseline Robots Sonucu

Okunan onceki raporlar:

- `docs/reports/rg-steward-admin-settings-safe-persist-2026-05-09.md`
- `docs/reports/rg-beacon-robots-dynamic-settings-fix-2026-05-09.md`

Smoke oncesi teknik baseline:

- Local server `http://127.0.0.1:8001` portunda erisilebilir durumdaydi.
- `public/robots.txt` fiziksel dosyasi yoktu.
- SQLite `settings` tablosunda `seo / robots_txt_extra` satiri yoktu.
- Public `/robots.txt` baseline:

```txt
User-agent: *
Allow: /
Sitemap: http://127.0.0.1:8001/sitemap.xml
```

- Baseline public ciktida `ROBOTS-SMOKE-20260509` marker'i yoktu.
- Sitemap satiri dynamic public ciktida mantikliydi ve `/sitemap.xml` adresine isaret ediyordu.

## Admin Save Sonucu

Canli admin smoke:

- Admin login URL acildi: `http://127.0.0.1:8001/admin/login`
- Admin credentials kullanildi: `admin@admin.com`
- `/admin/seo-settings` sayfasi authenticated olarak acildi.
- Sayfa basligi: `SEO Ayarları - Rose Garden Yönetim`
- `robots_txt_extra` field'i bulundu.
- Baseline admin field degeri bostu.

Kaydedilen marker:

```txt
# ROBOTS-SMOKE-20260509
```

Save sonrasi:

- Admin form tekrar acildiginda `robots_txt_extra` alani `# ROBOTS-SMOKE-20260509` degerini korudu.
- Bu, admin form save ve settings persist zincirinin calistigini dogruladi.

## Public `/robots.txt` Marker Sonucu

Marker save sonrasi public `/robots.txt` ciktisi:

```txt
User-agent: *
Allow: /
Sitemap: http://127.0.0.1:8001/sitemap.xml
# ROBOTS-SMOKE-20260509
```

Dogrulama sonucu:

- HTTP status `200`.
- Marker public ciktida gorundu.
- Sitemap satiri korunuyordu.
- Bu sonuc, onceki `robots_txt_extra admin'de persist ediyor ama public /robots.txt icinde gorunmuyor` blocker'inin kapandigini gosterir.

## Revert Sonucu

Revert islemi:

- `robots_txt_extra` alani baseline degerine, yani bos degere, geri alindi.
- SEO settings formu tekrar kaydedildi.
- Admin form tekrar acildiginda marker artik gorunmuyordu.

Revert sonrasi public `/robots.txt` ciktisi:

```txt
User-agent: *
Allow: /
Sitemap: http://127.0.0.1:8001/sitemap.xml
```

Dogrulama sonucu:

- Public `/robots.txt` marker icermiyor.
- Sitemap satiri korunuyor.
- Revert public yuzeye yansidi.

## Cleanup Sonucu

Cleanup kontrolleri:

- SQLite `settings` icinde `ROBOTS-SMOKE-20260509` marker count: `0`.
- Smoke oncesinde `seo / robots_txt_extra` satiri olmadigi icin revert sonrasi olusan bos/null satir cleanup ile kaldirildi.
- Cleanup sonrasi `seo / robots_txt_extra` satiri tekrar yok.
- Final public `/robots.txt` marker icermiyor.

## Blocker Kapandi Mi

Kapandi.

Kanıt:

- Admin SEO settings save calisti.
- `robots_txt_extra` admin form tekrar acildiginda kalici gorundu.
- Public `/robots.txt` marker'i dynamic ciktida gorundu.
- Revert sonrasi public `/robots.txt` marker'i kaldirdi.
- Kalici test marker kalmadi.
- `public/robots.txt` fiziksel dosyasi yok; dynamic route shadowing smoke sirasinda tekrarlanmadi.

## Settings Safe Persist Scope Kapanabilir Mi

Evet, settings safe persist scope bu dar blocker acisindan kapanabilir.

Sonraki guvenli scope:

- Operational integrations:
  - payment credential readiness
  - mail test path
  - SMS test path
  - delivery zone/slot operational prerequisites

## Blocker'lar

- Yok.

## Minor Issues

- In-app browser typing layer, email input uzerinde hata verdigi icin canli smoke ayrica Playwright Chromium browser sureciyle tamamlandi. Bu, browser-level akisi degistirmedi; admin login, form save, public HTTP yansima ve cleanup gercek runtime uzerinden dogrulandi.

## Accepted Risks

- Bu tur yalnizca `robots_txt_extra` alanini kapsadi.
- SEO settings icindeki diger alanlar tekrar test edilmedi.
- Payment, mail, SMS ve delivery entegrasyonlari bu turda tetiklenmedi.
- Deployment ortaminda stale `public/robots.txt` dosyasi release artifact/server uzerinde kalirsa dynamic route tekrar golgelenebilir; deploy paketinde dosya silme uygulanmali.

## Kod Degisikligi

- `kod degisikligi yok`
- Yalnizca bu rapor dosyasi olusturuldu.

## Yapilan Veri Degisiklikleri

- Gecici marker eklendi: `# ROBOTS-SMOKE-20260509`
- Marker admin SEO settings uzerinden kaydedildi.
- Marker admin SEO settings uzerinden geri alindi.
- Baseline'da olmayan bos/null `seo / robots_txt_extra` satiri cleanup ile kaldirildi.
- Kalici veri marker'i birakilmadi.

## Yapilan Dogrulamalar

- Required reports okundu.
- `Test-NetConnection 127.0.0.1 -Port 8001` -> basarili.
- `Test-Path public\robots.txt` -> `False`.
- Browser-level admin `/admin/seo-settings` acildi.
- Browser-level `robots_txt_extra` marker save.
- Browser-level admin form reopen persist kontrolu.
- Public `/robots.txt` HTTP marker kontrolu.
- Browser-level revert.
- Public `/robots.txt` revert kontrolu.
- SQLite cleanup marker kontrolu.
- Final public `/robots.txt` kontrolu.

## Sonraki Guvenli Adim

Operational integrations scope'a gec:

1. Payment settings credential readiness ve safe validation.
2. Mail settings test path, gercek gonderim kontrollu ve izole olacak sekilde.
3. SMS settings test path, gercek gonderim kontrollu ve izole olacak sekilde.
4. Delivery zone/time slot operational prerequisite kontrolu.
