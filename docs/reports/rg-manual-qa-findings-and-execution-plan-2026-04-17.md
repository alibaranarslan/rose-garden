# RG Manual QA Findings And Execution Plan

Date: 2026-04-17
Workspace: `C:\nwp0203\rose-garden`
Source: User-driven manual QA after Sentinel final smoke

## Purpose

This document records the latest manual QA findings from the storefront review and turns them into an execution order for the next agent rounds.

Scope for this phase:
- finish storefront quality gaps
- defer admin panel deep-check to a later phase
- defer full mobile verification to the last phase after storefront desktop fixes settle

## Current State Summary

- Technical smoke is largely green.
- Final smoke report says the project is live-ready from a blocker perspective.
- Manual QA surfaced multiple non-blocking but important storefront quality gaps.
- The biggest remaining problems are no longer infrastructure-level; they are storefront quality, localization completeness, merchandising consistency, and UX polish.

## Manual QA Findings

### 1. Core readiness and shell

- `/health` works.
- `/sitemap.xml` opens.
- `robots.txt` opens, but currently returns:
  - `User-agent: *`
  - `Disallow:`
- No heavy broken asset or 404 asset spam was observed.

### 2. Homepage / merchandising direction

- Homepage loads without error.
- Hero is considered close to ideal.
- Quick discovery is considered strong.
- Main issue: the homepage still does not fully meet the original target of becoming more colorful, more alive, more professionally merchandised, and denser in visible product exposure without causing fatigue.
- Large card-to-card whitespace still feels too wide.
- The selected showcase area could likely host up to three different products.
- A `3 x 3`, horizontally navigable product presentation concept may fit the page.
- The existing all-products access already partially covers this discovery need, so a stronger strategic bridge to the all-products page may be more useful than adding a redundant grid.
- The upcoming special occasion area is weak and should be strengthened with richer content composition:
  - image
  - product
  - copy
- Products are not fully the first perceived focus after hero; collections are more dominant than direct commerce momentum.
- There are still noticeable large empty spaces.

### 3. Branding / header / navigation

- Logo is sharp, but the text underneath looks visually crossed/struck and should not.
- Header does not overflow.
- Nav items are readable.
- `Tum Koleksiyon`, category, special occasions, and blog buttons in the nav could be:
  - uppercase
  - slightly larger
  - shifted a bit left for better alignment with the search bar
- Footer links work.
- Footer translations are not visibly broken.
- No icon/image breakage across the site.
- No placeholder spam.

### 4. Language switcher and localization quality

- Language switcher dropdown is still visually weak compared to other dropdowns.
- It should visually align better with the rest of the header controls.
- It may be made slightly smaller while keeping readability.
- Language options are readable.
- Click targets are large enough.
- Active language is correctly marked.
- Page-preserving language switching works.
- No nonsensical redirect or broken query string was observed.
- Mobile localization behavior has not been checked yet.

### 5. Translation philosophy and multilingual completeness

- The translation system must remain dynamic and admin-aware rather than hardcoded/static.
- English quality is not acceptable yet.
- Kurdish quality is not acceptable yet.
- No page should remain partially untranslated.
- No block should fall outside the dynamic translation model.
- Header links do not fully translate.
- After some translations, repeated navigation can produce `404 Not Found`.
- Footer opens selected locale pages, but overall translation quality remains inconsistent.
- Checkout with English selected still shows a large percentage of Turkish.
- Blog index may open under the selected locale, but text-language consistency is still weak.
- Similar problems likely exist across other surfaces.

### 6. Listing/category quality

- The category/listing surface behaves like a catalog, which is good.
- The top header-like block of the category page should be improved.
- The decorative product-image usage pattern is too repetitive; more varied product imagery should be used across these showcase placements.
- Product/category matching feels weak.
- Example finding:
  - entering the flower bouquet category shows a statement like `31 products listed in this category`
  - after around 8 bouquets, potted plants appear inside the grid
- This suggests category-product association or category filtering logic may be wrong, or the intended category behavior is being interpreted incorrectly.
- Product-image matching generally looks correct, but still deserves a pass.
- No broken or placeholder-heavy product cards.
- Toolbar/filter area is readable.
- Mobile listing has not been checked yet.

### 7. Search

- Turkish queries return results.
- English queries on Turkish surface return results.
- Kurdish queries on Turkish surface do not return results.
- Multi-word search works.
- Search result images are correct.
- Search result cards navigate to the correct product.
- Empty state is fine.
- Query persistence in the URL is fine.
- Mobile search has not been checked yet.

### 8. Cart

- Missing cart feedback:
  - adding a product to cart does not change the cart icon state
- Product name, image, and variant appear correct.
- Quantity update works.
- Coupon works.
- Totals and discount logic appear correct.
- Checkout CTA is clear.
- Empty cart state is correct.
- Locale switching does not break the cart surface.

### 9. Checkout

