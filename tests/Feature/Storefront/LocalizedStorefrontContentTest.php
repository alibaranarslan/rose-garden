<?php

namespace Tests\Feature\Storefront;

use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocalizedStorefrontContentTest extends TestCase
{
    use RefreshDatabase;

    public function test_storefront_home_uses_localized_general_and_module_content(): void
    {
        Setting::set('storefront', 'hero_heading', json_encode([
            'tr' => 'Yerel vitrin',
            'en' => 'Local showcase',
            'ku' => 'Vîtrîna herêmî',
        ], JSON_UNESCAPED_UNICODE));
        Setting::set('storefront', 'home_intro_heading', json_encode([
            'tr' => 'Kategori keşfi',
            'en' => 'Category discovery',
            'ku' => 'Keşfa kategoriyan',
        ], JSON_UNESCAPED_UNICODE));
        Setting::set('storefront', 'hero_highlights', json_encode([
            [
                'label' => ['tr' => 'Hazırlık', 'en' => 'Preparation', 'ku' => 'Amadekarî'],
                'value' => ['tr' => 'Atölye akışı', 'en' => 'Studio flow', 'ku' => 'Herika atolyeyê'],
            ],
        ], JSON_UNESCAPED_UNICODE));

        $this->get('/en/')
            ->assertOk()
            ->assertSee('Local showcase')
            ->assertSee('Category discovery')
            ->assertSee('Preparation')
            ->assertSee('Studio flow');

        $this->get('/ku/')
            ->assertOk()
            ->assertSee('Vîtrîna herêmî')
            ->assertSee('Keşfa kategoriyan')
            ->assertSee('Amadekarî')
            ->assertSee('Herika atolyeyê');
    }
}
