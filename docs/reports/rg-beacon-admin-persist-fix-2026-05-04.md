# RG Beacon Admin Persist Fix - 2026-05-04

## Amaç

Admin panelde browser-level olarak yapilan kucuk degisikliklerin gercekten kalici yazilmasini saglamak.
Bu tur yalnizca iki blocker zincirine odaklandi:

1. Product edit icinde localized `short_description` guncellemesi
2. General settings icinde `site_name.tr` guncellemesi

Ek hedef: yazilan degisikliklerin storefront tarafina dogrulanabilir sekilde yansiyabilmesi.

## Root Cause

Birden fazla sebep vardi:

1. `GeneralSettings` validation blockade
   Aynı sayfadaki bos TR homepage heading alanlari zorunlu oldugu icin, yalnizca `site_name.tr` degistirilse bile `save()` persistence zincirine ulasamiyordu. Livewire istegi 200 donuyor, fakat component validation error state icinde kaldigi icin `Setting::set(...)` yazilari gerceklesmiyordu.

2. Product gallery `FileUpload` hydration mismatch
   `EditProduct` icinde mevcut `product_images.image_path` degeri, edit formu hydrate olurken `FileUpload` state tarafinda bos diziye (`[]`) dusuyordu. Bu da mevcut galerili urunlerde `image_path` required validation hatasi uretip `short_description` save zincirini sessizce kesiyordu.

3. Storefront cache invalidation gap
   Guest HTML cache anahtari urun ve genel ayar degisikliklerini version bazinda izlemiyordu. DB write duzelse bile homepage/PDP cache sicakken storefront yansimasi gecikebilirdi.

## Etkilenen Admin Save Zincirleri

1. `App\Filament\Pages\GeneralSettings::save`
2. `App\Filament\Resources\ProductResource\Pages\EditProduct::afterSave`
3. Guest storefront HTML cache version zinciri (`App\Http\Middleware\CachePage`)

## Yapilan Duzeltmeler

### 1. General settings persist fix

- `GeneralSettings` icindeki ilgisiz homepage TR heading alanlarindaki hard validation blockade kaldirildi.
- `save()` sonunda storefront cache katmani explicit olarak temizleniyor.
- `save()` sonunda `system.storefront_content_version` bump edilerek guest HTML cache anahtari invalid hale getiriliyor.

Sonuc:
- `site_name.tr` artik tek basina kaydedilebiliyor.
- Save sonrasi homepage yeni marka adini ayni akista gosterebiliyor.

### 2. Product persist fix

- Product gallery icindeki `image_path` `FileUpload` alani, mevcut `ProductImage` kaydi zaten path tasiyorsa yeniden upload zorlamayacak sekilde daraltildi.
- Bosalan hydrate state save sirasinda eski `image_path` degerine dehydrate ediliyor; boylece mevcut galeri yolu silinmiyor.
- `EditProduct::afterSave()` sonunda storefront cache temizligi ve content version bump eklendi.

Sonuc:
- Existing urun kaydinda sadece `short_description` guncellemesi yapmak artik DB write uretiyor.
- Mevcut galeri yolu korunuyor.
- Save sonrasi PDP ayni test akisi icinde yeni kisa aciklamayi gosterebiliyor.

### 3. Storefront yansima guvencesi

- `Setting` modeline dar iki yardimci eklendi:
  - `forgetStorefrontCaches()`
  - `bumpStorefrontContentVersion()`
- `CachePage` surface version zincirine `system.storefront_content_version` eklendi.

Bu sayede:
- `site_name`
- PDP `short_description`
- benzeri customer-facing save etkileri cache TTL beklemeden yeni sayfa anahtariyla gorulebiliyor.

## Degistirilen Dosyalar

- `C:\nwp0203\rose-garden\app\Filament\Pages\GeneralSettings.php`
- `C:\nwp0203\rose-garden\app\Filament\Resources\ProductResource.php`
- `C:\nwp0203\rose-garden\app\Filament\Resources\ProductResource\Pages\EditProduct.php`
- `C:\nwp0203\rose-garden\app\Http\Middleware\CachePage.php`
- `C:\nwp0203\rose-garden\app\Models\Setting.php`
- `C:\nwp0203\rose-garden\tests\Feature\Admin\AdminPersistFixTest.php`

## Yapilan Dogrulamalar

Calistirilan testler:

1. `php artisan test --filter=AdminPersistFixTest`
2. `php artisan test --filter=SettingsGovernanceTest`
3. `php artisan test --filter=BrandingSettingsTest`

Test icinde dogrulanan somut akilar:

1. General settings save
   - once homepage eski marka adiyla request edildi
   - sonra `site_name.tr` admin tarafindan kaydedildi
   - `settings(group=general,key=site_name)` yeni degeri yazdi
   - `system.storefront_content_version` doldu
   - ikinci homepage request yeni marka adini gosterdi

2. Product edit save
   - once PDP eski `short_description` ile request edildi
   - admin tarafinda `short_description` degistirildi
   - `products.short_description->tr` yeni degeri yazdi
   - mevcut `product_images.image_path` korunmus kaldi
   - `system.storefront_content_version` guncellendi
   - ikinci PDP request yeni `short_description` degerini gosterdi

## Canli Senaryoda Artik Neyin Yazdigi

Bu tur sonrasi teknik olarak guvence altina alinan iki canli senaryo:

1. Product edit icinde localized `short_description` degisikligi DB'ye yazar.
2. General settings icinde `site_name.tr` degisikligi `settings` tablosuna yazar.

Ek olarak storefront yansimasi icin gerekli cache invalidation zinciri de save akisina baglandi.

## Kalan Riskler

1. Bu tur browser-level yeniden manuel smoke ile tekrar oynatilmadi; dogrulama Livewire/HTTP feature test seviyesinde tamamlandi.
2. `GeneralSettings` sayfasi halen tek buyuk save yuzeyi; gelecekte baska ilgisiz validation coupling'leri tekrar ortaya cikarsa section-based save ayrimi dusunulebilir.
3. `CreateProduct` ve diger admin resource save zincirleri bu turda ayrica genisletilmedi; scope yalnizca blocker olan edit/save senaryolariyla sinirli tutuldu.

## Sonraki Guvenli Adim

1. Kisa bir browser smoke ile ayni iki senaryoyu gercek admin UI uzerinde tekrar oynat.
2. Gerekirse ayni cache/version helper'ini diger customer-facing admin save zincirlerine dar kapsamli olarak yay.
