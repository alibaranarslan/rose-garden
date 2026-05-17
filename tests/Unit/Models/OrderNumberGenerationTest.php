<?php

namespace Tests\Unit\Models;

use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderNumberGenerationTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_it_uses_the_latest_sequence_instead_of_count_plus_one(): void
    {
        Carbon::setTestNow('2026-04-17 10:00:00');

        Order::create($this->orderAttributes([
            'order_number' => 'RG-20260417-0007',
            'sender_email' => 'manual@example.com',
        ]));

        $generated = Order::createWithGeneratedNumber($this->orderAttributes([
            'sender_email' => 'generated@example.com',
        ]));

        $this->assertSame('RG-20260417-0008', $generated->order_number);
    }

    public function test_next_generated_order_number_starts_a_new_day_from_one(): void
    {
        Carbon::setTestNow('2026-04-18 09:00:00');

        $this->assertSame('RG-20260418-0001', Order::nextGeneratedOrderNumber());
    }

    private function orderAttributes(array $overrides = []): array
    {
        return array_merge([
            'status' => 'awaiting_payment',
            'subtotal' => 100,
            'delivery_fee' => 0,
            'discount_amount' => 0,
            'loyalty_points_used' => 0,
            'total' => 100,
            'payment_method' => 'bank_transfer',
            'sender_name' => 'Ali Test',
            'sender_phone' => '05000000000',
            'sender_email' => 'ali@example.com',
            'recipient_name' => 'Ayse Test',
            'recipient_phone' => '05000000001',
            'recipient_address' => 'Ataturk Bulvari No: 1',
            'recipient_district' => 'Merkez',
            'delivery_date' => now()->addDay()->toDateString(),
        ], $overrides);
    }
}
