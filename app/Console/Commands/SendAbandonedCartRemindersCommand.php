<?php

namespace App\Console\Commands;

use App\Models\AbandonedCart;
use App\Notifications\AbandonedCartNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class SendAbandonedCartRemindersCommand extends Command
{
    protected $signature = 'cart:send-reminders';
    protected $description = 'Terk edilmiş sepet hatırlatma bildirimleri gönder';

    private const MAX_REMINDERS = 2;

    public function handle(): int
    {
        $carts = AbandonedCart::notRecovered()
            ->where('reminder_count', '<', self::MAX_REMINDERS)
            ->where(fn($q) => $q->whereNull('last_reminded_at')
                ->orWhere('last_reminded_at', '<=', now()->subHours(24)))
            ->with('user')
            ->get();

        $sent   = 0;
        $failed = 0;

        foreach ($carts as $cart) {
            try {
                if ($cart->user) {
                    $cart->user->notify(new AbandonedCartNotification($cart));
                } elseif (!empty($cart->email)) {
                    // Guest cart: route notification directly to email/phone
                    $notifiable = Notification::route('mail', $cart->email);
                    if (!empty($cart->phone)) {
                        $notifiable = $notifiable->route('vonage', $cart->phone);
                    }
                    $notifiable->notify(new AbandonedCartNotification($cart));
                } else {
                    continue;
                }

                $cart->update([
                    'reminder_count'  => $cart->reminder_count + 1,
                    'last_reminded_at' => now(),
                ]);

                $sent++;
            } catch (\Exception $e) {
                Log::error('Sepet hatırlatma gönderilemedi', [
                    'cart_id' => $cart->id,
                    'message' => $e->getMessage(),
                ]);
                $failed++;
            }
        }

        Log::info(__(':sent hatırlatma gönderildi, :failed başarısız.', ['sent' => $sent, 'failed' => $failed]));
        $this->info(__(':sent hatırlatma gönderildi, :failed başarısız.', ['sent' => $sent, 'failed' => $failed]));

        return self::SUCCESS;
    }
}
