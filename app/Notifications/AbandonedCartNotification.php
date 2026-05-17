<?php

namespace App\Notifications;

use App\Models\AbandonedCart;
use App\Models\NotificationTemplate;
use App\Notifications\Channels\SmsChannel;
use App\Notifications\Concerns\ResolvesNotificationRoutes;
use App\Services\SmsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AbandonedCartNotification extends Notification implements ShouldQueue
{
    use Queueable;
    use ResolvesNotificationRoutes;

    private const TEMPLATE_KEY = 'abandoned_cart';

    public function __construct(private AbandonedCart $cart) {}

    public function via(object $notifiable): array
    {
        $channels = [];

        if ($this->resolveMailRoute($notifiable) || ! empty($this->cart->email)) {
            $channels[] = 'mail';
        }

        if (($this->resolveSmsRoute($notifiable) || ! empty($this->cart->phone)) && app(SmsService::class)->isEnabled()) {
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
            ? $template->renderEmailSubject($variables, $locale, 'Sepetinizde urunler var!')
            : 'Sepetinizde urunler var!';

        $body = $template
            ? $template->renderEmailBody($variables, $locale)
            : 'Sepetinizde biraktiginiz urunler sizi bekliyor!';

        return (new MailMessage)
            ->subject($subject)
            ->markdown('emails.notification', ['body' => $body]);
    }

    public function toSms(object $notifiable): void
    {
        $template = NotificationTemplate::findByKey(self::TEMPLATE_KEY);
        $phone = $this->resolveSmsRoute($notifiable) ?? $this->cart->phone;

        if (! $template || empty($phone)) {
            return;
        }

        $message = $template->renderSms($this->buildVariables($notifiable), $this->resolveLocale($notifiable));
        app(SmsService::class)->send($phone, $message);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'cart_id' => $this->cart->id,
            'total_value' => $this->cart->total_value,
            'reminder_count' => $this->cart->reminder_count,
        ];
    }

    public function cartId(): int
    {
        return (int) $this->cart->id;
    }

    private function buildVariables(object $notifiable): array
    {
        $cartUrl = url('/sepet');

        return [
            'musteri_adi' => $notifiable->name ?? '',
            'sepet_tutari' => number_format((float) $this->cart->total_value, 2, ',', '.').' TL',
            'urun_sayisi' => (string) $this->cart->item_count,
            'sepet_linki' => $cartUrl,
            'site_url' => config('app.url'),
        ];
    }

    private function resolveLocale(object $notifiable): string
    {
        return $notifiable->preferred_language ?? 'tr';
    }
}
