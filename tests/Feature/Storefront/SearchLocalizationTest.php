<?php

namespace Tests\Feature\Storefront;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class SearchLocalizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_search_matches_english_and_kurdish_product_fields(): void
    {
        File::ensureDirectoryExists(storage_path('app/public/products'));
        File::put(storage_path('app/public/products/search-test.jpg'), 'search-image');

        $product = Product::create([
            'name' => [
                'tr' => 'Gul Buketi',
                'en' => 'Rose Bouquet',
                'ku' => 'Buketa Gul',
            ],
            'short_description' => [
                'tr' => 'Atolye hazirligi',
                'en' => 'Handmade atelier arrangement',
                'ku' => 'Aranjmana desti ya atolyeye',
            ],
            'slug' => 'gul-buketi',
            'price' => 450,
            'stock_status' => 'in_stock',
            'status' => 'active',
        ]);

        ProductImage::create([
            'product_id' => $product->id,
            'image_path' => 'storage/products/search-test.jpg',
            'alt_text' => 'Rose Bouquet',
            'is_primary' => true,
            'sort_order' => 1,
        ]);

        $this->get('/en/arama?q=atelier')
            ->assertOk()
            ->assertSeeText('Rose Bouquet');

        $this->get('/ku/arama?q=atolyeye')
            ->assertOk()
            ->assertSeeText('Buketa Gul');
    }

    public function test_search_matches_multi_term_cross_locale_queries_on_turkish_surface(): void
    {
        File::ensureDirectoryExists(storage_path('app/public/products'));
        File::put(storage_path('app/public/products/search-cross-locale.jpg'), 'search-image');

        $product = Product::create([
            'name' => [
                'tr' => 'Atolye Buketi',
                'en' => 'Rose Bouquet',
                'ku' => 'Buketa Gul',
            ],
            'short_description' => [
                'tr' => 'Yerel hazirlik',
                'en' => 'Handmade atelier arrangement',
                'ku' => 'Aranjmana desti ya atolyeye',
            ],
            'slug' => 'atolye-buketi',
            'price' => 650,
            'stock_status' => 'in_stock',
            'status' => 'active',
        ]);

        ProductImage::create([
            'product_id' => $product->id,
            'image_path' => 'storage/products/search-cross-locale.jpg',
            'alt_text' => 'Rose Bouquet',
            'is_primary' => true,
            'sort_order' => 1,
        ]);

        $this->get('/tr/arama?q=rose handmade')
            ->assertOk()
            ->assertSeeText('Atolye Buketi');

        $this->get('/tr/arama?q=buketa atolyeye')
            ->assertOk()
            ->assertSeeText('Atolye Buketi');
    }

    public function test_search_still_returns_active_products_without_product_image_rows(): void
    {
        Product::create([
            'name' => [
                'tr' => 'Kutlama Buketi',
                'en' => 'Celebration Bouquet',
                'ku' => 'Buketa Pirozbahi',
            ],
            'short_description' => [
                'tr' => 'Canli renkler',
                'en' => 'Bright floral arrangement',
                'ku' => 'Aranjmana gul a rengin',
            ],
            'slug' => 'kutlama-buketi',
            'price' => 550,
            'stock_status' => 'in_stock',
            'status' => 'active',
        ]);

        $this->get('/arama?q=celebration')
            ->assertOk()
            ->assertSeeText('Kutlama Buketi');

        $this->get('/arama?q=pirozbahi')
            ->assertOk()
            ->assertSeeText('Kutlama Buketi');
    }

    public function test_search_normalizes_kurdish_diacritics_on_turkish_surface(): void
    {
        Product::create([
            'name' => [
                'tr' => 'Bahar Buketi',
                'en' => 'Spring Bouquet',
                'ku' => 'Bûketa Gûl',
            ],
            'short_description' => [
                'tr' => 'Canli secki',
                'en' => 'Seasonal flowers',
                'ku' => 'Kulilka gul a demkî',
            ],
            'slug' => 'bahar-buketi',
            'price' => 590,
            'stock_status' => 'in_stock',
            'status' => 'active',
        ]);

        $this->get('/tr/arama?q=gul')
            ->assertOk()
            ->assertSeeText('Bahar Buketi');

        $this->get('/tr/arama?q=gûl')
            ->assertOk()
            ->assertSeeText('Bahar Buketi');
    }
}
