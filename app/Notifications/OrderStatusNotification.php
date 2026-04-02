<?php

namespace App\Notifications;

use App\Models\NotificationTemplate;
use App\Models\Order;
use App\Services\SmsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class OrderStatusNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private array $variables;

    public function __construct(private Order $order, private string $templateKey = 'order_status')
    {
        $this->variables = $this->buildVariables();
    }

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
        $template = NotificationTemplate::findByKey($this->templateKey);

        $subject = $template
            ? $this->replaceVariables($template->email_subject ?? 'Sipariş Durumu', $this->variables)
            : "Sipariş #{$this->order->order_number} Durumu";

        $body = $template
            ? $template->renderEmailBody($this->variables)
            : $this->defaultEmailBody();

        return (new MailMessage)
            ->subject($subject)
            ->markdown('emails.notification', [
                'body'  => $body,
                'order' => $this->order,
            ]);
    }

    public function toSms(object $notifiable): void
    {
        $template = NotificationTemplate::findByKey($this->templateKey);

        if (!$template) {
            Log::warning("SMS şablonu bulunamadı: {$this->templateKey}");
            return;
        }

        $message = $template->renderSms($this->variables);

        if (!empty($notifiable->phone)) {
            app(SmsService::class)->send($notifiable->phone, $message);
        }
    }

    public function toArray(object $notifiable): array
    {
        return [
            'order_id'     => $this->order->id,
            'order_number' => $this->order->order_number,
            'status'       => $this->order->status,
            'template_key' => $this->templateKey,
        ];
    }

    private function buildVariables(): array
    {
        return [
            'musteri_adi'    => $this->order->sender_name ?? '',
            'siparis_no'     => $this->order->order_number,
            'siparis_tarihi' => $this->order->created_at?->format('d.m.Y H:i') ?? now()->format('d.m.Y H:i'),
            'siparis_tutari' => number_format((float) $this->order->total, 2, ',', '.') . ' ₺',
            'odeme_yontemi'  => $this->order->payment_method,
            'durum'          => $this->getStatusLabel($this->order->status),
            'takip_linki'    => route('order.track', ['order_number' => $this->order->order_number]),
            'alici_adi'      => $this->order->recipient_name ?? '',
        ];
    }

    private function getStatusLabel(string $status): string
    {
        return match ($status) {
            'pending'          => 'Beklemede',
            'awaiting_payment' => 'Ödeme Bekleniyor',
            'paid'             => 'Ödendi',
            'preparing'        => 'Hazırlanıyor',
            'on_the_way'       => 'Yolda',
            'delivered'        => 'Teslim Edildi',
            'cancelled'        => 'İptal Edildi',
            default            => $status,
        };
    }

    private function defaultEmailBody(): string
    {
        return "Sipariş #{$this->order->order_number} durumunuz: {$this->getStatusLabel($this->order->status)}";
    }

    private function replaceVariables(string $text, array $variables): string
    {
        foreach ($variables as $key => $value) {
            $text = str_replace('{' . $key . '}', $value, $text);
        }
        return $text;
    }
}
