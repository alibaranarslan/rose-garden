# RG Storefront Restoration Brief

Date: 2026-04-17
Workspace: `C:\nwp0203\rose-garden`
Scope: storefront restoration strategy only; no code, CSS, route, or view edits

## Executive summary

- Live homepage owner is `App\Http\Controllers\StorefrontHomeController` -> `resources/views/home/layout-studio.blade.php`. `resources/views/home/index.blade.php` is not the live homepage owner and should be treated only as a legacy density/reference artifact.
- The current decision baseline in `docs/reports/current-site-design-architecture-decisions-2026-04-17.md` is correct in principle: product-first, truthful density, support secondary, utility surfaces utility-first.
- The previous acceptable screenshot shows stronger early merchandise proof and a fuller first-scroll rhythm, but it also over-accumulates editorial/trust/support sections and repeats similar visual families too often.
- The current screenshot swings too far in the opposite direction: more disciplined shell, but under-filled body rhythm, large empty intervals, weak first-two-viewport product proof, and too much perceived negative space relative to catalog depth.
- Restoration should move toward the previous version's density and merchandising energy without restoring its trust/blog/support pile-up.
- Homepage should feel full because real product/category proof appears earlier and more often, not because support content or decorative shells expand.
- Listing and PDP should carry the main merchandising load. Homepage should open the sale; PLP and PDP should close it.
- Cart, auth, and checkout should get calmer, shorter, more operational surfaces. They should not compete with storefront merchandising language.
- Blog, FAQ, delivery info, contact, and special-occasion pages should support product choice, but none of them should out-stage catalog, PLP, or PDP.
- Dark/light and `tr/en/ku` support need layout guardrails, not just token parity. Several current UI patterns use pills, chips, compact cards, and decorative surfaces that can break under longer copy or low-contrast themes.

## Inputs and evidence

- Decision baseline read: `docs/reports/current-site-design-architecture-decisions-2026-04-17.md`
- Surface map read: `docs/reports/rg-canonical-surface-map-2026-04-17.md`
- Visual references opened successfully:
  - previous acceptable reference: `C:\Users\Ali\Downloads\onceki.png`
  - current reference: `C:\Users\Ali\Downloads\simdiki.png`
- Core code surfaces reviewed:
  - `routes/web.php`
  - `app/Http/Controllers/StorefrontHomeController.php`
  - `app/Http/Controllers/ProductController.php`
  - `app/Http/Controllers/BlogController.php`
  - `app/Http/Controllers/SpecialOccasionController.php`
  - `app/Http/Controllers/PageController.php`
  - `app/Services/HomeModuleDataService.php`
  - `app/Services/LayoutConfigService.php`
  - `app/Support/StorefrontImage.php`
  - `resources/views/home/**`
  - `resources/views/products/**`
  - `resources/views/cart/**`
  - `resources/views/checkout/**`
  - `resources/views/account/**`
  - `resources/views/blog/**`
  - `resources/views/special-occasions/**`
  - `resources/views/pages/**`
  - `resources/views/layouts/**`
  - `resources/views/components/**`
  - `resources/css/app.css`

## Constraints and unclear items

- `unclear`: published Layout Studio state could not be read from DB because the local MySQL connection was unavailable in this environment.
- `unclear`: the decision document references restore docs under `handoff/03-quality/release-audit/...`, but those files were not present in this workspace.
- Therefore, section-activation conclusions are based on code ownership, template behavior, and the provided current screenshot, not on direct DB inspection of the current published layout revision.

## Visual delta: previous vs current

| Axis | Previous acceptable reference | Current reference | Restoration decision |
| --- | --- | --- | --- |
| Visual rhythm | Dense, continuous, little dead air, but too many similar section beats | Clean shell, but body rhythm collapses into large empty intervals | Restore continuity, not clutter |
| Section order | Hero -> category/discovery -> featured product -> more rails -> trust -> Instagram -> blog -> footer CTA | Hero -> sparse body with large gaps; current visible order appears closer to hero -> occasion/support -> featured -> footer CTA | Bring back early merchandise proof before support and before large empty areas |
| Product density | Strong in first two viewports and again in mid-page | Too little product proof too late | Increase real product/card density, especially before first scroll fatigue |
| Empty space | Premium but occasionally too padded in supporting sections | Over-padded to the point of feeling under-stocked | Tighten vertical gaps and reduce section dead zones |
| Trust/fallback intensity | Too many reassurance/support/editorial surfaces | Less trust clutter, but body still relies on support cards to fill space | Keep one compact reassurance beat only |
| Repeated blocks | Repeated large cards, similar photo families, repeated reassurance language | Repetition reduced, but body becomes thin | Avoid duplicate block roles; do not solve thinness with cloned support modules |
| Readability | Mostly readable, but some pages compete with too many panels | Readable, but under-filled and visually weak after hero | Keep readability; add density via product evidence, not via more prose |
| Premium balance | Warm, rich, slightly over-produced | Cleaner, but bordering on unfinished/minimal | Target "full but controlled", not "lightweight boutique landing page" |

