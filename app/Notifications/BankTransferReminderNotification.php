<?php

namespace App\Notifications;

use App\Models\NotificationTemplate;
use App\Models\Order;
use App\Models\Setting;
use App\Services\SmsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BankTransferReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private Order $order,
        private string $type = 'reminder' // 'reminder' | 'warning'
    ) {}

    public function via(object $notifiable): array
    {
        $channels = ['mail'];

        if (!empty($notifiable->phone) && config('services.sms.enabled')) {
            $channels[] = 'sms';
        }

        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        $templateKey = $this->type === 'warning' ? 'bank_transfer_warning' : 'bank_transfer_reminder';
        $template    = NotificationTemplate::findByKey($templateKey);
        $variables   = $this->buildVariables();

        [$subject, $body] = $this->resolveContent($template, $variables);

        return (new MailMessage)
            ->subject($subject)
            ->markdown('emails.notification', ['body' => $body]);
    }

    public function toSms(object $notifiable): void
    {
        $templateKey = $this->type === 'warning' ? 'bank_transfer_warning' : 'bank_transfer_reminder';
        $template    = NotificationTemplate::findByKey($templateKey);

        if (!$template || empty($notifiable->phone)) {
            return;
        }

        $message = $template->renderSms($this->buildVariables());
        app(SmsService::class)->send($notifiable->phone, $message);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'order_id'     => $this->order->id,
            'order_number' => $this->order->order_number,
            'type'         => $this->type,
        ];
    }

    private function buildVariables(): array
    {
        return [
            'müşteri_adı'  => $this->order->sender_name ?? '',
            'sipariş_no'   => $this->order->order_number,
            'toplam'       => number_format((float) $this->order->total, 2, ',', '.') . ' ₺',
            'son_tarih'    => now()->addHours(72)->format('d.m.Y H:i'),
            'banka_adı'    => Setting::get('payment', 'bank_name') ?? 'Banka Adı',
            'iban'         => Setting::get('payment', 'iban') ?? 'TRXX XXXX XXXX XXXX XXXX XXXX XX',
            'hesap_sahibi' => Setting::get('payment', 'account_holder') ?? 'Rose Garden',
            'açıklama'     => $this->order->order_number,
        ];
    }

    private function resolveContent(?NotificationTemplate $template, array $variables): array
    {
        if ($template) {
            $subject = $this->replaceVars($template->email_subject ?? '', $variables);
            $body    = $template->renderEmailBody($variables);
        } elseif ($this->type === 'warning') {
            $subject = __('Siparişiniz iptal edilecek - #:order_number', ['order_number' => $this->order->order_number]);
            $body    = __('Havale ödemeniz 72 saat içinde gerçekleşmediği takdirde siparişiniz iptal edilecektir.');
        } else {
            $subject = __('Havale hatırlatma - #:order_number', ['order_number' => $this->order->order_number]);
            $body    = __('Siparişiniz için havale ödemesini bekliyoruz.');
        }

        return [$subject, $body];
    }

    private function replaceVars(string $text, array $vars): string
    {
        foreach ($vars as $key => $value) {
            $text = str_replace('{' . $key . '}', $value, $text);
        }
        return $text;
    }
}
