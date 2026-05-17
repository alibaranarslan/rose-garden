<?php

namespace Tests\Unit\Services;

use App\Models\AbandonedCart;
use App\Models\User;
use App\Services\AbandonedCartReminderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AbandonedCartReminderServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_dispatch_reserves_the_reminder_slot_as_queued_and_blocks_immediate_retries(): void
    {
        Notification::fake();

        $user = User::factory()->create([
            'is_admin' => false,
            'is_active' => true,
            'phone' => '05000000000',
        ]);

        $cart = AbandonedCart::create([
            'user_id' => $user->id,
            'email' => $user->email,
            'phone' => $user->phone,
            'cart_data' => [['sku' => 'SKU-1', 'qty' => 1]],
            'total_value' => 850,
            'reminder_count' => 0,
            'recovered' => false,
            'abandoned_at' => now()->subHours(2),
        ]);

        $service = app(AbandonedCartReminderService::class);

        $firstResult = $service->dispatch($cart);
        $cart->refresh();

        $this->assertTrue($firstResult['sent']);
        $this->assertSame('queued', $firstResult['status']);
        $this->assertSame(1, $cart->reminder_count);
        $this->assertSame('queued', $cart->last_reminder_status);
        $this->assertNotNull($cart->last_reminded_at);

        $secondResult = $service->dispatch($cart->fresh());
        $cart->refresh();

        $this->assertFalse($secondResult['sent']);
        $this->assertSame('cooldown', $secondResult['status']);
        $this->assertSame(1, $cart->reminder_count);
    }
}
