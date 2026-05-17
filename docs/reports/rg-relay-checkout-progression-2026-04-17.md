# RG Relay Checkout Progression

Date: 2026-04-17
Workspace: `C:\nwp0203\rose-garden`
Scope: checkout entry and wizard only

## Amaç

- `App\Livewire\CheckoutWizard` üzerindeki checkout step progression akışını güvenli hale getirmek.
- Validation bloklarını görünür, alan-bazlı ve operasyonel hale getirmek.
- Eksik delivery zone, time slot, agreement veya payment config durumlarını yüzeye açıkça yazmak.
- `/odeme` shell, `checkout-wizard` runtime ve `CheckoutController` continuation akışını bozmadan checkout yüzeyini sessiz kilitlenmeden çıkarmak.

## Root cause

- Tek bir hata yoktu; problem birden fazla sebepten oluşuyordu.
- Birincil bloklayıcı, aktif teslimat bölgesi veya aktif saat aralığı eksik olduğunda wizard’ın bunu açıkça teşhis etmemesiydi.
- İkinci bloklayıcı, validation’ın teknik olarak çalışmasına rağmen kullanıcıya yeterince anlaşılır özet vermemesiydi; hata alanları mevcuttu ama progression neden durduğunu üst seviyede net anlatmıyordu.
- Üçüncü katkı faktörü, step alanlarında `wire:model.defer` kullanımıydı; bu kurgu doğrulanmış olsa da hydration hissini zayıflatıp “yazdım ama geçmedi” algısını artırabiliyordu. Bu yüzden kritik alanlar `blur/live` sync’e alındı.

## Progression neden bloklanıyordu

- Step 1 ve step 2’de kullanıcı valid bilgi girse bile yüzeyde iki durum ayrışmıyordu:
  - alan validation hatası
  - checkout config eksikliği
- Delivery config eksikse kullanıcı yalnızca genel bir uyarı görüyordu; neden ilerleyemediği tek bakışta anlaşılmıyordu.
- Payment tarafında card availability net değildi; PayTR yokken kart seçeneği hala varsayılan gibi davranabiliyordu.
- Bu nedenle wizard teknik olarak çalışsa bile yüzey sessiz ve kararsız görünüyordu.

## Düzeltilen noktalar

- Step 1/2/3 alanları `wire:model.defer` yerine daha güvenli `blur/live` sync ile bağlandı.
- Step 2 progression için aktif delivery zone ve active time slot yoksa doğrudan görünür `deliveryConfiguration` blocker eklendi.
- `createOrder()` artık eksik delivery config veya silinmiş/inactive zone-slot durumunda sessiz crash yerine açık hata veriyor.
- Delivery validation, render edilen aktif options ile hizalandı:
  - `delivery_zones`
  - `delivery_time_slots`
- Validation özeti step bazında okunur hale getirildi.
- Card payment unavailable durumunda UI açıklayıcı mesaj veriyor, kart seçeneği kapatılıyor ve banka transferi varsayılan fallback olarak davranıyor.
- Step butonlarına loading/disabled davranışı eklendi, böylece çift tıklama veya “hiçbir şey olmuyor” hissi azalıyor.

## Değiştirilen dosyalar

- `app/Livewire/CheckoutWizard.php`
- `resources/views/livewire/checkout-wizard.blade.php`
- `tests/Feature/Checkout/CheckoutFlowTest.php`

## Yapılan doğrulamalar

- `php artisan test --filter=CheckoutFlowTest`
- `php artisan test --filter=ProductCartCheckoutSurfaceTest`
- Sonuç: checkout ve storefront yüzey testleri geçti.
- Özellikle doğrulananlar:
  - step 1 -> step 2 geçişi
  - step 2 -> step 3 geçişi
  - alan bazlı validation özeti
  - delivery config blocker görünürlüğü
  - bank transfer flow’un kırılmaması

## Kalan riskler

- Gerçek ortamda delivery zone / time slot seed durumu değişirse step 2 hala config blocker gösterecek; bu doğru davranış, ama operasyonel veri girişi yine gerekecek.
- PayTR ve banka transfer bilgileri settings’e bağlı olduğu için production config drift’i checkout davranışını etkileyebilir.
- Browser-level manuel smoke testi bu turda yapılmadı; doğrulama test ve component render seviyesinde kaldı.

## Sonraki güvenli adım

- `/odeme` yüzeyinde kısa bir manuel smoke test yap:
  - delivery config var senaryo
  - delivery config yok senaryo
  - kart kapalı / bank transfer fallback senaryo
- Sonra gerekirse checkout copy’sini operasyon ekibi için daha da kısa hale getir, ama payment core logic’e dokunma.
