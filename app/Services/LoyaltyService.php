<?php

namespace App\Services;

use App\Models\LoyaltyPoint;
use App\Models\LoyaltyTransaction;
use App\Models\Order;
use App\Models\Setting;
use App\Models\SpecialOccasion;
use Illuminate\Support\Facades\Log;

class LoyaltyService
{
    public function estimateEarnedPoints(float $amount): float
    {
        $baseAmount = max(0, $amount);
        $rate = (float) (Setting::get('loyalty', 'earn_rate') ?? 0.05);

        if ($baseAmount <= 0 || $rate <= 0) {
            return 0.0;
        }

        return round($baseAmount * $rate * $this->getOccasionMultiplier());
    }

    public function earnPoints(Order $order): void
    {
        if (! $order->user_id) {
            return;
        }

        try {
            $rate = (float) (Setting::get('loyalty', 'earn_rate') ?? 0.05);
            $multiplier = $this->getOccasionMultiplier();

            // Exclude loyalty-points-paid portion from earning base
            $baseAmount = (float) $order->total - (float) ($order->loyalty_points_used ?? 0);

            if ($baseAmount <= 0 || $rate <= 0) {
                return;
            }

            $loyaltyPoint = LoyaltyPoint::firstOrCreate(
                ['user_id' => $order->user_id],
                ['balance' => 0, 'total_earned' => 0, 'total_spent' => 0]
            );

            $expiryMonths = (int) (Setting::get('loyalty', 'expiry_months') ?? 12);
            $expiresAt = $expiryMonths > 0 ? now()->addMonths($expiryMonths) : null;

            $loyaltyPoint->addPoints(
                $baseAmount * $rate,
                "Sipariş #{$order->order_number} puan kazanımı",
                $order->id,
                $multiplier,
                $expiresAt
            );
        } catch (\Exception $e) {
            Log::error('Puan kazanımı hatası', [
                'order_id' => $order->id,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function usePoints(Order $order, float $amount): bool
    {
        if (! $order->user_id) {
            return false;
        }

        $minAmount = (float) (Setting::get('loyalty', 'min_use_amount') ?? 10);

        if ($amount < $minAmount) {
            return false;
        }

        $loyaltyPoint = LoyaltyPoint::where('user_id', $order->user_id)->first();

        if (! $loyaltyPoint) {
            return false;
        }

        return $loyaltyPoint->spendPoints(
            $amount,
            "Sipariş #{$order->order_number} puan kullanımı",
            $order->id
        );
    }

    public function refundPoints(Order $order): void
    {
        if (! $order->user_id) {
            return;
        }

        try {
            $loyaltyPoint = LoyaltyPoint::where('user_id', $order->user_id)->first();

            if (! $loyaltyPoint) {
                return;
            }

            // Find earned transactions for this order and reverse them
            $earnedTransactions = LoyaltyTransaction::where('order_id', $order->id)
                ->where('type', 'earned')
                ->get();

            foreach ($earnedTransactions as $transaction) {
                LoyaltyTransaction::create([
                    'user_id' => $order->user_id,
                    'order_id' => $order->id,
                    'type' => 'refunded',
                    'amount' => $transaction->amount,
                    'description' => "Sipariş #{$order->order_number} iade - puan geri alındı",
                ]);

                $loyaltyPoint->decrement('balance', $transaction->amount);
                $loyaltyPoint->decrement('total_earned', $transaction->amount);
                $loyaltyPoint->touch('updated_at');
            }

            // Refund used points
            if ((float) $order->loyalty_points_used > 0) {
                $loyaltyPoint->addPoints(
                    (float) $order->loyalty_points_used,
                    "Sipariş #{$order->order_number} iptal - kullanılan puan iadesi",
                    $order->id
                );
            }
        } catch (\Exception $e) {
            Log::error('Puan iade hatası', [
                'order_id' => $order->id,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function expirePoints(): void
    {
        $expiryMonths = (int) (Setting::get('loyalty', 'expiry_months') ?? 12);

        if ($expiryMonths <= 0) {
            return;
        }

        // Find users whose points have passed their explicit expiry date
        $expiredPoints = LoyaltyPoint::where('balance', '>', 0)
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', now())
            ->get();

        foreach ($expiredPoints as $loyaltyPoint) {
            try {
                $balance = (float) $loyaltyPoint->balance;

                LoyaltyTransaction::create([
                    'user_id' => $loyaltyPoint->user_id,
                    'type' => 'expired',
                    'amount' => $balance,
                    'description' => "{$expiryMonths} ay kullanılmadığı için puan süresi doldu",
                ]);

                $loyaltyPoint->update([
                    'balance' => 0,
                    'updated_at' => now(),
                ]);

                Log::info('Puan süresi doldu', [
                    'user_id' => $loyaltyPoint->user_id,
                    'expired' => $balance,
                ]);
            } catch (\Exception $e) {
                Log::error('Puan sona erdirme hatası', [
                    'user_id' => $loyaltyPoint->user_id,
                    'message' => $e->getMessage(),
                ]);
            }
        }
    }

    private function getOccasionMultiplier(): float
    {
        $today = now();

        $occasion = SpecialOccasion::where('is_active', true)
            ->where('date_month', $today->month)
            ->where('date_day', $today->day)
            ->first();

        return $occasion ? (float) ($occasion->loyalty_multiplier ?? 1.0) : 1.0;
    }
}