### Synthesis

- Previous version is the better directional reference for energy, first-scroll merchandising, and catalog proof.
- Current version is the better directional reference for restraint, less trust clutter, and less editorial overreach.
- The correct restore target is a hybrid:
  - previous version's early product density
  - current version's stricter support hierarchy
  - stronger spacing discipline than both

## Storefront-wide design rules

1. Product-first always means real product proof appears before large support or editorial sections.
2. Fullness must come from category cards, product cards, and real collection proof, not from oversized explanatory cards.
3. One section should do one job. Discovery, editorial spotlight, reassurance, and social proof must not duplicate each other.
4. Homepage opens interest. Listing confirms availability. PDP resolves choice. Cart/checkout remove friction. Blog/static pages answer questions.
5. Trust content is allowed only as short conversion support, never as a parallel storytelling system.
6. Utility surfaces must not inherit homepage merchandising density.
7. Static and help pages must not become hidden landing pages for product merchandising.
8. Dark mode should preserve hierarchy through contrast, not through heavier overlays.
9. Multilingual support must be preserved by layout tolerance, not by trimming copy to the shortest language.
10. Admin controls should expose order, visibility, limits, and content sources, but not allow each surface to drift into a different design system.

## Product density and readability rules

- Homepage first 2 viewports on desktop must contain:
  - hero
  - visible category/discovery proof
  - at least one dense product proof module
- Homepage should show real product/category evidence before any blog, Instagram, or large reassurance block.
- Homepage may use only 1 dedicated reassurance band and 1 optional social/editorial band in the full page.
- PLP should show grid immediately after toolbar; support content must sit below the primary grid or below pagination.
- PLP support cap:
  - max 1 compact support cluster
  - max 3 support links
  - max 2 short explanatory notes
- PDP above-the-fold on desktop must show gallery, title, price, variants/add-to-cart, and primary reassurance without scrolling.
- PDP reassurance cap near buy box:
  - max 2 compact reassurance cards
  - max 2 supporting chips
  - highlight list only if it expresses real product-specific value
- Cart support cap:
  - max 2 short summary notes
  - no extra visual storytelling blocks
- Auth support cap:
  - max 1 small support note
  - no secondary marketing stack
- Checkout step cap:
  - 1 progress system
  - 1 focused form section per step
  - 1 short contextual note where needed
- Blog/article support cap:
  - max 1 related-products rail
  - no trust strips
  - no homepage-style marketing band

## Homepage restoration direction

### Current problem

- Live owner `resources/views/home/layout-studio.blade.php` is structurally correct, but current output appears under-filled.
- `layout-studio.blade.php` explicitly rejects `trust_badges` and `blog_preview` from body rendering. That removes clutter, but if category/new/best-seller modules are inactive or under-populated, the page becomes too thin.
- `components/store-hero.blade.php` is visually strong, but it already spends substantial space on highlights, pills, and spotlight framing. The following section must compensate with immediate product proof.
- Legacy `resources/views/home/index.blade.php` shows what fuller rhythm looked like, but it overshoots with too many sections and repeated support/editorial logic.

### Target user feeling

- "This store is alive, stocked, curated, and easy to shop."
- "I immediately see real bouquets/plants/categories, not just claims."
- "It feels premium because it is controlled and confident, not because it is sparse."

### Content and architecture direction

- Keep Layout Studio as the homepage owner.
- Do not revive `home/index.blade.php` as the live source.
- Use legacy homepage only as a reference for density and ordering, not as a template to restore wholesale.
- Treat homepage modules as four roles only:
  - hero
  - discovery
  - merchandise proof
  - one secondary support/editorial layer

