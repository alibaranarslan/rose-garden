# Rose Garden - Admin Panel Full Functionalization Plan

Date: 2026-05-09

## Purpose

Bring the Rose Garden admin panel from "critical paths smoke-tested" to "operationally reliable and customer-handover ready".

This scope is broader than opening pages. Every module must be checked for:

- Page access and authorization.
- Table rendering, search, filter, sort, pagination.
- Create, edit, view, delete/disable flows where applicable.
- Validation and safe failure messages.
- Persistence to database.
- Storefront or operational reflection where applicable.
- Encoding and Turkish label quality.
- Locale/translatable field behavior.
- File upload behavior.
- Dangerous action safety.
- No console/runtime/hydration errors in browser flows.

## Current Baseline

Admin tests currently pass:

```text
Tests: 18 passed, 88 assertions
```

Verified command:

```bash
php artisan test tests/Feature/Admin
```

This baseline proves selected admin safety fixes are intact, but it does not fully prove every admin module.

## Admin Surface Inventory

Resources:

- Products
- Categories
- Special Occasions
- Coupons
- Orders
- Payments
- Users
- Data Requests
- Blog Categories
- Blog Posts
- Pages
- Delivery Zones
- Delivery Time Slots
- Notification Templates
- Notification Logs
- Abandoned Carts
- Customer Events
- Keyword Dictionary
- Header Themes

Pages:

- Dashboard / Operations Desk
- General Settings
- SEO Settings
- Payment Settings
- SMS Settings
- Email Settings
- Loyalty Management
- Media Library
- Layout Studio
- Reports Analytics
- Cache Management

## Phase 1 - Admin Access And Navigation Audit

Concrete checks:

- Login as admin succeeds.
- `/admin` dashboard renders without 403/500.
- Every visible sidebar item opens.
- No mojibake such as `Ä`, `Å`, `Ã`, `?` in visible admin labels.
- No browser console errors during navigation.
- Non-admin user cannot access admin-only surfaces.

Deliverable:

- Route/module access matrix.
- Fixes for broken route, forbidden route, bad label, or encoding issues.

## Phase 2 - Catalog And Storefront Reflection

Modules:

- Products
- Categories
- Special Occasions
- Header Themes
- Layout Studio

Concrete checks:

- Create/edit product with image, gallery, price, sale price, stock, category, occasion, variants.
- Verify product page and listing reflect admin changes.
- Create/edit category and verify category listing page.
- Create/edit special occasion and verify special occasion page/home section behavior.
- Header/theme/layout changes do not break header or homepage.
- Product image removal/replacement does not leave broken thumbnails.

Primary risks:

- FileUpload path mismatch.
- Translatable field persistence mismatch.
- Layout module config shape mismatch.
- Storefront cache not invalidated after admin save.

## Phase 3 - Content And SEO Operations

Modules:

- Blog Categories
- Blog Posts
- Pages
- General Settings
- SEO Settings
- Media Library

Concrete checks:

- Blog post create/edit with rich text, featured image, related products, status.
- Public blog route reflects published content and hides draft content.
- Static page create/edit reflects public page.
- SEO title/description/canonical/robots changes persist.
- Media library does not expose broken file links.
- Storefront cache invalidates after relevant content saves.

Primary risks:

- RichEditor hydration edge cases.
- Draft/published visibility mismatch.
- SEO settings applying to wrong routes.
- Old static `public/robots.txt` returning instead of dynamic route on deployment.

## Phase 4 - Commerce Operations

Modules:

- Orders
- Payments
- Coupons
- Delivery Zones
- Delivery Time Slots
- Loyalty Management

Concrete checks:

- Admin can view/edit order status safely.
- Status update creates correct operational side effects where expected.
- Order print view works only for admin.
- Coupon create/edit works and checkout applies expected discount.
- Delivery zone/slot changes affect checkout correctly.
- Loyalty manual add/remove works and user balance updates.
- Payments table reflects order/payment records correctly.

Primary risks:

- Order status transitions causing duplicate notifications.
- Coupon limits not enforced consistently.
- Loyalty balance drift.
- Delivery setup accidentally blocking checkout.

## Phase 5 - Customer, Compliance, And Automation

Modules:

- Users
- Data Requests
- Customer Events
- Keyword Dictionary
- Abandoned Carts
- Notification Templates
- Notification Logs

Concrete checks:

- User profile/admin customer view opens and shows orders/loyalty.
- Manual loyalty adjustment from user view works.
- KVKK/data request status updates persist.
- Customer event create/edit feeds reminder logic.
- Keyword dictionary create/edit does not break event detection.
- Abandoned cart detection/reminder commands operate in safe test mode.
- Notification templates render TR/EN/KU fallback correctly.
- Notification test send is blocked safely when SMTP/SMS is not configured.

Primary risks:

- Real email/SMS dispatch accidentally triggered.
- Notification template variable mismatch.
- Locale fallback returning blank content.
- Automation commands silently failing.

## Phase 6 - Settings And Integrations

Modules:

- Payment Settings
- Email Settings
- SMS Settings
- Cache Management
- Reports Analytics

Concrete checks:

- Payment settings save PayTR and bank transfer values safely.
- Blank PayTR keeps credit card disabled.
- Bank transfer details appear in checkout/success/email only when configured.
- SMTP settings persist but test mail is not sent unless controlled recipient is set.
- SMS settings persist but test SMS is not sent unless controlled recipient and provider config are set.
- Cache clear actions work and do not delete wrong data.
- Reports export CSV downloads and includes expected columns.

Primary risks:

- Credential fields overwritten unintentionally.
- Test send action dispatching to real recipients.
- Cache action affecting unrelated data.
- Report export encoding issues.

## Execution Method

1. Start from automated coverage.
2. Add missing feature tests per module.
3. Use browser/live admin checks for forms that Filament tests cannot confidently prove.
4. Fix issues immediately, module by module.
5. Re-run focused tests after each fix.
6. Produce one report per completed phase.
7. Keep production integrations disabled unless real credentials and controlled recipients are provided.

## First Work Package

Start with **Phase 1 - Admin Access And Navigation Audit**.

Why first:

- It detects the widest class of broken admin surfaces quickly.
- It gives a clean module matrix before deeper CRUD work.
- It prevents spending time on a module whose page or route is already broken.

Acceptance criteria:

- All admin resources/pages open as admin.
- Representative unauthorized access is blocked.
- No visible admin mojibake in representative pages.
- No server errors.
- A route/module matrix is recorded.

## Current Recommendation

Proceed with Phase 1 immediately, then continue sequentially through phases 2-6.
