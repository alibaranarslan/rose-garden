# Rose Garden Admin Phase 4 - Commerce Operations Reflection

Date: 2026-05-09

## Scope

Admin panel commerce and checkout-affecting operations were tested against live storefront/checkout behavior:

- Coupon creation to cart and checkout total reflection
- Delivery zone and delivery time slot creation/edit reflection in checkout
- Payment settings reflection in checkout bank transfer details
- Admin order status edits producing operational status history

## Findings And Fixes

- Payment settings save now refreshes storefront caches and bumps the storefront content version.
- Order status changes now create `order_status_history` records through `OrderObserver`, so normal admin status edits leave an operational audit trail.
- Existing bank transfer approval action no longer needs to be the only path that writes status history; status history is centralized at the order status change layer.
- Added automated coverage proving admin-created coupons apply in cart and are persisted into checkout order totals.
- Added automated coverage proving admin-created delivery zones and time slots appear in checkout, and disappear when disabled.
- Added automated coverage proving admin payment settings normalize IBAN and render bank transfer details in checkout.
- Added automated coverage proving admin order status edits create a status-history row with the admin user as `changed_by`.

## Verification

- `php artisan test --filter=AdminCommerceOperationsReflectionTest` passed: 4 tests, 28 assertions.
- `php artisan test tests\Feature\Admin` passed: 35 tests, 261 assertions.
- `php artisan test tests\Feature\Checkout` passed: 6 tests, 42 assertions.
- `php artisan test --filter=ProductCartCheckoutSurfaceTest` passed: 9 tests, 41 assertions.
- `php artisan test --filter=OrderTrackingStatusTest` passed: 1 test, 3 assertions.

## Decision

Admin Phase 4 is complete for automated commerce/checkout reflection coverage. Next recommended phase is Admin Phase 5: customer, compliance, notifications, abandoned cart, data requests, and customer-account operational surfaces.
