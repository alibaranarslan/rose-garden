<?php

namespace Tests\Feature\Storefront;

use App\Models\Category;
use App\Models\Product;
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
            'ku' => 'Vitrina heremi',
        ], JSON_UNESCAPED_UNICODE));
        Setting::set('storefront', 'home_intro_heading', json_encode([
            'tr' => 'Kategori kesfi',
            'en' => 'Category discovery',
            'ku' => 'Kesfa kategoriyan',
        ], JSON_UNESCAPED_UNICODE));
        Setting::set('storefront', 'hero_highlights', json_encode([
            [
                'label' => ['tr' => 'Hazirlik', 'en' => 'Preparation', 'ku' => 'Amadekari'],
                'value' => ['tr' => 'Atolye akisi', 'en' => 'Studio flow', 'ku' => 'Herika atolye'],
            ],
        ], JSON_UNESCAPED_UNICODE));

        $category = Category::create([
            'name' => ['tr' => 'Gul Buketleri', 'en' => 'Rose Bouquets', 'ku' => 'Destegulen Gulan'],
            'slug' => 'gul-buketleri',
            'description' => ['tr' => 'Gul secimi', 'en' => 'Rose selection', 'ku' => 'Hilbijartina gulan'],
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $product = Product::create([
            'name' => ['tr' => 'Yerel Buket', 'en' => 'Local Bouquet', 'ku' => 'Destegula Heremi'],
            'slug' => 'yerel-buket',
            'short_description' => ['tr' => 'Yerel hazirlanir', 'en' => 'Prepared locally', 'ku' => 'Li hereme te amadekirin'],
            'description' => ['tr' => '<p>Yerel urun.</p>', 'en' => '<p>Local product.</p>', 'ku' => '<p>Berhema heremi.</p>'],
            'price' => 890,
            'stock_status' => 'in_stock',
            'status' => 'active',
            'is_featured' => true,
            'is_new' => true,
            'sort_order' => 1,
        ]);
        $product->categories()->attach($category);

        $this->get('/en/')
            ->assertOk()
            ->assertSee('Local showcase')
            ->assertSee('Category discovery')
            ->assertSee('Preparation')
            ->assertSee('Studio flow');

        $this->get('/ku/')
            ->assertOk()
            ->assertSee('Vitrina heremi')
            ->assertSee('Kesfa kategoriyan')
            ->assertSee('Amadekari')
            ->assertSee('Herika atolye');
    }
}
