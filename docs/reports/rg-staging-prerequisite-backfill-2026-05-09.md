# Rose Garden - Staging Prerequisites Backfill

Date: 2026-05-09

## Goal

Make the site safe to demo and smoke-test end to end without requiring live payment, SMTP, or SMS credentials.

## Implemented

- Added idempotent `StagingPrerequisiteSeeder`.
- Added `rg:seed-operational-prerequisites` command for local/staging baseline setup.
- Seeded 4 active delivery zones:
  - Adıyaman Merkez
  - Besni
  - Kahta
  - Gölbaşı
- Seeded 4 active delivery time slots.
- Rebuilt notification template seed data with clean Turkish text and `tr`, `en`, `ku` translations.
- Added the missing `bank_transfer_warning` notification template.
- Kept PayTR unset, SMTP unset, and SMS disabled unless real credentials are configured.
- Updated checkout success view so missing bank details do not render as blank bank/IBAN fields.
- Updated storefront checkout test expectations to assert correct Turkish characters.

## Local Baseline After Command

Command:

```bash
php artisan rg:seed-operational-prerequisites
```

Result:

```text
Active delivery zones: 4
Active delivery slots: 4
Active notification templates: 12
Operational prerequisites are ready for staging/local checkout smoke.
```

## Verification

Passed:

- `php artisan test --filter=StagingPrerequisiteSeederTest`
- `php artisan test --filter=DeliveryTimeSlotSeederTest`
- `php artisan test --filter=CheckoutFlowTest`
- `php artisan test --filter=ProductCartCheckoutSurfaceTest`
- `php artisan test --filter=GuestNotificationRoutingTest`
- `php artisan test --filter=OrderConfirmedEmailTest`
- `php artisan test --filter=DynamicMailConfigTest`
- `php artisan test --filter=SmsServiceTest`

## Remaining Gate

This does not make production integrations live. Production still requires real PayTR, SMTP, SMS, bank transfer, DNS/email authentication, and MySQL/runtime parity checks.
