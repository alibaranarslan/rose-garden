# RG Steward Admin Settings Safe Persist 2026-05-09

**Tarih:** 2026-05-09  
**Kapsam:** admin settings safe persist smoke, general/branding/SEO/read-only integrations/delivery settings readiness  
**Not:** Bu turda yeni kod yazilmadi. Payment credential testi, mail gonderimi, SMS gonderimi, notification dispatch, Layout Studio publish/revert, catalog/content edit ve mobile QA yapilmadi.

## Amac

Admin settings yuzeylerinde guvenli ayar degisikligi yapildiginda form save, SQLite settings storage persist, public/storefront yansima, cache davranisi ve geri alma akisini browser-level canli senaryoyla dogrulamak.

## Test Edilen Settings Page / Senaryolari

- Admin login ve settings navigation.
- General settings `site_name.tr` safe marker persist.
- General settings storefront title/header yansimasi.
- General settings revert.
- SEO settings `meta_title_suffix` safe marker persist.
- SEO settings `robots_txt_extra` safe marker persist denemesi.
- Public homepage title/meta yansimasi.
- Public `/robots.txt` yansima kontrolu.
- Branding readiness: text branding smoke, logo/favicon upload read-only.
- Payment/email/SMS read-only settings review, test gonderimi yapilmadi.
- Delivery zones/time slots read-only readiness review.
- General settings required validation smoke.
- Dar feature test dogrulamalari.

## Gercekten Acilan Admin Page'ler

- `/admin/login`
- `/admin`
- `/admin/general-settings`
- `/admin/seo-settings`
- `/admin/payment-settings`
- `/admin/email-settings`
- `/admin/sms-settings`
- `/admin/delivery-zones`
- `/admin/delivery-time-slots`

## Baseline

- Aktif local baseline SQLite.
- Test basinda `settings` tablosu bostu.
- Test basinda public homepage title: `Rose Garden Çiçek Çikolata | Rose Garden`.
- Test basinda `/robots.txt`: `User-agent: *` ve `Disallow:`.

## Yapilan Kucuk Veri Degisiklikleri

Marker:

- `SETTINGS-SMOKE-20260509`

General settings:

- `site_name.tr`: `Rose Garden` -> `Rose Garden SETTINGS-SMOKE-20260509`
- Save sonrasi admin input tekrar acildiginda marker kalici gorundu.
- Sonra `site_name.tr` tekrar `Rose Garden` yapildi.

SEO settings:

- `meta_title_suffix`: `| Rose Garden` -> `| Rose Garden SETTINGS-SMOKE-20260509`
- `robots_txt_extra`: bos -> `# SETTINGS-SMOKE-20260509`
- Save sonrasi admin SEO formunda iki deger de kalici gorundu.
- Sonra iki alan da ilk degerlerine geri alindi.

Validation:

- General settings `site_name.tr` bos birakilip save denendi.
- Admin yuzeyi required/zorunlu validation verdi.
- Sonra field tekrar guvenli degerle kaydedildi.

## Geri Alinan / Alinmayan Degisiklikler

- General `site_name.tr` marker geri alindi.
- SEO `meta_title_suffix` marker geri alindi.
- SEO `robots_txt_extra` marker geri alindi.
- Test baslangicinda `settings` tablosu bos oldugu icin smoke bitisinde testin olusturdugu settings satirlari temizlendi.
- Cleanup sonrasi `settings` tablosu tekrar `0` kayit.
- Public homepage marker icermiyor.
- Public `/robots.txt` marker icermiyor.
- Kod degisikligi yok.

## Storefront / Public Yansimalar

General settings:

- `site_name.tr` save sonrasi public homepage title `Rose Garden SETTINGS-SMOKE-20260509 | Rose Garden` oldu.
- Public homepage body/header alaninda `ROSE GARDEN SETTINGS-SMOKE-20260509` gorundu.
- Revert sonrasi homepage title `Rose Garden | Rose Garden` oldu ve marker kalkti.

SEO settings:

- `meta_title_suffix` save sonrasi public homepage title `Rose Garden | Rose Garden SETTINGS-SMOKE-20260509` oldu.
- Revert sonrasi homepage title `Rose Garden | Rose Garden` oldu ve marker kalkti.
- `robots_txt_extra` admin formunda ve settings storage'da kalici yazildi, ancak public `/robots.txt` marker'i gostermedi.

## Cache Invalidation Degerlendirmesi

- General settings save, onceki persist fix'teki `Setting::forgetStorefrontCaches()` ve `Setting::bumpStorefrontContentVersion()` zinciriyle public homepage'e ayni smoke icinde yansidi.
- General revert de public homepage'e ayni smoke icinde yansidi.
- SEO `meta_title_suffix` public homepage title'a ayni smoke icinde yansidi; bu smoke'ta stale homepage HTML gorulmedi.
- SEO `robots_txt_extra` ise public `/robots.txt` yuzeyine yansimadi. Koken cache degil; `public/robots.txt` statik dosyasi route'u golgeliyor gibi davraniyor. Route kodu `robots_txt_extra` okuyor, fakat canli HTTP sonucu statik dosya icerigini dondurdu.

