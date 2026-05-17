<?php

namespace Tests\Feature\Console;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\SpecialOccasion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class ImportIncomingProductsCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_import_syncs_launch_catalog_and_applies_default_highlights(): void
    {
        $category = Category::create([
            'name' => ['tr' => 'Cicek Buketleri'],
            'slug' => 'cicek-buketleri',
            'description' => ['tr' => ''],
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $legacy = Product::create([
            'name' => ['tr' => 'Eski Urun', 'en' => 'Old Product', 'ku' => 'Old Product'],
            'slug' => 'eski-urun',
            'short_description' => ['tr' => 'x', 'en' => 'x', 'ku' => 'x'],
            'description' => ['tr' => '<p>x</p>', 'en' => '<p>x</p>', 'ku' => '<p>x</p>'],
            'sku' => 'RG-ESKI-0001',
            'price' => 10,
            'stock_status' => 'in_stock',
            'status' => 'active',
            'is_featured' => false,
            'is_new' => false,
            'delivery_note' => ['tr' => '', 'en' => '', 'ku' => ''],
            'meta_title' => ['tr' => 't', 'en' => 't', 'ku' => 't'],
            'meta_description' => ['tr' => 'd', 'en' => 'd', 'ku' => 'd'],
            'sort_order' => 1,
        ]);
        $legacy->categories()->attach($category);

        $existingImported = Product::create([
            'name' => ['tr' => 'Guncellenecek Urun', 'en' => 'Replace Me', 'ku' => 'Replace Me'],
            'slug' => 'import-test-urun',
            'short_description' => ['tr' => 'eski', 'en' => 'old', 'ku' => 'old'],
            'description' => ['tr' => '<p>eski</p>', 'en' => '<p>old</p>', 'ku' => '<p>old</p>'],
            'sku' => 'RG-KEEP-0001',
            'price' => 111,
            'stock_status' => 'out_of_stock',
            'status' => 'inactive',
            'is_featured' => false,
            'is_new' => false,
            'delivery_note' => ['tr' => '', 'en' => '', 'ku' => ''],
            'meta_title' => ['tr' => 'old', 'en' => 'old', 'ku' => 'old'],
            'meta_description' => ['tr' => 'old', 'en' => 'old', 'ku' => 'old'],
            'product_highlights' => [],
            'sort_order' => 99,
        ]);
        ProductImage::create([
            'product_id' => $existingImported->id,
            'image_path' => 'https://images.unsplash.com/old-image.jpg',
            'alt_text' => 'Old remote image',
            'is_primary' => true,
            'sort_order' => 1,
        ]);

        $incoming = storage_path('app/_test_import_incoming_'.uniqid());
        File::ensureDirectoryExists($incoming);

        $png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==', true);
        File::put($incoming.DIRECTORY_SEPARATOR.'fixture-import.png', $png);
        File::put($incoming.DIRECTORY_SEPARATOR.'uuid-orphan.jpg', $png);

        $catalogPath = storage_path('app/_test_import_catalog_'.uniqid().'.json');
        File::put($catalogPath, json_encode([
            'fixture-import.png' => [
                'slug' => 'import-test-urun',
                'category_slug' => 'cicek-buketleri',
                'price' => 199.5,
                'tags' => ['Yeni Etiket'],
                'name' => ['tr' => 'Ice Aktarilan Test', 'en' => 'Import Test', 'ku' => 'Import Test'],
                'short_description' => ['tr' => 'Kisa', 'en' => 'Short', 'ku' => 'Short'],
                'description' => ['tr' => '<p>Aciklama</p>', 'en' => '<p>Description</p>', 'ku' => '<p>Description</p>'],
                'delivery_note' => ['tr' => 'Not', 'en' => 'Note', 'ku' => 'Note'],
                'meta_title' => ['tr' => 'Meta', 'en' => 'Meta', 'ku' => 'Meta'],
                'meta_description' => ['tr' => 'Desc meta', 'en' => 'Desc meta', 'ku' => 'Desc meta'],
                'is_featured' => true,
            ],
        ], JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE));

        $this->artisan('products:import-incoming', [
            '--incoming-dir' => $incoming,
            '--catalog' => $catalogPath,
            '--force' => true,
        ])->assertSuccessful();

        $legacy->refresh();
        $existingImported->refresh();

        $this->assertSame('inactive', $legacy->status);
        $this->assertSame('active', $existingImported->status);
        $this->assertSame('RG-KEEP-0001', $existingImported->sku);
        $this->assertTrue($existingImported->is_featured);
        $this->assertSame('199.50', $existingImported->price);
        $this->assertSame([$category->id], $existingImported->categories()->pluck('categories.id')->all());
        $this->assertSame(['yeni-etiket'], $existingImported->tags()->pluck('slug')->all());
        $this->assertCount(1, $existingImported->images);
        $this->assertSame('storage/products/import-test-urun.png', $existingImported->images()->value('image_path'));
        $this->assertTrue((bool) $existingImported->images()->value('is_primary'));
        $this->assertNotEmpty($existingImported->getTranslation('product_highlights', 'tr'));
        $this->assertSame('sparkles', $existingImported->getTranslation('product_highlights', 'tr')[0]['icon']);
        $this->assertTrue(File::exists(storage_path('app/public/products/import-test-urun.png')));
        $this->assertSame(1, ProductImage::query()->where('product_id', $existingImported->id)->count());

        File::deleteDirectory($incoming);
        File::delete($catalogPath);
        File::delete(storage_path('app/public/products/import-test-urun.png'));
    }

    public function test_dry_run_does_not_touch_database(): void
    {
        Category::create([
            'name' => ['tr' => 'Cicek Buketleri'],
            'slug' => 'cicek-buketleri',
            'description' => ['tr' => ''],
            'is_active' => true,
            'sort_order' => 1,
        ]);

        Product::create([
            'name' => ['tr' => 'Kalacak', 'en' => 'Stays', 'ku' => 'Stays'],
            'slug' => 'kalacak-urun',
            'short_description' => ['tr' => 'x', 'en' => 'x', 'ku' => 'x'],
            'description' => ['tr' => '<p>x</p>', 'en' => '<p>x</p>', 'ku' => '<p>x</p>'],
            'sku' => 'RG-KALACAK-01',
            'price' => 50,
            'stock_status' => 'in_stock',
            'status' => 'active',
            'is_featured' => false,
            'is_new' => false,
            'delivery_note' => ['tr' => '', 'en' => '', 'ku' => ''],
            'meta_title' => ['tr' => 't', 'en' => 't', 'ku' => 't'],
            'meta_description' => ['tr' => 'd', 'en' => 'd', 'ku' => 'd'],
            'sort_order' => 1,
        ]);

        $incoming = storage_path('app/_test_import_incoming_dry_'.uniqid());
        File::ensureDirectoryExists($incoming);
        File::put($incoming.DIRECTORY_SEPARATOR.'dry.png', 'not-a-real-png');

        $catalogPath = storage_path('app/_test_import_catalog_dry_'.uniqid().'.json');
        File::put($catalogPath, json_encode([
            'dry.png' => [
                'slug' => 'dry-run-slug',
                'category_slug' => 'cicek-buketleri',
                'price' => 1,
                'tags' => [],
                'name' => ['tr' => 'Dry', 'en' => 'Dry', 'ku' => 'Dry'],
                'short_description' => ['tr' => 's', 'en' => 's', 'ku' => 's'],
                'description' => ['tr' => '<p>d</p>', 'en' => '<p>d</p>', 'ku' => '<p>d</p>'],
                'delivery_note' => ['tr' => '', 'en' => '', 'ku' => ''],
                'meta_title' => ['tr' => 'm', 'en' => 'm', 'ku' => 'm'],
                'meta_description' => ['tr' => 'md', 'en' => 'md', 'ku' => 'md'],
            ],
        ], JSON_THROW_ON_ERROR));

        $this->artisan('products:import-incoming', [
            '--incoming-dir' => $incoming,
            '--catalog' => $catalogPath,
            '--dry-run' => true,
        ])->assertSuccessful();

        $this->assertSame('active', Product::query()->where('slug', 'kalacak-urun')->value('status'));
        $this->assertNull(Product::query()->where('slug', 'dry-run-slug')->first());

        File::deleteDirectory($incoming);
        File::delete($catalogPath);
    }

    public function test_import_syncs_derived_categories_and_special_occasions_for_supported_catalogs(): void
    {
        $root = Category::create([
            'name' => ['tr' => 'Saksı Bitkileri'],
            'slug' => 'saksi-cicekleri',
            'description' => ['tr' => ''],
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $child = Category::create([
            'name' => ['tr' => 'Orkideler'],
            'slug' => 'orkideler',
            'description' => ['tr' => ''],
            'parent_id' => $root->id,
            'is_active' => true,
            'sort_order' => 2,
        ]);

        $occasion = SpecialOccasion::create([
            'name' => ['tr' => 'Anneler Günü'],
            'slug' => 'anneler-gunu',
            'date_month' => 5,
            'date_day' => 11,
            'category_id' => $root->id,
            'loyalty_multiplier' => 1.5,
            'is_active' => true,
        ]);

        $incoming = storage_path('app/_test_import_incoming_orchid_'.uniqid());
        File::ensureDirectoryExists($incoming);

        $png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==', true);
        File::put($incoming.DIRECTORY_SEPARATOR.'orchid.png', $png);

        $catalogPath = storage_path('app/_test_import_catalog_orchid_'.uniqid().'.json');
        File::put($catalogPath, json_encode([
            'orchid.png' => [
                'slug' => 'test-orkide',
                'category_slug' => 'saksi-cicekleri',
                'price' => 3990,
                'tags' => ['Özel Gün', 'Lüks'],
                'name' => ['tr' => '2 Dal Beyaz Orkide', 'en' => 'White Orchid', 'ku' => 'Orkide'],
                'short_description' => ['tr' => 'Kısa', 'en' => 'Short', 'ku' => 'Short'],
                'description' => ['tr' => '<p>Açıklama</p>', 'en' => '<p>Description</p>', 'ku' => '<p>Description</p>'],
                'delivery_note' => ['tr' => 'Not', 'en' => 'Note', 'ku' => 'Note'],
                'meta_title' => ['tr' => 'Meta', 'en' => 'Meta', 'ku' => 'Meta'],
                'meta_description' => ['tr' => 'Desc meta', 'en' => 'Desc meta', 'ku' => 'Desc meta'],
            ],
        ], JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE));

        $this->artisan('products:import-incoming', [
            '--incoming-dir' => $incoming,
            '--catalog' => $catalogPath,
            '--force' => true,
        ])->assertSuccessful();

        $product = Product::query()->where('slug', 'test-orkide')->firstOrFail();

        $this->assertEqualsCanonicalizing(
            [$root->id, $child->id],
            $product->categories()->pluck('categories.id')->all()
        );
        $this->assertSame([$occasion->id], $product->specialOccasions()->pluck('special_occasions.id')->all());

        File::deleteDirectory($incoming);
        File::delete($catalogPath);
        File::delete(storage_path('app/public/products/test-orkide.png'));
    }
}
