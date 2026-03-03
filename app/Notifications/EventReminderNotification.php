<?php

namespace App\Notifications;

use App\Models\CustomerEvent;
use App\Models\NotificationTemplate;
use App\Services\SmsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EventReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private const TEMPLATE_KEY = 'event_reminder';

    public function __construct(private CustomerEvent $event) {}

    public function via(object $notifiable): array
    {
        $channels = [];

        if (!empty($notifiable->email)) {
            $channels[] = 'mail';
        }

        if (!empty($notifiable->phone) && config('services.sms.enabled')) {
            $channels[] = 'sms';
        }

        return $channels ?: ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $template  = NotificationTemplate::findByKey(self::TEMPLATE_KEY);
        $variables = $this->buildVariables($notifiable);

        $subject = $template
            ? $this->replaceVars($template->email_subject ?? __('Yaklaşan Özel Gün Hatırlatması'), $variables)
            : __(':event_label yaklaşıyor!', ['event_label' => $this->event->event_label]);

        $body = $template
            ? $template->renderEmailBody($variables)
            : __('Sayın :name, :event_label günü yaklaşıyor. Sevdiklerinize özel bir hediye hazırlayın!', ['name' => $notifiable->name, 'event_label' => $this->event->event_label]);

        return (new MailMessage)
            ->subject($subject)
            ->markdown('emails.notification', ['body' => $body]);
    }

    public function toSms(object $notifiable): void
    {
        $template = NotificationTemplate::findByKey(self::TEMPLATE_KEY);

        if (!$template || empty($notifiable->phone)) {
            return;
        }

        $message = $template->renderSms($this->buildVariables($notifiable));
        app(SmsService::class)->send($notifiable->phone, $message);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'event_id'   => $this->event->id,
            'event_type' => $this->event->event_type,
            'event_label' => $this->event->event_label,
        ];
    }

    private function buildVariables(object $notifiable): array
    {
        $eventDate  = now()->setDate(now()->year, $this->event->event_month, $this->event->event_day);
        if ($eventDate->isPast()) $eventDate->addYear();

        return [
            'müşteri_adı'   => $notifiable->name ?? '',
            'olay_adı'      => $this->event->event_label ?? '',
            'alıcı_adı'     => $this->event->recipient_name ?? '',
            'gün_kaldı'     => (string) $eventDate->diffInDays(now()),
            'tarih'         => $eventDate->format('d.m.Y'),
            'site_url'      => config('app.url'),
        ];
    }

    private function replaceVars(string $text, array $vars): string
    {
        foreach ($vars as $key => $value) {
            $text = str_replace('{' . $key . '}', $value, $text);
        }
        return $text;
    }
}
