<?php

namespace Tests\Feature\Admin;

use App\Filament\Pages\GeneralSettings;
use App\Filament\Resources\ProductResource\Pages\EditProduct;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminPersistFixTest extends TestCase
{
    use RefreshDatabase;

    public function test_general_settings_persists_site_name_without_unrelated_home_copy_and_invalidates_storefront_cache(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
            'is_active' => true,
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Rose Garden');

        Livewire::actingAs($admin)
            ->test(GeneralSettings::class)
            ->set('data.site_name.tr', 'Rose Garden Persist Live')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame(
            'Rose Garden Persist Live',
            data_get(json_decode((string) Setting::get('general', 'site_name', '{}'), true), 'tr')
        );
        $this->assertNotSame('', (string) Setting::get('system', 'storefront_content_version', ''));

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Rose Garden Persist Live');
    }

    public function test_product_edit_persists_short_description_and_preserves_existing_gallery_path(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
            'is_active' => true,
        ]);

        $category = Category::create([
            'name' => ['tr' => 'Buketler'],
            'slug' => 'buketler',
            'is_active' => true,
        ]);

        $product = Product::create([
            'name' => ['tr' => 'Persist Urunu'],
            'slug' => 'persist-urunu',
            'short_description' => ['tr' => 'ilk aciklama'],
            'description' => ['tr' => '<p>Detay</p>'],
            'price' => 890,
            'stock_status' => 'in_stock',
            'status' => 'active',
        ]);

        $product->categories()->attach($category->id);

        $image = ProductImage::create([
            'product_id' => $product->id,
            'image_path' => 'products/missing-edit-image.jpg',
            'alt_text' => 'Persist',
            'is_primary' => true,
            'sort_order' => 1,
        ]);

        $this->get(route('products.show', ['slug' => $product->slug]))
            ->assertOk()
            ->assertSee('ilk aciklama');

        Livewire::actingAs($admin)
            ->test(EditProduct::class, ['record' => $product->getRouteKey()])
            ->set('data.short_description', 'admin persist marker')
            ->call('save')
            ->assertHasNoErrors();

        $product->refresh();
        $image->refresh();

        $this->assertSame('admin persist marker', $product->getTranslation('short_description', 'tr'));
        $this->assertSame('products/missing-edit-image.jpg', $image->image_path);
        $this->assertNotSame('', (string) Setting::get('system', 'storefront_content_version', ''));

        $this->get(route('products.show', ['slug' => $product->slug]))
            ->assertOk()
            ->assertSee('admin persist marker')
            ->assertDontSee('ilk aciklama');
    }
}
