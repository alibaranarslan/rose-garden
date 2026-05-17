# RG Manual Admin Storefront Smoke Log

Date: 2026-04-18
Workspace: `C:\nwp0203\rose-garden`
Source: User-run manual smoke after admin deep-check

## Progress Tracking

### 1. Global Yayın Sağlığı

Status: `karisik`

Confirmed working:
- `/health` works correctly
- `/robots.txt` opens
- `/sitemap.xml` opens
- homepage opens without initial fatal load error
- network tab is clean at a high level; observed requests are `200`

Detected problems:
- Browser reports Quirks Mode warning:
  - `This page is in quirks mode. Page layout may be impacted. Use "<!DOCTYPE html>" for standards mode.`
- Alpine / Livewire runtime errors exist on homepage:
  - `scrollRail is not defined`
  - `canPrev is not defined`
  - `canNext is not defined`
- Error trace points to a homepage rail block using:
  - `x-data="scrollRail()"`
  - `:disabled="!canPrev"`
  - `:disabled="!canNext"`
- This indicates at least one interactive homepage rail/control shipped without the required Alpine controller/state registration.

Non-blocking note:
- Cookie warning about `_ga_WRTKSNRSBR` expiry overwrite observed.
- This is noted but not currently treated as the main storefront blocker.

## Immediate Implication

Before continuing broad visual/manual validation, homepage shell JavaScript integrity should be treated as a real issue because:
- interactive product rail controls are failing at runtime
- console is not clean
- homepage UX observations can be polluted by missing Alpine behavior

## Next Manual Block To Run

Proceed next with:
- `2. Header ve Üst Shell`

### 2. Header ve Üst Shell

Status: `karisik`

Confirmed working:
- logo is clear and sharp
- logo subtitle no longer looks struck/corrupted
- nav items are generally readable
- auth/account actions are visible
- favorites entry is noticeable
- cart affordance is visible and clean
- overall header structure is stable
- dark mode header contrast is acceptable
- no broken icons, overflow, or structural shell breakage observed

Detected problems:
- search bar is not horizontally aligned with the adjacent controls/buttons
- uppercase/casing treatment across nav and broader shell is using Turkish-character substitutions poorly:
  - examples observed:
    - `Ö` appearing like `O`
    - `I` / `İ` behavior looking inconsistent
- the language switcher dropdown remains visually broken:
  - appears too transparent
  - readability is poor
  - in dark mode the dropdown background stays close to light mode while text styling follows dark-mode assumptions, causing weak contrast and unreadable combinations

## Next Manual Block To Run

Proceed next with:
- `3. Dil Değiştirici ve Locale Sürekliliği`

### 3. Dil Değiştirici ve Locale Sürekliliği

Status: `karisik`

Confirmed working:
- locale switching works
- returning between locales lands on the correct page
- URL continuity is logical
- no meaningless query string is introduced by locale switching
- header links open under the correct selected locale
- footer links open under the correct selected locale
- blog surfaces preserve locale continuity at a routing level

Detected problems:
- translation completeness remains inadequate across the storefront
- continuity works logically, but content-language continuity does not
- English surfaces still contain partially untranslated Turkish content blocks
- Kurdish coverage is still considered incomplete
- blog surfaces preserve locale context, but translation gaps continue
- the issue is not primarily locale switching anymore; it is translation package / translation logic / translation scope / translation sufficiency
- this likely affects:
  - shell copy
  - module copy
  - content-fed blocks
  - possibly admin-fed localized text paths

Visual confirmation:
- user-provided screenshots confirm that the English homepage still contains mixed-language content rather than a fully English experience
- locale continuity appears operational, but localization completeness is not production-grade yet

Escalation:
- this is now a Priority 1 quality issue
- this should be handled as a dedicated localization coverage overhaul rather than treated as a minor checklist note
- admin-fed text and dynamic translation behavior should also be verified in that fix round

## Critical Follow-up Issue: Localization Coverage Overhaul

Status: `kayit altina alindi`

