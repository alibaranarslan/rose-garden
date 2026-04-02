<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NotificationTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            // Keys used by OrderObserver (must match exactly)
            ['key' => 'order_created',   'name' => 'Yeni Siparis Onay (Musteri)', 'channel' => 'both',
                'sms_body'      => 'Sayin {musteri_adi}, {siparis_no} numarali siparisiniz alinmistir. Tutar: {siparis_tutari}',
                'email_subject' => 'Siparisiz Alindi — {siparis_no}',
                'email_body'    => '<p>Merhaba {musteri_adi},</p><p>{siparis_no} numarali siparisiz basariyla alindi. Teslimat tarihi: {siparis_tarihi}. Toplam: {siparis_tutari}</p>',
            ],
            ['key' => 'order_status',    'name' => 'Siparis Durum Degisikligi', 'channel' => 'both',
                'sms_body'      => 'Merhaba {musteri_adi}, #{siparis_no} nolu siparisiniz {durum} durumunda. Takip: {takip_linki}',
                'email_subject' => 'Siparisiz Guncellendi — {siparis_no}',
                'email_body'    => '<p>Merhaba {musteri_adi},</p><p>{siparis_no} numarali siparisizin durumu: <strong>{durum}</strong></p>',
            ],
            ['key' => 'admin_new_order', 'name' => 'Yeni Siparis Bildirimi (Admin)', 'channel' => 'mail',
                'sms_body'      => null,
                'email_subject' => 'Yeni Siparis: {siparis_no}',
                'email_body'    => '<p>Yeni siparis alindi. Siparis No: {siparis_no}, Musteri: {musteri_adi}, Tutar: {siparis_tutari}</p>',
            ],
            // Legacy keys kept for backward compatibility
            ['key' => 'order_confirmed', 'name' => 'Siparis Alindi (Eski)', 'channel' => 'sms',
                'sms_body' => 'Sayin {musteri_adi}, {siparis_no} numarali siparisiniz alinmistir.',
                'email_subject' => null, 'email_body' => null,
            ],
            ['key' => 'payment_received', 'name' => 'Odeme Onaylandi', 'channel' => 'sms',
                'sms_body' => 'Odemeniz onaylanmistir. Siparis {siparis_no} hazirlaniyor.',
                'email_subject' => null, 'email_body' => null,
            ],
            ['key' => 'order_preparing', 'name' => 'Siparis Hazirlaniyor', 'channel' => 'sms',
                'sms_body' => 'Siparis {siparis_no} hazirlaniyor.',
                'email_subject' => null, 'email_body' => null,
            ],
            ['key' => 'order_on_the_way', 'name' => 'Siparis Yolda', 'channel' => 'sms',
                'sms_body' => '{musteri_adi} siparisiniz yola cikmistir.',
                'email_subject' => null, 'email_body' => null,
            ],
            ['key' => 'order_delivered', 'name' => 'Siparis Teslim Edildi', 'channel' => 'sms',
                'sms_body' => 'Siparisiniz teslim edilmistir. Rose Garden\'i tercih ettiginiz icin tesekkur ederiz.',
                'email_subject' => null, 'email_body' => null,
            ],
            ['key' => 'bank_transfer_reminder', 'name' => 'Havale Hatirlatma', 'channel' => 'sms',
                'sms_body' => 'Havale bekleniyor. Siparis: {siparis_no}. IBAN bilgisi icin bize ulasabilirsiniz.',
                'email_subject' => null, 'email_body' => null,
            ],
            ['key' => 'abandoned_cart', 'name' => 'Terk Edilen Sepet', 'channel' => 'sms',
                'sms_body' => 'Sepetinizde urunler bekliyor! Siparisinizi tamamlayin.',
                'email_subject' => null, 'email_body' => null,
            ],
            ['key' => 'event_reminder', 'name' => 'Etkinlik Hatirlatma', 'channel' => 'sms',
                'sms_body' => 'Ozel bir gun yaklasiyor! Sevdikleriniz icin hazirlik yapmayi unutmayin.',
                'email_subject' => null, 'email_body' => null,
            ],
        ];

        foreach ($templates as $template) {
            DB::table('notification_templates')->updateOrInsert(
                ['key' => $template['key']],
                [
                    'name'          => $template['name'],
                    'channel'       => $template['channel'],
                    'sms_body'      => $template['sms_body'] ?? null,
                    'email_subject' => $template['email_subject'] ?? null,
                    'email_body'    => $template['email_body'] ?? null,
                    'variables'     => null,
                    'is_active'     => true,
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]
            );
        }
    }
}
