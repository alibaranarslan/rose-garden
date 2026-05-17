<?php

namespace Tests\Feature\Payments;

use App\Models\LoyaltyPoint;
use App\Models\LoyaltyTransaction;
use App\Models\Order;
use App\Models\User;
use App\Services\PaytrService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class PaytrCallbackIdempotencyTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    public function test_success_callback_is_idempotent_for_paid_orders(): void
    {
        $user = User::factory()->create();

        LoyaltyPoint::create([
            'user_id' => $user->id,
            'balance' => 0,
            'total_earned' => 0,
            'total_spent' => 0,
        ]);

        $order = Order::create([
            'user_id' => $user->id,
            'order_number' => 'RG-TEST-1001',
            'status' => 'pending',
            'payment_method' => 'credit_card',
            'sender_name' => 'Ali Test',
            'sender_phone' => '05000000000',
            'sender_email' => 'ali@example.com',
            'recipient_name' => 'Ayse Test',
            'recipient_phone' => '05000000001',
            'recipient_address' => 'Ataturk Bulvari No: 1',
            'recipient_district' => 'Merkez',
            'delivery_date' => now()->addDay()->toDateString(),
            'delivery_time_slot' => '09:00 - 12:00',
            'subtotal' => 1000,
            'delivery_fee' => 0,
            'discount_total' => 0,
            'total' => 1000,
        ]);

        $mock = Mockery::mock(PaytrService::class);
        $mock->shouldReceive('verifyCallback')->twice()->andReturn(true);
        $this->app->instance(PaytrService::class, $mock);

        $payload = [
            'merchant_oid' => 'RG-TEST-1001',
            'status' => 'success',
            'total_amount' => '100000',
            'payment_type' => 'card',
        ];

        $this->post('/api/paytr/callback', $payload)->assertOk()->assertSee('OK');
        $this->post('/api/paytr/callback', $payload)->assertOk()->assertSee('OK');

        $this->assertDatabaseCount('payments', 1);
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'paid',
        ]);

        $this->assertSame(1, LoyaltyTransaction::where('order_id', $order->id)->where('type', 'earned')->count());
    }
}
