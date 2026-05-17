<?php

namespace Tests\Feature\Storefront;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class LocalizationSurfaceTest extends TestCase
{
    use RefreshDatabase;

    public function test_locale_prefixed_storefront_home_uses_english_shell_copy(): void
    {
        $this->get('/en/')
            ->assertOk()
            ->assertSee('Explore the Collection')
            ->assertSee('English')
            ->assertSee('Türkçe')
            ->assertSee('Kurdî')
            ->assertSee('Search')
            ->assertSee('/tr', false)
            ->assertSee('/ku', false)
            ->assertDontSee('/tr/en', false);
    }

    public function test_query_locale_keeps_storefront_shell_in_selected_language(): void
    {
        $this->get('/?locale=en')
            ->assertOk()
            ->assertSee('Explore the Collection')
            ->assertSee('Search');
    }

    public function test_english_cart_checkout_auth_and_search_surfaces_do_not_fall_back_to_turkish_shell_copy(): void
    {
        $this->get('/en/sepet')
            ->assertOk()
            ->assertSeeText('My Cart')
            ->assertSeeText('Items in your cart')
            ->assertSeeText('Order summary')
            ->assertDontSeeText('Sepetinizdeki ürünler')
            ->assertDontSeeText('Sipariş özeti');

        $this->get('/en/odeme')
            ->assertOk()
            ->assertSeeText('Payment')
            ->assertSeeText('1. Details')
            ->assertSeeText('Sender')
            ->assertDontSeeText('Gönderici')
            ->assertDontSeeText('Teslimat adresi');

        $this->get('/en/giris')
            ->assertOk()
            ->assertSeeText('Log in')
            ->assertDontSeeText('Giriş yap');

        $this->get('/en/arama?q=gul')
            ->assertOk()
            ->assertSeeText('Product search results')
            ->assertDontSeeText('Ürün arama sonuçları');
    }

    public function test_kurdish_cart_checkout_auth_and_search_surfaces_do_not_fall_back_to_turkish_shell_copy(): void
    {
        $this->get('/ku/sepet')
            ->assertOk()
            ->assertSeeText('Selikê Min')
            ->assertSeeText('Berhemên di selika we de')
            ->assertSeeText('Kurteya siparişê')
            ->assertDontSeeText('Sepetinizdeki ürünler')
            ->assertDontSeeText('Sipariş özeti');

        $this->get('/ku/odeme')
            ->assertOk()
            ->assertSeeText('Daxistin')
            ->assertSeeText('1. Agahî')
            ->assertSeeText('Şander')
            ->assertDontSeeText('Gönderici')
            ->assertDontSeeText('Teslimat adresi');

        $this->get('/ku/giris')
            ->assertOk()
            ->assertSeeText('Têkeve')
            ->assertDontSeeText('Giriş yap');

        $this->get('/ku/arama?q=gul')
            ->assertOk()
            ->assertSeeText('Encamên lêgerîna berheman')
            ->assertDontSeeText('Ürün arama sonuçları');
    }

    public function test_storefront_locale_audit_has_complete_static_translation_key_coverage(): void
    {
        $this->artisan('storefront:locale-audit', ['--fail-on-missing' => true])
            ->assertSuccessful();
    }

    public function test_locale_prefixed_shell_links_preserve_selected_locale(): void
    {
        $this->get('/en/')
            ->assertOk()
            ->assertSee('/en/urunler', false)
            ->assertSee('/en/blog', false)
            ->assertSee('/en/iletisim', false)
            ->assertSee('/en/sayfa/gizlilik-politikasi', false)
            ->assertSee('/en/hesabim', false)
            ->assertDontSee('href="/urunler"', false)
            ->assertDontSee('href="/iletisim"', false);
    }

    public function test_locale_prefixed_footer_visual_links_preserve_current_locale(): void
    {
        $product = Product::create([
            'name' => ['tr' => 'Footer Buketi'],
            'slug' => 'footer-buketi',
            'short_description' => ['tr' => 'Footer kart test ürünü'],
            'description' => ['tr' => '<p>Footer kart test ürünü.</p>'],
            'price' => 890,
            'stock_status' => 'in_stock',
            'status' => 'active',
            'is_featured' => true,
        ]);

        File::ensureDirectoryExists(storage_path('app/public/products'));
        File::put(storage_path('app/public/products/footer-buketi.jpg'), 'local-image');

        ProductImage::create([
            'product_id' => $product->id,
            'image_path' => 'storage/products/footer-buketi.jpg',
            'alt_text' => 'Footer Buketi',
            'is_primary' => true,
            'sort_order' => 1,
        ]);

        $this->get('/tr/')
            ->assertOk()
            ->assertSee('/tr/urun/', false)
            ->assertDontSee('href="http://127.0.0.1:8001/urun/', false)
            ->assertDontSee('href="http://localhost/urun/', false)
            ->assertDontSee('href="/urun/', false);
    }

    public function test_guest_account_navigation_redirects_to_locale_prefixed_login(): void
    {
        $this->get('/en/hesabim')
            ->assertRedirect('/en/giris');

        $this->get('/tr/hesabim')
            ->assertRedirect('/tr/giris');
    }

    public function test_default_locale_prefixed_auth_links_preserve_current_prefix(): void
    {
        $this->get('/tr/giris')
            ->assertOk()
            ->assertSee('/tr/kayit', false)
            ->assertSee('/tr/sifremi-unuttum', false)
            ->assertDontSee('x-data="rgGuestLoyaltyPrompt()', false)
            ->assertDontSee('href="http://localhost/kayit"', false)
            ->assertDontSee('href="http://localhost/sifremi-unuttum"', false);

        $this->get('/tr/kayit')
            ->assertOk()
            ->assertSee('/tr/giris', false)
            ->assertDontSee('x-data="rgGuestLoyaltyPrompt()', false);
    }

    public function test_kvkk_consent_surface_is_localized_and_keeps_locale_links(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/en/kvkk-onayi')
            ->assertOk()
            ->assertSee('KVKK Disclosure Approval')
            ->assertSee('/en/kvkk-reddet', false)
            ->assertSee('/en/kvkk-onayi', false);

        $this->actingAs($user)
            ->get('/ku/kvkk-onayi')
            ->assertOk()
            ->assertSee('Qebûlkirina Ronîkirina KVKK');
    }

    public function test_kvkk_validation_message_is_localized_on_turkish_surface(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->from('/tr/kvkk-onayi')
            ->post('/tr/kvkk-onayi', [])
            ->assertRedirect('/tr/kvkk-onayi')
            ->assertSessionHasErrors('kvkk_accepted');

        $this->followingRedirects()
            ->actingAs($user)
            ->from('/tr/kvkk-onayi')
            ->post('/tr/kvkk-onayi', [])
            ->assertSee('KVKK aydınlatma metnini kabul etmeniz zorunludur.');
    }
}
