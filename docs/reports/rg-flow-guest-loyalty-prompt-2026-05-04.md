# Guest Loyalty Prompt

## Amaç
Guest kullanıcıyı rahatsız etmeyen, ama net çalışan bir üyelik + puan teşvik katmanı eklemek. Hedef; mevcut loyalty akışını bozmadan site girişinde küçük bir popup ve checkout içinde kısa bir teaser ile üyelik yönlendirmesi yapmak.

## Eklenen Guest Popup Davranışı
- Popup sadece guest kullanıcılarda render ediliyor.
- `layouts/app.blade.php` içine küçük, kapatılabilir bir yüzey olarak eklendi.
- `Daha sonra` aksiyonu localStorage üzerinde cooldown kaydı bırakıyor.
- Popup tekrar tekrar çıkmıyor; 7 günlük bekleme süresi kullanılıyor.
- CTA doğrudan locale-aware register rotasına gidiyor.

## Eklenen Checkout Teaser Davranışı
- Checkout wizard içinde yalnızca guest kullanıcıya gösterilen küçük bir teşvik kartı eklendi.
- Kart, checkout adım 3 içinde payment yüzeyine yakın duruyor ve checkout akışını kesmiyor.
- CTA locale-aware register rotasına gidiyor.
- Mesaj kısa tutuldu: üyelikle siparişlerden Paraçicek Puan birikeceği anlatılıyor.

## Loyalty Estimate Mantığı
- `App\Services\LoyaltyService` içine dar kapsamlı `estimateEarnedPoints(float $amount): float` helper'ı eklendi.
- Helper, mevcut `earn_rate` ayarını ve mevcut occasion multiplier mantığını kullanıyor.
- Checkout teaser için tahmin; mevcut checkout subtotal + varsa delivery fee - varsa coupon discount üzerinden hesaplanıyor.
- Bu sadece bir yaklaşık değer; gerçek earning zinciri değiştirilmedi.

## Değiştirilen Dosyalar
- `app/Services/LoyaltyService.php`
- `app/Livewire/CheckoutWizard.php`
- `resources/views/livewire/checkout-wizard.blade.php`
- `resources/views/layouts/app.blade.php`
- `resources/views/components/guest-loyalty-prompt.blade.php`
- `resources/views/components/language-switcher.blade.php`
- `resources/views/layouts/checkout.blade.php`
- `resources/views/layouts/partials/header.blade.php`
- `tests/Feature/Storefront/PublicSurfaceSmokeTest.php`
- `tests/Feature/Storefront/ProductCartCheckoutSurfaceTest.php`

## Yapılan Doğrulamalar
- `php artisan test --filter=ProductCartCheckoutSurfaceTest`
- `php artisan test --filter=PublicSurfaceSmokeTest`
- `php artisan test --filter=StorefrontCompatibilityTest`
- `npm run build`
- Browser smoke:
  - guest popup gerçek browser'da göründü
  - checkout shell temiz yüklendi
  - console/page error oluşmadı

## Kalan Riskler
- Popup cooldown'u localStorage tabanlı; kullanıcı storage temizlerse tekrar görünebilir.
- Checkout teaser yaklaşık hesap kullanıyor; toplam, delivery fee ve kupon durumuna göre gerçek earning ile küçük fark olabilir.
- Browser smoke kısa ve dar kapsamlı; cihaz ve tema kombinasyonlarının tamamını kapsamaz.

## Sonraki Güvenli Adım
- İstenirse aynı guest prompt mantığını cart veya account giriş noktalarında çok dar biçimde genişletmek.
- İstenirse teaser metnini A/B test için tek bir feature flag altında yönetecek hale getirmek.
