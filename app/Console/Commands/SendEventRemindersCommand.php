<?php

namespace App\Console\Commands;

use App\Models\CustomerEvent;
use App\Notifications\EventReminderNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendEventRemindersCommand extends Command
{
    protected $signature = 'events:send-reminders';
    protected $description = 'Yaklaşan müşteri olayları için hatırlatma bildirimleri gönder';

    public function handle(): int
    {
        $events = CustomerEvent::active()
            ->with('user')
            ->get()
            ->filter(fn($event) => $event->isDueForReminder())
            ->filter(fn($event) => $event->last_reminded_at === null
                || $event->last_reminded_at->lt(now()->subDays(300)));

        $sent   = 0;
        $failed = 0;

        foreach ($events as $event) {
            if (!$event->user) {
                continue;
            }

            try {
                $event->user->notify(new EventReminderNotification($event));

                $event->update(['last_reminded_at' => now()]);
                $sent++;
            } catch (\Exception $e) {
                Log::error('Olay hatırlatma gönderilemedi', [
                    'event_id' => $event->id,
                    'message'  => $e->getMessage(),
                ]);
                $failed++;
            }
        }

        Log::info(__(':sent olay hatırlatması gönderildi, :failed başarısız.', ['sent' => $sent, 'failed' => $failed]));
        $this->info(__(':sent olay hatırlatması gönderildi, :failed başarısız.', ['sent' => $sent, 'failed' => $failed]));

        return self::SUCCESS;
    }
}
