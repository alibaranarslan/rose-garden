<?php

namespace Tests\Feature\Notifications;

use App\Models\Order;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderConfirmedEmailTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_confirmation_email_uses_runtime_payment_settings_contract(): void
    {
        Setting::set('general', 'site_name', 'Rose Garden Atelier');
        Setting::set('payment', 'bank_name', 'Test Bank');
        Setting::set('payment', 'bank_iban', 'TR12 3456 7890 1234 5678 90');
        Setting::set('payment', 'bank_account_holder', 'Rose Garden Ltd');
        Setting::set('payment', 'transfer_timeout_hours', 48);

        $order = Order::create([
            'status' => 'awaiting_payment',
            'subtotal' => 1250,
            'delivery_fee' => 0,
            'discount_amount' => 0,
            'loyalty_points_used' => 0,
            'total' => 1250,
            'payment_method' => 'bank_transfer',
            'sender_name' => 'Ali Test',
            'sender_phone' => '05000000000',
            'sender_email' => 'ali@example.com',
            'recipient_name' => 'Ayşe Test',
            'recipient_phone' => '05000000001',
            'recipient_address' => 'Atatürk Bulvarı No: 1',
            'recipient_district' => 'Merkez',
            'delivery_date' => now()->addDay()->toDateString(),
        ]);

        $html = view('emails.order-confirmed', ['order' => $order])->render();

        $this->assertStringContainsString('Rose Garden Atelier', $html);
        $this->assertStringContainsString('Test Bank', $html);
        $this->assertStringContainsString('Rose Garden Ltd', $html);
        $this->assertStringContainsString('TR12345678901234567890', $html);
        $this->assertStringContainsString('48 saat', $html);
        $this->assertStringNotContainsString('72 saat', $html);
    }
}
