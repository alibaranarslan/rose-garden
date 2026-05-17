<?php

namespace Database\Seeders;

use App\Models\NotificationTemplate;
use Illuminate\Database\Seeder;

class NotificationTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'key' => 'order_created',
                'name' => ['tr' => 'Yeni sipariş onayı', 'en' => 'New order confirmation', 'ku' => 'Pejirandina fermana nû'],
                'channel' => 'both',
                'sms_body' => ['tr' => 'Sayın {musteri_adi}, {siparis_no} numaralı siparişiniz alınmıştır. Tutar: {siparis_tutari}', 'en' => 'Dear {musteri_adi}, your order {siparis_no} has been received. Total: {siparis_tutari}', 'ku' => '{musteri_adi}, fermana we {siparis_no} hat wergirtin. Tevayî: {siparis_tutari}'],
                'email_subject' => ['tr' => 'Siparişiniz alındı - {siparis_no}', 'en' => 'Your order has been received - {siparis_no}', 'ku' => 'Fermana we hat wergirtin - {siparis_no}'],
                'email_body' => ['tr' => '<p>Merhaba {musteri_adi},</p><p>{siparis_no} numaralı siparişiniz başarıyla alındı. Teslimat tarihi: {siparis_tarihi}. Toplam: {siparis_tutari}</p>', 'en' => '<p>Hello {musteri_adi},</p><p>Your order {siparis_no} has been received. Delivery date: {siparis_tarihi}. Total: {siparis_tutari}</p>', 'ku' => '<p>Silav {musteri_adi},</p><p>Fermana we {siparis_no} hat wergirtin. Roja radestkirinê: {siparis_tarihi}. Tevayî: {siparis_tutari}</p>'],
                'variables' => ['musteri_adi', 'siparis_no', 'siparis_tarihi', 'siparis_tutari'],
            ],
            [
                'key' => 'order_status',
                'name' => ['tr' => 'Sipariş durum değişikliği', 'en' => 'Order status update', 'ku' => 'Guherandina rewşa fermanê'],
                'channel' => 'both',
                'sms_body' => ['tr' => 'Merhaba {musteri_adi}, {siparis_no} numaralı siparişinizin durumu: {durum}. Takip: {takip_linki}', 'en' => 'Hello {musteri_adi}, your order {siparis_no} status is {durum}. Track: {takip_linki}', 'ku' => 'Silav {musteri_adi}, rewşa fermana we {siparis_no}: {durum}. Şopandin: {takip_linki}'],
                'email_subject' => ['tr' => 'Siparişiniz güncellendi - {siparis_no}', 'en' => 'Your order was updated - {siparis_no}', 'ku' => 'Fermana we hate nûkirin - {siparis_no}'],
                'email_body' => ['tr' => '<p>Merhaba {musteri_adi},</p><p>{siparis_no} numaralı siparişinizin güncel durumu: <strong>{durum}</strong></p>', 'en' => '<p>Hello {musteri_adi},</p><p>Your order {siparis_no} is now: <strong>{durum}</strong></p>', 'ku' => '<p>Silav {musteri_adi},</p><p>Rewşa fermana we {siparis_no}: <strong>{durum}</strong></p>'],
                'variables' => ['musteri_adi', 'siparis_no', 'durum', 'takip_linki'],
            ],
            [
                'key' => 'admin_new_order',
                'name' => ['tr' => 'Yeni sipariş bildirimi', 'en' => 'New order alert', 'ku' => 'Hişyariya fermana nû'],
                'channel' => 'email',
                'sms_body' => null,
                'email_subject' => ['tr' => 'Yeni sipariş: {siparis_no}', 'en' => 'New order: {siparis_no}', 'ku' => 'Fermana nû: {siparis_no}'],
                'email_body' => ['tr' => '<p>Yeni sipariş alındı. Sipariş No: {siparis_no}, Müşteri: {musteri_adi}, Tutar: {siparis_tutari}</p>', 'en' => '<p>A new order was placed. Order No: {siparis_no}, Customer: {musteri_adi}, Total: {siparis_tutari}</p>', 'ku' => '<p>Fermaneke nû hat. Jimare: {siparis_no}, Mişterî: {musteri_adi}, Tevayî: {siparis_tutari}</p>'],
                'variables' => ['siparis_no', 'musteri_adi', 'siparis_tutari'],
            ],
            [
                'key' => 'order_confirmed',
                'name' => ['tr' => 'Sipariş alındı', 'en' => 'Order received', 'ku' => 'Ferman hat wergirtin'],
                'channel' => 'sms',
                'sms_body' => ['tr' => 'Sayın {musteri_adi}, {siparis_no} numaralı siparişiniz alınmıştır.', 'en' => 'Dear {musteri_adi}, your order {siparis_no} has been received.', 'ku' => '{musteri_adi}, fermana we {siparis_no} hat wergirtin.'],
                'email_subject' => null,
                'email_body' => null,
                'variables' => ['musteri_adi', 'siparis_no'],
            ],
            [
                'key' => 'payment_received',
                'name' => ['tr' => 'Ödeme onaylandı', 'en' => 'Payment confirmed', 'ku' => 'Dayîn hate pejirandin'],
                'channel' => 'sms',
                'sms_body' => ['tr' => 'Ödemeniz onaylandı. {siparis_no} numaralı siparişiniz hazırlanıyor.', 'en' => 'Your payment was confirmed. Order {siparis_no} is being prepared.', 'ku' => 'Dayîna we hate pejirandin. Fermana {siparis_no} tê amade kirin.'],
                'email_subject' => null,
                'email_body' => null,
                'variables' => ['siparis_no'],
            ],
            [
                'key' => 'order_preparing',
                'name' => ['tr' => 'Sipariş hazırlanıyor', 'en' => 'Order is being prepared', 'ku' => 'Ferman tê amade kirin'],
                'channel' => 'sms',
                'sms_body' => ['tr' => '{siparis_no} numaralı siparişiniz hazırlanıyor.', 'en' => 'Your order {siparis_no} is being prepared.', 'ku' => 'Fermana we {siparis_no} tê amade kirin.'],
                'email_subject' => null,
                'email_body' => null,
                'variables' => ['siparis_no'],
            ],
            [
                'key' => 'order_on_the_way',
                'name' => ['tr' => 'Sipariş yolda', 'en' => 'Order on the way', 'ku' => 'Ferman li rê ye'],
                'channel' => 'sms',
                'sms_body' => ['tr' => '{musteri_adi}, siparişiniz yola çıktı.', 'en' => '{musteri_adi}, your order is on the way.', 'ku' => '{musteri_adi}, fermana we ket rê.'],
                'email_subject' => null,
                'email_body' => null,
                'variables' => ['musteri_adi'],
            ],
            [
                'key' => 'order_delivered',
                'name' => ['tr' => 'Sipariş teslim edildi', 'en' => 'Order delivered', 'ku' => 'Ferman hat radestkirin'],
                'channel' => 'sms',
                'sms_body' => ['tr' => 'Siparişiniz teslim edildi. Rose Garden\'ı tercih ettiğiniz için teşekkür ederiz.', 'en' => 'Your order was delivered. Thank you for choosing Rose Garden.', 'ku' => 'Fermana we hate radestkirin. Spas ku Rose Garden hilbijart.'],
                'email_subject' => null,
                'email_body' => null,
                'variables' => [],
            ],
            [
                'key' => 'bank_transfer_reminder',
                'name' => ['tr' => 'Havale hatırlatma', 'en' => 'Bank transfer reminder', 'ku' => 'Bîranîna veguhastina bankê'],
                'channel' => 'both',
                'sms_body' => ['tr' => '{siparis_no} numaralı siparişiniz için {toplam} havale bekleniyor. IBAN: {iban}', 'en' => 'We are waiting for bank transfer of {toplam} for order {siparis_no}. IBAN: {iban}', 'ku' => 'Ji bo fermana {siparis_no} veguhastina {toplam} tê hêvîkirin. IBAN: {iban}'],
                'email_subject' => ['tr' => 'Havale bilginiz bekleniyor - {siparis_no}', 'en' => 'Bank transfer pending - {siparis_no}', 'ku' => 'Veguhastina bankê tê hêvîkirin - {siparis_no}'],
                'email_body' => ['tr' => '<p>Merhaba {musteri_adi},</p><p>{siparis_no} numaralı siparişiniz için {toplam} tutarında havale bekliyoruz. Son tarih: {son_tarih}</p><p>Banka: {banka_adi}<br>IBAN: {iban}<br>Hesap sahibi: {hesap_sahibi}<br>Açıklama: {aciklama}</p>', 'en' => '<p>Hello {musteri_adi},</p><p>We are waiting for a bank transfer of {toplam} for order {siparis_no}. Deadline: {son_tarih}</p><p>Bank: {banka_adi}<br>IBAN: {iban}<br>Account holder: {hesap_sahibi}<br>Description: {aciklama}</p>', 'ku' => '<p>Silav {musteri_adi},</p><p>Ji bo fermana {siparis_no} veguhastina {toplam} tê hêvîkirin. Dema dawî: {son_tarih}</p><p>Bank: {banka_adi}<br>IBAN: {iban}<br>Xwediyê hesabê: {hesap_sahibi}<br>Danasîn: {aciklama}</p>'],
                'variables' => ['musteri_adi', 'siparis_no', 'toplam', 'son_tarih', 'banka_adi', 'iban', 'hesap_sahibi', 'aciklama'],
            ],
            [
                'key' => 'bank_transfer_warning',
                'name' => ['tr' => 'Havale son uyarı', 'en' => 'Bank transfer final warning', 'ku' => 'Hişyariya dawî ya veguhastina bankê'],
                'channel' => 'both',
                'sms_body' => ['tr' => '{siparis_no} numaralı siparişiniz için havale süresi dolmak üzere. Son tarih: {son_tarih}', 'en' => 'Bank transfer time for order {siparis_no} is about to expire. Deadline: {son_tarih}', 'ku' => 'Dema veguhastina bankê ji bo fermana {siparis_no} diqede. Dema dawî: {son_tarih}'],
                'email_subject' => ['tr' => 'Havale süresi dolmak üzere - {siparis_no}', 'en' => 'Bank transfer deadline approaching - {siparis_no}', 'ku' => 'Dema veguhastina bankê nêzîk e - {siparis_no}'],
                'email_body' => ['tr' => '<p>{siparis_no} numaralı siparişinizin havale süresi dolmak üzere. Ödeme ulaşmazsa siparişiniz beklemede kalabilir.</p>', 'en' => '<p>The bank transfer window for order {siparis_no} is about to expire. If payment is not received, the order may remain pending.</p>', 'ku' => '<p>Dema veguhastina bankê ji bo fermana {siparis_no} diqede. Ger dayîn negihîje, ferman dikare li bendê bimîne.</p>'],
                'variables' => ['siparis_no', 'son_tarih'],
            ],
            [
                'key' => 'abandoned_cart',
                'name' => ['tr' => 'Terk edilen sepet', 'en' => 'Abandoned cart', 'ku' => 'Sepeta hiştî'],
                'channel' => 'both',
                'sms_body' => ['tr' => 'Sepetinizde {urun_sayisi} ürün bekliyor. Siparişinizi tamamlamak için: {sepet_linki}', 'en' => '{urun_sayisi} items are waiting in your cart. Complete your order: {sepet_linki}', 'ku' => '{urun_sayisi} berhem di sepeta we de ne. Fermana xwe temam bikin: {sepet_linki}'],
                'email_subject' => ['tr' => 'Sepetinizde ürünler bekliyor', 'en' => 'Items are waiting in your cart', 'ku' => 'Berhem di sepeta we de ne'],
                'email_body' => ['tr' => '<p>Sepetinizde {urun_sayisi} ürün ve {sepet_tutari} tutarında seçim bekliyor.</p><p><a href="{sepet_linki}">Sepete dön</a></p>', 'en' => '<p>{urun_sayisi} items worth {sepet_tutari} are waiting in your cart.</p><p><a href="{sepet_linki}">Return to cart</a></p>', 'ku' => '<p>{urun_sayisi} berhem bi nirxa {sepet_tutari} di sepeta we de ne.</p><p><a href="{sepet_linki}">Vegere sepetê</a></p>'],
                'variables' => ['urun_sayisi', 'sepet_tutari', 'sepet_linki'],
            ],
            [
                'key' => 'event_reminder',
                'name' => ['tr' => 'Özel gün hatırlatma', 'en' => 'Special day reminder', 'ku' => 'Bîranîna roja taybet'],
                'channel' => 'both',
                'sms_body' => ['tr' => '{olay_adi} yaklaşıyor. {gun_kaldi} gün kaldı. Sevdikleriniz için hazırlık yapmayı unutmayın.', 'en' => '{olay_adi} is approaching. {gun_kaldi} days left. Do not forget to prepare for your loved ones.', 'ku' => '{olay_adi} nêzîk dibe. {gun_kaldi} roj mane. Ji bo kesên xwe yên hêja amade bibin.'],
                'email_subject' => ['tr' => '{olay_adi} yaklaşıyor', 'en' => '{olay_adi} is approaching', 'ku' => '{olay_adi} nêzîk dibe'],
                'email_body' => ['tr' => '<p>{olay_adi} için {gun_kaldi} gün kaldı. Rose Garden seçkileriyle hazırlığınızı tamamlayabilirsiniz.</p>', 'en' => '<p>{gun_kaldi} days left until {olay_adi}. You can complete your preparation with Rose Garden selections.</p>', 'ku' => '<p>Ji bo {olay_adi} {gun_kaldi} roj mane. Hûn dikarin bi hilbijartinên Rose Garden amade bibin.</p>'],
                'variables' => ['olay_adi', 'gun_kaldi', 'tarih'],
            ],
        ];

        foreach ($templates as $template) {
            NotificationTemplate::query()->updateOrCreate(
                ['key' => $template['key']],
                [
                    'name' => $template['name'],
                    'channel' => $template['channel'],
                    'sms_body' => $template['sms_body'] ?? null,
                    'email_subject' => $template['email_subject'] ?? null,
                    'email_body' => $template['email_body'] ?? null,
                    'variables' => $template['variables'] ?? [],
                    'is_active' => true,
                ]
            );
        }
    }
}