## Branding Settings Readiness

- Ayrica `BrandingSettings` sayfasi yok; marka text/logo/favicon governance'i `GeneralSettings` altinda.
- `site_name.tr` text branding smoke basarili.
- Storefront shell/title/header yansimasi goruldu.
- Logo ve favicon upload alanlari read-only incelendi; kalici dosya state'i uretmemek icin upload denenmedi.
- File upload alanlari image-only ve max size guardrail tasiyor.

## Locale / Default Settings

- General settings localized alanlari TR/EN/KU tablariyla gorundu.
- Bu turda sadece TR safe persist oynatildi.
- Locale route continuity bozulmadi; default locale veya supported locale ayari degistirilmedi.

## Delivery Settings Readiness

- Delivery zones ve delivery time slots ayri resource olarak acildi.
- SQLite smoke baseline'da iki yuzey de bos:
  - `delivery_zones = 0`
  - `delivery_time_slots = 0`
- Zone/slot toggle veya create yapilmadi.
- Bu durum settings persist blocker degil, ancak operasyonel veri prerequisite'i olarak ayrica ele alinmali.

## Payment / Email / SMS Read-only Review

- `/admin/payment-settings` acildi; PayTR ve havale/EFT alanlari gorundu.
- Payment credential degisikligi veya test odeme yapilmadi.
- `/admin/email-settings` acildi; test e-postasi aksiyonu gorundu ama tetiklenmedi.
- `/admin/sms-settings` acildi; test SMS aksiyonu gorundu ama tetiklenmedi.
- Bu yuzeyler operational integrations scope'a birakildi.

## Validation / Error Handling Degerlendirmesi

- General `site_name.tr` required validation browser-level goruldu.
- Silent fail gorulmedi.
- General ve SEO save sonrasinda admin form tekrar acildiginda persisted input degerleri goruldu.
- SEO robots alaninda silent DB fail yok; admin/storage persist var ama public route yansimasi yok.

## Blocker'lar

- **SEO robots public yansima blocker:** `robots_txt_extra` admin formundan kaydediliyor ve formda kalici gorunuyor, ancak public `/robots.txt` marker'i gostermiyor. Route kodu settings degerini okumaya hazir olsa da runtime HTTP sonucu statik `public/robots.txt` icerigini donduruyor. Admin UI'da "robots.txt ek kurallari" olarak anlatilan alan public SEO yuzeyini gercekte etkilemiyor.

## Minor Issues

- SQLite smoke baseline'da `settings` tablosu bos oldugu icin test baslangicinda fallback degerlerle ilerleniyor.
- General settings tek buyuk form; save bircok settings key'i birlikte yazar. Test bitisinde tablo snapshot'i bos oldugu icin test satirlari temizlendi.
- Payment/email/SMS aksiyon butonlari gorunur; bu turda tetiklenmedi. Gercek operator icin test aksiyonlari ayrica kontrollu integrations scope'ta ele alinmali.
- Delivery zone/time slot resource'lari bos; canli operasyon icin veri prerequisite'i.

## Accepted Risks

- Logo/favicon upload denenmedi.
- Payment, mail, SMS credential persist veya test gonderimi denenmedi.
- Delivery zone/slot create/toggle yapilmadi.
- Locale EN/KU persist bu turda oynatilmadi.
- Layout Studio publish/revert yapilmadi.

## Kod Degisikligi

- `kod degisikligi yok`

## Yapilan Dogrulamalar

- Browser-level admin login.
- Browser-level General settings save/reopen/public/revert.
- Browser-level SEO settings save/reopen/public/revert.
- Public homepage title/body marker checks.
- Public `/robots.txt` marker checks.
- General required validation smoke.
- Cleanup: `settings` tablosu tekrar `0`.
- Cleanup sonrasi homepage ve robots marker icermiyor.

Dar feature testler:

- `php artisan test --filter=SettingsGovernanceTest` -> passed.
- `php artisan test --filter=BrandingSettingsTest` -> passed.
- `php artisan test --filter=AdminPersistFixTest` -> passed.

## Sonraki Guvenli Adim

1. `robots_txt_extra` public yansima blocker'i icin dar fix turu ac.
2. Fix sonrasi sadece `/admin/seo-settings` -> `robots_txt_extra` -> `/robots.txt` browser smoke'u tekrar calistir.
3. Ardindan operational integrations scope'a gec:
   - payment credentials readiness
   - mail test path
   - SMS test path
   - delivery zone/slot data prerequisites
