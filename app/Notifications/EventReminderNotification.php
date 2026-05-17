<?php

namespace App\Notifications;

use App\Models\CustomerEvent;
use App\Models\NotificationTemplate;
use App\Notifications\Channels\SmsChannel;
use App\Notifications\Concerns\ResolvesNotificationRoutes;
use App\Services\SmsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EventReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;
    use ResolvesNotificationRoutes;

    private const TEMPLATE_KEY = 'event_reminder';

    public function __construct(private CustomerEvent $event) {}

    public function via(object $notifiable): array
    {
        $channels = [];

        if ($this->resolveMailRoute($notifiable)) {
            $channels[] = 'mail';
        }

        if ($this->resolveSmsRoute($notifiable) && app(SmsService::class)->isEnabled()) {
            $channels[] = SmsChannel::class;
        }

        return $channels ?: ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $template = NotificationTemplate::findByKey(self::TEMPLATE_KEY);
        $variables = $this->buildVariables($notifiable);
        $locale = $this->resolveLocale($notifiable);

        $subject = $template
            ? $template->renderEmailSubject($variables, $locale, 'Yaklaşan Özel Gün Hatırlatması')
            : "{$this->event->event_label} yaklaşıyor!";

        $body = $template
            ? $template->renderEmailBody($variables, $locale)
            : "Sayın {$notifiable->name}, {$this->event->event_label} günü yaklaşıyor. Sevdiklerinize özel bir hediye hazırlayın!";

        return (new MailMessage)
            ->subject($subject)
            ->markdown('emails.notification', ['body' => $body]);
    }

    public function toSms(object $notifiable): void
    {
        $template = NotificationTemplate::findByKey(self::TEMPLATE_KEY);

        $phone = $this->resolveSmsRoute($notifiable);

        if (! $template || empty($phone)) {
            return;
        }

        $message = $template->renderSms($this->buildVariables($notifiable), $this->resolveLocale($notifiable));
        app(SmsService::class)->send($phone, $message);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'event_id' => $this->event->id,
            'event_type' => $this->event->event_type,
            'event_label' => $this->event->event_label,
        ];
    }

    private function buildVariables(object $notifiable): array
    {
        $eventDate = now()->setDate(now()->year, $this->event->event_month, $this->event->event_day);
        if ($eventDate->isPast()) {
            $eventDate->addYear();
        }

        return [
            'musteri_adi' => $notifiable->name ?? '',
            'olay_adi' => $this->event->event_label ?? '',
            'alici_adi' => $this->event->recipient_name ?? '',
            'gun_kaldi' => (string) $eventDate->diffInDays(now()),
            'tarih' => $eventDate->format('d.m.Y'),
            'site_url' => config('app.url'),
        ];
    }

    private function resolveLocale(object $notifiable): string
    {
        return $notifiable->preferred_language ?? 'tr';
    }
}
