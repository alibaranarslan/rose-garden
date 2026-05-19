<?php

namespace Tests\Feature\Admin;

use App\Filament\Pages\GeneralSettings;
use App\Filament\Pages\SeoSettings;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SettingsGovernanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_general_settings_falls_back_to_automatic_hero_when_manual_product_is_invalid(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
            'is_active' => true,
        ]);

        Livewire::actingAs($admin)
            ->test(GeneralSettings::class)
            ->set('data.hero_heading.tr', 'Hero başlığı')
            ->set('data.home_intro_heading.tr', 'Koleksiyon başlığı')
            ->set('data.showcase_heading.tr', 'Seçkin vitrin başlığı')
            ->set('data.best_sellers_heading.tr', 'Çok satanlar başlığı')
            ->set('data.hero_spotlight_mode', 'manual')
            ->set('data.hero_spotlight_product_id', 999999)
            ->call('save');

        $this->assertSame('best_seller', Setting::get('storefront', 'hero_spotlight_mode'));
        $this->assertSame('', Setting::get('storefront', 'hero_spotlight_product_id'));
    }

    public function test_general_settings_rejects_invalid_contact_inputs(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
            'is_active' => true,
        ]);

        Livewire::actingAs($admin)
            ->test(GeneralSettings::class)
            ->set('data.contact_email', 'not-an-email')
            ->set('data.contact_phone', 'telefon')
            ->call('save')
            ->assertHasErrors([
                'data.contact_email',
                'data.contact_phone',
            ]);

        $this->assertNull(Setting::get('contact', 'contact_email'));
        $this->assertNull(Setting::get('contact', 'contact_phone'));
    }

    public function test_general_settings_rejects_non_http_social_links(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
            'is_active' => true,
        ]);

        Livewire::actingAs($admin)
            ->test(GeneralSettings::class)
            ->set('data.contact_email', 'info@example.test')
            ->set('data.contact_phone', '+90 416 214 00 00')
            ->set('data.social_links', [
                ['platform' => 'instagram', 'url' => 'ftp://example.test/rose'],
            ])
            ->call('save')
            ->assertHasErrors(['data.social_links.0.url']);

        $this->assertNull(Setting::get('social', 'links'));
    }

    public function test_general_settings_normalizes_contact_and_social_links(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
            'is_active' => true,
        ]);

        Livewire::actingAs($admin)
            ->test(GeneralSettings::class)
            ->set('data.contact_email', 'INFO@EXAMPLE.TEST')
            ->set('data.contact_phone', '+90   416 214 00 00')
            ->set('data.whatsapp_phone', '0 416 214 00 00')
            ->set('data.social_links', [
                ['platform' => 'instagram', 'url' => ' https://instagram.com/rosegarden '],
                ['platform' => '', 'url' => ''],
            ])
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame('info@example.test', Setting::get('contact', 'contact_email'));
        $this->assertSame('+90 416 214 00 00', Setting::get('contact', 'contact_phone'));
        $this->assertSame('0 416 214 00 00', Setting::get('contact', 'whatsapp_phone'));
        $this->assertSame(
            [['platform' => 'instagram', 'url' => 'https://instagram.com/rosegarden']],
            json_decode((string) Setting::get('social', 'links'), true)
        );
        $this->assertNotSame('', (string) Setting::get('system', 'storefront_content_version', ''));
    }

    public function test_seo_settings_normalizes_canonical_domain_before_persisting(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
            'is_active' => true,
        ]);

        Livewire::actingAs($admin)
            ->test(SeoSettings::class)
            ->set('data.canonical_domain', 'https://example.test/shop')
            ->call('save');

        $this->assertSame('https://example.test', Setting::get('seo', 'canonical_domain'));
    }

    public function test_seo_settings_rejects_invalid_quality_inputs(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
            'is_active' => true,
        ]);

        Livewire::actingAs($admin)
            ->test(SeoSettings::class)
            ->set('data.canonical_domain', 'mailto:bad@example.test')
            ->set('data.og_default_image', 'ftp://example.test/image.jpg')
            ->set('data.google_analytics_id', 'UA-OLD')
            ->set('data.google_search_console_code', '<meta name="google-site-verification" content="abc">')
            ->call('save')
            ->assertHasErrors([
                'data.canonical_domain',
                'data.og_default_image',
                'data.google_analytics_id',
                'data.google_search_console_code',
            ]);

        $this->assertNull(Setting::get('seo', 'canonical_domain'));
        $this->assertNull(Setting::get('seo', 'og_default_image'));
        $this->assertNull(Setting::get('seo', 'google_analytics_id'));
        $this->assertNull(Setting::get('seo', 'google_search_console_code'));
    }

    public function test_seo_settings_removes_manual_sitemap_lines_from_extra_robots_rules(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
            'is_active' => true,
        ]);

        Livewire::actingAs($admin)
            ->test(SeoSettings::class)
            ->set('data.robots_txt_extra', "Disallow: /tmp\nSitemap: https://wrong.test/sitemap.xml\nAllow: /public")
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame("Disallow: /tmp\nAllow: /public", Setting::get('seo', 'robots_txt_extra'));
    }
}
