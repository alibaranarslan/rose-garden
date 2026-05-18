# Mobile Hero Background Polish

Date: 2026-05-19

## Scope

Targeted mobile-only storefront polish for the hero text/image composition. Desktop hero layout and unrelated surfaces were not redesigned.

## Change

- Increased the mobile hero background image scale from cover to a controlled `auto 134%`.
- Shifted the background image slightly right so the floral/product area is more visible.
- Reduced the right-side overlay opacity while preserving stronger text-side contrast.
- Added a dark-mode-specific overlay balance so the image remains visible without lowering headline readability.

## Validation

- `npm run build`
- `php artisan test tests\Feature\Storefront\PublicSurfaceSmokeTest.php`
- Playwright mobile smoke on `http://127.0.0.1:8001/tr`

## Evidence

- Mobile screenshot: `%LOCALAPPDATA%\Temp\rg-hero-mobile-bg-polish\mobile-hero.png`
- CTA interaction: `Koleksiyonu Keşfet` opened `/tr/urunler`
- Console errors: none
- Horizontal overflow: false

