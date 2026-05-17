<?php

namespace Tests\Feature\Storefront;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\SpecialOccasion;
use App\Services\HomeModuleDataService;
use App\Services\LayoutConfigService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class StorefrontVisibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_storefront_ready_scope_and_pages_exclude_remote_only_products(): void
    {
        $category = Category::create([
            'name' => ['tr' => 'Cicek Buketleri'],
            'slug' => 'cicek-buketleri',
            'description' => ['tr' => 'Lansman katalogu'],
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $localProduct = Product::create([
            'name' => ['tr' => 'Yerel Buket', 'en' => 'Local Bouquet'],
            'slug' => 'yerel-buket',
            'short_description' => ['tr' => 'Yerel gorselli urun'],
            'description' => ['tr' => '<p>Yerel urun.</p>'],
            'price' => 890,
            'stock_status' => 'in_stock',
            'status' => 'active',
            'is_featured' => true,
            'is_new' => true,
            'delivery_note' => ['tr' => 'Ayni gun teslimat.'],
            'product_highlights' => [
                'tr' => [
                    ['icon' => 'sparkles', 'title' => 'Butik Hazirlik', 'body' => 'Atolyede hazirlanir.', 'sort_order' => 1],
                ],
            ],
            'sort_order' => 1,
        ]);
        $localProduct->categories()->attach($category);

        File::ensureDirectoryExists(storage_path('app/public/products'));
        File::put(storage_path('app/public/products/yerel-buket.jpg'), 'local-image');

        ProductImage::create([
            'product_id' => $localProduct->id,
            'image_path' => 'storage/products/yerel-buket.jpg',
            'alt_text' => 'Yerel Buket',
            'is_primary' => true,
            'sort_order' => 1,
        ]);

        $remoteProduct = Product::create([
            'name' => ['tr' => 'Remote Buket', 'en' => 'Remote Bouquet'],
            'slug' => 'remote-buket',
            'short_description' => ['tr' => 'Remote gorsel'],
            'description' => ['tr' => '<p>Remote urun.</p>'],
            'price' => 790,
            'stock_status' => 'in_stock',
            'status' => 'active',
            'is_featured' => true,
            'is_new' => true,
            'sort_order' => 2,
        ]);
        $remoteProduct->categories()->attach($category);

        ProductImage::create([
            'product_id' => $remoteProduct->id,
            'image_path' => 'https://images.unsplash.com/photo-12345',
            'alt_text' => 'Remote image',
            'is_primary' => true,
            'sort_order' => 1,
        ]);

        $this->assertSame(['yerel-buket'], Product::storefrontReady()->pluck('slug')->all());

        $this->get(route('products.index'))
            ->assertOk()
            ->assertSeeText('Yerel Buket')
            ->assertDontSeeText('Remote Buket')
            ->assertDontSee('images.unsplash.com', false);

        $this->get(route('products.category', ['slug' => $category->slug]))
            ->assertOk()
            ->assertSeeText('Yerel Buket')
            ->assertDontSeeText('Remote Buket')
            ->assertDontSee('images.unsplash.com', false);

        $this->get(route('home'))
            ->assertOk()
            ->assertSeeText('Yerel Buket')
            ->assertDontSeeText('Remote Buket')
            ->assertDontSee('images.unsplash.com', false);
    }

    public function test_special_occasion_pages_use_real_product_visuals_when_products_are_attached(): void
    {
        $category = Category::create([
            'name' => ['tr' => 'Çiçek Buketleri'],
            'slug' => 'cicek-buketleri',
            'description' => ['tr' => 'Lansman katalogu'],
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $occasion = SpecialOccasion::create([
            'name' => ['tr' => 'Anneler Günü'],
            'slug' => 'anneler-gunu',
            'date_month' => 5,
            'date_day' => 11,
            'category_id' => $category->id,
            'loyalty_multiplier' => 1.5,
            'is_active' => true,
        ]);

        $product = Product::create([
            'name' => ['tr' => 'Pembe Zambak Buketi', 'en' => 'Pink Lily Bouquet'],
            'slug' => 'pembe-zambak-buketi',
            'short_description' => ['tr' => 'Yerel görselli ürün'],
            'description' => ['tr' => '<p>Yerel ürün.</p>'],
            'price' => 1390,
            'stock_status' => 'in_stock',
            'status' => 'active',
            'is_featured' => true,
            'is_new' => true,
            'sort_order' => 1,
        ]);
        $product->categories()->attach($category);
        $product->specialOccasions()->attach($occasion);

        File::ensureDirectoryExists(storage_path('app/public/products'));
        File::put(storage_path('app/public/products/pembe-zambak-buketi.jpg'), 'local-image');

        ProductImage::create([
            'product_id' => $product->id,
            'image_path' => 'storage/products/pembe-zambak-buketi.jpg',
            'alt_text' => 'Pembe Zambak Buketi',
            'is_primary' => true,
            'sort_order' => 1,
        ]);

        $this->get(route('special-occasions.index'))
            ->assertOk()
            ->assertSee('storage/products/pembe-zambak-buketi.jpg', false);

        $this->get(route('special-occasions.show', ['slug' => $occasion->slug]))
            ->assertOk()
            ->assertSee('storage/products/pembe-zambak-buketi.jpg', false);
    }

    public function test_home_modules_and_related_products_do_not_require_secondary_image_rows(): void
    {
        $category = Category::create([
            'name' => ['tr' => 'Atolye Seckileri'],
            'slug' => 'gul-buketleri',
            'description' => ['tr' => 'Storefront test kategorisi'],
            'is_active' => true,
            'sort_order' => 1,
        ]);

        File::ensureDirectoryExists(storage_path('app/public/products'));
        File::put(storage_path('app/public/products/ana-urun.jpg'), 'primary-image');

        $primaryProduct = Product::create([
            'name' => ['tr' => 'Ana Urun', 'en' => 'Primary Product'],
            'slug' => 'ana-urun',
            'short_description' => ['tr' => 'Hero ve PDP icin ana urun'],
            'description' => ['tr' => '<p>Ana urun.</p>'],
            'price' => 990,
            'stock_status' => 'in_stock',
            'status' => 'active',
            'is_featured' => true,
            'is_new' => false,
            'sort_order' => 1,
        ]);
        $primaryProduct->categories()->attach($category);

        ProductImage::create([
            'product_id' => $primaryProduct->id,
            'image_path' => 'storage/products/ana-urun.jpg',
            'alt_text' => 'Ana Urun',
            'is_primary' => true,
            'sort_order' => 1,
        ]);

        $secondaryProduct = Product::create([
            'name' => ['tr' => 'Ikinci Urun', 'en' => 'Secondary Product'],
            'slug' => 'ikinci-urun',
            'short_description' => ['tr' => 'Image row olmadan da gorunur'],
            'description' => ['tr' => '<p>Ikinci urun.</p>'],
            'price' => 880,
            'stock_status' => 'in_stock',
            'status' => 'active',
            'is_featured' => false,
            'is_new' => true,
            'sort_order' => 2,
        ]);
        $secondaryProduct->categories()->attach($category);

        /** @var LayoutConfigService $layoutService */
        $layoutService = app(LayoutConfigService::class);
        $draft = $layoutService->getDraftState();
        $modules = collect($draft['modules'])->map(function (array $module) {
            if (in_array($module['key'], ['hero', 'category_showcase', 'new_arrivals'], true)) {
                return $module;
            }

            $module['is_active'] = false;

            return $module;
        })->all();

        foreach ($modules as &$module) {
            if ($module['key'] === 'new_arrivals') {
                $module['settings']['content_limit'] = 8;
            }
        }
        unset($module);

        $layoutService->storeDraftState($modules, $draft['appearance']);
        $layoutService->publishDraft();

        $layoutState = $layoutService->getPublishedState();
        $homeData = app(HomeModuleDataService::class)->collect($layoutState);

        $visibleOnHome = collect($homeData['newProducts'] ?? [])
            ->contains(fn (Product $product) => $product->is($secondaryProduct))
            || (($homeData['featuredShowcase'] ?? null) instanceof Product && $homeData['featuredShowcase']->is($secondaryProduct));

        $this->assertTrue($visibleOnHome);
        $this->assertTrue(collect($homeData['categories'] ?? [])->contains(fn ($item) => (int) $item->id === (int) $category->id));

        $this->get(route('home'))
            ->assertOk()
            ->assertSeeText('Ikinci Urun');

        $this->get(route('products.show', ['slug' => $primaryProduct->slug]))
            ->assertOk()
            ->assertSeeText('Ikinci Urun');
    }

    public function test_product_lightbox_uses_active_image_fallback_before_opening(): void
    {
        $category = Category::create([
            'name' => ['tr' => 'PDP Test'],
            'slug' => 'pdp-test',
            'description' => ['tr' => 'PDP test kategorisi'],
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $product = Product::create([
            'name' => ['tr' => 'Lightbox Test Urunu'],
            'slug' => 'lightbox-test-urunu',
            'short_description' => ['tr' => 'Lightbox test'],
            'description' => ['tr' => '<p>Lightbox test urunu.</p>'],
            'price' => 500,
            'stock_status' => 'in_stock',
            'status' => 'active',
            'sort_order' => 1,
        ]);
        $product->categories()->attach($category);

        $this->get(route('products.show', ['slug' => $product->slug]))
            ->assertOk()
            ->assertSee(':src="lightboxImage || activeImage"', false)
            ->assertDontSee('<img :src="lightboxImage" alt=', false);
    }
}