- Invalid email shows an error, but on Turkish surface the error appears in English.
- Missing required fields show errors, but on Turkish surface the error appears in English.
- Valid information advances to step 2.
- The “nothing happened” feeling is mostly gone, though a slight perception remains.
- Dark mode form readability is acceptable.
- Delivery/time-slot related checks passed from the user’s perspective.
- Final step generally works.
- Dark mode header/logo harmony is weak on the final stage and may also affect earlier checkout steps.

### 10. Auth / account discoverability

- Account creation is not prominent enough across the storefront.
- Login page opens.
- Register page opens.
- Forgot password opens.
- There does not appear to be a clear in-account settings path for password change/reset.
- Logout path is not sufficiently visible.
- Favorites page opens, but the favorites entry point is not prominent enough on the site.
- Orders page opens.
- Account sidebar localization is only partially aligned with the selected locale.

### 11. Content surfaces

- Blog index opens.
- Blog detail opens.
- Existing blog set feels too empty.
- Around 15 quality blog articles are desired.
- These should be SEO-friendly, aligned with product strategy, and aligned with brand vision/mission.
- Static pages open.
- Special occasions index opens.
- Special occasions detail opens.
- No broken images on these surfaces.
- Translation issues remain.
- Some localized paths/content combinations can still lead to `404`.
- Locale switching itself does not fully collapse these surfaces, but language quality is incomplete.

### 12. Dark mode

- Header contrast should be improved.
- Hero is readable, but can improve.
- Product cards are clean.
- Form fields are readable.
- CTAs remain clear.
- Language switcher contrast is especially poor.
- Checkout dark mode header/logo harmony should be reviewed.

## Deferred Areas

The user explicitly wants these after storefront completion:

1. Admin panel deep-check
2. Mobile verification and fixes

These are intentionally not the current execution focus.

## Priority Classification

### Priority 1

- Dynamic multilingual completeness across storefront
- Broken/misaligned locale navigation and locale-driven 404 behavior
- Checkout validation/error localization
- Kurdish search gap
- Category/product mismatch in listing behavior

### Priority 2

- Homepage merchandising polish and density rebalance
- Weak special occasion block
- Header/nav readability polish
- Cart icon state feedback
- Auth/account discoverability and logout/settings visibility
- Dark mode contrast and checkout logo/header harmony

### Priority 3

- Blog content expansion program
- Product-image variety improvements in decorative placements
- Remaining copy and content refinement

## Recommended Execution Order

### Phase 1: Localization and route/content integrity

Goal:
- make language behavior trustworthy before visual polishing continues

Includes:
- storefront-wide dynamic translation completeness audit
- locale-aware links and locale persistence fixes
- localized checkout/auth validation messaging
- blog/static/special-occasion multilingual cleanup
- Kurdish search gap
- removal of locale-related 404 regressions

### Phase 2: Catalog truth and merchandising correctness

Goal:
- ensure category logic and product grouping are trustworthy

Includes:
- category-product alignment audit
- listing/category filtering correction
- search/catalog consistency review
- product-image pairing verification pass where needed

### Phase 3: Homepage and storefront polish

Goal:
- move storefront from technically ready to visually convincing

Includes:
- homepage density rebalance
- reduction of oversized whitespace
- showcase strategy improvements
- stronger bridge toward all-products discovery
- stronger special occasions block
- nav/header readability polish
- language switcher visual polish
- dark mode header contrast polish

### Phase 4: Utility surface polish

Goal:
- improve micro-feedback and account usability

Includes:
- cart icon feedback state
- register/favorites discoverability
- logout/settings path visibility
- account sidebar localization cleanup
- checkout dark-mode logo/header alignment

### Phase 5: Content expansion

Goal:
- improve perceived completeness and SEO depth

Includes:
- blog program planning and seeding strategy
- editorial consistency improvements

### Phase 6: Deferred validation phases

Run only after storefront phases above settle:

1. Admin panel deep-check
2. Full mobile QA and mobile fixes

## Proposed Next Agent Order

### Agent Round A

Focus:
- localization integrity and locale-driven storefront consistency

Reason:
- this is the broadest remaining cross-site quality issue and will contaminate every later QA pass if left unresolved

### Agent Round B

Focus:
- catalog/category truth and search/category consistency

Reason:
- the storefront cannot be trusted if category pages mix unrelated product types

### Agent Round C

Focus:
- homepage merchandising polish and storefront visual density tuning

Reason:
- once language and catalog correctness settle, visual improvements can be applied with lower regression risk

### Agent Round D

Focus:
- utility polish for cart/account/discoverability/dark-mode leftovers

### Agent Round E

Focus:
- blog/content expansion preparation

## Working Rule For Next Commands

- One agent command at a time
- Each agent should produce a short markdown report in `docs/reports`
- Admin panel deep-check is deferred
- Mobile QA is deferred until storefront desktop issues settle

