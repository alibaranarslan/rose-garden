<?php

namespace Tests\Feature\Storefront;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogIntegrityTest extends TestCase
{
    use RefreshDatabase;

    public function test_category_listing_uses_selected_subtree_and_subtree_counts(): void
    {
        $bouquets = $this->makeCategory('cicek-buketleri', ['tr' => 'Cicek Buketleri']);
        $roses = $this->makeCategory('gul-buketleri', ['tr' => 'Gul Buketleri'], $bouquets);
        $plants = $this->makeCategory('saksi-cicekleri', ['tr' => 'Saksi Cicekleri'], null, 20);
        $orchids = $this->makeCategory('orkideler', ['tr' => 'Orkideler'], $plants, 21);

        $this->makeProduct('gul-buketi-bir', ['tr' => 'Gul Buketi Bir'], [$roses]);
        $this->makeProduct('gul-buketi-iki', ['tr' => 'Gul Buketi Iki'], [$roses]);
        $this->makeProduct('orkide-bir', ['tr' => 'Orkide Bir'], [$orchids]);

        $response = $this->get(route('products.category', ['slug' => $bouquets->slug]));

        $response
            ->assertOk()
            ->assertSeeText('Gul Buketi Bir')
            ->assertSeeText('Gul Buketi Iki');

        $products = $response->viewData('products');
        $allCategories = $response->viewData('allCategories');

        $this->assertSame(2, $products->total());
        $this->assertSame(['gul-buketi-bir', 'gul-buketi-iki'], $products->getCollection()->pluck('slug')->all());
        $this->assertSame(3, $response->viewData('catalogTotalCount'));
        $this->assertSame(2, (int) $allCategories->firstWhere('slug', 'cicek-buketleri')->products_count);
        $this->assertSame(2, (int) $allCategories->firstWhere('slug', 'gul-buketleri')->products_count);
        $this->assertSame(1, (int) $allCategories->firstWhere('slug', 'saksi-cicekleri')->products_count);
        $this->assertSame(1, (int) $allCategories->firstWhere('slug', 'orkideler')->products_count);
    }

    public function test_related_products_stay_in_specific_catalog_family_and_sort_categories_by_depth(): void
    {
        $bouquets = $this->makeCategory('cicek-buketleri', ['tr' => 'Cicek Buketleri']);
        $roses = $this->makeCategory('gul-buketleri', ['tr' => 'Gul Buketleri'], $bouquets);
        $premium = $this->makeCategory('premium-buketler', ['tr' => 'Premium Buketler'], $bouquets, 12);
        $plants = $this->makeCategory('saksi-cicekleri', ['tr' => 'Saksi Cicekleri'], null, 20);
        $orchids = $this->makeCategory('orkideler', ['tr' => 'Orkideler'], $plants, 21);

        $primary = $this->makeProduct('kadife-gul', ['tr' => 'Kadife Gul'], [$bouquets, $roses]);
        $roseSiblingOne = $this->makeProduct('kadife-gul-iki', ['tr' => 'Kadife Gul Iki'], [$roses]);
        $roseSiblingTwo = $this->makeProduct('kadife-gul-uc', ['tr' => 'Kadife Gul Uc'], [$roses]);
        $roseSiblingThree = $this->makeProduct('kadife-gul-dort', ['tr' => 'Kadife Gul Dort'], [$roses]);
        $roseSiblingFour = $this->makeProduct('kadife-gul-bes', ['tr' => 'Kadife Gul Bes'], [$roses]);
        $this->makeProduct('premium-gul', ['tr' => 'Premium Gul'], [$premium]);
        $this->makeProduct('beyaz-orkide', ['tr' => 'Beyaz Orkide'], [$orchids]);

        $response = $this->get(route('products.show', ['slug' => $primary->slug]));

        $response
            ->assertOk()
            ->assertSeeText('Kadife Gul Iki')
            ->assertSeeText('Kadife Gul Uc')
            ->assertSeeText('Kadife Gul Dort')
            ->assertSeeText('Kadife Gul Bes')
            ->assertDontSeeText('Premium Gul')
            ->assertDontSeeText('Beyaz Orkide');

        /** @var Product $viewProduct */
        $viewProduct = $response->viewData('product');
        $related = $response->viewData('related');

        $this->assertSame('gul-buketleri', $viewProduct->categories->first()?->slug);
        $this->assertSame(
            [$roseSiblingOne->slug, $roseSiblingTwo->slug, $roseSiblingThree->slug, $roseSiblingFour->slug],
            $related->pluck('slug')->all()
        );
        $this->assertTrue($related->every(fn (Product $product) => $product->categories->first()?->slug === 'gul-buketleri'));
    }

    private function makeCategory(string $slug, array $name, ?Category $parent = null, int $sortOrder = 10): Category
    {
        return Category::create([
            'name' => $name,
            'slug' => $slug,
            'description' => ['tr' => 'Storefront kategori testi'],
            'parent_id' => $parent?->id,
            'sort_order' => $sortOrder,
            'is_active' => true,
        ]);
    }

    /**
     * @param  array<int, Category>  $categories
     */
    private function makeProduct(string $slug, array $name, array $categories): Product
    {
        $product = Product::create([
            'name' => $name + ['en' => $name['tr'], 'ku' => $name['tr']],
            'slug' => $slug,
            'short_description' => ['tr' => 'Katalog butunlugu testi'],
            'description' => ['tr' => '<p>Katalog butunlugu testi.</p>'],
            'price' => 890,
            'stock_status' => 'in_stock',
            'status' => 'active',
            'is_featured' => false,
            'is_new' => false,
            'sort_order' => 1,
        ]);

        $product->categories()->attach(collect($categories)->pluck('id')->all());

        return $product;
    }
}
