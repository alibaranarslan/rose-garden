<?php

namespace Tests\Feature\Filament;

use App\Filament\Resources\ProductResource;
use App\Filament\Resources\ProductResource\Pages\CreateProduct;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Livewire\Livewire;
use Tests\TestCase;

class ProductResourceFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_highlight_state_is_normalized_for_admin_submission(): void
    {
        $normalized = ProductResource::normalizeHighlightState([
            ['icon' => 'truck', 'title' => 'Teslimat', 'body' => 'Ayni gun akis.'],
            ['icon' => 'gift', 'title' => 'Jest', 'body' => 'Not karti ile gelir.'],
            ['icon' => 'sparkles', 'title' => '', 'body' => 'Bos satir temizlenmeli.'],
        ]);

        $this->assertSame([
            ['icon' => 'truck', 'title' => 'Teslimat', 'body' => 'Ayni gun akis.', 'sort_order' => 1],
            ['icon' => 'gift', 'title' => 'Jest', 'body' => 'Not karti ile gelir.', 'sort_order' => 2],
        ], $normalized);
    }

    public function test_uploaded_gallery_state_is_normalized_to_single_path(): void
    {
        $this->assertSame(
            'products/uploaded.jpg',
            ProductResource::normalizeUploadedImagePath(['upload-key' => 'products/uploaded.jpg'])
        );

        $this->assertSame(
            'products/existing.jpg',
            ProductResource::normalizeUploadedImagePath([], 'products/existing.jpg')
        );

        $this->assertSame(
            'products/storage-prefix.jpg',
            ProductResource::normalizeUploadedImagePath('storage/products/storage-prefix.jpg')
        );

        $this->assertSame(
            ['first' => 'products/from-form.jpg'],
            ProductResource::normalizeImagePathForFileUpload(['first' => 'storage/products/from-form.jpg'])
        );

        $this->assertNull(ProductResource::normalizeUploadedImagePath([]));
    }

    public function test_admin_product_image_url_resolves_existing_storage_backed_image(): void
    {
        $relative = 'products/admin-visible-product.jpg';

        File::ensureDirectoryExists(storage_path('app/public/products'));
        File::ensureDirectoryExists(public_path('storage/products'));
        File::put(storage_path('app/public/'.$relative), 'test-image');
        File::put(public_path('storage/'.$relative), 'test-image');

        try {
            $product = Product::create([
                'name' => ['tr' => 'Admin Gorsel Testi'],
                'slug' => 'admin-gorsel-testi',
                'short_description' => ['tr' => 'Admin gorsel testi'],
                'description' => ['tr' => '<p>Aciklama</p>'],
                'price' => 890,
                'stock_status' => 'in_stock',
                'status' => 'active',
            ]);

            $product->images()->create([
                'image_path' => 'storage/'.$relative,
                'alt_text' => 'Admin test',
                'is_primary' => true,
                'sort_order' => 1,
            ]);

            $this->assertSame(url('/storage/'.$relative), ProductResource::adminProductImageUrl($product));
        } finally {
            File::delete(storage_path('app/public/'.$relative));
            File::delete(public_path('storage/'.$relative));
        }
    }

    public function test_primary_image_rule_keeps_single_cover_after_gallery_changes(): void
    {
        $product = Product::create([
            'name' => ['tr' => 'Galeri Testi'],
            'slug' => 'galeri-testi',
            'short_description' => ['tr' => 'Galeri testi'],
            'description' => ['tr' => '<p>Aciklama</p>'],
            'price' => 890,
            'stock_status' => 'in_stock',
            'status' => 'active',
        ]);

        $product->images()->createMany([
            ['image_path' => 'products/one.jpg', 'alt_text' => 'One', 'is_primary' => false, 'sort_order' => 1],
            ['image_path' => 'products/two.jpg', 'alt_text' => 'Two', 'is_primary' => true, 'sort_order' => 2],
            ['image_path' => 'products/three.jpg', 'alt_text' => 'Three', 'is_primary' => true, 'sort_order' => 3],
        ]);

        $product->ensurePrimaryImage();
        $product->refresh()->load('images');

        $this->assertSame('products/two.jpg', $product->primaryImage);
        $this->assertSame(1, $product->images->where('is_primary', true)->count());
    }

    public function test_product_create_accepts_browser_uploaded_gallery_state(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
            'is_active' => true,
        ]);

        $category = Category::create([
            'name' => ['tr' => 'Smoke Kategori'],
            'slug' => 'smoke-kategori',
            'is_active' => true,
        ]);

        Livewire::actingAs($admin)
            ->test(CreateProduct::class)
            ->set('data.name', 'Smoke Urun')
            ->set('data.slug', 'smoke-urun')
            ->set('data.short_description', 'Smoke aciklama')
            ->set('data.price', 345)
            ->set('data.status', 'active')
            ->set('data.stock_status', 'in_stock')
            ->set('data.categories', [$category->id])
            ->set('data.images', [[
                'image_path' => ['upload-key' => 'products/browser-upload.jpg'],
                'alt_text' => null,
                'is_primary' => true,
                'sort_order' => 0,
            ]])
            ->set('data.variants', [])
            ->call('create')
            ->assertHasNoErrors();

        $product = Product::where('slug', 'smoke-urun')->with('images')->firstOrFail();

        $this->assertSame('products/browser-upload.jpg', $product->images->first()->image_path);
        $this->assertSame('', $product->images->first()->alt_text);
        $this->assertTrue($product->images->first()->is_primary);
    }
}
