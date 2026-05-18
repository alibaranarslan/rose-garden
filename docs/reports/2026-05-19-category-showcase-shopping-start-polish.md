# Category Showcase Shopping Start Polish

Date: 2026-05-19

## Scope

Targeted homepage polish for the "Alışverişe Başla" block. No catalog data, checkout, admin, payment, or broader redesign changes.

## Finding

The old block mixed a "Kategori seç, ürünü daha hızlı bul" message with a short product list. The product list is not hardcoded, but the UI did not explain its source, so it looked like a static and semantically disconnected three-product module.

## Change

- Reframed the panel as a two-step shopping path: choose a category route first, then use the automatic product suggestions.
- Added visible category route cards before the product suggestions.
- Renamed the product list to "Hazır Ürün Önerileri" and added copy explaining that it is generated from storefront data.
- Moved the panel before the category card grid on mobile, so the explanation appears before the category visuals.
- Cleaned visible Turkish copy in this touched Blade section.

## Validation

- `php -l resources\views\home\sections\category-showcase.blade.php`
- `npm run build`
- `php artisan test tests\Feature\Storefront\PublicSurfaceSmokeTest.php`
- Playwright mobile smoke on `http://127.0.0.1:8001/tr`
- Playwright mobile smoke on `https://rosegardencicekcilik.com.tr/tr`

## Evidence

- Category route click: first visible route opened `/tr/kategori/gul-buketleri`.
- Horizontal overflow: false.
- Console errors: none.
- Screenshot: `%LOCALAPPDATA%\Temp\rg-category-showcase-polish\mobile-category-showcase-clean.png`
- Live screenshot: `%LOCALAPPDATA%\Temp\rg-live-category-showcase-polish\mobile-category-showcase-live.png`

## Deployment

- Deployed subtree branch: `deploy/rose-garden-main-c589e98`
- Live URL checked: `https://rosegardencicekcilik.com.tr/tr`
- Browser path note: in-app Browser setup timed out, so rendered validation used Playwright fallback.
