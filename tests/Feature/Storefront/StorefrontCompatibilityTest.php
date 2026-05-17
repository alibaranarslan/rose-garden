<?php

namespace Tests\Feature\Storefront;

use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\Category;
use App\Models\Favorite;
use App\Models\LoyaltyPoint;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Page;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\SpecialOccasion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class StorefrontCompatibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_storefront_pages_render_with_launch_catalog_data(): void
    {
        $content = $this->seedStorefrontContent();

        $this->get(route('home'))
            ->assertOk()
            ->assertSeeText('Rose Garden')
            ->assertSeeText('Buket No. 1');

        $this->get(route('products.index'))
            ->assertOk()
            ->assertSeeText('Buket No. 1')
            ->assertSeeText('Rose Garden');

        $this->get(route('products.category', ['slug' => $content['category']->slug]))
            ->assertOk()
            ->assertSeeText('Buket No. 1')
            ->assertSeeText('Buketler');

        $this->get(route('products.show', ['slug' => $content['product']->slug]))
            ->assertOk()
            ->assertSeeText('Buket No. 1')
            ->assertSeeText('Butik Hazirlik')
            ->assertSeeText('Sepete');

        $this->get(route('search', ['q' => 'buket']))
            ->assertOk()
            ->assertSeeText('Buket No. 1');

        $this->get(route('cart'))
            ->assertOk()
            ->assertSeeText('Sepet');

        $this->get(route('checkout'))
            ->assertOk()
            ->assertSeeText('Teslimat');

        $this->get(route('blog.index'))
            ->assertOk()
            ->assertSeeText('Bahar icin cicek onerileri');

        $this->get(route('blog.show', ['slug' => $content['post']->slug]))
            ->assertOk()
            ->assertSeeText('Bahar icin cicek onerileri')
            ->assertSeeText('Buket No. 1');

        $this->get(route('special-occasions.index'))
            ->assertOk()
            ->assertSeeText('Anneler Gunu');

        $this->get(route('special-occasions.show', ['slug' => $content['occasion']->slug]))
            ->assertOk()
            ->assertSeeText('Anneler Gunu')
            ->assertSeeText('Buket No. 1');

        $this->get(route('contact'))
            ->assertOk()
            ->assertSeeText('İletişim');

        $this->get(route('faq'))
            ->assertOk()
            ->assertSeeText('Sorulan');

        $this->get(route('delivery.info'))
            ->assertOk()
            ->assertSeeText('Teslimat');

        $this->get(route('order.track'))
            ->assertOk()
            ->assertSeeText('Sipariş');

        $this->get(route('page.show', ['slug' => $content['page']->slug]))
            ->assertOk()
            ->assertSeeText('Rose Garden')
            ->assertSeeText('Tasarlanmis');

        $this->get(route('login'))
            ->assertOk()
            ->assertSeeText('Giriş yap');

        $this->get(route('register'))
            ->assertOk()
            ->assertSeeText('Kayıt ol');

        $this->get(route('password.request'))
            ->assertOk()
            ->assertSeeText('Şifremi unuttum');
    }

    public function test_authenticated_account_pages_render_with_storefront_layout_changes(): void
    {
        $content = $this->seedStorefrontContent();
        $user = User::factory()->create([
            'name' => 'Ali Test',
            'phone' => '05000000000',
            'marketing_consent' => true,
            'kvkk_accepted_at' => now(),
        ]);

        LoyaltyPoint::create([
            'user_id' => $user->id,
            'balance' => 40,
            'total_earned' => 60,
            'total_spent' => 20,
        ]);

        Favorite::create([
            'user_id' => $user->id,
            'product_id' => $content['product']->id,
            'created_at' => now(),
        ]);

        $order = Order::create([
            'user_id' => $user->id,
            'status' => 'paid',
            'subtotal' => 890,
            'delivery_fee' => 0,
            'discount_amount' => 0,
            'loyalty_points_used' => 0,
            'total' => 890,
            'payment_method' => 'bank_transfer',
            'sender_name' => 'Ali Test',
            'sender_phone' => '05000000000',
            'sender_email' => $user->email,
            'recipient_name' => 'Ayse Test',
            'recipient_phone' => '05000000001',
            'recipient_address' => 'Ataturk Bulvari No: 1',
            'recipient_district' => 'Merkez',
            'delivery_date' => now()->addDay()->toDateString(),
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $content['product']->id,
            'product_name' => 'Buket No. 1',
            'quantity' => 1,
            'unit_price' => 890,
            'total_price' => 890,
        ]);

        $this->actingAs($user);

        $this->get(route('account.dashboard'))
            ->assertOk()
            ->assertSeeText('Merhaba')
            ->assertSeeText('Son siparişleriniz')
            ->assertSeeText('Çıkış yap');

        $this->get(route('account.orders'))
            ->assertOk()
            ->assertSeeText($order->order_number);

        $this->get(route('account.order.show', ['orderNumber' => $order->order_number]))
            ->assertOk()
            ->assertSeeText($order->order_number)
            ->assertSeeText('Buket No. 1');

        $this->get(route('account.favorites'))
            ->assertOk()
            ->assertSeeText('Favorilerim')
            ->assertSeeText('Buket No. 1');

        $this->get(route('account.loyalty'))
            ->assertOk()
            ->assertSeeText('40');

        $this->get(route('account.addresses'))
            ->assertOk()
            ->assertSeeText('Adres');

        $this->get(route('account.profile'))
            ->assertOk()
            ->assertSeeText('Profil')
            ->assertSeeText('E-posta')
            ->assertSeeText('Şifre sıfırla');

        $this->get(route('account.kvkk'))
            ->assertOk()
            ->assertSeeText('KVKK')
            ->assertSeeText($user->email);
    }

    public function test_catalog_keeps_active_products_visible_without_product_image_records(): void
    {
        Product::create([
            'name' => ['tr' => 'Atolye Secimi', 'en' => 'Atelier Pick'],
            'slug' => 'atolye-secimi',
            'short_description' => ['tr' => 'Yer tutucu gorsel ile de listelenir.'],
            'price' => 640,
            'stock_status' => 'in_stock',
            'status' => 'active',
        ]);

        $this->get(route('products.index'))
            ->assertOk()
            ->assertSeeText('Atolye Secimi');
    }

    private function seedStorefrontContent(): array
    {
        File::ensureDirectoryExists(storage_path('app/public/products'));
        File::put(storage_path('app/public/products/buket-no-1.jpg'), 'seed-image');

        $category = Category::create([
            'name' => ['tr' => 'Buketler'],
            'slug' => 'cicek-buketleri',
            'description' => ['tr' => 'Sezonun buket seckileri'],
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $product = Product::create([
            'name' => ['tr' => 'Buket No. 1', 'en' => 'Bouquet No. 1'],
            'slug' => 'buket-no-1',
            'short_description' => ['tr' => 'Tasarlanmis butik buket'],
            'description' => ['tr' => '<p>Katmanli cicek ve yesillik kompozisyonu.</p>'],
            'price' => 890,
            'stock_status' => 'in_stock',
            'status' => 'active',
            'is_featured' => true,
            'is_new' => true,
            'view_count' => 12,
            'delivery_note' => ['tr' => 'Ayni gun teslimat.'],
            'product_highlights' => [
                'tr' => [
                    ['icon' => 'sparkles', 'title' => 'Butik Hazirlik', 'body' => 'Atolyede hazirlanir.', 'sort_order' => 1],
                    ['icon' => 'truck', 'title' => 'Teslimat', 'body' => 'Adres teyidi yapilir.', 'sort_order' => 2],
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
            'name' => ['tr' => 'Cicek Rehberi'],
            'slug' => 'cicek-rehberi',
            'sort_order' => 1,
        ]);

        $author = User::factory()->create([
            'name' => 'Editor Rose Garden',
            'email' => 'editor@example.com',
        ]);

        $post = BlogPost::create([
            'title' => ['tr' => 'Bahar icin cicek onerileri'],
            'slug' => 'bahar-icin-cicek-onerileri',
            'excerpt' => ['tr' => 'Ev ve hediye icin cicek secimi notlari.'],
            'content' => ['tr' => '<p>Bahar sezonunda buket secimini kolaylastiran notlar.</p>'],
            'featured_image' => 'images/blog/flower-care.svg',
            'blog_category_id' => $blogCategory->id,
            'author_id' => $author->id,
            'status' => 'published',
            'published_at' => now()->subDay(),
        ]);

        $post->products()->attach($product);

        $page = Page::create([
            'title' => ['tr' => 'Rose Garden Hakkinda'],
            'slug' => 'rose-garden-hakkinda',
            'content' => ['tr' => '<p>Tasarlanmis butik cicek deneyimi.</p>'],
            'meta_title' => ['tr' => 'Rose Garden Hakkinda'],
            'meta_description' => ['tr' => 'Butik florist hikayemiz.'],
            'is_published' => true,
            'sort_order' => 1,
        ]);

        $occasion = SpecialOccasion::create([
            'name' => ['tr' => 'Anneler Gunu'],
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
