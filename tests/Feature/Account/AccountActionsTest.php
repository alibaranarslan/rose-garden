<?php

namespace Tests\Feature\Account;

use App\Models\Address;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountActionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_manage_addresses(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->post(route('account.addresses.store'), [
            'label' => 'Ev',
            'recipient_name' => 'Ali Test',
            'recipient_phone' => '05000000000',
            'address_line' => 'Merkez Mahallesi No: 1',
            'district' => 'Merkez',
            'city' => 'Adiyaman',
            'postal_code' => '02000',
            'is_default' => 1,
        ])->assertRedirect();

        $address = Address::query()->firstOrFail();

        $this->assertTrue($address->is_default);

        $this->put(route('account.addresses.update', ['address' => $address->id]), [
            'label' => 'Is',
            'recipient_name' => 'Ali Test',
            'recipient_phone' => '05000000000',
            'address_line' => 'Organize Sanayi',
            'district' => 'Merkez',
            'city' => 'Adiyaman',
            'postal_code' => '02000',
        ])->assertRedirect();

        $this->assertDatabaseHas('addresses', [
            'id' => $address->id,
            'label' => 'Is',
            'address_line' => 'Organize Sanayi',
        ]);

        $this->delete(route('account.addresses.delete', ['address' => $address->id]))
            ->assertRedirect();

        $this->assertDatabaseCount('addresses', 0);
    }

    public function test_reorder_adds_previous_order_items_back_to_cart(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $product = Product::create([
            'name' => ['tr' => 'Bahar Buketi'],
            'slug' => 'bahar-buketi',
            'price' => 350,
            'stock_status' => 'in_stock',
            'status' => 'active',
        ]);

        $order = Order::create([
            'user_id' => $user->id,
            'status' => 'delivered',
            'subtotal' => 350,
            'delivery_fee' => 0,
            'discount_amount' => 0,
            'loyalty_points_used' => 0,
            'total' => 350,
            'payment_method' => 'bank_transfer',
            'sender_name' => 'Ali Test',
            'sender_phone' => '05000000000',
            'sender_email' => $user->email,
            'recipient_name' => 'Ayse Test',
            'recipient_phone' => '05000000001',
            'recipient_address' => 'Merkez Mahallesi No: 1',
            'recipient_district' => 'Merkez',
            'delivery_date' => now()->toDateString(),
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name' => 'Bahar Buketi',
            'quantity' => 2,
            'unit_price' => 175,
            'total_price' => 350,
            'card_message' => 'Tekrar siparis',
        ]);

        $this->post(route('account.order.reorder', ['orderNumber' => $order->order_number]))
            ->assertRedirect(route('cart'));

        $this->assertDatabaseHas('cart_items', [
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);
    }

    public function test_user_can_export_personal_data(): void
    {
        $user = User::factory()->create([
            'phone' => '05000000000',
            'marketing_consent' => true,
            'kvkk_accepted_at' => now(),
        ]);

        $this->actingAs($user);

        $response = $this->get(route('account.kvkk.export'));

        $response->assertOk();
        $this->assertStringContainsString($user->email, $response->streamedContent());
        $this->assertDatabaseHas('data_requests', [
            'user_id' => $user->id,
            'type' => 'export',
            'status' => 'completed',
        ]);
    }
}
