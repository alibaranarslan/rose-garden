# Rose Garden Upcoming Special Occasion Selection

## Scope

- Fix the storefront "Yaklaşan Özel Gün" selection so past calendar dates in the current month are not shown as upcoming.
- Keep the change limited to date-selection behavior; no visual redesign or content rewrite was included.

## Root Cause

- The home occasion query selected every active occasion in the current month without checking whether `date_day` had already passed.
- On May 19, this allowed May 11 `anneler-gunu` to remain eligible and appear as the upcoming occasion.

## Change

- Added `SpecialOccasion::nearestActive()` as the single source for selecting the nearest active annual occurrence.
- Updated both live homepage data (`HomeModuleDataService`) and the legacy home reference controller to use this model-level date behavior.
- The selection now uses existing `nextOccurrence()` / `daysUntil()` logic, so an already-passed date rolls to next year and no longer outranks a closer future occasion.

## Regression Test

- Added `HomeSpecialOccasionSelectionTest`.
- Fixed-date scenario: May 19, 2026.
- Data:
  - Anneler Günü: May 11
  - Kurban Bayramı: May 27
  - Yılbaşı: December 31
- Expected and verified active occasion: `kurban-bayrami`, `8` days away.

## Validation

- `php -l app\Models\SpecialOccasion.php`
- `php -l app\Services\HomeModuleDataService.php`
- `php -l app\Http\Controllers\HomeController.php`
- `php -l tests\Feature\Storefront\HomeSpecialOccasionSelectionTest.php`
- `php artisan test tests\Feature\Storefront\HomeSpecialOccasionSelectionTest.php tests\Feature\Storefront\PublicSurfaceSmokeTest.php`
- `npm run build`

## Live Smoke

- URL: `https://rosegardencicekcilik.com.tr/tr`
- Viewport: mobile 390px.
- Result:
  - Home `occasion_spotlight` section exists.
  - Section text contains `Kurban Bayramı`.
  - Section text does not contain `Anneler Günü`.
  - Date shown: `27 Mayıs`.
  - Status shown: `8 gün kaldı`.
  - Console errors: none.
  - Horizontal overflow: `false`.

## Screenshot

- `%LOCALAPPDATA%\Temp\rg-upcoming-occasion\home-occasion-section-mobile.png`

## Deployment

- Deployed subtree branch: `deploy/rose-garden-main-c844b19`
