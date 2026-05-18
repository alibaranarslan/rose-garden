# Mobile Header Theme Toggle

Date: 2026-05-19

## Scope

Targeted mobile header polish. The night/dark mode feature already existed, but on mobile it was only discoverable inside the expanded menu. This pass makes the feature visible in the first header row without changing navigation, cart, search, checkout, admin, or catalog behavior.

## Change

- Added a dedicated mobile theme toggle next to the hamburger menu in the first header row.
- Kept the existing theme toggle inside the mobile menu for users who open the menu.
- Matched the new mobile toggle size, border, surface, shadow, and dark-mode contrast with the cart and menu controls.
- Cleaned the theme toggle accessible labels and added EN/KU translation entries for the new proper Turkish keys.

## Validation

- `php -l resources\views\layouts\partials\header.blade.php`
- `php -l resources\views\components\theme-toggle.blade.php`
- `npm run build`
- `php artisan test tests\Feature\Storefront\PublicSurfaceSmokeTest.php`

## Evidence

- Pending live smoke after deployment:
  - Mobile header shows cart, logo, visible theme toggle, and menu in the first row.
  - Tapping the visible theme toggle switches `html.dark`.
  - Header has no horizontal overflow.
  - Console errors are absent.
