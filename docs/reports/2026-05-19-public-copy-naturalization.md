# Public Copy Naturalization

Date: 2026-05-19

## Scope

Targeted public storefront copy pass. The goal was to remove self-referential wording that described the site mechanics instead of speaking to the customer. No layout, catalog data, checkout logic, admin behavior, payment, SMS, or SEO routing changes were made.

## Finding

Several visible storefront texts used implementation or internal review language such as "PDP", "akış", "blok", "otomatik", "manuel", "fallback", "karar alanı", and "ara katman". These phrases made the site sound like it was praising its own mechanics rather than inviting the customer to choose flowers, gifts, and delivery options.

## Change

- Replaced the featured showcase CTA copy "Vitrinden PDP’ye tek adımda geçiş" with natural buying language: "Detayları gör, siparişe geç".
- Reworked homepage, category discovery, special occasion, PDP, PLP, blog, FAQ, contact, checkout shell, footer, and trust badge copy where internal system wording was visible.
- Kept the functional meaning of each area intact while changing the tone to customer-facing marketing language.
- Added critical EN/KU translation entries for the new customer-facing keys to avoid locale fallback on the highest-visibility changed labels.

## Validation

- Blade syntax check for all touched public Blade files.
- `npm run build`
- `php artisan test tests\Feature\Storefront\PublicSurfaceSmokeTest.php`
- Source scan over public Blade files for the targeted internal terms.
- Playwright live mobile smoke on `https://rosegardencicekcilik.com.tr/tr` and `https://rosegardencicekcilik.com.tr/tr/urun/mor-ruya-karisik-buket`.

## Evidence

- Public Blade scan no longer finds customer-visible `PDP`, `proof`, `otomatik`, `manuel`, `fallback`, `ara katman`, `karar alan`, `rotanı`, `vitrine geç`, or `ezberletmek` in the targeted public surfaces.
- Remaining matches are either code variable names such as `fallback`, harmless customer wording such as "tek bakışta", or component props, not visible self-referential marketing copy.
- Live mobile body text scan found none of these terms on home/PDP: `PDP`, `proof`, `fallback`, `ara katman`, `otomatik derlenir`, `manuel olarak`, `karar alanı`, `Vitrinden`.
- Live console errors: none.
- Live horizontal overflow: false.
- Screenshots:
  - `%LOCALAPPDATA%\Temp\rg-public-copy-naturalization\home-featured-copy-mobile.png`
  - `%LOCALAPPDATA%\Temp\rg-public-copy-naturalization\pdp-copy-mobile.png`

## Deployment

- Deployed subtree branch: `deploy/rose-garden-main-79f5cae`
- Browser path note: in-app Browser setup timed out, so rendered validation used Playwright fallback.
