# RG Beacon Admin Content Hydration Fix - 2026-05-09

## Amac

Content operations retry turunda bulunan iki admin UI hydration blocker'ini dar kapsamda kapatmak:

- Blog/Page RichEditor iceriginin normal browser klavye girisiyle Livewire form state'e guvenilir tasinmasi
- SpecialOccasion `date_month` ve `date_day` select degerlerinin normal browser secimiyle save state'e tasinmasi

Bu turda storefront redesign, settings QA, catalog QA, Layout Studio publish veya genis refactor yapilmadi.

## Root Cause

Sorun validation'in kendisi degildi. Iki yuzeyde de operator UI'da deger gorunuyordu, ancak Livewire backend snapshot submit aninda eski state'e donebiliyordu.

Kok neden siniflari:

1. `RichEditor` Trix editor `wire:ignore` + deferred entangle ile calisiyordu. Editor icindeki HTML gorunur olsa bile backend snapshot submit oncesi guvenilir sekilde guncellenmiyordu.
2. Blog/Page/SpecialOccasion create formlarinda title/name alanindaki auto-slug `live(onBlur: true)` istegi, operator sonraki alanlara gecerken eski form snapshot'i ile geri donup RichEditor/select state'ini `null` hale getirebiliyordu.
3. SpecialOccasion ay/gun select alanlari normal select secimiyle ekranda dolsa da live backend state'e hemen commit edilmiyordu; pending auto-slug response'u bu degerleri geri dusurebiliyordu.

## RichEditor Duzeltmesi

`BlogPostResource` ve `PageResource` icindeki `content` RichEditor alanlari:

- `->live(debounce: 500)` ile backend form state'e baglandi.
- Blog content `required` davranisi korundu.
- Page content alaninin mevcut nullable davranisi degistirilmedi.

Ek olarak Blog/Page title alanlarindaki create-time auto-slug live roundtrip'i kaldirildi. Slug alani zorunlu kalmaya devam ediyor; operator slug'i acikca dolduruyor. Bu, RichEditor yazilirken eski Livewire response'un editor state'ini geri dusurmesini engelliyor.

## SpecialOccasion Select Duzeltmesi

`SpecialOccasionResource` icindeki:

- `date_month`
- `date_day`

alanlari:

- `->live()` ile secim aninda backend state'e baglandi.
- `->dehydrateStateUsing(...)` ile DB'ye integer olarak yazilacak sekilde normalize edildi.
- `required` validation korundu.

Ek olarak SpecialOccasion name alanindaki create-time auto-slug live roundtrip'i kaldirildi. Slug alani zorunlu kalmaya devam ediyor.

## Degistirilen Dosyalar

- `C:\nwp0203\rose-garden\app\Filament\Resources\BlogPostResource.php`
- `C:\nwp0203\rose-garden\app\Filament\Resources\PageResource.php`
- `C:\nwp0203\rose-garden\app\Filament\Resources\SpecialOccasionResource.php`
- `C:\nwp0203\rose-garden\tests\Feature\Admin\AdminContentHydrationTest.php`
- `C:\nwp0203\rose-garden\docs\reports\rg-beacon-admin-content-hydration-fix-2026-05-09.md`

## Yapilan Dogrulamalar

Komutlar:

- `php artisan optimize:clear`
- `php -l app\Filament\Resources\BlogPostResource.php`
- `php -l app\Filament\Resources\PageResource.php`
- `php -l app\Filament\Resources\SpecialOccasionResource.php`
- `php artisan test --filter=AdminContentHydrationTest`
- `php artisan test --filter=AdminContent`
- `php artisan test --filter=AdminPersistFixTest`

Browser-level smoke:

- `/admin/blog-posts/create`
  - title, slug ve RichEditor icerigi normal browser input ile dolduruldu.
  - submit sonrasi edit ekranina gecildi.
  - `content` metni edit ekraninda gorundu.
- `/admin/pages/create`
  - title, slug ve RichEditor icerigi normal browser input ile dolduruldu.
  - submit sonrasi edit ekranina gecildi.
  - `content` metni edit ekraninda gorundu.
- `/admin/special-occasions/create`
  - name, slug, `date_month=5`, `date_day=9` normal browser input/select ile dolduruldu.
  - submit sonrasi edit ekranina gecildi.
  - required date validation tekrar etmedi.

Cleanup:

- Browser smoke'un olusturdugu `hydration-%` slug'li blog/page/special occasion test kayitlari silindi.
- Son kontrol: uc tabloda da `hydration-%` slug sayisi `0`.

## Kalan Riskler

- Auto-slug kolayligi bu uc content create formunda kaldirildi; slug alanlari zorunlu oldugu icin operator slug'i elle doldurur. Gerekirse daha sonra client-side slug preview gibi ayri ve race uretmeyen bir UX eklenebilir.
- Browser smoke headless Playwright ile yapildi; uzun manuel editor senaryolari ve EN/KU locale entry bu turda kapsanmadi.
- Layout Studio publish/revert bu scope disinda tutuldu.

## Sonraki Guvenli Adim

Content operations retry scope'u tekrar oynatilabilir:

1. Blog create/edit/save/public yansima
2. Page create/edit/save/publish toggle/public yansima
3. SpecialOccasion create/edit/save/public yansima
4. Layout Studio read-only veya ayri kontrollu publish smoke
