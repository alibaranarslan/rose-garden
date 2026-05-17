# RG Admin Live Data Operation Smoke

Date: 2026-05-09

## Scope

- Live browser login to the local admin panel.
- Real admin create/edit/reflection flow for catalog, coupon, page, and product data.
- Storefront verification for admin-created public records.
- Cleanup of smoke records after verification.

## Live Operations

- Created a category from `/admin/categories/create`.
- Created a coupon from `/admin/coupons/create`.
- Created a page from `/admin/pages/create`.
- Created a product with uploaded gallery image, category, variant, price, stock, and delivery note from `/admin/products/create`.
- Verified the product detail URL returned `200`.
- Verified the category URL returned `200` and listed the created product.
- Verified the page URL returned `200`.
- Edited the created product in admin and verified the storefront product detail updated.
- Cleaned created smoke records with the `smoke-test-*` / `SMOKE*` prefixes.
- Verified cleaned smoke URLs return `404`.

## Finding Fixed

Live product creation with an uploaded image failed with a `500` Livewire update response. The root cause was the Filament `FileUpload` state being passed to the `ProductImage.image_path` relationship as an array-like upload state instead of a single persisted path.

## Changes

- Added `ProductResource::normalizeUploadedImagePath()` to normalize browser upload state to one string path.
- Updated product gallery `FileUpload` dehydration to preserve an existing image path on edit and store only a single path on create.
- Normalized blank gallery `alt_text` to an empty string to avoid null insert edge cases.
- Added regression coverage for browser-style uploaded gallery state during product creation.

## Verification

- Live browser smoke for create, edit, storefront reflection, and cleanup: passed.
- `php artisan test tests\Feature\Filament\ProductResourceFormTest.php`: passed.
- `php artisan test tests\Feature\Admin`: passed, 43 tests / 330 assertions.
- `php artisan test tests\Feature\Filament\ProductResourceFormTest.php tests\Feature\Storefront\StorefrontVisibilityTest.php`: passed, 7 tests / 33 assertions.