### Ideal homepage section order

1. Hero
2. Compact discovery band with category proof plus 2 mini product cards
3. Primary product proof module
4. Secondary product proof module
5. Optional special-occasion module if seasonally relevant and product-backed
6. Optional social or blog module, only one of them
7. Footer CTA
8. Footer

### Hero + first 2 viewport rule

- Viewport 1: hero must show headline, CTA, hero image, and one product spotlight.
- Viewport 2: user must already see category proof and real purchasable product cards.
- By the end of viewport 2, the user should have seen:
  - at least 4 category/product decision anchors
  - at least 1 dense product module
  - no more than 1 reassurance micro-layer

### Which modules should shrink

- `components/store-hero.blade.php`
  - shrink chip/pill count if the next section is weak
  - do not let hero become the whole page's density substitute
- `home/sections/occasion-spotlight.blade.php`
  - keep compact unless it has strong seasonal relevance and real product count
- `home/sections/instagram-preview.blade.php`
  - keep as a short CTA band, not a visual gallery system

### Which modules should be removed from main flow

- Any large blog-preview return on homepage if it competes with merchandise proof
- Any trust block larger than a short reassurance strip
- Any duplicate editorial/product spotlight that repeats the hero role

### Which modules should strengthen

- `home/sections/category-showcase.blade.php`
  - this is the best place to restore fullness without fake density
- `home/sections/product-rail.blade.php`
  - at least one rail must appear early and feel populated
- `home/sections/featured-showcase.blade.php`
  - keep only if it functions as a true single-product focal shift after discovery

### Keep

- `components/store-hero.blade.php` visual language
- Layout Studio ownership and module concept
- early category + mini-card combination direction in `home/sections/category-showcase.blade.php`
- one featured product moment

### Reduce

- hero support pills/highlights if downstream density is weak
- occasion explanatory copy
- footer promo visual dominance relative to body

### Remove

- homepage trust/blog support stacks as parallel storytelling lanes
- any attempt to restore fullness by re-adding large fallback bands
- legacy duplicate homepage logic as production source

### Redesign

- Make discovery section the first post-hero proof zone.
- Make one product rail visible before any seasonal/support/editorial block.
- Tighten vertical spacing between hero, discovery, and primary product proof.
- Keep the page visually full through card count and module cadence, not through taller panels.

### Acceptance criteria

- Hero is followed by visible catalog proof within one scroll gesture.
- First two viewports contain both category proof and real product cards.
- Homepage contains no more than 1 dedicated reassurance band and 1 secondary editorial/social band total.
- No large dead space between hero, discovery, and first merchandise module.
- Homepage feels fuller than current, but less cluttered than the previous reference.

## Listing/category restoration direction

### Current problem

- `resources/views/products/index.blade.php` keeps the grid central, which is correct.
- `components/product-list-layout.blade.php` still spends a lot of energy on a large hero shell, category pills, visual card, and explanation before the main grid.
- Post-grid support cluster in `products/index.blade.php` is conceptually valid, but it should be shorter and clearly secondary.

### Target user feeling

- "I landed in a real product catalog quickly."
- "Filters help me narrow; they do not replace browsing."
- "Support is available, but nothing pulls me away from the grid."

### Content and architecture direction

- Keep PLP as a grid-first decision surface.
- Let hero/header support orientation only; do not turn PLP into a mini-landing page.
- Keep filter density high and explanatory density low.

### Merchandise-first density rules

- Listing hero should not consume more than roughly the first 25 to 30 percent of desktop page depth before the toolbar/grid appears.
- Grid must be visible immediately after toolbar with no extra marketing strip above it.
- Desktop should expose a strong card field early; mobile should expose first cards without long explanatory preamble.

### Grid-center disruptors

- oversized hero explanation in `components/product-list-layout.blade.php`
- side explanation card labelled as collection logic
- below-grid support cluster with multiple links plus multiple notes

### Fallback/reassurance upper bound

- 1 compact support cluster below the grid
- 3 support links maximum
- 2 short notes maximum
- no second product-like support section after pagination

### Keep

- `components/product-grid.blade.php` density
- left filter rail + mobile drawer structure
- `components/product-card.blade.php` as the primary merchandising unit

### Reduce

- PLP hero decorative depth
- explanatory copy around category logic
- post-grid support notes

### Remove

