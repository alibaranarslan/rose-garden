<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['group' => 'general', 'key' => 'site_name', 'value' => 'Rose Garden Çiçek & Çikolata'],
            ['group' => 'seo', 'key' => 'robots_txt', 'value' => "User-agent: *\nAllow: /\nSitemap: https://example.com/sitemap.xml"],
            ['group' => 'payment', 'key' => 'transfer_timeout_hours', 'value' => '72'],
            ['group' => 'loyalty', 'key' => 'earn_rate', 'value' => '0.05'],
            ['group' => 'loyalty', 'key' => 'min_use_amount', 'value' => '50'],
            ['group' => 'loyalty', 'key' => 'expiry_months', 'value' => '12'],
            ['group' => 'delivery', 'key' => 'default_fee', 'value' => '0'],
            ['group' => 'delivery', 'key' => 'free_above_amount', 'value' => '500'],
            ['group' => 'social', 'key' => 'instagram', 'value' => 'https://www.instagram.com/rosegardencicek'],
            ['group' => 'social', 'key' => 'facebook', 'value' => 'https://www.facebook.com/rosegardencicek'],
            ['group' => 'contact', 'key' => 'contact_phone', 'value' => '+90 416 214 00 00'],
            ['group' => 'contact', 'key' => 'contact_email', 'value' => 'info@rosegardencicek.com'],
            ['group' => 'contact', 'key' => 'whatsapp_phone', 'value' => '904162140000'],
        ];

        foreach ($settings as $setting) {
            DB::table('settings')->updateOrInsert(
                ['group' => $setting['group'], 'key' => $setting['key']],
                ['value' => $setting['value'], 'created_at' => now(), 'updated_at' => now()]
            );
        }
    }
}
