# Rose Garden Mobile PDP / Checkout / Popup Polish

Date: 2026-05-19

## Scope

This pass handled the next three storefront polish items only:

1. Mobile PDP add-to-cart flow.
2. Mobile checkout CTA visibility.
3. Cookie / loyalty popup clash with sales CTAs.

No broad redesign, payment integration, admin work, or localization overhaul was included.

## Findings

- Mobile PDP kept the primary add-to-cart action below the initial viewport, which made the product purchase path feel slow on phones.
- Mobile checkout kept the next-step CTA below the initial viewport, so the first payment-flow action was not immediately available.
- Cookie consent stayed static on commerce paths and did not block CTAs, but the guest loyalty prompt could still be eligible on product/cart routes and needed to be excluded from direct purchase paths.
- During smoke, the PDP fixed bar initially stayed relative to the product buybox because the mobile buybox used `backdrop-filter`; mobile-only blur removal was needed for correct viewport anchoring.

## Changes

- Added a compact mobile purchase bar to the product-detail add-to-cart component.
- Added a compact mobile checkout action bar and hid the duplicate bottom action row only on mobile.
- Suppressed the guest loyalty prompt on PDP and cart routes while keeping it available on the public shell/home.
- Added mobile CSS for the fixed action bars, safe-area spacing, dark-mode contrast, and product-buybox fixed-position compatibility.

## Validation

- `php artisan test tests\Feature\Storefront\ProductCartCheckoutSurfaceTest.php tests\Feature\Checkout\CheckoutFlowTest.php tests\Feature\Storefront\PublicSurfaceSmokeTest.php`
  - Result: 39 passed, 193 assertions.
- `npm run build`
  - Result: passed, generated `public/build/assets/app-DOmgma1r.css`.
- Local Playwright mobile smoke at 390x844:
  - PDP: mobile purchase bar `top=763`, `bottom=830`, visible in viewport.
  - PDP add-to-cart click: success notice and cart link visible.
  - Checkout: mobile action bar `top=763`, `bottom=830`, visible in viewport.
  - Checkout normal mobile duplicate actions hidden.
  - Loyalty prompt markup absent on PDP and checkout commerce path.
  - Cookie consent z-index remained below the purchase bar (`40` vs bar `55`).
  - Horizontal overflow: false.
  - Console/page errors: none.

## Evidence

- Local screenshots:
  - `%LOCALAPPDATA%\Temp\rg-mobile-pdp-checkout-after-final\pdp-mobile.png`
  - `%LOCALAPPDATA%\Temp\rg-mobile-pdp-checkout-after-final\checkout-mobile.png`

## Remaining Risk

- This pass improves the critical mobile CTA availability. It does not change checkout form content, payment provider behavior, or deeper cart/discount rules.