- any future addition of blog/trust/social blocks inside PLP
- any duplicate mini-gallery outside the main grid

### Redesign

- Shorten hero copy and visual emphasis so the grid lands earlier.
- Preserve filters and counts, but shift perceived weight from top shell to product field.
- Keep post-grid support as a short exit/help zone, not a second information page.

### Acceptance criteria

- User reaches visible product cards immediately after the toolbar.
- Grid remains the clear visual and conversion center.
- Support blocks never read as a second primary surface.
- Page feels full due to product count and card rhythm, not due to helper content.

## PDP restoration direction

### Current problem

- `resources/views/products/show.blade.php` has solid core structure: gallery left, sticky buy box right, related products below.
- The buy box still carries too many adjacent reassurance elements: delivery card, preparation card, chips, highlights, WhatsApp, share, and later support notes.
- Product story block under gallery is useful but can become too editorial if PDP already contains long description plus highlight cards plus related-products notes.

### Target user feeling

- "I understand the product, trust it, and can buy it without scanning many support boxes."
- "The page feels premium because the gallery and buy box dominate."

### Content and architecture direction

- PDP hierarchy must be:
  - gallery
  - title/price/variant/CTA
  - short delivery/preparation reassurance
  - description
  - related products
- Support content should clarify purchase risk, not expand into a brand essay.

### Merchandise-first density rules

- Desktop above fold must show main gallery, title, price, variant controls, and add-to-cart simultaneously.
- Product-specific highlights should survive only if they are truly product-specific.
- Related products should remain a product rail, not a fallback logic explanation area.

### Buy-box disruptors

- dual reassurance cards plus multiple chips
- WhatsApp plus share competing at the action layer
- large highlight stack immediately below add-to-cart

### Fallback/reassurance upper bound

- max 2 compact reassurance cards near buy box
- max 2 reassurance chips
- highlight stack only when backed by real product data
- below-related support notes should be removed or reduced to one short note if related inventory is empty

### Keep

- gallery and sticky buy box layout
- related products rail
- delivery note as a short operational reassurance

### Reduce

- number of reassurance elements around the add-to-cart zone
- brand-language repetition across short description, reassurance cards, chips, and highlights

### Remove

- any generic reassurance that is not specific to this product or this purchase flow
- any duplicate alternative-path content under related products

### Redesign

- Compress reassurance around the buy box into one tightly-scoped cluster.
- Preserve long description lower on page, but keep it subordinate to the commercial core.
- Make the purchase path visually calmer and more decisive.

### Acceptance criteria

- Buy box reads as the dominant decision block.
- Gallery and purchase controls remain central on first view.
- Reassurance exists, but does not outgrow commercial content.
- Related-products section stays merchandise-led even when inventory is thin.

## Cart/login/checkout restoration direction

### Cart

#### Current problem

- `resources/views/livewire/cart-page.blade.php` is structurally good, but support notes under the order summary are already close to the upper useful limit.
- Empty-cart state is acceptable, but must not turn into a mini-homepage.

#### Target user feeling

- "I can review, edit, and continue quickly."

#### Content and architecture direction

- Cart is an editing and commitment surface, not a discovery surface.

#### Keep

- line-item clarity
- visible order summary
- in-cart card-message editing

#### Reduce

- extra supportive prose in summary area

#### Remove

- any future merchandising band beyond empty-cart recovery links

#### Redesign

- Keep summary compact and operational.
- Make support notes short, transactional, and non-promotional.

#### Acceptance criteria

- Summary is scannable in seconds.
- No merchandising-style blocks appear in non-empty cart.

### Login/register/reset

#### Current problem

- `components/auth-split-layout.blade.php` creates a branded split-screen, which is fine.
- Login/register pages add extra support notes and benefit stacks that can drift toward marketing language.

#### Target user feeling

- "This is secure, clear, and fast."

#### Content and architecture direction

- Auth should keep brand warmth but reduce persuasion density.

#### Keep

- split layout
- single strong hero/brand panel

#### Reduce

- extra support card on login
- benefit stack length on register

#### Remove

- any secondary merchandising CTA that distracts from auth completion

#### Redesign

- Keep one short trust/security/support note only.
- Preserve Google auth option if active, but visually subordinate it to the main form.

#### Acceptance criteria

- Form remains the dominant element.
- Brand panel supports trust, not merchandising.