This issue is broader than locale routing continuity. The current problem is:
- locale switching works
- route continuity works
- but translation coverage is incomplete and inconsistent across runtime surfaces

This means the remaining work must cover:
- translation packages / locale resources
- blade-level visible copy
- module-level copy
- content-fed blocks
- account/auth/checkout utility copy
- blog/static/special occasion copy
- admin-fed localized text behavior
- fallback behavior when a locale field is missing

Planned follow-up work for this issue:
1. Audit visible storefront copy surface-by-surface for `tr/en/ku`.
2. Classify missing coverage by source:
   - static translation key gap
   - dynamic content locale gap
   - fallback mismatch
   - admin-fed content not localized
3. Verify whether admin-originated localized fields are actually rendered per selected locale.
4. Close mixed-language surfaces so each locale feels whole:
   - English pages should read fully English
   - Kurdish pages should read fully Kurdish
5. Re-run manual locale walkthrough after the fix round.

Tracking note:
- This remains open.
- It should be handled in a dedicated fix round, but the manual checklist continues in parallel as requested.

## Next Manual Block To Run

Proceed next with:
- `4. Ana Sayfa`

### 4. Ana Sayfa

Status: `karisik`

Confirmed working:
- hero feels strong and balanced
- purchase direction is clearer than before
- large whitespace has been significantly reduced
- selected showcase feels fuller and more commercial
- selected showcase usage is efficient
- quick discovery now supports shopping intent
- collection/product balance feels good
- blog/trust/support still do not overpower commerce
- CTAs are clear
- page overall feels more professional and more alive
- density feels fuller without becoming overwhelming

Detected problems:
- the hero red bouquet image still repeats too often across the homepage and feels visually recycled
- the special occasion block is still not strong enough from both:
  - commercial perspective
  - design/composition perspective
- the right side of the special occasion area feels underused / empty and could be supported by broader special-occasion catalog or related content

Runtime issue still present:
- homepage console still reports Alpine runtime failures:
  - `scrollRail is not defined`
  - `canPrev is not defined`
  - `canNext is not defined`
- this confirms homepage rail interaction remains broken even though overall visual polish improved
- homepage rail/controller registration should be treated as an active fix item

## Current High-Priority Open Items From Manual Smoke

1. Homepage rail JavaScript/Alpine breakage
2. Language/translation coverage insufficiency
3. Language switcher dropdown readability/contrast
4. Search bar alignment issue in header
5. Homepage special occasion block remains underpowered
6. Repetitive product-image usage in homepage hero-adjacent merchandising

## Next Manual Block To Run

Proceed next with:
- `5. Listing / Kategori`

### 5. Listing / Kategori

Status: `temiz`

Confirmed working:
- category heading and grid content are aligned
- no unrelated product leakage was observed
- product count and grid behavior feel consistent
- no nonsensical product mixing was observed
- category upper block feels orderly and meaningful
- the page clearly reads as a catalog
- images match the correct products
- no broken cards or placeholder-heavy behavior observed
- toolbar / filter area is readable
- the page gives a clear “I am in the correct category” feeling
- search -> listing -> PDP logic feels consistent
- landing/header visual and semantic alignment is strong

## Next Manual Block To Run

Proceed next with:
- `6. Ürün Detay`

### 6. Ürün Detay

Status: `karisik`

Confirmed working:
- primary product image is correct
- gallery works correctly
- thumbnail interactions work correctly
- product parameters feel coherent
- add-to-cart area is understandable
- reassurance/help content does not overpower the product
- related products feel relevant
- no obviously unrelated products observed in related products
- PDP feels commercial but still relatively calm
- visual, copy, and purchase flow feel aligned

Detected problems:
- buy-box can still be improved:
  - delivery note
  - preparation language/copy
  should likely move lower in hierarchy and become less intrusive
- product detail page can return `404` when switching locale

