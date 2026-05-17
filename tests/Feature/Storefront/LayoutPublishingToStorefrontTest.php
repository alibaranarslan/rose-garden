<?php

namespace Tests\Feature\Storefront;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Services\LayoutConfigService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class LayoutPublishingToStorefrontTest extends TestCase
{
    use RefreshDatabase;

    public function test_published_layout_settings_are_reflected_on_storefront_home(): void
    {
        File::ensureDirectoryExists(storage_path('app/public/products'));
        File::put(storage_path('app/public/products/rg-layout-hero.jpg'), 'hero-image');
        File::put(storage_path('app/public/products/rg-layout-new-old.jpg'), 'new-old-image');
        File::put(storage_path('app/public/products/rg-layout-new-latest.jpg'), 'new-latest-image');

        $categoryOne = Category::create([
            'name' => ['tr' => 'Atolye Buketleri'],
            'slug' => 'gul-buketleri',
            'description' => ['tr' => 'Birinci kategori'],
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $categoryTwo = Category::create([
            'name' => ['tr' => 'Cam Aranjmanlar'],
            'slug' => 'orkideler',
            'description' => ['tr' => 'Ikinci kategori'],
            'is_active' => true,
            'sort_order' => 2,
        ]);

        $heroProduct = Product::create([
            'name' => ['tr' => 'Hero Vitrin Urunu'],
            'slug' => 'hero-vitrin-urunu',
            'short_description' => ['tr' => 'Hero icin vitrinde'],
            'description' => ['tr' => '<p>Hero urunu.</p>'],
            'price' => 1200,
            'stock_status' => 'in_stock',
            'status' => 'active',
            'is_featured' => true,
            'is_new' => false,
            'sort_order' => 1,
        ]);
        $heroProduct->categories()->attach($categoryOne);

        ProductImage::create([
            'product_id' => $heroProduct->id,
            'image_path' => 'storage/products/rg-layout-hero.jpg',
            'alt_text' => 'Hero Vitrin Urunu',
            'is_primary' => true,
            'sort_order' => 1,
        ]);

        $olderNewProduct = Product::create([
            'name' => ['tr' => 'Yeni Urun Eski'],
            'slug' => 'yeni-urun-eski',
            'short_description' => ['tr' => 'Eski yeni urun'],
            'description' => ['tr' => '<p>Eski yeni urun.</p>'],
            'price' => 900,
            'stock_status' => 'in_stock',
            'status' => 'active',
            'is_featured' => false,
            'is_new' => true,
            'sort_order' => 2,
            'updated_at' => now()->subDay(),
            'created_at' => now()->subDay(),
        ]);
        $olderNewProduct->categories()->attach($categoryOne);

        ProductImage::create([
            'product_id' => $olderNewProduct->id,
            'image_path' => 'storage/products/rg-layout-new-old.jpg',
            'alt_text' => 'Yeni Urun Eski',
            'is_primary' => true,
            'sort_order' => 1,
        ]);

        $latestNewProduct = Product::create([
            'name' => ['tr' => 'Yeni Urun Son'],
            'slug' => 'yeni-urun-son',
            'short_description' => ['tr' => 'En guncel yeni urun'],
            'description' => ['tr' => '<p>En guncel yeni urun.</p>'],
            'price' => 950,
            'stock_status' => 'in_stock',
            'status' => 'active',
            'is_featured' => false,
            'is_new' => true,
            'sort_order' => 3,
            'updated_at' => now(),
            'created_at' => now(),
        ]);
        $latestNewProduct->categories()->attach($categoryTwo);

        ProductImage::create([
            'product_id' => $latestNewProduct->id,
            'image_path' => 'storage/products/rg-layout-new-latest.jpg',
            'alt_text' => 'Yeni Urun Son',
            'is_primary' => true,
            'sort_order' => 1,
        ]);

        /** @var LayoutConfigService $service */
        $service = app(LayoutConfigService::class);
        $draft = $service->getDraftState();
        $modules = collect($draft['modules'])->map(function (array $module) {
            if (in_array($module['key'], ['hero', 'category_showcase', 'new_arrivals'], true)) {
                return $module;
            }

            $module['is_active'] = false;

            return $module;
        })->all();

        foreach ($modules as &$module) {
            if ($module['key'] === 'hero') {
                $module['settings']['title_override']['tr'] = 'Storefront Hero Override';
                $module['settings']['subtitle_override']['tr'] = 'Hero alt baslik override';
            }

            if ($module['key'] === 'category_showcase') {
                $module['settings']['title_override']['tr'] = 'Kategori Override Basligi';
                $module['settings']['subtitle_override']['tr'] = 'Kategori override aciklamasi';
                $module['settings']['cta_enabled'] = true;
                $module['settings']['cta_label']['tr'] = 'Kategorilere Git';
                $module['settings']['cta_url'] = '/urunler';
                $module['settings']['content_limit'] = 2;
            }

            if ($module['key'] === 'new_arrivals') {
                $module['settings']['content_limit'] = 1;
            }
        }
        unset($module);

        $service->storeDraftState($modules, $draft['appearance']);
        $service->publishDraft();

        $this->get(route('home'))
            ->assertOk()
            ->assertSeeText('Storefront Hero Override')
            ->assertSeeText('Hero alt baslik override')
            ->assertSeeText('Kategori Override Basligi')
            ->assertSeeText('Kategori override aciklamasi')
            ->assertSeeText('Kategorilere Git');
    }
}
