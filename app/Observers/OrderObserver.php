<?php

namespace App\Observers;

use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Models\User;
use App\Notifications\OrderStatusNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class OrderObserver
{
    public function created(Order $order): void
    {
        $this->notifyCustomer($order, 'order_created');
        $this->notifyAdmins($order);
    }

    public function updated(Order $order): void
    {
        if (! $order->wasChanged('status')) {
            return;
        }

        OrderStatusHistory::create([
            'order_id' => $order->id,
            'status' => $order->status,
            'note' => 'Sipariş durumu güncellendi.',
            'changed_by' => auth()->id(),
            'created_at' => now(),
        ]);

        $this->notifyCustomer($order, 'order_status');

        Log::info('Sipariş durumu değişti', [
            'order_id' => $order->id,
            'old_status' => $order->getOriginal('status'),
            'new_status' => $order->status,
        ]);
    }

    private function notifyCustomer(Order $order, string $event): void
    {
        try {
            if ($order->user) {
                $order->user->notify(new OrderStatusNotification($order, $event));

                return;
            }

            if ($order->sender_email) {
                $notifiable = Notification::route('mail', $order->sender_email);

                if (! empty($order->sender_phone)) {
                    $notifiable = $notifiable->route('sms', $order->sender_phone);
                }

                $notifiable->notify(new OrderStatusNotification($order, $event));
            }
        } catch (\Throwable $exception) {
            Log::warning('Müşteri sipariş bildirimi gönderilemedi', [
                'order_id' => $order->id,
                'event' => $event,
                'message' => $exception->getMessage(),
            ]);
        }
    }

    private function notifyAdmins(Order $order): void
    {
        try {
            $admins = User::where('is_admin', true)->get();

            foreach ($admins as $admin) {
                $admin->notify(new OrderStatusNotification($order, 'admin_new_order'));
            }
        } catch (\Throwable $exception) {
            Log::warning('Admin sipariş bildirimi gönderilemedi', ['message' => $exception->getMessage()]);
        }
    }
}