Critical follow-up note:
- PDP locale-switch `404` should be tracked as a dedicated integrity issue
- likely root-cause areas to inspect later:
  - localized route generation
  - product slug/locale mapping
  - translated product URL strategy
  - fallback behavior when localized product fields are incomplete
- this is now recorded for a future fix round

## Current High-Priority Open Items From Manual Smoke

1. Homepage rail JavaScript/Alpine breakage
2. Translation/localization coverage insufficiency
3. Language switcher dropdown readability/contrast
4. Search bar alignment issue in header
5. Homepage special occasion block remains underpowered
6. Repetitive product-image usage in homepage hero-adjacent merchandising
7. PDP locale switch can produce `404`
8. PDP buy-box delivery/preparation copy hierarchy can be improved

## Next Manual Block To Run

Proceed next with:
- `7. Search`

## Manual Smoke Note For Search

The user explicitly does not want to spend manual QA energy creating ambiguity around the search surface at this stage.

Decision:
- search will not be expanded manually in this checklist round
- the future target agent handling search/localization/search-result integrity should gather the relevant outputs itself
- that agent should validate:
  - Turkish, English, and Kurdish queries
  - multi-term behavior
  - result-card correctness
  - search-to-PDP continuity
  - locale behavior on results
  - mixed-language leakage in search surfaces

Implication:
- search remains an open validation area
- it should be handled in the next relevant fix/verification prompt as an explicit self-verified agent task rather than a manual-user checklist item

### 8. Cart ve Utility Feedback

Status: `buyuk olcude temiz`

Confirmed working:
- cart icon / badge visibly changes after add-to-cart
- no “was it added?” hesitation remains
- badge count appears correct
- product name is correct on cart surface
- product image is correct
- variant information is correct
- cart generally feels friction-light

Needs extra data / not fully verified manually:
- coupon behavior was not fully verified because valid coupon inventory is not known during this manual run
- total/discount logic tied to coupon behavior was therefore not fully verified in this pass
- locale continuity on cart was not re-tested in this pass because the user requested clearer instruction before re-checking that behavior

Clarification note for later manual passes:
- “locale continuity on cart” means:
  - while on the cart page, change language
  - confirm the cart page stays as the cart page in the newly selected locale
  - confirm items remain visible and the cart surface does not break or redirect unexpectedly

## Next Manual Block To Run

Proceed next with:
- `9. Checkout`

### 9. Checkout

Status: `buyuk olcude temiz`

Confirmed working:
- checkout flow is functioning without visible blocking issues
- overall order progression appears smooth

Detected problem:
- dark mode checkout header/logo harmony is not acceptable
- this remains a real visual polish issue and should be fixed

## Current High-Priority Open Items From Manual Smoke

1. Homepage rail JavaScript/Alpine breakage
2. Translation/localization coverage insufficiency
3. Language switcher dropdown readability/contrast
4. Search bar alignment issue in header
5. Homepage special occasion block remains underpowered
6. Repetitive product-image usage in homepage hero-adjacent merchandising
7. PDP locale switch can produce `404`
8. PDP buy-box delivery/preparation copy hierarchy can be improved
9. Checkout dark-mode header/logo harmony is unacceptable

## Next Manual Block To Run

Proceed next with:
- `10. Auth ve Hesap`

### 10. Auth ve Hesap

Status: `temiz`

Confirmed working:
- no new blocking or notable friction was reported in this pass
- prior utility/auth/account improvements remain acceptable
- account/auth direction is considered stable enough to continue without new intervention in this checklist round

## Next Manual Block To Run

Proceed next with:
- `11. Blog ve İçerik Yüzeyleri`

### 11. Blog ve İçerik Yüzeyleri

Status: `genel olarak calisiyor`

Confirmed working:
- blog/content surfaces are broadly functioning
- no fresh structural breakage was reported in this pass

Detected issues:
- blog volume still feels low
- translation coverage problems continue on content surfaces as well

Implication:
- this reinforces the already-open localization coverage overhaul issue
- content completeness remains a separate editorial/program issue, especially for blog depth

