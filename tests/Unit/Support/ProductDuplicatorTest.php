<?php

namespace Tests\Unit\Support;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\SpecialOccasion;
use App\Models\Tag;
use App\Support\ProductDuplicator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductDuplicatorTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_a_draft_copy_with_related_storefront_data(): void
    {
        $category = Category::create([
            'name' => ['tr' => 'Cicek Buketleri'],
            'slug' => 'cicek-buketleri',
            'description' => ['tr' => ''],
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $tag = Tag::create([
            'name' => ['tr' => 'Romantik'],
            'slug' => 'romantik',
        ]);

        $occasion = SpecialOccasion::create([
            'name' => ['tr' => 'Anneler Gunu'],
            'slug' => 'anneler-gunu',
            'date_month' => 5,
            'date_day' => 12,
            'category_id' => $category->id,
            'loyalty_multiplier' => 1.0,
            'is_active' => true,
        ]);

        $product = Product::create([
            'name' => ['tr' => 'Asil Buket', 'en' => 'Elegant Bouquet'],
            'slug' => 'asil-buket',
            'short_description' => ['tr' => 'Asil buket'],
            'description' => ['tr' => '<p>Aciklama</p>'],
            'sku' => 'RG-ASIL-0001',
            'price' => 1200,
            'stock_status' => 'in_stock',
            'status' => 'active',
            'is_featured' => true,
            'is_new' => true,
            'view_count' => 18,
            'delivery_note' => ['tr' => 'Ayni gun teslimat'],
            'product_highlights' => [
                'tr' => [
                    ['icon' => 'sparkles', 'title' => 'Butik Hazirlik', 'body' => 'Atolyede hazirlanir.', 'sort_order' => 1],
                    ['icon' => 'truck', 'title' => 'Teslimat', 'body' => 'Kontrollu teslimat.', 'sort_order' => 2],
                ],
            ],
            'sort_order' => 1,
        ]);

        $product->categories()->attach($category);
        $product->tags()->attach($tag);
        $product->specialOccasions()->attach($occasion);

        ProductImage::create([
            'product_id' => $product->id,
            'image_path' => 'storage/products/asil-buket-1.jpg',
            'alt_text' => 'Kapak',
            'is_primary' => true,
            'sort_order' => 1,
        ]);
        ProductImage::create([
            'product_id' => $product->id,
            'image_path' => 'storage/products/asil-buket-2.jpg',
            'alt_text' => 'Detay',
            'is_primary' => false,
            'sort_order' => 2,
        ]);

        ProductVariant::create([
            'product_id' => $product->id,
            'name' => ['tr' => 'Standart'],
            'price' => 1200,
            'sale_price' => null,
            'stock_status' => 'in_stock',
            'sort_order' => 1,
            'is_active' => true,
        ]);
        ProductVariant::create([
            'product_id' => $product->id,
            'name' => ['tr' => 'Buyuk'],
            'price' => 1490,
            'sale_price' => 1390,
            'stock_status' => 'in_stock',
            'sort_order' => 2,
            'is_active' => true,
        ]);

        $clone = ProductDuplicator::duplicate($product);

        $this->assertNotSame($product->id, $clone->id);
        $this->assertSame('draft', $clone->status);
        $this->assertFalse($clone->is_featured);
        $this->assertSame(0, $clone->view_count);
        $this->assertStringStartsWith('asil-buket-kopya-', $clone->slug);
        $this->assertSame($product->getTranslations('product_highlights'), $clone->getTranslations('product_highlights'));
        $this->assertSame([$category->id], $clone->categories->modelKeys());
        $this->assertSame([$tag->id], $clone->tags->modelKeys());
        $this->assertSame([$occasion->id], $clone->specialOccasions->modelKeys());
        $this->assertCount(2, $clone->images);
        $this->assertSame('storage/products/asil-buket-1.jpg', $clone->primaryImage);
        $this->assertCount(2, $clone->variants);
        $this->assertSame(['Standart', 'Buyuk'], $clone->variants->map(fn (ProductVariant $variant) => $variant->getTranslation('name', 'tr'))->all());
    }
}
