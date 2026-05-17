<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['group' => 'general', 'key' => 'site_name', 'value' => 'Rose Garden Çiçek Çikolata'],
            ['group' => 'seo', 'key' => 'robots_txt', 'value' => "User-agent: *\nAllow: /\nSitemap: https://adiyamancicekcisi.com.tr/sitemap.xml"],
            ['group' => 'seo', 'key' => 'google_analytics_id', 'value' => 'G-WRTKSNRSBR'],
            ['group' => 'payment', 'key' => 'transfer_timeout_hours', 'value' => '72'],
            ['group' => 'loyalty', 'key' => 'earn_rate', 'value' => '0.05'],
            ['group' => 'loyalty', 'key' => 'min_use_amount', 'value' => '50'],
            ['group' => 'loyalty', 'key' => 'expiry_months', 'value' => '12'],
            ['group' => 'delivery', 'key' => 'default_fee', 'value' => '0'],
            ['group' => 'delivery', 'key' => 'free_above_amount', 'value' => '500'],
            ['group' => 'social', 'key' => 'instagram', 'value' => 'https://www.instagram.com/rosegardencicek'],
            ['group' => 'social', 'key' => 'facebook', 'value' => 'https://www.facebook.com/rosegardencicek'],
            ['group' => 'contact', 'key' => 'contact_phone', 'value' => '+90 416 214 00 00'],
            ['group' => 'contact', 'key' => 'contact_email', 'value' => 'info@adiyamancicekcisi.com.tr'],
            ['group' => 'contact', 'key' => 'address', 'value' => 'Yeni Sanayi Mah. 2819 Sk. No: 70/2B K.A.06 Adıyaman Merkez'],
            ['group' => 'contact', 'key' => 'tax_id', 'value' => '18343232668'],
            ['group' => 'contact', 'key' => 'whatsapp_phone', 'value' => '904162140000'],
            ['group' => 'storefront', 'key' => 'hero_heading', 'value' => ''],
            ['group' => 'storefront', 'key' => 'hero_subheading', 'value' => ''],
            ['group' => 'storefront', 'key' => 'hero_highlights', 'value' => json_encode([
                ['label' => 'Hazırlık', 'value' => 'Atölyede elde kurulan buketler ve rafine hediye akışları'],
                ['label' => 'Teslimat', 'value' => 'Adıyaman içi hızlı teslimat ve kişisel not desteği'],
                ['label' => 'Seçki', 'value' => 'Çiçek, çikolata ve hediye setlerini aynı vitrin diliyle sunar'],
            ])],
            ['group' => 'storefront', 'key' => 'home_intro_heading', 'value' => ''],
            ['group' => 'storefront', 'key' => 'home_intro_body', 'value' => ''],
            ['group' => 'storefront', 'key' => 'home_intro_points', 'value' => json_encode([
                ['title' => 'Hazırlık yaklaşımı', 'text' => 'Her sipariş atölyede o an için yeniden kurulur; seri üretim hissi taşımaz.'],
                ['title' => 'Deneyim yaklaşımı', 'text' => 'Teslimat saati, not kartı ve sunum detayları ürünün parçası gibi düşünülür.'],
            ])],
            ['group' => 'storefront', 'key' => 'showcase_heading', 'value' => ''],
            ['group' => 'storefront', 'key' => 'showcase_body', 'value' => ''],
            ['group' => 'storefront', 'key' => 'showcase_points', 'value' => json_encode([
                ['title' => 'Sunum', 'text' => 'Çiçek tonu, eşlik eden detay ve not kartı tek bir bütün gibi ele alınır.'],
                ['title' => 'Teslimat', 'text' => 'Acele hissi yaratmadan hızlı, temiz ve güven veren teslimat akışı.'],
            ])],
            ['group' => 'storefront', 'key' => 'best_sellers_heading', 'value' => ''],
            ['group' => 'storefront', 'key' => 'best_sellers_body', 'value' => ''],
            ['group' => 'storefront', 'key' => 'hero_spotlight_mode', 'value' => 'best_seller'],
            ['group' => 'storefront', 'key' => 'hero_spotlight_product_id', 'value' => null],
        ];

        foreach ($settings as $setting) {
            DB::table('settings')->updateOrInsert(
                ['group' => $setting['group'], 'key' => $setting['key']],
                ['value' => $setting['value'], 'created_at' => now(), 'updated_at' => now()]
            );
        }
    }
}
