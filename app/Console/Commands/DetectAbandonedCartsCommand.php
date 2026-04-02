<?php

namespace App\Console\Commands;

use App\Models\AbandonedCart;
use App\Models\CartItem;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DetectAbandonedCartsCommand extends Command
{
    protected $signature = 'cart:detect-abandoned {--hours=2 : Kaç saatten eski sepetler terk edilmiş sayılsın}';
    protected $description = 'Terk edilmiş sepetleri tespit et ve kaydet';

    public function handle(): int
    {
        $hours     = (int) $this->option('hours');
        $cutoffAt  = now()->subHours($hours);
        $detected  = 0;

        // Find cart items grouped by session/user that haven't been updated
        $cartGroups = CartItem::with(['product', 'variant'])
            ->where('updated_at', '<', $cutoffAt)
            ->select('user_id', 'session_id', DB::raw('MAX(updated_at) as last_active'), DB::raw('COUNT(*) as item_count'))
            ->groupBy('user_id', 'session_id')
            ->get();

        foreach ($cartGroups as $group) {
            // Skip if already tracked
            $alreadyTracked = AbandonedCart::where(function ($q) use ($group) {
                if ($group->user_id) {
                    $q->where('user_id', $group->user_id);
                } else {
                    $q->where('session_id', $group->session_id);
                }
            })
                ->where('recovered', false)
                ->where('abandoned_at', '>=', now()->subDays(7))
                ->exists();

            if ($alreadyTracked) {
                continue;
            }

            // Get cart items snapshot
            $cartItems = CartItem::with('product')
                ->when($group->user_id, fn($q) => $q->where('user_id', $group->user_id))
                ->when(!$group->user_id, fn($q) => $q->where('session_id', $group->session_id))
                ->get();

            if ($cartItems->isEmpty()) {
                continue;
            }

            $totalValue = $cartItems->sum(fn($item) => $item->subtotal);

            $cartData = $cartItems->map(fn($item) => [
                'product_id'   => $item->product_id,
                'product_name' => $item->product?->getTranslation('name', 'tr') ?? '',
                'quantity'     => $item->quantity,
                'subtotal'     => $item->subtotal,
            ])->toArray();

            // Resolve contact info for guest carts (for notification routing)
            $user      = $group->user_id ? \App\Models\User::find($group->user_id) : null;
            $cartEmail = $user?->email;
            $cartPhone = $user?->phone;

            if (!$cartEmail) {
                // Try to get email from the most recent cart item's session-stored data
                $cartEmail = session('guest_email');
            }

            try {
                AbandonedCart::create([
                    'user_id'        => $group->user_id,
                    'session_id'     => $group->session_id,
                    'cart_data'      => $cartData,
                    'total_value'    => $totalValue,
                    'recovered'      => false,
                    'reminder_count' => 0,
                    'abandoned_at'   => $group->last_active,
                    'email'          => $cartEmail,
                    'phone'          => $cartPhone,
                ]);
                $detected++;
            } catch (\Exception $e) {
                Log::warning('Terk edilmiş sepet kaydedilemedi', ['message' => $e->getMessage()]);
            }
        }

        Log::info(__(':count terk edilmiş sepet tespit edildi.', ['count' => $detected]));
        $this->info(__(':count terk edilmiş sepet tespit edildi.', ['count' => $detected]));

        return self::SUCCESS;
    }
}
