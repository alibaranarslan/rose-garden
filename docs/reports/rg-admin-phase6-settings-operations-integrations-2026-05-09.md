# RG Admin Phase 6 - Settings, Operations, Integrations

Date: 2026-05-09

## Scope

- Admin settings panels for e-mail and SMS runtime behavior.
- Reports and analytics revenue integrity.
- Deployment/operations-facing admin reliability checks.

## Findings

- E-mail settings were persisted, but the active mail configuration was not refreshed immediately after save. Test sends applied the config later, but the save action itself left room for stale runtime state in the same request lifecycle.
- SMS settings were persisted, but a previously resolved `SmsService` instance could remain stale if the container had already resolved it before the save.
- Reports mixed scopes: total revenue excluded cancelled/refunded orders, while average order value, product revenue ranking, daily revenue support data, and coupon denominator could use different effective scopes.

## Changes

- `EmailSettings::save()` now applies `DynamicMailConfig` immediately after persisting admin values.
- `SmsSettings::save()` now forgets any resolved `SmsService` instance after persisting admin values.
- `ReportsAnalytics` now uses one revenue-order scope for revenue, AOV, top products, daily revenue, and coupon usage.
- Report date range resolution now guards invalid or reversed custom date inputs.
- Added admin feature tests covering e-mail runtime config refresh, SMS stale instance clearing, and cancelled-order exclusion from revenue metrics.

## Verification

- `php artisan test --filter=AdminSettingsOperationsIntegrationTest`

Result: passed, 3 tests / 13 assertions.
