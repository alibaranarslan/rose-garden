<?php

namespace App\Notifications;

use App\Models\NotificationTemplate;
use App\Models\Order;
use App\Notifications\Channels\SmsChannel;
use App\Notifications\Concerns\ResolvesNotificationRoutes;
use App\Services\SmsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class OrderStatusNotification extends Notification implements ShouldQueue
{
    use Queueable;
    use ResolvesNotificationRoutes;

    private array $variables;

    public function __construct(private Order $order, private string $templateKey = 'order_status')
    {
        $this->variables = $this->buildVariables();
    }

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
        $template = NotificationTemplate::findByKey($this->templateKey);
        $locale = $this->resolveLocale($notifiable);

        $subject = $template
            ? $template->renderEmailSubject($this->variables, $locale, 'Sipariş Durumu')
            : "Sipariş #{$this->order->order_number} Durumu";

        $body = $template
            ? $template->renderEmailBody($this->variables, $locale)
            : $this->defaultEmailBody();

        $actionUrl = $this->resolveActionUrl();
        $actionText = $actionUrl ? $this->resolveActionText() : null;

        return (new MailMessage)
            ->subject($subject)
            ->markdown('emails.notification', [
                'subject' => $subject,
                'body' => $body,
                'order' => $this->order,
                'actionUrl' => $actionUrl,
                'actionText' => $actionText,
            ]);
    }

    public function toSms(object $notifiable): void
    {
        $template = NotificationTemplate::findByKey($this->templateKey);
        $phone = $this->resolveSmsRoute($notifiable);

        if (! $template) {
            Log::warning("SMS şablonu bulunamadı: {$this->templateKey}");

            return;
        }

        $message = $template->renderSms($this->variables, $this->resolveLocale($notifiable));

        if (! empty($phone)) {
            app(SmsService::class)->send($phone, $message);
        }
    }

    public function toArray(object $notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'status' => $this->order->status,
            'template_key' => $this->templateKey,
        ];
    }

    private function buildVariables(): array
    {
        return [
            'musteri_adi' => $this->order->sender_name ?? '',
            'siparis_no' => $this->order->order_number,
            'siparis_tarihi' => $this->order->created_at?->format('d.m.Y H:i') ?? now()->format('d.m.Y H:i'),
            'siparis_tutari' => number_format((float) $this->order->total, 2, ',', '.').' TL',
            'odeme_yontemi' => $this->getPaymentMethodLabel($this->order->payment_method),
            'durum' => $this->getStatusLabel($this->order->status),
            'takip_linki' => $this->resolveActionUrl() ?? route('order.track'),
            'alici_adi' => $this->order->recipient_name ?? '',
        ];
    }

    private function getStatusLabel(string $status): string
    {
        return match ($status) {
            'pending' => 'Beklemede',
            'awaiting_payment' => 'Ödeme Bekleniyor',
            'paid' => 'Ödendi',
            'preparing' => 'Hazırlanıyor',
            'on_the_way' => 'Yolda',
            'delivered' => 'Teslim Edildi',
            'cancelled' => 'İptal Edildi',
            'refunded' => 'İade Edildi',
            default => $status,
        };
    }

    private function defaultEmailBody(): string
    {
        return "Sipariş #{$this->order->order_number} durumunuz: {$this->getStatusLabel($this->order->status)}";
    }

    private function getPaymentMethodLabel(string $paymentMethod): string
    {
        return match ($paymentMethod) {
            'credit_card' => 'Kredi/Banka Kartı',
            'bank_transfer' => 'Havale/EFT',
            default => $paymentMethod,
        };
    }

    private function resolveActionUrl(): ?string
    {
        if ($this->templateKey === 'admin_new_order') {
            return url('/admin/orders');
        }

        if ($this->order->user_id) {
            return route('account.order.show', ['orderNumber' => $this->order->order_number]);
        }

        return route('order.track');
    }

    private function resolveActionText(): string
    {
        if ($this->templateKey === 'admin_new_order') {
            return 'Siparişi Görüntüle';
        }

        return $this->order->user_id ? 'Sipariş Detayını Gör' : 'Sipariş Takip Sayfasına Git';
    }

    private function resolveLocale(object $notifiable): string
    {
        return $notifiable->preferred_language
            ?? $this->order->user?->preferred_language
            ?? 'tr';
    }
}
