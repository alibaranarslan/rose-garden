<?php

namespace Tests\Feature\Admin;

use App\Filament\Pages\MediaLibrary;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Tests\TestCase;

class AdminMediaLibraryTest extends TestCase
{
    use RefreshDatabase;

    public function test_media_library_does_not_delete_media_attached_to_existing_records(): void
    {
        $admin = $this->adminUser();
        $product = Product::query()->create([
            'name' => ['tr' => 'Medya Korumalı Ürün'],
            'slug' => 'medya-korumali-urun',
            'price' => 500,
            'stock_status' => 'in_stock',
            'status' => 'active',
        ]);
        $media = $this->mediaFor($product->getMorphClass(), $product->getKey(), 'attached-product.jpg');

        Livewire::actingAs($admin)
            ->test(MediaLibrary::class)
            ->call('deleteMedia', $media->id);

        $this->assertDatabaseHas('media', ['id' => $media->id]);
    }

    public function test_media_library_deletes_orphaned_media_only(): void
    {
        $admin = $this->adminUser();
        $media = $this->mediaFor(Product::class, 999999, 'orphan-product.jpg');

        Livewire::actingAs($admin)
            ->test(MediaLibrary::class)
            ->call('deleteMedia', $media->id);

        $this->assertDatabaseMissing('media', ['id' => $media->id]);
    }

    public function test_media_library_normalizes_view_mode_and_search_input(): void
    {
        $admin = $this->adminUser();

        Livewire::actingAs($admin)
            ->test(MediaLibrary::class)
            ->call('setViewMode', 'table')
            ->assertSet('viewMode', 'grid')
            ->call('setViewMode', 'list')
            ->assertSet('viewMode', 'list')
            ->set('search', '  '.str_repeat('a', 120).'  ')
            ->assertSet('search', str_repeat('a', 100));
    }

    private function adminUser(): User
    {
        return User::factory()->create([
            'is_admin' => true,
            'is_active' => true,
        ]);
    }

    private function mediaFor(string $modelType, int $modelId, string $fileName): Media
    {
        return Media::query()->create([
            'model_type' => $modelType,
            'model_id' => $modelId,
            'collection_name' => 'images',
            'name' => pathinfo($fileName, PATHINFO_FILENAME),
            'file_name' => $fileName,
            'mime_type' => 'image/jpeg',
            'disk' => 'public',
            'conversions_disk' => 'public',
            'size' => 2048,
            'manipulations' => [],
            'custom_properties' => [],
            'generated_conversions' => [],
            'responsive_images' => [],
            'order_column' => 1,
        ]);
    }
}
