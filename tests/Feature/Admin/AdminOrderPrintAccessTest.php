<?php

namespace Tests\Feature\Admin;

use App\Models\Order;
use App\Models\User;
use App\Models\DeliveryTimeSlot;
use App\Models\DeliveryZone;
use Database\Seeders\DeliveryTimeSlotSeeder;
use Database\Seeders\DeliveryZoneSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminOrderPrintAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_cannot_open_admin_order_print_view(): void
    {
        $this->seed([
            DeliveryZoneSeeder::class,
            DeliveryTimeSlotSeeder::class,
        ]);

        $customer = User::factory()->create([
            'is_admin' => false,
            'is_active' => true,
        ]);

        $order = $this->createOrder();

        $this->actingAs($customer)
            ->get(route('orders.print', $order))
            ->assertForbidden();
    }

    public function test_admin_can_open_admin_order_print_view(): void
    {
        $this->seed([
            DeliveryZoneSeeder::class,
            DeliveryTimeSlotSeeder::class,
        ]);

        $admin = User::factory()->create([
            'is_admin' => true,
            'is_active' => true,
        ]);

        $order = $this->createOrder();

        $this->actingAs($admin)
            ->get(route('orders.print', $order))
            ->assertOk()
            ->assertSee($order->order_number);
    }

    private function createOrder(): Order
    {
        $zoneId = DeliveryZone::query()->value('id');
        $slotId = DeliveryTimeSlot::query()->value('id');

        return Order::create([
            'order_number' => 'RG-TEST-1001',
            'status' => 'pending',
            'subtotal' => 950,
            'delivery_fee' => 50,
            'discount_amount' => 0,
            'loyalty_points_used' => 0,
            'total' => 1000,
            'payment_method' => 'bank_transfer',
            'sender_name' => 'Test Musteri',
            'sender_phone' => '05000000000',
            'sender_email' => 'musteri@example.com',
            'recipient_name' => 'Alici Kisi',
            'recipient_phone' => '05000000001',
            'recipient_address' => 'Test Adres',
            'recipient_district' => 'Merkez',
            'delivery_zone_id' => $zoneId,
            'delivery_date' => now()->toDateString(),
            'delivery_time_slot_id' => $slotId,
            'ip_address' => '127.0.0.1',
        ]);
    }
}
