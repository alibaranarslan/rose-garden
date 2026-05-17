<?php

namespace Tests\Feature\Admin;

use App\Filament\Pages\PaymentSettings;
use App\Filament\Resources\CouponResource\Pages\CreateCoupon;
use App\Filament\Resources\DeliveryTimeSlotResource\Pages\CreateDeliveryTimeSlot;
use App\Filament\Resources\DeliveryTimeSlotResource\Pages\EditDeliveryTimeSlot;
use App\Filament\Resources\DeliveryZoneResource\Pages\CreateDeliveryZone;
use App\Filament\Resources\DeliveryZoneResource\Pages\EditDeliveryZone;
use App\Filament\Resources\OrderResource\Pages\EditOrder;
use App\Filament\Resources\OrderResource\Pages\ListOrders;
use App\Livewire\CartPage;
use App\Livewire\CheckoutWizard;
use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\DeliveryTimeSlot;
use App\Models\DeliveryZone;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Contracts\Notifications\Dispatcher as NotificationDispatcher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use RuntimeException;
use Tests\TestCase;

class AdminCommerceOperationsReflectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_created_coupon_affects_cart_and_checkout_order_totals(): void
    {
        Notification::fake();

        $admin = $this->adminUser();

        Livewire::actingAs($admin)
            ->test(CreateCoupon::class)
            ->set('data.code', 'ADMIN75')
            ->set('data.type', 'fixed_amount')
            ->set('data.value', 75)
            ->set('data.min_order_amount', 100)
            ->set('data.max_uses', 10)
            ->set('data.max_uses_per_user', 1)
            ->set('data.is_active', true)
            ->call('create')
            ->assertHasNoErrors();

        $coupon = Coupon::query()->where('code', 'ADMIN75')->firstOrFail();
        $product = $this->product('phase4-coupon-product', 400);
        $zone = $this->deliveryZone(40);
        $slot = $this->deliveryTimeSlot();

        CartItem::query()->create([
            'user_id' => $admin->id,
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        Livewire::test(CartPage::class)
            ->set('couponCode', 'ADMIN75')
            ->call('applyCoupon')
            ->assertSet('couponMessage', 'Kupon uygulandı.');

        $this->assertSame($coupon->id, session('cart_coupon_id'));

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
            ->set('paymentMethod', 'bank_transfer')
            ->set('distanceSalesAgreement', true)
            ->set('kvkkAcknowledgement', true)
            ->set('explicitConsent', true)
            ->call('createOrder')
            ->assertHasNoErrors();

        $order = Order::query()->firstOrFail();

        $this->assertSame($coupon->id, $order->coupon_id);
        $this->assertSame('75.00', $order->discount_amount);
        $this->assertSame('365.00', $order->total);
        $this->assertSame(1, $coupon->fresh()->used_count);
    }

    public function test_coupon_rejects_invalid_financial_rules(): void
    {
        $admin = $this->adminUser();

        Livewire::actingAs($admin)
            ->test(CreateCoupon::class)
            ->set('data.code', 'BADFINANCE')
            ->set('data.type', 'percentage')
            ->set('data.value', 125)
            ->set('data.min_order_amount', -1)
            ->set('data.max_uses', -5)
            ->set('data.max_uses_per_user', 0)
            ->set('data.starts_at', now()->addDay()->format('Y-m-d H:i:s'))
            ->set('data.expires_at', now()->subDay()->format('Y-m-d H:i:s'))
            ->set('data.is_active', true)
            ->call('create')
            ->assertHasErrors([
                'data.value',
                'data.min_order_amount',
                'data.max_uses',
                'data.max_uses_per_user',
                'data.expires_at',
            ]);

        $this->assertDatabaseMissing('coupons', [
            'code' => 'BADFINANCE',
        ]);
    }

    public function test_free_delivery_coupon_normalizes_value_to_zero(): void
    {
        $admin = $this->adminUser();

        Livewire::actingAs($admin)
            ->test(CreateCoupon::class)
            ->set('data.code', 'free-ship')
            ->set('data.type', 'free_delivery')
            ->set('data.value', 999)
            ->set('data.min_order_amount', 100)
            ->set('data.max_uses_per_user', 1)
            ->set('data.is_active', true)
            ->call('create')
            ->assertHasNoErrors();

        $coupon = Coupon::query()->where('code', 'FREE-SHIP')->firstOrFail();

        $this->assertSame('0.00', $coupon->value);
        $this->assertSame(0.0, $coupon->calculateDiscount(500));
    }

    public function test_admin_delivery_zone_and_slot_changes_reflect_in_checkout(): void
    {
        $admin = $this->adminUser();

        Livewire::actingAs($admin)
            ->test(CreateDeliveryZone::class)
            ->set('data.name', 'Admin Phase4 Zone')
            ->set('data.fee', 33)
            ->set('data.min_free_amount', 900)
            ->set('data.cutoff_time', '22:00')
            ->set('data.is_active', true)
            ->set('data.sort_order', 1)
            ->call('create')
            ->assertHasNoErrors();

        Livewire::actingAs($admin)
            ->test(CreateDeliveryTimeSlot::class)
            ->set('data.label', '18:00 - 20:00 Admin')
            ->set('data.start_time', '18:00')
            ->set('data.end_time', '20:00')
            ->set('data.is_active', true)
            ->set('data.sort_order', 1)
            ->call('create')
            ->assertHasNoErrors();

        $zone = DeliveryZone::query()->where('name', 'Admin Phase4 Zone')->firstOrFail();
        $slot = DeliveryTimeSlot::query()->where('label', '18:00 - 20:00 Admin')->firstOrFail();

        Livewire::test(CheckoutWizard::class)
            ->set('step', 2)
            ->assertSee('Admin Phase4 Zone')
            ->assertSee('18:00 - 20:00 Admin');

        Livewire::actingAs($admin)
            ->test(EditDeliveryZone::class, ['record' => $zone->getRouteKey()])
            ->set('data.is_active', false)
            ->call('save')
            ->assertHasNoErrors();

        Livewire::actingAs($admin)
            ->test(EditDeliveryTimeSlot::class, ['record' => $slot->getRouteKey()])
            ->set('data.is_active', false)
            ->call('save')
            ->assertHasNoErrors();

        Livewire::test(CheckoutWizard::class)
            ->set('step', 2)
            ->assertDontSee('Admin Phase4 Zone')
            ->assertDontSee('18:00 - 20:00 Admin')
            ->assertSee('Teslimat seçenekleri hazır değil.');
    }

    public function test_delivery_zone_rejects_negative_operational_values(): void
    {
        $admin = $this->adminUser();

        Livewire::actingAs($admin)
            ->test(CreateDeliveryZone::class)
            ->set('data.name', 'Negatif Teslimat')
            ->set('data.fee', -10)
            ->set('data.min_free_amount', -1)
            ->set('data.cutoff_time', '18:00')
            ->set('data.is_active', true)
            ->set('data.sort_order', -5)
            ->call('create')
            ->assertHasErrors([
                'data.fee',
                'data.min_free_amount',
                'data.sort_order',
            ]);

        $this->assertDatabaseMissing('delivery_zones', [
            'name' => 'Negatif Teslimat',
        ]);
    }

    public function test_delivery_time_slot_rejects_reversed_time_range(): void
    {
        $admin = $this->adminUser();

        Livewire::actingAs($admin)
            ->test(CreateDeliveryTimeSlot::class)
            ->set('data.label', 'Ters Saat')
            ->set('data.start_time', '20:00')
            ->set('data.end_time', '18:00')
            ->set('data.is_active', true)
            ->set('data.sort_order', 1)
            ->call('create')
            ->assertHasErrors(['data.start_time']);

        $this->assertDatabaseMissing('delivery_time_slots', [
            'label' => 'Ters Saat',
        ]);
    }

    public function test_payment_settings_reflect_bank_details_in_checkout(): void
    {
        $admin = $this->adminUser();

        Livewire::actingAs($admin)
            ->test(PaymentSettings::class)
            ->set('data.paytr_merchant_id', '')
            ->set('data.paytr_merchant_key', '')
            ->set('data.paytr_merchant_salt', '')
            ->set('data.bank_name', 'Phase4 Bank')
            ->set('data.bank_iban', 'tr12 0000 0000 0000 0000 0000 01')
            ->set('data.bank_account_holder', 'Rose Garden Phase4')
            ->set('data.transfer_timeout_hours', 48)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame('TR120000000000000000000001', Setting::get('payment', 'bank_iban'));
        $this->assertNotSame('', (string) Setting::get('system', 'storefront_content_version', ''));

        Livewire::test(CheckoutWizard::class)
            ->set('step', 3)
            ->assertSee('Phase4 Bank')
            ->assertSee('TR120000000000000000000001')
            ->assertSee('Rose Garden Phase4')
            ->assertSee('48 saat');
    }

    public function test_payment_settings_reject_partial_or_invalid_bank_details(): void
    {
        $admin = $this->adminUser();

        Livewire::actingAs($admin)
            ->test(PaymentSettings::class)
            ->set('data.bank_name', 'Eksik Bank')
            ->set('data.bank_iban', 'BAD-IBAN')
            ->set('data.bank_account_holder', '')
            ->call('save')
            ->assertHasErrors(['data.bank_iban', 'data.bank_account_holder']);

        $this->assertNull(Setting::get('payment', 'bank_name'));
        $this->assertNull(Setting::get('payment', 'bank_iban'));
        $this->assertNull(Setting::get('payment', 'bank_account_holder'));
    }

    public function test_payment_settings_reject_partial_paytr_credentials(): void
    {
        $admin = $this->adminUser();

        Livewire::actingAs($admin)
            ->test(PaymentSettings::class)
            ->set('data.paytr_merchant_id', 'merchant')
            ->set('data.paytr_merchant_key', '')
            ->set('data.paytr_merchant_salt', '')
            ->call('save')
            ->assertHasErrors(['data.paytr_merchant_key', 'data.paytr_merchant_salt']);

        $this->assertNull(Setting::get('payment', 'paytr_merchant_id'));
        $this->assertNull(Setting::get('payment', 'paytr_merchant_key'));
        $this->assertNull(Setting::get('payment', 'paytr_merchant_salt'));
    }

    public function test_admin_order_status_edit_records_operational_history(): void
    {
        Notification::fake();

        $admin = $this->adminUser();
        $order = Order::query()->create([
            'status' => 'paid',
            'subtotal' => 500,
            'delivery_fee' => 0,
            'discount_amount' => 0,
            'loyalty_points_used' => 0,
            'total' => 500,
            'payment_method' => 'bank_transfer',
            'sender_name' => 'Ali Test',
            'sender_phone' => '05000000000',
            'sender_email' => 'ali@example.com',
            'recipient_name' => 'Ayse Test',
            'recipient_phone' => '05000000001',
            'recipient_address' => 'Ataturk Bulvari No: 1',
            'recipient_district' => 'Merkez',
            'delivery_date' => now()->addDay()->toDateString(),
        ]);

        Livewire::actingAs($admin)
            ->test(EditOrder::class, ['record' => $order->getRouteKey()])
            ->set('data.status', 'preparing')
            ->set('data.admin_note', 'Phase4 admin note')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame('preparing', $order->fresh()->status);
        $this->assertDatabaseHas('order_status_history', [
            'order_id' => $order->id,
            'status' => 'preparing',
            'changed_by' => $admin->id,
        ]);
        $this->assertSame(1, OrderStatusHistory::query()->where('order_id', $order->id)->count());
    }

    public function test_bank_transfer_approval_updates_payment_even_when_customer_notification_fails(): void
    {
        $admin = $this->adminUser();
        $customer = User::factory()->create(['is_admin' => false, 'is_active' => true]);
        $order = Order::withoutEvents(fn () => Order::query()->create([
            'order_number' => 'RG-PHASE4-APPROVE',
            'user_id' => $customer->id,
            'status' => 'awaiting_payment',
            'subtotal' => 500,
            'delivery_fee' => 0,
            'discount_amount' => 0,
            'loyalty_points_used' => 0,
            'total' => 500,
            'payment_method' => 'bank_transfer',
            'sender_name' => 'Ali Test',
            'sender_phone' => '05000000000',
            'sender_email' => $customer->email,
            'recipient_name' => 'Ayse Test',
            'recipient_phone' => '05000000001',
            'recipient_address' => 'Ataturk Bulvari No: 1',
            'recipient_district' => 'Merkez',
            'delivery_date' => now()->addDay()->toDateString(),
        ]));
        Payment::query()->create([
            'order_id' => $order->id,
            'payment_method' => 'bank_transfer',
            'amount' => 500,
            'status' => 'pending',
        ]);

        $this->mock(NotificationDispatcher::class, function ($mock): void {
            $mock->shouldReceive('send')->andThrow(new RuntimeException('mail transport down'));
        });

        Livewire::actingAs($admin)
            ->test(ListOrders::class)
            ->callTableAction('approve_payment', $order)
            ->assertHasNoTableActionErrors();

        $this->assertSame('paid', $order->fresh()->status);
        $this->assertSame('completed', $order->payment()->first()->status);
        $this->assertDatabaseHas('order_status_history', [
            'order_id' => $order->id,
            'status' => 'paid',
            'changed_by' => $admin->id,
        ]);
    }

    public function test_bank_transfer_approval_does_not_mark_order_paid_without_pending_payment(): void
    {
        $admin = $this->adminUser();
        $order = Order::withoutEvents(fn () => Order::query()->create([
            'order_number' => 'RG-PHASE4-NO-PAYMENT',
            'status' => 'awaiting_payment',
            'subtotal' => 500,
            'delivery_fee' => 0,
            'discount_amount' => 0,
            'loyalty_points_used' => 0,
            'total' => 500,
            'payment_method' => 'bank_transfer',
            'sender_name' => 'Ali Test',
            'sender_phone' => '05000000000',
            'sender_email' => 'ali@example.com',
            'recipient_name' => 'Ayse Test',
            'recipient_phone' => '05000000001',
            'recipient_address' => 'Ataturk Bulvari No: 1',
            'recipient_district' => 'Merkez',
            'delivery_date' => now()->addDay()->toDateString(),
        ]));

        Livewire::actingAs($admin)
            ->test(ListOrders::class)
            ->callTableAction('approve_payment', $order)
            ->assertHasNoTableActionErrors();

        $this->assertSame('awaiting_payment', $order->fresh()->status);
        $this->assertSame(0, OrderStatusHistory::query()->where('order_id', $order->id)->count());
    }

    private function adminUser(): User
    {
        return User::factory()->create([
            'is_admin' => true,
            'is_active' => true,
        ]);
    }

    private function product(string $slug, float $price): Product
    {
        return Product::query()->create([
            'name' => ['tr' => 'Phase4 Product'],
            'slug' => $slug,
            'price' => $price,
            'stock_status' => 'in_stock',
            'status' => 'active',
        ]);
    }

    private function deliveryZone(float $fee): DeliveryZone
    {
        return DeliveryZone::query()->create([
            'name' => 'Phase4 Zone',
            'fee' => $fee,
            'min_free_amount' => null,
            'cutoff_time' => '22:00',
            'is_active' => true,
            'sort_order' => 1,
        ]);
    }

    private function deliveryTimeSlot(): DeliveryTimeSlot
    {
        return DeliveryTimeSlot::query()->create([
            'label' => '18:00 - 20:00',
            'start_time' => '18:00',
            'end_time' => '20:00',
            'is_active' => true,
            'sort_order' => 1,
        ]);
    }
}
