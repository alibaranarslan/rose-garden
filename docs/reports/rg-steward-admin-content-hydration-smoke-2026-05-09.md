# RG Steward Admin Content Hydration Smoke 2026-05-09

**Tarih:** 2026-05-09  
**Kapsam:** dar admin content hydration smoke, normal browser operator input/select akisi  
**Not:** Bu turda yeni kod yazilmadi. Storefront redesign, settings QA, catalog QA, mobile QA ve Layout Studio publish/revert yapilmadi.

## Amac

Beacon tarafindan kapatildigi raporlanan iki content hydration blocker'ini normal browser-level operator akisi ile dogrulamak:

- Blog/Page RichEditor iceriginin normal klavye girisiyle save state'e tasinmasi.
- SpecialOccasion `date_month` ve `date_day` select degerlerinin normal browser secimiyle save state'e tasinmasi.

## Okunan Baglam

- `rg-steward-admin-content-operations-retry-2026-05-09.md`
- `rg-beacon-admin-content-hydration-fix-2026-05-09.md`

## Canli Test Edilen Senaryolar

- Admin login.
- Blog create: normal title, slug, excerpt, RichEditor klavye girisi, status select.
- Page create: normal title, slug, RichEditor klavye girisi, meta description.
- Page publish/unpublish: normal checkbox ile yayindan alma ve tekrar yayina alma.
- Special occasion create: normal name, slug, `date_month`, `date_day`, loyalty multiplier input/select.
- Layout Studio read-only acilis kontrolu.
- Test kayit cleanup.

## Gercekten Acilan Resource / Page'ler

- `/admin/login`
- `/admin`
- `/admin/blog-posts/create`
- `/admin/pages/create`
- `/admin/pages/4/edit`
- `/admin/special-occasions/create`
- `/admin/special-occasions/3/edit`
- `/admin/layout-studio`
- `/blog`
- `/blog/hydration-smoke-blog-20260509`
- `/sayfa/hydration-smoke-page-20260509`
- `/ozel-gunler`
- `/ozel-gunler/hydration-smoke-special-20260509`

## Blog RichEditor Blocker Kapandi Mi?

Evet.

Normal browser akisi:

- `title`: `Hydration Smoke Blog`
- `slug`: `hydration-smoke-blog-20260509`
- `excerpt`: `Hydration smoke blog excerpt HYDRATION-SMOKE-20260509`
- RichEditor icerigi klavye ile yazildi: `Hydration smoke blog body HYDRATION-SMOKE-20260509`
- `status`: `published`
- Submit normal gorunur `Oluştur` butonuyla yapildi.

Sonuc:

- Public `/blog/hydration-smoke-blog-20260509` 200 dondu.
- Public blog detail marker'i gosterdi.
- Public `/blog` index `Hydration Smoke Blog` basligini gosterdi.
- Onceki `icerik alani zorunludur` validation blocker'i tekrar uremedi.

## Page RichEditor Blocker Kapandi Mi?

Evet.

Normal browser akisi:

- `title`: `Hydration Smoke Page`
- `slug`: `hydration-smoke-page-20260509`
- RichEditor icerigi klavye ile yazildi: `Hydration smoke page body HYDRATION-SMOKE-20260509`
- `meta_description`: `Hydration smoke page meta HYDRATION-SMOKE-20260509`
- Submit normal gorunur `Oluştur` butonuyla yapildi.

Sonuc:

- Public `/sayfa/hydration-smoke-page-20260509` 200 dondu.
- Public page detail RichEditor body marker'ini ve meta description marker'ini gosterdi.
- Page edit ekraninda RichEditor icerigi tekrar gorundu.

## SpecialOccasion Date Select Blocker Kapandi Mi?

Evet.

Normal browser akisi:

- `name`: `Hydration Smoke Special`
- `slug`: `hydration-smoke-special-20260509`
- `date_month`: `5`
- `date_day`: `9`
- `loyalty_multiplier`: `1.0`
- Submit normal gorunur `Oluştur` butonuyla yapildi.

Sonuc:

- Admin save sonrasi `/admin/special-occasions/3/edit` ekranina gecildi.
- SQLite kaydinda `date_month=5`, `date_day=9`, `is_active=1` goruldu.
- Public `/ozel-gunler/hydration-smoke-special-20260509` 200 dondu.
- Public special occasion detail `Hydration Smoke Special` metnini gosterdi.
- Public `/ozel-gunler` index de test kaydini gosterdi.
- Onceki date select hydration blocker'i tekrar uremedi.

## Storefront Yansimalari

- Blog detail: `/blog/hydration-smoke-blog-20260509` -> 200, marker gorundu.
- Blog index: `/blog` -> 200, blog title gorundu.
- Page detail: `/sayfa/hydration-smoke-page-20260509` -> 200, RichEditor body ve meta marker gorundu.
- Page unpublish: `/sayfa/hydration-smoke-page-20260509` -> 404.
- Page republish: `/sayfa/hydration-smoke-page-20260509` -> 200.
- Special occasion detail: `/ozel-gunler/hydration-smoke-special-20260509` -> 200.
- Special occasion index: `/ozel-gunler` -> 200, test special occasion gorundu.

## Temizlenen Test Kayitlari

Silinen test kayitlari:

- `blog_posts`: `hydration-smoke-blog-20260509`
- `pages`: `hydration-smoke-page-20260509`
- `special_occasions`: `hydration-smoke-special-20260509`

Cleanup sonucu:

- `blog_posts`: `hydration-smoke%` remaining `0`
- `pages`: `hydration-smoke%` remaining `0`
- `special_occasions`: `hydration-smoke%` remaining `0`

Cleanup sonrasi public test detail URL'leri:

- `/blog/hydration-smoke-blog-20260509` -> 404
- `/sayfa/hydration-smoke-page-20260509` -> 404
- `/ozel-gunler/hydration-smoke-special-20260509` -> 404

## Layout Studio Read-only Kontrol

- `/admin/layout-studio` acildi.
- Modul listesi gorundu:
  - `Announcement Bar`
  - `Hero Spotlight`
  - `Blog Seckisi`
- Draft akisi goruldu.
- Publish/revert denenmedi.

## Blocker'lar

- Bu dar hydration smoke kapsaminda blocker bulunmadi.
- Blog RichEditor blocker kapandi.
- Page RichEditor blocker kapandi.
- SpecialOccasion date select blocker kapandi.

## Minor Issues

- Playwright URL raporlamasinda create submit sonrasi URL bazen `/create` olarak kaldi; ancak admin body edit ekranina dondu ve DB/public yansima kaydi dogruladi.
- Layout Studio textinde legacy ASCII isimler devam ediyor: `Blog Seckisi`.
- Page publish/unpublish ilk genel scriptte yanlis URL/state uzerinden tekrar 200 kaldi; gerçek `/admin/pages/4/edit` ile tekrarlandiginda dogru 404/200 sonucu alindi.

## Accepted Risks

- EN/KU locale entry bu dar smoke kapsaminda test edilmedi.
- Blog draft/archive ve special occasion inactive toggle ayrica denenmedi.
- Layout Studio publish/revert yapilmadi; global homepage etkisi nedeniyle read-only kaldi.

## Kod Degisikligi

- `kod degisikligi yok`

## Settings Scope'a Gecilebilir Mi?

Evet. Content operations hydration blocker'lari browser-level normal operator akisiyle kapandi. Settings scope'a gecilebilir.
