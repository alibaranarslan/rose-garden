<?php

namespace Tests\Feature;

use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTrackingStatusTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_tracking_page_uses_human_friendly_status_labels(): void
    {
        $order = Order::create([
            'status' => 'on_the_way',
            'subtotal' => 100,
            'delivery_fee' => 0,
            'discount_amount' => 0,
            'loyalty_points_used' => 0,
            'total' => 100,
            'payment_method' => 'bank_transfer',
            'sender_name' => 'Guest Customer',
            'sender_phone' => '05000000000',
            'sender_email' => 'guest@example.com',
            'recipient_name' => 'Recipient',
            'recipient_phone' => '05000000001',
            'recipient_address' => 'Ataturk Bulvari No: 1',
            'recipient_district' => 'Merkez',
            'delivery_date' => now()->addDay()->toDateString(),
        ]);

        $response = $this->post(route('order.track.submit'), [
            'order_number' => $order->order_number,
        ]);

        $response->assertOk();
        $response->assertSeeText('Sipariş bulundu');
        $response->assertSeeText('Yolda');
        $response->assertDontSeeText('On_the_way');
    }

    public function test_order_tracking_empty_submission_shows_visible_validation_message(): void
    {
        $response = $this->followingRedirects()
            ->from(route('order.track'))
            ->post(route('order.track.submit'), [
                'order_number' => '',
            ]);

        $response->assertOk();
        $response->assertSeeText('sipariş numarası alanı zorunludur');
    }
}
