<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NotificationTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            ['key' => 'order_confirmed', 'name' => 'Siparis Alindi', 'channel' => 'sms', 'sms_body' => 'Sayin {musteri_adi}, {siparis_no} numarali siparisiniz alinmistir.'],
            ['key' => 'payment_received', 'name' => 'Odeme Onaylandi', 'channel' => 'sms', 'sms_body' => 'Odemeniz onaylanmistir. Siparisiniz hazirlaniyor.'],
            ['key' => 'order_preparing', 'name' => 'Siparis Hazirlaniyor', 'channel' => 'sms', 'sms_body' => 'Siparisiniz hazirlaniyor.'],
            ['key' => 'order_on_the_way', 'name' => 'Siparis Yolda', 'channel' => 'sms', 'sms_body' => '{alici_adi} adina siparisiniz yola cikmistir.'],
            ['key' => 'order_delivered', 'name' => 'Siparis Teslim Edildi', 'channel' => 'sms', 'sms_body' => 'Siparisiniz teslim edilmistir. Rose Garden\'i tercih ettiginiz icin tesekkur ederiz.'],
            ['key' => 'bank_transfer_reminder', 'name' => 'Havale Hatirlatma', 'channel' => 'sms', 'sms_body' => 'Havale bekleniyor. Son odeme: {son_tarih}. IBAN: {iban}'],
            ['key' => 'abandoned_cart', 'name' => 'Terk Edilen Sepet', 'channel' => 'sms', 'sms_body' => 'Sepetinizde urunler bekliyor! Siparisinizi tamamlayin: {link}'],
            ['key' => 'event_reminder', 'name' => 'Etkinlik Hatirlatma', 'channel' => 'sms', 'sms_body' => '{olay_turu} yaklasiyor! {alici_adi} icin guzel bir surpriz hazirlayin.'],
        ];

        foreach ($templates as $template) {
            DB::table('notification_templates')->updateOrInsert(
                ['key' => $template['key']],
                [
                    'name' => $template['name'],
                    'channel' => $template['channel'],
                    'sms_body' => $template['sms_body'],
                    'email_subject' => null,
                    'email_body' => null,
                    'variables' => null,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