### Checkout entry / checkout flow

#### Current problem

- `resources/views/layouts/checkout.blade.php` already separates checkout typography and shell, which is good.
- `resources/views/livewire/checkout-wizard.blade.php` is utility-correct but still carries multiple stacked informational areas in later steps.
- Checkout should be calmer than current storefront, not equally expressive.

#### Target user feeling

- "Secure, orderly, and low-friction."

#### Content and architecture direction

- Checkout should prioritize sequence, field clarity, and cost visibility.
- Visual language should be calm and precise, not plush or content-rich.

#### Utility-first operational rules

- 1 progress indicator only
- 1 primary task per step
- 1 compact contextual note where required
- summary, payment info, and legal confirmations must feel operational, not promotional

#### Keep

- dedicated checkout shell
- clear step progression
- payment/bank info separation

#### Reduce

- auxiliary explanatory copy
- decorative service chips if they compete with form tasks

#### Remove

- any homepage-style trust storytelling
- any non-operational aside that delays field completion

#### Redesign

- Step 1: identity/address only
- Step 2: delivery logistics only
- Step 3: payment/legal confirmation only
- Keep support language short and confidence-building, not sales-oriented

#### Acceptance criteria

- Each step has one dominant task.
- Conversion tone is calm, direct, and low-noise.
- Checkout feels shorter than the sum of its forms.

## Blog/static/special occasion direction

### Blog listing/detail

#### Current problem

- `resources/views/blog/index.blade.php` and `blog/show.blade.php` are readable, but still carry a strong editorial shell plus stats plus note cards plus related products.
- Blog can easily become visually richer than core commerce surfaces.

#### Target user feeling

- "Helpful, credible, and still connected to the store."

#### Keep

- page hero pattern
- related products on detail

#### Reduce

- editorial note panels and stats density
- supportive copy around cards

#### Remove

- any trust-strip behavior
- any homepage-style promo rhythm

#### Redesign

- Blog listing should privilege article cards, not explanatory side panels.
- Blog detail should privilege content readability, then one related-products rail.

#### Acceptance criteria

- Blog helps product choice without becoming more visually dominant than PLP/PDP.

### Special occasions

#### Current problem

- `special-occasions/index.blade.php` and `show.blade.php` are visually rich and may become over-produced relative to the rest of storefront.
- These pages are at risk of becoming themed editorial microsites instead of seasonal merchandising pages.

#### Target user feeling

- "This is a seasonal merchandising shortcut, not a detached campaign page."

#### Keep

- special-occasion ownership and product linkage
- one hero atmosphere layer
- featured rail plus full grid on detail

#### Reduce

- note-card count
- decorative themed panels
- timeline and splash sections if they crowd product proof

#### Remove

- any decorative block that pushes products too far down

#### Redesign

- Index should act like seasonal navigation plus product entry.
- Detail should act like themed PLP: quick rail first, full grid second, notes tertiary.

#### Acceptance criteria

- Product proof appears early.
- Seasonal theme never outruns product discoverability.

### Static pages / contact / FAQ / delivery info

#### Current problem

- `pages/contact.blade.php`, `pages/faq.blade.php`, and `pages/delivery-info.blade.php` are useful, but each still behaves like a merchandised content experience.
- `pages/show.blade.php` is the cleanest of the static templates because it is mostly content + hero.

#### Target user feeling

- "This page answers my question and gets out of the way."

#### Keep

- contact direct actions
- FAQ accordion
- delivery tables and operational facts
- CMS page readability style

#### Reduce

- stats cards on utility pages
- helper sidebars on FAQ/delivery/contact

#### Remove

- any product-like card rhythm on utility content
- any extra promotional CTA beyond one next-best action

#### Redesign

- Contact: contact methods first, form second, map optional third
- FAQ: questions first, one support CTA only
- Delivery info: operational data first, notes second
- CMS pages: readable prose, minimal chrome

#### Acceptance criteria

- Utility pages answer operational questions faster than they market the brand.
- None of these pages feel like secondary landing pages.

## Dark mode / light mode / multilingual guardrails

### Dark/light guardrails

- Primary CTAs must maintain strong contrast in both themes.
- Key body copy must not sit on low-contrast tinted cards.
- Decorative grids, glows, and overlays must never reduce product image legibility.
- Product cards need neutral-enough image stages so light and dark themes both preserve product color accuracy.
- Support chips should never become the highest-contrast element on a page.

