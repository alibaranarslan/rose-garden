<?php

namespace Tests\Feature\Storefront;

use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\Category;
use App\Models\Page;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\SpecialOccasion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class TrustLocaleIntegrityTest extends TestCase
{
    use RefreshDatabase;

    public function test_locale_prefixed_detail_routes_do_not_treat_locale_as_slug(): void
    {
        $content = $this->seedLocalizedContent();

        $this->get('/en/urun/'.$content['product']->slug)
            ->assertOk()
            ->assertSeeText('Bouquet No. 1')
            ->assertSee('/tr/urun/'.$content['product']->slug, false)
            ->assertSee('/ku/urun/'.$content['product']->slug, false);

        $this->get('/en/kategori/'.$content['category']->slug)
            ->assertOk()
            ->assertSeeText('Bouquets')
            ->assertSeeText('Bouquet No. 1');

        $this->get('/en/blog/'.$content['post']->slug)
            ->assertOk()
            ->assertSeeText('Flower picks for spring');

        $this->get('/en/sayfa/'.$content['page']->slug)
            ->assertOk()
            ->assertSeeText('About Rose Garden');

        $this->get('/en/ozel-gunler/'.$content['occasion']->slug)
            ->assertOk()
            ->assertSeeText('Mothers Day');
    }

    public function test_admin_fed_localized_content_resolves_for_selected_locale(): void
    {
        $content = $this->seedLocalizedContent();

        $this->get('/ku/urun/'.$content['product']->slug)
            ->assertOk()
            ->assertSeeText('Buket No. 1 KU')
            ->assertSeeText('Dizayna buketa butik');

        $this->get('/ku/blog/'.$content['post']->slug)
            ->assertOk()
            ->assertSeeText('Hilbijartinen kulilkan ji bo bihar');

        $this->get('/ku/sayfa/'.$content['page']->slug)
            ->assertOk()
            ->assertSeeText('Derbare Rose Garden de');

        $this->get('/ku/ozel-gunler/'.$content['occasion']->slug)
            ->assertOk()
            ->assertSeeText('Roja Dayikan');
    }

    /**
     * @return array{category: Category, product: Product, post: BlogPost, page: Page, occasion: SpecialOccasion}
     */
    private function seedLocalizedContent(): array
    {
        File::ensureDirectoryExists(storage_path('app/public/products'));
        File::put(storage_path('app/public/products/buket-no-1.jpg'), 'seed-image');

        $category = Category::create([
            'name' => ['tr' => 'Buketler', 'en' => 'Bouquets', 'ku' => 'Buketan'],
            'slug' => 'cicek-buketleri',
            'description' => [
                'tr' => 'Sezonun buket seckileri',
                'en' => 'Seasonal bouquet picks',
                'ku' => 'Hilbijartinen buketan yen demsali',
            ],
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $product = Product::create([
            'name' => ['tr' => 'Buket No. 1', 'en' => 'Bouquet No. 1', 'ku' => 'Buket No. 1 KU'],
            'slug' => 'buket-no-1',
            'short_description' => [
                'tr' => 'Tasarlanmis butik buket',
                'en' => 'Boutique bouquet design',
                'ku' => 'Dizayna buketa butik',
            ],
            'description' => [
                'tr' => '<p>Katmanli cicek ve yesillik kompozisyonu.</p>',
                'en' => '<p>Layered flower and greenery composition.</p>',
                'ku' => '<p>Kompozisyona kulilkan u keskahiyan.</p>',
            ],
            'price' => 890,
            'stock_status' => 'in_stock',
            'status' => 'active',
            'is_featured' => true,
            'is_new' => true,
            'delivery_note' => [
                'tr' => 'Ayni gun teslimat.',
                'en' => 'Same-day delivery.',
                'ku' => 'Radestkirina heman roje.',
            ],
            'product_highlights' => [
                'tr' => [
                    ['icon' => 'sparkles', 'title' => 'Butik Hazirlik', 'body' => 'Atolyede hazirlanir.', 'sort_order' => 1],
                ],
                'en' => [
                    ['icon' => 'sparkles', 'title' => 'Boutique Preparation', 'body' => 'Prepared in the studio.', 'sort_order' => 1],
                ],
                'ku' => [
                    ['icon' => 'sparkles', 'title' => 'Amadekariya butik', 'body' => 'Di atolyeye de te amadekirin.', 'sort_order' => 1],
                ],
            ],
            'sort_order' => 1,
        ]);

        $product->categories()->attach($category);

        ProductImage::create([
            'product_id' => $product->id,
            'image_path' => 'storage/products/buket-no-1.jpg',
            'alt_text' => 'Buket No. 1',
            'is_primary' => true,
            'sort_order' => 1,
        ]);

        $blogCategory = BlogCategory::create([
            'name' => ['tr' => 'Cicek Rehberi', 'en' => 'Flower Guide', 'ku' => 'Rehbera Kulilkan'],
            'slug' => 'cicek-rehberi',
            'sort_order' => 1,
        ]);

        $author = User::factory()->create([
            'name' => 'Editor Rose Garden',
            'email' => 'editor@example.com',
        ]);

        $post = BlogPost::create([
            'title' => [
                'tr' => 'Bahar icin cicek onerileri',
                'en' => 'Flower picks for spring',
                'ku' => 'Hilbijartinen kulilkan ji bo bihar',
            ],
            'slug' => 'bahar-icin-cicek-onerileri',
            'excerpt' => [
                'tr' => 'Ev ve hediye icin cicek secimi notlari.',
                'en' => 'Notes for choosing flowers for home and gifts.',
                'ku' => 'Noten hilbijartina kulilkan ji bo mal u diyariyan.',
            ],
            'content' => [
                'tr' => '<p>Bahar sezonunda buket secimini kolaylastiran notlar.</p>',
                'en' => '<p>Notes that make bouquet selection easier in spring.</p>',
                'ku' => '<p>Noten ku hilbijartina buketan di bihar de hesan dikin.</p>',
            ],
            'featured_image' => 'images/blog/flower-care.svg',
            'blog_category_id' => $blogCategory->id,
            'author_id' => $author->id,
            'status' => 'published',
            'published_at' => now()->subDay(),
        ]);

        $post->products()->attach($product);

        $page = Page::create([
            'title' => ['tr' => 'Rose Garden Hakkinda', 'en' => 'About Rose Garden', 'ku' => 'Derbare Rose Garden de'],
            'slug' => 'rose-garden-hakkinda',
            'content' => [
                'tr' => '<p>Tasarlanmis butik cicek deneyimi.</p>',
                'en' => '<p>A tailored boutique flower experience.</p>',
                'ku' => '<p>Tecrubeyeke kulilkan a butik a hati sewirandin.</p>',
            ],
            'meta_title' => ['tr' => 'Rose Garden Hakkinda', 'en' => 'About Rose Garden', 'ku' => 'Derbare Rose Garden de'],
            'meta_description' => ['tr' => 'Butik florist hikayemiz.', 'en' => 'Our boutique florist story.', 'ku' => 'Ciroka me ya florist e butik.'],
            'is_published' => true,
            'sort_order' => 1,
        ]);

        $occasion = SpecialOccasion::create([
            'name' => ['tr' => 'Anneler Gunu', 'en' => 'Mothers Day', 'ku' => 'Roja Dayikan'],
            'slug' => 'anneler-gunu',
            'date_month' => now()->month,
            'date_day' => min(now()->day + 1, 28),
            'category_id' => $category->id,
            'loyalty_multiplier' => 1.0,
            'is_active' => true,
        ]);

        $product->specialOccasions()->attach($occasion);

        return compact('category', 'product', 'post', 'page', 'occasion');
    }
}
