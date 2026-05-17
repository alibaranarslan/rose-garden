<?php

namespace App\Services;

use App\Models\AbandonedCart;
use App\Notifications\AbandonedCartNotification;
use App\Notifications\Channels\SmsChannel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class AbandonedCartReminderService
{
    public const MAX_REMINDERS = 2;

    public const COOLDOWN_HOURS = 24;

    public function eligibleQuery(): Builder
    {
        return AbandonedCart::query()
            ->where('recovered', false)
            ->where('reminder_count', '<', self::MAX_REMINDERS)
            ->where(function (Builder $query) {
                $query->whereNull('last_reminded_at')
                    ->orWhere('last_reminded_at', '<=', now()->subHours(self::COOLDOWN_HOURS));
            });
    }

    public function eligibilityStatus(AbandonedCart $cart): string
    {
        if ($cart->recovered) {
            return 'recovered';
        }

        if (($cart->reminder_count ?? 0) >= self::MAX_REMINDERS) {
            return 'limit_reached';
        }

        if ($cart->last_reminded_at && $cart->last_reminded_at->gt(now()->subHours(self::COOLDOWN_HOURS))) {
            return 'cooldown';
        }

        return 'eligible';
    }

    public function canSend(AbandonedCart $cart): bool
    {
        return $this->eligibilityStatus($cart) === 'eligible';
    }

    public function dispatch(AbandonedCart $cart): array
    {
        $reservation = DB::transaction(function () use ($cart): array {
            $lockedCart = AbandonedCart::query()
                ->with('user')
                ->lockForUpdate()
                ->find($cart->getKey());

            if (! $lockedCart) {
                return ['sent' => false, 'status' => 'failed', 'channel' => null];
            }

            $lockedCart->forceFill([
                'last_reminder_attempted_at' => now(),
                'last_reminder_error' => null,
            ])->save();

            if (! $this->canSend($lockedCart)) {
                $status = $this->eligibilityStatus($lockedCart);

                $lockedCart->forceFill([
                    'last_reminder_status' => $status,
                    'last_reminder_error' => 'Kayıt hatırlatma gönderimi için uygun değil.',
                ])->save();

                return ['sent' => false, 'status' => $status, 'channel' => null];
            }

            $notifiable = $this->resolveNotifiable($lockedCart);

            if (! $notifiable) {
                $lockedCart->forceFill([
                    'last_reminder_status' => 'failed',
                    'last_reminder_error' => 'Kullanılabilir e-posta veya telefon bulunamadı.',
                ])->save();

                return ['sent' => false, 'status' => 'failed', 'channel' => null];
            }

            $notification = new AbandonedCartNotification($lockedCart);
            $channel = $this->normalizeChannels($notification->via($notifiable));

            $lockedCart->forceFill([
                'reminder_count' => ($lockedCart->reminder_count ?? 0) + 1,
                'last_reminded_at' => now(),
                'last_reminder_status' => 'queued',
                'last_reminder_channel' => $channel,
                'last_reminder_error' => null,
            ])->save();

            return [
                'sent' => true,
                'status' => 'queued',
                'channel' => $channel,
                'notifiable' => $notifiable,
                'notification' => $notification,
                'cart_id' => $lockedCart->id,
            ];
        });

        if (! ($reservation['sent'] ?? false)) {
            return $reservation;
        }

        try {
            $reservation['notifiable']->notify($reservation['notification']);

            return [
                'sent' => true,
                'status' => 'queued',
                'channel' => $reservation['channel'] ?? null,
            ];
        } catch (\Throwable $exception) {
            AbandonedCart::query()
                ->whereKey($reservation['cart_id'] ?? null)
                ->update([
                    'last_reminder_status' => 'failed',
                    'last_reminder_error' => $exception->getMessage(),
                    'last_reminder_attempted_at' => now(),
                ]);

            return [
                'sent' => false,
                'status' => 'failed',
                'channel' => $reservation['channel'] ?? null,
            ];
        }
    }

    private function resolveNotifiable(AbandonedCart $cart): ?object
    {
        if ($cart->user) {
            return $cart->user;
        }

        if (! filled($cart->email) && ! filled($cart->phone)) {
            return null;
        }

        if (filled($cart->email)) {
            $notifiable = Notification::route('mail', $cart->email);

            if (filled($cart->phone)) {
                return $notifiable->route('sms', $cart->phone);
            }

            return $notifiable;
        }

        return Notification::route('sms', $cart->phone);
    }

    private function normalizeChannels(array $channels): string
    {
        return collect($channels)
            ->map(fn (string $channel) => match ($channel) {
                'mail' => 'email',
                SmsChannel::class => 'sms',
                default => $channel,
            })
            ->unique()
            ->implode('+');
    }
}