### Multilingual guardrails

- `tr/en/ku` copy must be validated on:
  - hero headline length
  - CTA width
  - chip/pill overflow
  - filter labels
  - checkout legal consent lines
  - FAQ accordion titles
- Do not rely on all-caps microcopy for essential information if Kurdish or English strings are longer.
- One-line pill systems should not carry critical copy.
- Section titles must tolerate longer English/Kurdish strings without collapsing layout or forcing awkward line clamps.

## Keep / reduce / remove / redesign matrix

| Area | Keep | Reduce | Remove | Redesign |
| --- | --- | --- | --- | --- |
| Homepage | hero language, discovery logic, one featured spotlight | hero support density, occasion copy | large trust/blog stacks | stronger early product cadence |
| PLP | grid, filters, toolbar | top hero shell, post-grid notes | any extra marketing zones | faster path from hero to grid |
| PDP | gallery + sticky buy box + related rail | reassurance around buy box | generic fallback notes | compress decision support |
| Cart | line-item clarity, summary | summary notes | merchandising behavior | more operational summary rhythm |
| Auth | split layout, secure tone | benefits/support clutter | extra persuasive blocks | simpler trust-first auth UI |
| Checkout | dedicated shell, steps | helper copy | decorative storytelling | task-first step hierarchy |
| Blog | article cards, readable prose, related products | editorial side notes | trust-strip behavior | content-first, store-second balance |
| Special occasions | seasonal ownership, quick rail + grid | note-card/theme density | campaign-like filler | seasonal PLP behavior |
| Static/help | one hero, direct answers | stats/side panels | productized utility cards | operational-first content pages |

## Admin and architecture implications

### What should be admin-manageable

- Homepage module order, visibility, content limit, title override, subtitle override, CTA, background tone
- Homepage hero spotlight source and homepage copy
- Seasonal header themes
- Product/category/blog/special-occasion assignments and imagery
- Appearance tokens already exposed by `LayoutConfigService`

### What requires content ops

- `tr/en/ku` homepage copy quality
- category naming and short descriptions
- PDP short descriptions, highlights, delivery notes
- blog titles, excerpts, related-product assignment
- special-occasion product assignment quality
- static utility copy accuracy for FAQ and delivery rules

### Where token/preset/module control is enough

- homepage module order
- homepage spacing tone
- module visibility
- appearance tokens
- section-level CTA presence

### Where token/preset/module control is not enough

- PLP hero-vs-grid weight
- PDP buy-box support overload
- cart/auth/checkout utility hierarchy
- FAQ/delivery/contact over-merchandising
- multilingual overflow handling
- fallback/reassurance caps

### Architectural notes

- Keep Layout Studio as homepage source of truth.
- Do not route storefront back to `home/index.blade.php`.
- Duplicate locale route definitions and missing DB verification are real concerns, but route changes are outside this brief.
- FAQ and delivery info are still Blade-embedded content surfaces; they need content-governance decisions, not just style decisions.

## Safe implementation order

1. Lock storefront-wide density, support-cap, dark/light, and multilingual rules.
2. Rebalance homepage section order and module weights without reviving legacy homepage code.
3. Tighten PLP hero/support weight and protect grid centrality.
4. Compress PDP reassurance around the buy box and preserve gallery/CTA dominance.
5. Simplify cart, auth, and checkout toward utility-first behavior.
6. Reduce editorial/support overreach on blog, special occasions, FAQ, contact, and delivery info.
7. Finalize admin controls and content-ops checklist per locale.

## Acceptance checklist

- Homepage is visibly fuller than current within the first 2 viewports.
- Homepage remains cleaner and less repetitive than the previous reference.
- Real product/category proof appears before support/editorial sections.
- No surface uses trust/support modules to fake catalog depth.
- PLP remains unmistakably grid-first.
- PDP remains unmistakably gallery/buy-box-first.
- Cart, auth, and checkout read as utility-first and conversion-calm.
- Blog and static/help pages support the store without overshadowing it.
- Special occasions behave like themed merchandising pages, not detached campaign pages.
- Dark mode preserves contrast and hierarchy.
- `tr/en/ku` layouts remain stable under longer copy.
- Admin control is sufficient for module sequencing and content population, but critical hierarchy rules stay template-level.

