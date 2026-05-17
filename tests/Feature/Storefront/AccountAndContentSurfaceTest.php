<?php

namespace Tests\Feature\Storefront;

use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Models\Page;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\SpecialOccasion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountAndContentSurfaceTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_tracking_and_account_surfaces_render_expected_data(): void
    {
        $user = User::factory()->create();

        $product = Product::create([
            'name' => ['tr' => 'Takip Ürünü'],
            'slug' => 'takip-urunu',
            'price' => 350,
            'stock_status' => 'in_stock',
            'status' => 'active',
        ]);

        ProductImage::create([
            'product_id' => $product->id,
            'image_path' => 'images/product-placeholder.svg',
            'is_primary' => true,
            'sort_order' => 1,
        ]);

        $order = Order::create([
            'user_id' => $user->id,
            'status' => 'on_the_way',
            'subtotal' => 350,
            'delivery_fee' => 0,
            'discount_amount' => 0,
            'loyalty_points_used' => 0,
            'total' => 350,
            'payment_method' => 'bank_transfer',
            'sender_name' => 'Ali Test',
            'sender_phone' => '05000000000',
            'sender_email' => $user->email,
            'recipient_name' => 'Ayşe Test',
            'recipient_phone' => '05000000001',
            'recipient_address' => 'Adres',
            'recipient_district' => 'Merkez',
            'delivery_date' => now()->addDay()->toDateString(),
        ]);

        OrderStatusHistory::create([
            'order_id' => $order->id,
            'status' => 'paid',
            'note' => 'Ödeme alındı',
        ]);

        OrderStatusHistory::create([
            'order_id' => $order->id,
            'status' => 'on_the_way',
            'note' => 'Kurye teslimata çıktı',
        ]);

        $this->post(route('order.track.submit'), [
            'order_number' => $order->order_number,
        ])->assertOk()
            ->assertSeeText('Yolda')
            ->assertSeeText('Durum ge')
            ->assertSeeText('Kurye teslimata çıktı');

        $this->actingAs($user)
            ->get(route('account.orders'))
            ->assertOk()
            ->assertSeeText($order->order_number);

        $this->actingAs($user)
            ->get(route('account.order.show', ['orderNumber' => $order->order_number]))
            ->assertOk()
            ->assertSeeText('Tekrar sipari')
            ->assertSeeText($order->order_number);
    }

    public function test_google_oauth_button_is_conditionally_rendered_on_auth_pages(): void
    {
        config()->set('services.google.client_id', null);

        $this->get(route('login'))
            ->assertOk()
            ->assertDontSeeText('Google ile');

        $this->get(route('register'))
            ->assertOk()
            ->assertDontSeeText('Google ile');

        config()->set('services.google.client_id', 'google-client-id');

        $this->get(route('login'))
            ->assertOk()
            ->assertSeeText('Google ile');

        $this->get(route('register'))
            ->assertOk()
            ->assertSeeText('Google ile');
    }

    public function test_blog_and_static_pages_render_when_published(): void
    {
        $category = BlogCategory::create([
            'name' => ['tr' => 'Bakım'],
            'slug' => 'bakim',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $author = User::factory()->create();

        $post = BlogPost::create([
            'title' => ['tr' => 'Blog Yazısı'],
            'slug' => 'blog-yazisi',
            'excerpt' => ['tr' => 'Kısa özet'],
            'content' => ['tr' => '<p>Detay içerik</p>'],
            'status' => 'published',
            'published_at' => now(),
            'category_id' => $category->id,
            'author_id' => $author->id,
        ]);

        foreach ([
            'gizlilik-politikasi' => 'Gizlilik Politikası',
            'kvkk-aydinlatma' => 'KVKK',
            'mesafeli-satis-sozlesmesi' => 'Mesafeli Satış Sözleşmesi',
            'iade-iptal' => 'İade',
            'cerez-politikasi' => 'Çerez',
        ] as $slug => $title) {
            Page::create([
                'title' => $title,
                'slug' => $slug,
                'content' => '<p>'.$title.' içeriği</p>',
                'status' => 'published',
            ]);
        }

        $this->get(route('blog.index'))
            ->assertOk()
            ->assertSeeText('Blog Yazısı');

        $this->get(route('blog.show', ['slug' => $post->slug]))
            ->assertOk()
            ->assertSeeText('Blog Yazısı');

        $this->get(route('contact'))->assertOk();
        $this->get(route('faq'))->assertOk();
        $this->get(route('page.show', ['slug' => 'gizlilik-politikasi']))->assertOk();
        $this->get(route('page.show', ['slug' => 'kvkk-aydinlatma']))->assertOk();
        $this->get(route('page.show', ['slug' => 'mesafeli-satis-sozlesmesi']))->assertOk();
        $this->get(route('page.show', ['slug' => 'iade-iptal']))->assertOk();
        $this->get(route('page.show', ['slug' => 'cerez-politikasi']))->assertOk();
    }

    public function test_special_occasion_links_stay_locale_prefixed(): void
    {
        $occasion = SpecialOccasion::create([
            'name' => [
                'tr' => 'Anneler Gunu',
                'en' => 'Mothers Day',
                'ku' => 'Roja Dayikan',
            ],
            'slug' => 'anneler-gunu',
            'date_month' => 5,
            'date_day' => 12,
            'is_active' => true,
        ]);

        $this->get('/en/ozel-gunler')
            ->assertOk()
            ->assertSee('/en/ozel-gunler/'.$occasion->slug, false)
            ->assertDontSee('href="/ozel-gunler/'.$occasion->slug.'"', false);
    }
}
