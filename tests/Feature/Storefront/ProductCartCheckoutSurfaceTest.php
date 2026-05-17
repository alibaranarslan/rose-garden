<?php

namespace Tests\Feature\Storefront;

use App\Livewire\AddToCart;
use App\Livewire\CartIcon;
use App\Livewire\CheckoutWizard;
use App\Livewire\FavoriteToggle;
use App\Livewire\CartPage;
use App\Models\Address;
use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\DeliveryTimeSlot;
use App\Models\DeliveryZone;
use App\Models\Favorite;
use App\Models\LoyaltyPoint;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\Setting;
use App\Models\User;
use App\Services\PaytrService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Mockery;
use Tests\TestCase;

class ProductCartCheckoutSurfaceTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_detail_page_renders_gallery_actions_and_favorite_toggle(): void
    {
        $product = $this->makeStorefrontProduct('zarif-gul-buketi', 890);

        ProductVariant::create([
            'product_id' => $product->id,
            'name' => ['tr' => 'Standart'],
            'price' => 890,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $response = $this->get(route('products.show', ['slug' => $product->slug]));

        $response->assertOk()
            ->assertSee($product->name)
            ->assertSee('api.whatsapp.com/send')
            ->assertSee('Toggle favorite')
            ->assertSee('Sepete')
            ->assertSee('Standart');
    }

    public function test_add_to_cart_uses_selected_variant_quantity_and_card_message(): void
    {
        $product = $this->makeStorefrontProduct('premium-orkide', 1250);

        $firstVariant = ProductVariant::create([
            'product_id' => $product->id,
            'name' => ['tr' => 'Standart'],
            'price' => 1250,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $secondVariant = ProductVariant::create([
            'product_id' => $product->id,
            'name' => ['tr' => 'Deluxe'],
            'price' => 1490,
            'sale_price' => 1390,
            'is_active' => true,
            'sort_order' => 2,
        ]);

        Livewire::test(AddToCart::class, ['productId' => $product->id, 'layout' => 'detail'])
            ->assertSet('variantId', $firstVariant->id)
            ->set('variantId', $secondVariant->id)
            ->set('quantity', 3)
            ->set('cardMessage', 'Kutlu olsun')
            ->call('addToCart');

        $this->assertDatabaseHas('cart_items', [
            'product_id' => $product->id,
            'variant_id' => $secondVariant->id,
            'quantity' => 3,
            'card_message' => 'Kutlu olsun',
        ]);
    }

    public function test_cart_icon_refreshes_after_cart_updates_in_the_same_session(): void
    {
        session(['cart_session_id' => 'cart-feedback-session']);

        $product = $this->makeStorefrontProduct('cart-feedback-rose', 620);

        $cartIcon = Livewire::test(CartIcon::class)
            ->assertSet('count', 0);

        Livewire::test(AddToCart::class, ['productId' => $product->id, 'layout' => 'detail'])
            ->set('quantity', 2)
            ->call('addToCart');

        $cartIcon->call('refreshCount')
            ->assertSet('count', 2);
    }

    public function test_favorite_toggle_redirects_guest_and_toggles_for_authenticated_user(): void
    {
        $product = $this->makeStorefrontProduct('favori-buket', 740);

        Livewire::test(FavoriteToggle::class, ['productId' => $product->id])
            ->call('toggle')
            ->assertRedirect(route('login'));

        $user = User::factory()->create();
        $this->actingAs($user);

        Livewire::test(FavoriteToggle::class, ['productId' => $product->id])
            ->call('toggle')
            ->assertSet('isFavorited', true)
            ->call('toggle')
            ->assertSet('isFavorited', false);

        $this->assertSame(0, Favorite::query()->count());
    }

    public function test_cart_coupon_flow_updates_discount_and_total(): void
    {
        $product = Product::create([
            'name' => ['tr' => 'Kuponlu Ürün'],
            'slug' => 'kuponlu-urun',
            'price' => 1000,
            'stock_status' => 'in_stock',
            'status' => 'active',
        ]);

        session(['cart_session_id' => 'coupon-cart-session']);

        CartItem::create([
            'session_id' => 'coupon-cart-session',
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $coupon = Coupon::create([
            'code' => 'INDIRIM10',
            'type' => 'percentage',
            'value' => 10,
            'min_order_amount' => 500,
            'is_active' => true,
            'used_count' => 0,
        ]);

        Livewire::test(CartPage::class)
            ->set('couponCode', $coupon->code)
            ->call('applyCoupon')
            ->assertSet('couponMessage', 'Kupon uygulandı.');

        $this->assertSame($coupon->id, session('cart_coupon_id'));
    }

    public function test_checkout_combines_coupon_and_loyalty_points_and_allows_saved_address_selection(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $defaultAddress = Address::create([
            'user_id' => $user->id,
            'label' => 'Ev',
            'recipient_name' => 'İlk Alıcı',
            'recipient_phone' => '05000000001',
            'address_line' => 'İlk adres',
            'district' => 'Merkez',
            'city' => 'Adıyaman',
            'is_default' => true,
        ]);

        $secondAddress = Address::create([
            'user_id' => $user->id,
            'label' => 'Ofis',
            'recipient_name' => 'İkinci Alıcı',
            'recipient_phone' => '05000000002',
            'address_line' => 'İkinci adres',
            'district' => 'Kahta',
            'city' => 'Adıyaman',
            'is_default' => false,
        ]);

        LoyaltyPoint::create([
            'user_id' => $user->id,
            'balance' => 80,
            'total_earned' => 80,
            'total_spent' => 0,
        ]);

        $zone = DeliveryZone::create([
            'name' => 'Merkez',
            'fee' => 25,
            'min_free_amount' => 0,
            'cutoff_time' => '20:00',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $slot = DeliveryTimeSlot::create([
            'label' => '12:00 - 15:00',
            'start_time' => '12:00',
            'end_time' => '15:00',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $product = Product::create([
            'name' => ['tr' => 'Birleşik İndirim Ürünü'],
            'slug' => 'birlesik-indirim-urunu',
            'price' => 500,
            'stock_status' => 'in_stock',
            'status' => 'active',
        ]);

        CartItem::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $coupon = Coupon::create([
            'code' => 'SABIT50',
            'type' => 'fixed_amount',
            'value' => 50,
            'min_order_amount' => 100,
            'is_active' => true,
            'used_count' => 0,
        ]);

        session(['cart_coupon_id' => $coupon->id]);

        Livewire::test(CheckoutWizard::class)
            ->assertSet('savedAddressId', $defaultAddress->id)
            ->set('savedAddressId', $secondAddress->id)
            ->assertSet('recipientName', 'İkinci Alıcı')
            ->assertSet('recipientDistrict', 'Kahta')
            ->set('step', 3)
            ->set('senderName', 'Ali Test')
            ->set('senderPhone', '05000000000')
            ->set('senderEmail', $user->email)
            ->set('recipientName', 'İkinci Alıcı')
            ->set('recipientPhone', '05000000002')
            ->set('recipientAddress', 'İkinci adres')
            ->set('recipientDistrict', 'Kahta')
            ->set('deliveryDate', now()->addDay()->toDateString())
            ->set('deliveryTimeSlotId', $slot->id)
            ->set('deliveryZoneId', $zone->id)
            ->set('paymentMethod', 'bank_transfer')
            ->set('useLoyaltyPoints', true)
            ->set('loyaltyPointsToUse', 20)
            ->set('distanceSalesAgreement', true)
            ->set('kvkkAcknowledgement', true)
            ->set('explicitConsent', true)
            ->call('createOrder')
            ->assertHasNoErrors();

        $order = Order::query()->firstOrFail();

        $this->assertSame('50.00', $order->discount_amount);
        $this->assertSame('20.00', $order->loyalty_points_used);
        $this->assertSame('430.00', $order->total);
    }

    public function test_guest_checkout_shows_loyalty_teaser_with_estimate(): void
    {
        session(['cart_session_id' => 'guest-loyalty-teaser-session']);

        $product = Product::create([
            'name' => ['tr' => 'Teaser Urunu'],
            'slug' => 'teaser-urunu',
            'price' => 500,
            'stock_status' => 'in_stock',
            'status' => 'active',
        ]);

        CartItem::create([
            'session_id' => 'guest-loyalty-teaser-session',
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        Livewire::test(CheckoutWizard::class)
            ->set('step', 3)
            ->assertSeeText('Üye ol, puan biriktir')
            ->assertSeeText('yaklaşık 25 Paraçiçek Puan');
    }

    public function test_credit_card_payment_page_renders_when_paytr_is_configured(): void
    {
        Setting::set('payment', 'paytr_merchant_id', 'merchant');
        Setting::set('payment', 'paytr_merchant_key', 'key');
        Setting::set('payment', 'paytr_merchant_salt', 'salt');

        $order = Order::create([
            'status' => 'pending',
            'subtotal' => 1000,
            'delivery_fee' => 0,
            'discount_amount' => 0,
            'loyalty_points_used' => 0,
            'total' => 1000,
            'payment_method' => 'credit_card',
            'sender_name' => 'Ali Test',
            'sender_phone' => '05000000000',
            'sender_email' => 'ali@example.com',
            'recipient_name' => 'Ayşe Test',
            'recipient_phone' => '05000000001',
            'recipient_address' => 'Adres',
            'recipient_district' => 'Merkez',
            'delivery_date' => now()->addDay()->toDateString(),
            'ip_address' => '127.0.0.1',
        ]);

        session(['last_order_number' => $order->order_number]);

        $paytr = Mockery::mock(PaytrService::class);
        $paytr->shouldReceive('createToken')->once()->andReturn('token-123');
        $paytr->shouldReceive('getIframeUrl')->once()->with('token-123')->andReturn('https://www.paytr.com/odeme/guvenli/token-123');
        $this->app->instance(PaytrService::class, $paytr);

        $this->get(route('checkout.payment', ['order' => $order->id]))
            ->assertOk()
            ->assertSee('token-123')
            ->assertSee('paytr.com/odeme/guvenli');
    }

    public function test_checkout_validation_messages_follow_turkish_locale(): void
    {
        app()->setLocale('tr');

        DeliveryZone::create([
            'name' => 'Merkez',
            'fee' => 25,
            'min_free_amount' => 0,
            'cutoff_time' => '20:00',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        DeliveryTimeSlot::create([
            'label' => '12:00 - 15:00',
            'start_time' => '12:00',
            'end_time' => '15:00',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        Livewire::test(CheckoutWizard::class)
            ->set('step', 3)
            ->set('paymentMethod', 'bank_transfer')
            ->call('createOrder')
            ->assertHasErrors(['distanceSalesAgreement', 'kvkkAcknowledgement', 'explicitConsent'])
            ->assertSee('mesafeli satış sözleşmesi alanı kabul edilmelidir')
            ->assertSee('KVKK aydınlatma metni alanı kabul edilmelidir')
            ->assertSee('açık rıza alanı kabul edilmelidir');
    }

    private function makeStorefrontProduct(string $slug, float $price): Product
    {
        $product = Product::create([
            'name' => ['tr' => 'Test Ürünü'],
            'slug' => $slug,
            'price' => $price,
            'sale_price' => $price - 100,
            'stock_status' => 'in_stock',
            'status' => 'active',
            'is_new' => true,
            'short_description' => ['tr' => 'Kısa açıklama'],
            'description' => ['tr' => '<p>Detaylı açıklama</p>'],
            'delivery_note' => ['tr' => 'Aynı gün teslimat mümkündür.'],
        ]);

        ProductImage::create([
            'product_id' => $product->id,
            'image_path' => 'images/product-placeholder.svg',
            'is_primary' => true,
            'sort_order' => 1,
        ]);

        return $product;
    }
}
