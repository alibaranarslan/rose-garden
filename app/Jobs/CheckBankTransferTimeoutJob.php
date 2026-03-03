<?php

namespace App\Jobs;

use App\Models\Order;
use App\Notifications\BankTransferReminderNotification;
use App\Services\LoyaltyService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckBankTransferTimeoutJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function handle(LoyaltyService $loyaltyService): void
    {
        $now = now();

        // Fetch all pending bank transfer orders
        $orders = Order::awaitingBankTransfer()
            ->with(['user', 'items.product', 'payment'])
            ->get();

        foreach ($orders as $order) {
            try {
                $ageHours = $order->created_at->diffInHours($now);

                if ($ageHours >= 72) {
                    $this->cancelOrder($order, $loyaltyService);
                } elseif ($ageHours >= 48 && !$this->reminderAlreadySent($order, '48h')) {
                    $this->sendReminder($order, '48h');
                }
            } catch (\Exception $e) {
                Log::error('Havale timeout kontrolü hatası', [
                    'order_id' => $order->id,
                    'message'  => $e->getMessage(),
                ]);
            }
        }
    }

    private function cancelOrder(Order $order, LoyaltyService $loyaltyService): void
    {
        DB::transaction(function () use ($order, $loyaltyService) {
            $order->update([
                'status'              => 'cancelled',
                'cancelled_at'        => now(),
                'cancellation_reason' => 'Havale süresi doldu (72 saat)',
            ]);

            // With simple stock model (in_stock/out_of_stock), no quantity restore needed.
            // Products remain in their current stock_status.

            // Refund any loyalty points used
            if ((float) $order->loyalty_points_used > 0) {
                $loyaltyService->refundPoints($order);
            }

            Log::info('Havale siparişi otomatik iptal edildi', ['order_id' => $order->id]);
        });

        // Send cancellation notification
        if ($order->user) {
            $order->user->notify(new BankTransferReminderNotification($order, 'warning'));
        }
    }

    private function sendReminder(Order $order, string $stage): void
    {
        if ($order->user) {
            $order->user->notify(new BankTransferReminderNotification($order, 'reminder'));
        }

        // Tag order so we don't re-send
        $order->update(['admin_note' => ($order->admin_note ?? '') . " | Havale hatırlatma gönderildi ({$stage})"]);

        Log::info('Havale hatırlatma gönderildi', ['order_id' => $order->id, 'stage' => $stage]);
    }

    private function reminderAlreadySent(Order $order, string $stage): bool
    {
        return str_contains($order->admin_note ?? '', "Havale hatırlatma gönderildi ({$stage})");
    }
}
