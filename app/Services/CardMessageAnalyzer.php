<?php

namespace App\Services;

use App\Models\CustomerEvent;
use App\Models\KeywordDictionary;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CardMessageAnalyzer
{
    public function analyze(string $message, int $userId, int $orderId, ?string $recipientName = null): ?CustomerEvent
    {
        if (empty(trim($message))) {
            return null;
        }

        $eventType = $this->detectEventType($message);

        if ($eventType === null) {
            return null;
        }

        try {
            // Check for existing event of same type for same user/recipient
            $existingEvent = CustomerEvent::where('user_id', $userId)
                ->where('event_type', $eventType)
                ->when($recipientName, fn($q) => $q->where('recipient_name', $recipientName))
                ->first();

            if ($existingEvent) {
                // Update source_order_id if not set
                if (!$existingEvent->source_order_id) {
                    $existingEvent->update(['source_order_id' => $orderId]);
                }
                return $existingEvent;
            }

            // Estimate event date (assume next occurrence of the order date month/day)
            $eventMonth = now()->month;
            $eventDay   = now()->day;

            // Parse event from order delivery date would be ideal but we use now() as fallback
            $event = CustomerEvent::create([
                'user_id'             => $userId,
                'event_type'          => $eventType,
                'event_label'         => $this->getEventLabel($eventType),
                'recipient_name'      => $recipientName,
                'event_month'         => $eventMonth,
                'event_day'           => $eventDay,
                'detected_from'       => 'card_message',
                'source_order_id'     => $orderId,
                'reminder_days_before' => 7,
                'is_active'           => true,
            ]);

            Log::info('Müşteri olayı tespit edildi', [
                'user_id'    => $userId,
                'event_type' => $eventType,
                'order_id'   => $orderId,
            ]);

            return $event;
        } catch (\Exception $e) {
            Log::error('Kart mesajı analizi hatası', [
                'message' => $e->getMessage(),
                'user_id' => $userId,
            ]);
            return null;
        }
    }

    private function detectEventType(string $text): ?string
    {
        $keywords = $this->getKeywordCache();
        $text     = mb_strtolower($text, 'UTF-8');

        foreach ($keywords as $keyword => $eventType) {
            if (mb_strpos($text, mb_strtolower($keyword, 'UTF-8'), 0, 'UTF-8') !== false) {
                return $eventType;
            }
        }

        return null;
    }

    private function getKeywordCache(): array
    {
        return Cache::remember('card_message.keywords', 3600, function () {
            return KeywordDictionary::active()
                ->pluck('event_type', 'keyword')
                ->toArray();
        });
    }

    private function getEventLabel(string $eventType): string
    {
        return match ($eventType) {
            'birthday'    => 'Doğum Günü',
            'anniversary' => 'Yıldönümü',
            'mothers_day' => 'Anneler Günü',
            'valentines'  => 'Sevgililer Günü',
            'new_year'    => 'Yeni Yıl',
            default       => ucfirst(str_replace('_', ' ', $eventType)),
        };
    }
}
