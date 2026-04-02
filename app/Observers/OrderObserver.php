<?php

namespace App\Observers;

use App\Models\Order;
use App\Models\User;
use App\Notifications\OrderStatusNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class OrderObserver
{
    public function created(Order $order): void
    {
        // Notify customer (registered or guest)
        if ($order->user) {
            $order->user->notify(new OrderStatusNotification($order, 'order_created'));
        } elseif ($order->sender_email) {
            Notification::route('mail', $order->sender_email)
                ->notify(new OrderStatusNotification($order, 'order_created'));
        }

        // Notify admin users
        $this->notifyAdmins($order);
    }

    public function updated(Order $order): void
    {
        if (!$order->wasChanged('status')) {
            return;
        }

        if ($order->user) {
            $order->user->notify(new OrderStatusNotification($order, 'order_status'));
        } elseif ($order->sender_email) {
            Notification::route('mail', $order->sender_email)
                ->notify(new OrderStatusNotification($order, 'order_status'));
        }

        Log::info('Sipariş durumu değişti', [
            'order_id'   => $order->id,
            'old_status' => $order->getOriginal('status'),
            'new_status' => $order->status,
        ]);
    }

    private function notifyAdmins(Order $order): void
    {
        try {
            $admins = User::where('is_admin', true)->get();

            foreach ($admins as $admin) {
                $admin->notify(new OrderStatusNotification($order, 'admin_new_order'));
            }
        } catch (\Exception $e) {
            Log::warning('Admin sipariş bildirimi gönderilemedi', ['message' => $e->getMessage()]);
        }
    }
}