## Current High-Priority Open Items From Manual Smoke

1. Homepage rail JavaScript/Alpine breakage
2. Translation/localization coverage insufficiency
3. Language switcher dropdown readability/contrast
4. Search bar alignment issue in header
5. Homepage special occasion block remains underpowered
6. Repetitive product-image usage in homepage hero-adjacent merchandising
7. PDP locale switch can produce `404`
8. PDP buy-box delivery/preparation copy hierarchy can be improved
9. Checkout dark-mode header/logo harmony is unacceptable
10. Blog/content depth remains thin

## Next Manual Block To Run

Proceed next with:
- `12. Admin-Fed Public Etki`

### 12. Admin-Fed Public Etki

Status: `manuel checklistten cikarildi`

Decision:
- this section will not be completed as a user-run manual checklist block
- the user explicitly wants admin to be tested later by a dedicated agent
- that future agent is expected to:
  - enter admin directly in the local environment
  - inspect parameters, functions, and real flows live
  - exercise admin scenarios end-to-end instead of relying on a user manual pass

Implication:
- admin-fed public impact remains an open verification area
- it is deferred intentionally
- it should be covered by a dedicated admin live-test / admin scenario agent later

## Deferred Dedicated Admin Verification Scope

Future agent should cover:
- login-free local admin access assumptions in current environment
- settings pages
- resource CRUD paths
- publish/unpublish flows
- media/upload flows
- localized content entry behavior
- homepage/layout controls
- payment/delivery/settings scenarios
- storefront reflection after admin-side changes

## Next Manual Block To Run

Proceed next with:
- `13. Operasyonel Hazırlık`

### 13. Operasyonel Hazırlık

Status: `ertelendi`

Decision:
- this block is intentionally postponed for now
- no manual judgment was recorded in this pass

Reason:
- user wants to defer this operational-readiness assessment at the moment

Implication:
- operational readiness remains partially unverified at the manual level
- this should be revisited after the next storefront fix rounds and before final live deployment approval

## Manual Smoke Round Status

Current manual smoke is sufficient to proceed with targeted storefront fix planning.

Most important open storefront issues identified in this round:
1. Homepage rail JavaScript/Alpine breakage
2. Translation/localization coverage insufficiency
3. Language switcher dropdown readability/contrast
4. Search bar alignment issue in header
5. Homepage special occasion block remains underpowered
6. Repetitive product-image usage in homepage hero-adjacent merchandising
7. PDP locale switch can produce `404`
8. PDP buy-box delivery/preparation copy hierarchy can be improved
9. Checkout dark-mode header/logo harmony is unacceptable
10. Blog/content depth remains thin

Deferred areas:
- admin live scenario testing
- operational readiness verification
- mobile QA

## Local Restart Outcome

Status: `blocking`

Action taken:
- local site process on `127.0.0.1:8001` was stopped and restarted cleanly

Observed result after restart:
- server listens on port `8001`
- but homepage request now returns `500 Internal Server Error`

Confirmed technical cause:
- application is attempting to connect to MySQL at runtime
- DB connection fails with:
  - `SQLSTATE[HY000] [2002] connection actively refused`
- failure occurs in session/database boot path, so storefront cannot currently be reviewed visually after restart

Implication:
- current customer-ready fast-track scope is temporarily blocked by local runtime environment
- no further meaningful browser-based visual validation should be trusted until local DB/runtime is restored

## Final Quick Smoke Findings

Status: `micro-fix gerekli`

Detected issues:
- homepage special-occasions area still looks empty / underfilled
- visible shell copy is using ASCII-fallback/transliteration style in places, harming polish:
  - `Hesabım` appears as `Hesabim`
  - `Çıkış` appears as `cikis`
  - `Özel Günler` appears as `ozel gunler`
- header search bar and adjacent controls are still not aligned cleanly

Interpretation:
- these are highly visible customer-facing defects
- they are not deep infrastructure blockers
- they should be treated as the next highest ROI micro-fix set for the fast-track scope
