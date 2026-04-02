<?php

namespace App\Observers;

use App\Models\OrderItem;
use App\Services\CardMessageAnalyzer;
use Illuminate\Support\Facades\Log;

class OrderItemObserver
{
    public function __construct(private CardMessageAnalyzer $analyzer) {}

    public function created(OrderItem $orderItem): void
    {
        if (empty($orderItem->card_message)) {
            return;
        }

        $order = $orderItem->order;

        if (!$order) {
            return;
        }

        // Analyze card message even for guest orders (user_id may be null)
        try {
            $this->analyzer->analyze(
                $orderItem->card_message,
                $order->user_id, // nullable for guest orders
                $order->id,
                $order->recipient_name
            );
        } catch (\Exception $e) {
            Log::warning('Kart mesajı analizi başarısız', [
                'order_item_id' => $orderItem->id,
                'message'       => $e->getMessage(),
            ]);
        }
    }
}
