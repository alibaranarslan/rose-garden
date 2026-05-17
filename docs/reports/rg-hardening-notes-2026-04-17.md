# RG Hardening Notes

Date: 2026-04-17
Workspace: `C:\nwp0203\rose-garden`
Scope: storefront hardening only; no storefront UI restore/redesign work

## Locale ownership

- `tr/en/ku` locale seti `App\Support\StorefrontLocale` içinde merkezileştirildi.
- `SetLocale`, `LocalizedSettings`, language switcher, SEO alternate/canonical üretimi ve route locale constraint artık aynı locale kaynağını kullanıyor.
- Locale-aware URL üretimi için `StorefrontLocale::route()` ve `StorefrontLocale::currentRequestUrl()` eklendi.
- Bu sayede locale alias URL üretimi view katmanında elle regex/path kurmaktan çıkarıldı.

## Route model

- Storefront route kaydı canonical named group ve locale alias group için tek paylaşılan tanımdan üretiliyor.
- Canonical ownership modeli korundu:
  - named route owner non-prefixed grup
  - locale-prefixed grup public alias
- Alias grup ile canonical grup arasındaki middleware/route drift riski azaltıldı.
- Bilinçli olarak ertelenen nokta:
  - Laravel’in global `route()` helper semantiği değiştirilmedi
  - locale-prefixed alias route’lar named-owner yapılmadı
  - locale-aware prefixed URL ihtiyacı için merkezi helper kullanıldı

## Search scope

- Search artık `name` ve `short_description` alanlarında `tr/en/ku` kapsamını birlikte tarıyor.
- Sorgu üretimi driver-aware tutuldu:
  - sqlite
  - pgsql
  - mysql/json-extract default
- Davranış basit tutuldu; full-text veya ağır search altyapısına gidilmedi.

## Order numbering

- `Order` modeli artık günlük sıra için `count()+1` kullanmıyor.
- Yeni üretim son mevcut günlük sipariş numarasına göre ilerliyor.
- Checkout akışı `Order::createWithGeneratedNumber()` üzerinden unique index tabanlı collision-retry kullanıyor.
- Sipariş numarası formatı korundu:
  - `RG-YYYYMMDD-####`

## Kalan borçlar

- Generic Laravel `route()` helper storefront locale prefix owner hâline getirilmedi; merkezi helper ile güvenli kullanım yolu açıldı.
- Duplicate route modeli tamamen fiziksel olarak tek route grubuna indirilmedi; riskli davranış değişikliği yerine ortak tanım üzerinden stabilize edildi.
- Locale-aware link üretiminin tüm repo boyunca helper’a taşınması ayrı bir cleanup turu olabilir.
