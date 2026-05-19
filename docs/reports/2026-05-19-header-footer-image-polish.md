# Rose Garden Header/Footer/Image Polish Evidence

Date: 2026-05-19

## Scope

- Header navigation: mobile category tab and desktop category flyout regression guard.
- Mobile footer: compactness pass without redesigning the brand block.
- Product images: initial catalog row prioritization and responsive image sizing.

## Changes

- Mobile `Kategoriler` nav item now opens the in-page mobile category panel instead of navigating to a broad catalog URL.
- Special occasion navigation data now ignores invalid calendar rows before date sorting, preventing malformed admin content from breaking public header navigation.
- Product cards now mark only the first 4 catalog images as eager/high priority; the rest are lazy/low priority.
- Product card `sizes` now matches the mobile two-column catalog width: `(max-width: 767px) 46vw`.
- Mobile footer spacing, heading size, paragraph length, CTA padding and contact card spacing were tightened.

## Validation

- `php artisan test tests\Feature\Storefront\PublicSurfaceSmokeTest.php`
  - Result: PASS, 21 tests, 99 assertions.
- `npm run build`
  - Result: PASS.
- `php artisan storefront:optimize-images --widths=320,480,640,960 --quality=78`
  - Result: PASS, generated=0, skipped=224, unreadable=12, failed=0.
  - Note: unreadable files are pre-existing legacy placeholders/test JPG names; command completed successfully and current optimized variants remain available.

## Render Smoke

Tool: Playwright fallback, used for deterministic 390px and 1440px viewport validation.

- Mobile `/tr`: 200 OK.
- Mobile category tab:
  - `mobilePanelVisible`: true.
  - URL after tapping `Kategoriler`: `http://127.0.0.1:8001/tr`.
  - Screenshot: `C:\Users\Ali\AppData\Local\Temp\rg-header-footer-image-smoke\mobile-home-category-panel.png`.
- Mobile `/tr/urunler`: 200 OK.
  - Product cards: 16.
  - Product image priority: high=4, low=12, eager=4, lazy=12.
  - First card `sizes`: `(max-width: 767px) 46vw, (min-width: 1280px) 18rem, 33vw`.
  - Horizontal overflow: false.
  - Screenshot: `C:\Users\Ali\AppData\Local\Temp\rg-header-footer-image-smoke\mobile-plp-footer.png`.
- Desktop `/tr`: 200 OK.
  - Category flyout visible on hover: true.
  - First category href: `http://127.0.0.1:8001/tr/kategori/cicek-buketleri`.
  - First category status: 200.
  - Screenshot: `C:\Users\Ali\AppData\Local\Temp\rg-header-footer-image-smoke\desktop-category-flyout.png`.
- Console/page errors: none.

## Status

This package closes the current three-item polish slice. No unrelated `haber-sitesi` changes were touched.
