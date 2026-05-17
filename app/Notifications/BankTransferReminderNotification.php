<?php

namespace App\Notifications;

use App\Models\NotificationTemplate;
use App\Models\Order;
use App\Notifications\Channels\SmsChannel;
use App\Notifications\Concerns\ResolvesNotificationRoutes;
use App\Services\SmsService;
use App\Support\PaymentSettings;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BankTransferReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;
    use ResolvesNotificationRoutes;

    public function __construct(
        private Order $order,
        private string $type = 'reminder'
    ) {}

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
        $templateKey = $this->type === 'warning' ? 'bank_transfer_warning' : 'bank_transfer_reminder';
        $template = NotificationTemplate::findByKey($templateKey);
        $variables = $this->buildVariables();
        $locale = $this->resolveLocale($notifiable);

        [$subject, $body] = $this->resolveContent($template, $variables, $locale);

        return (new MailMessage)
            ->subject($subject)
            ->markdown('emails.notification', ['body' => $body]);
    }

    public function toSms(object $notifiable): void
    {
        $templateKey = $this->type === 'warning' ? 'bank_transfer_warning' : 'bank_transfer_reminder';
        $template = NotificationTemplate::findByKey($templateKey);

        $phone = $this->resolveSmsRoute($notifiable);

        if (! $template || empty($phone)) {
            return;
        }

        $message = $template->renderSms($this->buildVariables(), $this->resolveLocale($notifiable));
        app(SmsService::class)->send($phone, $message);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'type' => $this->type,
        ];
    }

    private function buildVariables(): array
    {
        $payment = PaymentSettings::bankTransferDetails();
        $timeoutHours = (int) ($payment['transfer_timeout_hours'] ?? 72);

        return [
            'musteri_adi' => $this->order->sender_name ?? '',
            'siparis_no' => $this->order->order_number,
            'toplam' => number_format((float) $this->order->total, 2, ',', '.').' TL',
            'son_tarih' => now()->addHours($timeoutHours > 0 ? $timeoutHours : 72)->format('d.m.Y H:i'),
            'banka_adi' => $payment['bank_name'] ?: 'Banka Adı',
            'iban' => $payment['bank_iban'] ?: 'TRXX XXXX XXXX XXXX XXXX XXXX XX',
            'hesap_sahibi' => $payment['bank_account_holder'] ?: 'Rose Garden',
            'aciklama' => $this->order->order_number,
        ];
    }

    private function resolveContent(?NotificationTemplate $template, array $variables, string $locale): array
    {
        if ($template) {
            $subject = $template->renderEmailSubject($variables, $locale);
            $body = $template->renderEmailBody($variables, $locale);
        } elseif ($this->type === 'warning') {
            $subject = "Siparişiniz iptal edilecek - #{$this->order->order_number}";
            $body = 'Havale ödemeniz 72 saat içinde gerçekleşmediği takdirde siparişiniz iptal edilecektir.';
        } else {
            $subject = "Havale hatırlatma - #{$this->order->order_number}";
            $body = 'Siparişiniz için havale ödemesini bekliyoruz.';
        }

        return [$subject, $body];
    }

    private function resolveLocale(object $notifiable): string
    {
        return $notifiable->preferred_language
            ?? $this->order->user?->preferred_language
            ?? 'tr';
    }
}
