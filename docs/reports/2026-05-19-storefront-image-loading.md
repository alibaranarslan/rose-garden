# Storefront Image Loading Optimization

Date: 2026-05-19

## Scope

Targeted public-surface performance fix for slow storefront image loading. No redesign, no product data mutation, and no unrelated ADH changes.

## Root Cause

- Several storefront product assets were uploaded as very large original PNG files, including 17-26 MB images.
- Homepage product rails were eager-loading every card image, so below-the-fold images competed with first-view assets.
- Several homepage sections rendered raw `/storage/products/*` URLs instead of size-appropriate derivatives.

## Changes

- Added `php artisan storefront:optimize-images` to generate WebP derivatives for storage-backed product, category, and blog images.
- Added `StorefrontImage::optimizedImgSrc()` and `StorefrontImage::optimizedImgSrcset()` helpers.
- Updated hero, product cards, product rails, category cards, mini product cards, selected showcase, and special-occasion spotlight to prefer optimized WebP variants when present.
- Limited eager product-rail loading to the first two cards.

## Local Evidence

- Before targeted section coverage, mobile homepage image payload included originals such as:
  - `orgulu-patos.png`: 20,918,893 bytes
  - `guzmanya-saksi.png`: 17,949,167 bytes
  - `2li-beyaz-orkide.png`: 16,827,834 bytes
- After section coverage, mobile homepage total image payload dropped to 1,965,988 bytes in local Playwright smoke.
- The largest remaining image responses were branding PNGs; product/catalog images were served from `/storage/optimized/*.webp`.
- Local Playwright smoke reported no console errors and no horizontal overflow.

## Validation

- `php artisan storefront:optimize-images --quality=78`
- `npm run build`
- `php artisan test tests\Feature\Storefront\PublicSurfaceSmokeTest.php`

