<?php

namespace Tests\Feature\Checkout;

use App\Livewire\CheckoutWizard;
use App\Models\CartItem;
use App\Models\DeliveryTimeSlot;
use App\Models\DeliveryZone;
use App\Models\LoyaltyPoint;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Tests\TestCase;

class CheckoutFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_create_a_bank_transfer_order(): void
    {
        Notification::fake();

        $zone = DeliveryZone::create([
            'name' => 'Merkez',
            'fee' => 25,
            'min_free_amount' => 0,
            'cutoff_time' => '20:00',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $slot = DeliveryTimeSlot::create([
            'label' => '09:00 - 12:00',
            'start_time' => '09:00',
            'end_time' => '12:00',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $product = Product::create([
            'name' => ['tr' => 'Kirmizi Gul Buketi'],
            'slug' => 'kirmizi-gul-buketi',
            'price' => 250,
            'stock_status' => 'in_stock',
            'status' => 'active',
        ]);

        session(['cart_session_id' => 'guest-checkout-session']);

        CartItem::create([
            'session_id' => 'guest-checkout-session',
            'product_id' => $product->id,
            'quantity' => 1,
            'card_message' => 'Mutlu yillar',
        ]);

        $component = Livewire::test(CheckoutWizard::class)
            ->set('step', 3)
            ->set('senderName', 'Ali Test')
            ->set('senderPhone', '05000000000')
            ->set('senderEmail', 'ali@example.com')
            ->set('recipientName', 'Ayse Test')
            ->set('recipientPhone', '05000000001')
            ->set('recipientAddress', 'Ataturk Bulvari No: 1')
            ->set('recipientDistrict', 'Merkez')
            ->set('deliveryDate', now()->addDay()->toDateString())
            ->set('deliveryTimeSlotId', $slot->id)
            ->set('deliveryZoneId', $zone->id)
            ->set('paymentMethod', 'bank_transfer')
            ->set('distanceSalesAgreement', true)
            ->set('kvkkAcknowledgement', true)
            ->set('explicitConsent', true)
            ->call('createOrder')
            ->assertHasNoErrors();

        $order = Order::query()->first();

        $this->assertNotNull($order);
        $this->assertSame('bank_transfer', $order->payment_method);
        $this->assertSame('awaiting_payment', $order->status);
        $this->assertSame($order->order_number, session('last_order_number'));
        $component->assertRedirect(route('checkout.success').'?order='.$order->order_number);
    }

    public function test_locale_prefixed_checkout_keeps_prefix_after_bank_transfer_order(): void
    {
        Notification::fake();

        $zone = DeliveryZone::create([
            'name' => 'Merkez',
            'fee' => 25,
            'min_free_amount' => 0,
            'cutoff_time' => '20:00',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $slot = DeliveryTimeSlot::create([
            'label' => '09:00 - 12:00',
            'start_time' => '09:00',
            'end_time' => '12:00',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $product = Product::create([
            'name' => ['tr' => 'Kirmizi Gul Buketi'],
            'slug' => 'kirmizi-gul-buketi-locale',
            'price' => 250,
            'stock_status' => 'in_stock',
            'status' => 'active',
        ]);

        session(['cart_session_id' => 'guest-prefixed-checkout-session']);

        CartItem::create([
            'session_id' => 'guest-prefixed-checkout-session',
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $component = Livewire::test(CheckoutWizard::class)
            ->set('prefixLocaleRoutes', true)
            ->set('step', 3)
            ->set('senderName', 'Ali Test')
            ->set('senderPhone', '05000000000')
            ->set('senderEmail', 'ali@example.com')
            ->set('recipientName', 'Ayse Test')
            ->set('recipientPhone', '05000000001')
            ->set('recipientAddress', 'Ataturk Bulvari No: 1')
            ->set('recipientDistrict', 'Merkez')
            ->set('deliveryDate', now()->addDay()->toDateString())
            ->set('deliveryTimeSlotId', $slot->id)
            ->set('deliveryZoneId', $zone->id)
            ->set('paymentMethod', 'bank_transfer')
            ->set('distanceSalesAgreement', true)
            ->set('kvkkAcknowledgement', true)
            ->set('explicitConsent', true)
            ->call('createOrder')
            ->assertHasNoErrors();

        $order = Order::query()->firstOrFail();

        $component->assertRedirect(url('/tr/odeme/basarili').'?order='.$order->order_number);
    }

    public function test_checkout_progresses_through_steps_with_valid_input(): void
    {
        Notification::fake();

        $zone = DeliveryZone::create([
            'name' => 'Merkez',
            'fee' => 25,
            'min_free_amount' => 0,
            'cutoff_time' => '20:00',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $slot = DeliveryTimeSlot::create([
            'label' => '09:00 - 12:00',
            'start_time' => '09:00',
            'end_time' => '12:00',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $product = Product::create([
            'name' => ['tr' => 'Kirmizi Gul Buketi'],
            'slug' => 'kirmizi-gul-buketi',
            'price' => 250,
            'stock_status' => 'in_stock',
            'status' => 'active',
        ]);

        session(['cart_session_id' => 'guest-checkout-progress']);

        CartItem::create([
            'session_id' => 'guest-checkout-progress',
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        Livewire::test(CheckoutWizard::class)
            ->set('senderName', 'Ali Test')
            ->set('senderPhone', '05000000000')
            ->set('senderEmail', 'ali@example.com')
            ->set('recipientName', 'Ayse Test')
            ->set('recipientPhone', '05000000001')
            ->set('recipientAddress', 'Ataturk Bulvari No: 1')
            ->set('recipientDistrict', 'Merkez')
            ->call('nextStep')
            ->assertSet('step', 2)
            ->set('deliveryDate', now()->addDay()->toDateString())
            ->set('deliveryTimeSlotId', $slot->id)
            ->set('deliveryZoneId', $zone->id)
            ->call('nextStep')
            ->assertSet('step', 3);
    }

    public function test_checkout_shows_validation_summary_for_step_one_errors(): void
    {
        Notification::fake();

        session(['cart_session_id' => 'guest-checkout-step-one-errors']);

        $product = Product::create([
            'name' => ['tr' => 'Kirmizi Gul Buketi'],
            'slug' => 'kirmizi-gul-buketi-step-one',
            'price' => 250,
            'stock_status' => 'in_stock',
            'status' => 'active',
        ]);

        CartItem::create([
            'session_id' => 'guest-checkout-step-one-errors',
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        Livewire::test(CheckoutWizard::class)
            ->call('nextStep')
            ->assertHasErrors([
                'senderName' => 'required',
                'senderPhone' => 'required',
                'senderEmail' => 'required',
                'recipientName' => 'required',
                'recipientPhone' => 'required',
                'recipientAddress' => 'required',
                'recipientDistrict' => 'required',
            ])
            ->assertSee('Devam etmeden önce şu alanları kontrol edin')
            ->assertSee('Gönderici adı')
            ->assertSee('Alıcı adı');
    }

    public function test_checkout_shows_delivery_configuration_blocker_when_delivery_setup_is_missing(): void
    {
        Notification::fake();

        $product = Product::create([
            'name' => ['tr' => 'Kirmizi Gul Buketi'],
            'slug' => 'kirmizi-gul-buketi-step-two',
            'price' => 250,
            'stock_status' => 'in_stock',
            'status' => 'active',
        ]);

        session(['cart_session_id' => 'guest-checkout-missing-delivery']);

        CartItem::create([
            'session_id' => 'guest-checkout-missing-delivery',
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        Livewire::test(CheckoutWizard::class)
            ->set('senderName', 'Ali Test')
            ->set('senderPhone', '05000000000')
            ->set('senderEmail', 'ali@example.com')
            ->set('recipientName', 'Ayse Test')
            ->set('recipientPhone', '05000000001')
            ->set('recipientAddress', 'Ataturk Bulvari No: 1')
            ->set('recipientDistrict', 'Merkez')
            ->call('nextStep')
            ->assertSet('step', 2)
            ->set('deliveryDate', now()->addDay()->toDateString())
            ->set('deliveryTimeSlotId', 999)
            ->set('deliveryZoneId', 999)
            ->call('nextStep')
            ->assertHasErrors(['deliveryConfiguration'])
            ->assertSee('Teslimat ilerleyemiyor')
            ->assertSee('Aktif teslimat bölgesi tanımlanmalı.')
            ->assertSee('Aktif saat aralığı tanımlanmalı.');
    }

    public function test_credit_card_checkout_is_blocked_when_paytr_is_not_configured(): void
    {
        Notification::fake();

        $zone = DeliveryZone::create([
            'name' => 'Merkez',
            'fee' => 0,
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
            'name' => ['tr' => 'Beyaz Orkide'],
            'slug' => 'beyaz-orkide',
            'price' => 300,
            'stock_status' => 'in_stock',
            'status' => 'active',
        ]);

        session(['cart_session_id' => 'guest-credit-card-session']);

        CartItem::create([
            'session_id' => 'guest-credit-card-session',
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        Livewire::test(CheckoutWizard::class)
            ->set('step', 3)
            ->set('senderName', 'Ali Test')
            ->set('senderPhone', '05000000000')
            ->set('senderEmail', 'ali@example.com')
            ->set('recipientName', 'Ayse Test')
            ->set('recipientPhone', '05000000001')
            ->set('recipientAddress', 'Ataturk Bulvari No: 1')
            ->set('recipientDistrict', 'Merkez')
            ->set('deliveryDate', now()->addDay()->toDateString())
            ->set('deliveryTimeSlotId', $slot->id)
            ->set('deliveryZoneId', $zone->id)
            ->set('paymentMethod', 'credit_card')
            ->set('distanceSalesAgreement', true)
            ->set('kvkkAcknowledgement', true)
            ->set('explicitConsent', true)
            ->call('createOrder')
            ->assertHasErrors(['paymentMethod']);

        $this->assertDatabaseCount('orders', 0);
    }

    public function test_authenticated_user_can_spend_loyalty_points_during_checkout(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $this->actingAs($user);

        LoyaltyPoint::create([
            'user_id' => $user->id,
            'balance' => 60,
            'total_earned' => 60,
            'total_spent' => 0,
        ]);

        $zone = DeliveryZone::create([
            'name' => 'Merkez',
            'fee' => 0,
            'min_free_amount' => 0,
            'cutoff_time' => '20:00',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $slot = DeliveryTimeSlot::create([
            'label' => '15:00 - 18:00',
            'start_time' => '15:00',
            'end_time' => '18:00',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $product = Product::create([
            'name' => ['tr' => 'Premium Buket'],
            'slug' => 'premium-buket',
            'price' => 500,
            'stock_status' => 'in_stock',
            'status' => 'active',
        ]);

        CartItem::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        Livewire::test(CheckoutWizard::class)
            ->set('step', 3)
            ->set('senderName', 'Ali Test')
            ->set('senderPhone', '05000000000')
            ->set('senderEmail', $user->email)
            ->set('recipientName', 'Ayse Test')
            ->set('recipientPhone', '05000000001')
            ->set('recipientAddress', 'Ataturk Bulvari No: 1')
            ->set('recipientDistrict', 'Merkez')
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

        $this->assertSame('20.00', $order->fresh()->loyalty_points_used);
        $this->assertSame('480.00', $order->fresh()->total);
        $this->assertSame('40.00', LoyaltyPoint::where('user_id', $user->id)->value('balance'));
    }
}
