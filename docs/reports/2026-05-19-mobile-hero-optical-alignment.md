# Mobile Hero Optical Alignment

Date: 2026-05-19

## Scope

Targeted mobile-only optical alignment correction for the storefront hero intro card.

## Change

- Reduced the mobile hero intro card width by `0.25rem` and centered it with `margin-inline: auto`.
- Adjusted the mobile background image position from `right -2.25rem` to `right -2rem`.
- Kept the larger background image scale and readability overlay from the previous polish.

## Validation

- `npm run build`
- `php artisan test tests\Feature\Storefront\PublicSurfaceSmokeTest.php`
- Playwright mobile smoke on `http://127.0.0.1:8001/tr`

## Evidence

- Mobile card bounds after fix: left `18px`, right margin `18px`, width `354px` on a `390px` viewport.
- Horizontal overflow: false.
- Console errors: none.
- Screenshot: `%LOCALAPPDATA%\Temp\rg-hero-shift-fix\mobile-hero.png`

