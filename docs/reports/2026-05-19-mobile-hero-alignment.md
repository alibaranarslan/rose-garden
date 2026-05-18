# Rose Garden Mobile Hero Alignment

## Scope

- Align the mobile home hero intro card with the hero grid so the background image layer and foreground text block share the same left reference.
- Keep the desktop hero unchanged.

## Change

- Updated the mobile `.rg-store-hero-intro-card` rule from centered `calc(100% - 0.25rem)` width to full-width grid alignment:
  - `width: 100%`
  - `margin-inline: 0`

## Evidence

- Before live measurement:
  - Hero grid left: `16px`
  - Hero intro card left: `18px`
  - Delta: `2px`
- After live measurement on `https://rosegardencicekcilik.com.tr/tr`:
  - Hero grid left: `16px`
  - Hero intro card left: `16px`
  - Delta: `0px`
  - Horizontal overflow: `false`
  - Console errors: none

## Screenshots

- Before: `%LOCALAPPDATA%\Temp\rg-hero-mobile-alignment\before-hero-align.png`
- After: `%LOCALAPPDATA%\Temp\rg-hero-mobile-alignment\after-hero-align.png`

## Validation

- `npm run build`
- `php artisan test tests\Feature\Storefront\PublicSurfaceSmokeTest.php`
- Live Playwright mobile smoke on 390px viewport

## Deployment

- Deployed subtree branch: `deploy/rose-garden-main-55b4d3b`
- Browser path note: in-app Browser was attempted first; Playwright fallback was used for precise mobile geometry measurement.
