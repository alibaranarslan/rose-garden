<?php

namespace App\Notifications;

use App\Models\AbandonedCart;
use App\Models\NotificationTemplate;
use App\Services\SmsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AbandonedCartNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private const TEMPLATE_KEY   = 'abandoned_cart';
    private const MAX_REMINDERS  = 2;

    public function __construct(private AbandonedCart $cart) {}

    public function via(object $notifiable): array
    {
        $channels = [];

        if (!empty($notifiable->email ?? $this->cart->email)) {
            $channels[] = 'mail';
        }

        if (!empty($notifiable->phone ?? $this->cart->phone) && config('services.sms.enabled')) {
            $channels[] = 'sms';
        }

        return $channels ?: ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $template  = NotificationTemplate::findByKey(self::TEMPLATE_KEY);
        $variables = $this->buildVariables($notifiable);

        $subject = $template
            ? $this->replaceVars($template->email_subject ?? 'Sepetinizde ürünler var!', $variables)
            : 'Sepetinizde ürünler var!';

        $body = $template
            ? $template->renderEmailBody($variables)
            : "Sepetinizde bıraktığınız ürünler sizi bekliyor!";

        return (new MailMessage)
            ->subject($subject)
            ->markdown('emails.notification', ['body' => $body]);
    }

    public function toSms(object $notifiable): void
    {
        $template = NotificationTemplate::findByKey(self::TEMPLATE_KEY);
        $phone    = $notifiable->phone ?? $this->cart->phone;

        if (!$template || empty($phone)) {
            return;
        }

        $message = $template->renderSms($this->buildVariables($notifiable));
        app(SmsService::class)->send($phone, $message);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'cart_id'       => $this->cart->id,
            'total_value'   => $this->cart->total_value,
            'reminder_count' => $this->cart->reminder_count,
        ];
    }

    private function buildVariables(object $notifiable): array
    {
        $cartUrl = url('/sepet');

        return [
            'müşteri_adı'  => $notifiable->name ?? '',
            'sepet_tutarı' => number_format((float) $this->cart->total_value, 2, ',', '.') . ' ₺',
            'ürün_sayısı'  => (string) $this->cart->item_count,
            'sepet_linki'  => $cartUrl,
            'site_url'     => config('app.url'),
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
