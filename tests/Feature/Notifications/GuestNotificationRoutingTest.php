<?php

namespace Tests\Feature\Notifications;

use App\Models\AbandonedCart;
use App\Models\Order;
use App\Notifications\AbandonedCartNotification;
use App\Notifications\Channels\SmsChannel;
use App\Notifications\OrderStatusNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class GuestNotificationRoutingTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_order_notifications_are_routed_to_mail_and_sms_when_phone_exists(): void
    {
        config()->set('services.sms.enabled', true);
        Notification::fake();

        $order = Order::create([
            'status' => 'pending',
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

        $order->update(['status' => 'paid']);

        Notification::assertSentOnDemandTimes(OrderStatusNotification::class, 2);
        Notification::assertSentOnDemand(
            OrderStatusNotification::class,
            function (OrderStatusNotification $notification, array $channels, object $notifiable): bool {
                return in_array('mail', $channels, true)
                    && in_array(SmsChannel::class, $channels, true)
                    && $notifiable->routeNotificationFor('mail', $notification) === 'guest@example.com'
                    && $notifiable->routeNotificationFor('sms', $notification) === '05000000000';
            }
        );
    }

    public function test_guest_abandoned_cart_reminders_use_the_custom_sms_route(): void
    {
        config()->set('services.sms.enabled', true);
        Notification::fake();

        AbandonedCart::create([
            'session_id' => 'guest-cart-session',
            'email' => 'guest@example.com',
            'phone' => '05000000000',
            'cart_data' => [
                ['product_id' => 1, 'quantity' => 1],
            ],
            'total_value' => 250,
            'reminder_count' => 0,
            'recovered' => false,
            'abandoned_at' => now()->subHours(6),
        ]);

        Artisan::call('cart:send-reminders');

        Notification::assertSentOnDemand(
            AbandonedCartNotification::class,
            function (AbandonedCartNotification $notification, array $channels, object $notifiable): bool {
                return in_array('mail', $channels, true)
                    && in_array(SmsChannel::class, $channels, true)
                    && $notifiable->routeNotificationFor('mail', $notification) === 'guest@example.com'
                    && $notifiable->routeNotificationFor('sms', $notification) === '05000000000';
            }
        );
    }
}
