<?php

namespace Tests\Feature\Storefront;

use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class BrandingSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_general_settings_are_reflected_on_storefront_layout(): void
    {
        Setting::set('general', 'site_name', 'Rose Garden Atelier');
        Setting::set('general', 'site_tagline', 'Butik çiçek deneyimi');
        Setting::set('general', 'logo_path', 'settings/rg-logo.png');
        Setting::set('general', 'favicon_path', 'settings/rg-favicon.png');
        Setting::set('social', 'links', json_encode([
            ['platform' => 'twitter', 'url' => 'https://x.com/rg-test'],
            ['platform' => 'youtube', 'url' => 'https://youtube.com/@rg-test'],
        ]));

        Cache::flush();

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Rose Garden Atelier')
            ->assertSee('Butik çiçek deneyimi')
            ->assertSee('/storage/settings/rg-logo.png', false)
            ->assertSee('/storage/settings/rg-favicon.png', false)
            ->assertSee('https://x.com/rg-test', false);
    }

    public function test_storefront_branding_falls_back_to_packaged_assets_when_settings_are_empty(): void
    {
        Cache::flush();

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Rose Garden Çiçek Çikolata', false)
            ->assertSee('<title>Rose Garden Çiçek Çikolata | Rose Garden</title>', false)
            ->assertSee('images/branding/rg-logo-dark.png', false)
            ->assertSee('images/branding/favicon.png', false);
    }

    public function test_localized_branding_settings_are_resolved_for_selected_locale(): void
    {
        Setting::set('general', 'site_name', json_encode([
            'tr' => 'Rose Garden Atelier',
            'en' => 'Rose Garden Atelier',
            'ku' => 'Atolyeya Rose Garden',
        ], JSON_UNESCAPED_UNICODE));
        Setting::set('general', 'site_tagline', json_encode([
            'tr' => 'Butik çiçek deneyimi',
            'en' => 'Boutique floral experience',
            'ku' => 'Tecrubeya butîk a kulîlkan',
        ], JSON_UNESCAPED_UNICODE));

        Cache::flush();

        $this->get('/en/')
            ->assertOk()
            ->assertSee('Rose Garden Atelier')
            ->assertSee('Boutique floral experience');
    }

    public function test_canonical_domain_is_normalized_in_meta_tags(): void
    {
        Setting::set('seo', 'canonical_domain', 'https://example.test/shop');

        Cache::flush();

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('rel="canonical" href="https://example.test"', false)
            ->assertDontSee('/shop', false);
    }
}
