<?php

namespace App\Console\Commands;

use App\Models\AbandonedCart;
use App\Services\AbandonedCartReminderService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendAbandonedCartRemindersCommand extends Command
{
    protected $signature = 'cart:send-reminders';

    protected $description = 'Terk edilmiş sepet hatırlatma bildirimleri gönder';

    public function handle(): int
    {
        $service = app(AbandonedCartReminderService::class);
        $carts = $service->eligibleQuery()->with('user')->get();

        $queued = 0;
        $failed = 0;

        foreach ($carts as $cart) {
            try {
                $result = $service->dispatch($cart);
                $queued += ($result['sent'] ?? false) ? 1 : 0;
                $failed += ($result['sent'] ?? false) ? 0 : 1;
            } catch (\Exception $e) {
                Log::error('Sepet hatırlatma gönderilemedi', [
                    'cart_id' => $cart->id,
                    'message' => $e->getMessage(),
                ]);
                $failed++;
            }
        }

        Log::info(__(':queued hatırlatma kuyruğa alındı, :failed başarısız.', ['queued' => $queued, 'failed' => $failed]));
        $this->info(__(':queued hatırlatma kuyruğa alındı, :failed başarısız.', ['queued' => $queued, 'failed' => $failed]));

        return self::SUCCESS;
    }
}
