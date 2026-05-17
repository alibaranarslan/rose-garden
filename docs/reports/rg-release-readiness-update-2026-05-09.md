# Rose Garden - Release Readiness Update

Date: 2026-05-09

## Decision

Rose Garden is **demo/staging-ready** for customer-facing walkthroughs and local checkout smoke tests.

Rose Garden is **not production-ready yet** because live operational credentials and production runtime parity are not complete.

## Closed / Accepted Scopes

- Storefront customer-facing polish and visible Turkish literal cleanup.
- Header, homepage, mobile, cart, checkout, PDP, category, blog, and customer-facing QA rounds.
- Admin catalog operations smoke.
- Admin content hydration fixes and smoke.
- Admin settings safe persist smoke.
- Dynamic robots.txt settings fix.
- Operational integrations review.
- Staging prerequisites backfill.

## Current Operational Baseline

Verified local baseline after:

```bash
php artisan rg:seed-operational-prerequisites
```

Current counts:

```text
delivery_zones: 4 active
delivery_time_slots: 4 active
notification_templates: 12 active
paytr_configured=no
bank_configured=no
sms_enabled=no
sms_can_send=no
```

## Demo / Staging Ready Means

- Customer can browse storefront pages.
- Product/category/cart/checkout surfaces can be demonstrated.
- Checkout no longer fails because delivery zones, delivery slots, or notification templates are empty.
- Credit card payment remains safely closed until PayTR is configured.
- SMS remains safely disabled until real provider credentials are configured.
- Missing bank details no longer render as blank bank/IBAN fields on checkout success.

## Production Gates Still Open

- Real PayTR merchant ID/key/salt must be configured and tested.
- Real bank transfer details must be entered.
- Real SMTP provider must be configured and verified.
- SPF, DKIM, and DMARC must be confirmed for the sender domain.
- Real SMS provider credentials must be configured and tested with a controlled recipient.
- MySQL/MariaDB production parity must be restored and verified.
- Production `.env`, queue, scheduler, cache, session, HTTPS, and deploy verification must be run against the target host.
- Stale `public/robots.txt` must remain absent in deployment so dynamic robots settings are used.

## Recommended Next Scope

Open a dedicated **Admin Panel Full Functionalization** scope.

Purpose:

- Test every admin module through actual browser/admin flows.
- Identify broken forms, bad labels, encoding issues, missing validation, unsafe actions, hydration failures, persistence gaps, and storefront reflection gaps.
- Fix issues module by module until the admin panel is operationally reliable.

This should be treated as the last large internal quality gate before production credential work.
