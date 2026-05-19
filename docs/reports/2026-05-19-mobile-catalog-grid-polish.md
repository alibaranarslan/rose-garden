# Rose Garden Mobile Catalog Grid Polish

## Scope

- Improve `/tr/urunler` mobile browsing so the catalog feels like a product grid instead of one large product per scroll.
- Compact the mobile catalog hero so "Rose Garden Katalog / Tüm Ürünler" does not consume the first screen.
- Keep desktop catalog behavior unchanged.

## Root Cause

- The catalog product grid used one column below `520px`, so a 390px phone showed one large product per scroll.
- Product cards kept desktop-like content density on mobile: category, description, variant/quantity controls, and large card height.
- The compact page hero still rendered description and stats on mobile, making the first product grid start below the initial viewport.

## Changes

- Catalog grid now uses two columns on mobile.
- Product cards in catalog mobile view are compact:
  - Smaller card radius and shadow.
  - Reduced image padding.
  - Category and description hidden inside mobile catalog cards.
  - Variant and quantity controls hidden inside mobile catalog cards.
  - Product name, price, and add-to-cart CTA remain visible.
- Product listing hero now has a `rg-plp-hero` class and mobile-only compact behavior:
  - Smaller title.
  - Description hidden on mobile.
  - Stats hidden on mobile.
  - Category pills remain horizontally scrollable.

## Before/After Metrics

- Before:
  - Hero height: `536.8px`
  - Product grid top: `1085.1px`
  - Grid columns: `358px`
  - First product card height: `667px`
  - Visible products in first viewport: `0`
- After:
  - Hero height: `250.0px`
  - Product grid top: `794.3px`
  - Grid columns: `173.5px 173.5px`
  - Product card height: `343.8px`
  - Visible products in first viewport: `2`
  - Visible products when grid is aligned to viewport: `6`

## Validation

- `php -l resources\views\components\product-grid.blade.php`
- `php -l resources\views\components\product-list-layout.blade.php`
- `php -l resources\views\components\product-card.blade.php`
- `php -l resources\views\livewire\add-to-cart.blade.php`
- `npm run build`
- `php artisan test tests\Feature\Storefront\PublicSurfaceSmokeTest.php`
- Live Playwright mobile smoke on `https://rosegardencicekcilik.com.tr/tr/urunler`

## Live Smoke

- URL: `https://rosegardencicekcilik.com.tr/tr/urunler`
- Viewport: mobile 390px.
- Console errors: none.
- Horizontal overflow: `false`.

## Screenshots

- Before: `%LOCALAPPDATA%\Temp\rg-mobile-catalog-grid\before-mobile-catalog.png`
- After top: `%LOCALAPPDATA%\Temp\rg-mobile-catalog-grid\final-mobile-catalog-top.png`
- After grid: `%LOCALAPPDATA%\Temp\rg-mobile-catalog-grid\final-mobile-catalog-grid.png`

## Deployment

- Deployed subtree branch: `deploy/rose-garden-main-e444cf7`
- Browser path note: in-app Browser opened the live route successfully, but mobile geometry verification used Playwright fallback for a fixed 390px viewport.
