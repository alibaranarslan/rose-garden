<?php

namespace App\Support;

use App\Filament\Pages\EmailSettings;
use App\Filament\Pages\PaymentSettings as PaymentSettingsPage;
use App\Filament\Pages\SeoSettings;
use App\Filament\Pages\SmsSettings;
use App\Models\Setting;
use App\Services\SmsService;

class ProductionReadiness
{
    public function snapshot(): array
    {
        $items = [
            $this->bankTransfer(),
            $this->paytr(),
            $this->email(),
            $this->sms(),
            $this->seo(),
        ];

        $readyCount = collect($items)->where('ready', true)->count();

        return [
            'title' => 'Canlıya Hazırlık',
            'summary' => 'Müşteriye açılmadan önce gerçek para, iletişim ve SEO girdilerinin tamamlanma durumunu gösterir.',
            'ready_count' => $readyCount,
            'total_count' => count($items),
            'state' => $readyCount === count($items) ? 'Canlıya hazır' : $readyCount.'/'.count($items).' başlık hazır',
            'tone' => $readyCount === count($items) ? 'success' : 'warning',
            'items' => $items,
        ];
    }

    private function bankTransfer(): array
    {
        $details = PaymentSettings::bankTransferDetails();
        $missing = [];

        if (! $this->filled($details['bank_name'])) {
            $missing[] = 'Banka adı';
        }

        if (! $this->filled($details['bank_iban'])) {
            $missing[] = 'IBAN';
        }

        if (! $this->filled($details['bank_account_holder'])) {
            $missing[] = 'Hesap sahibi';
        }

        return $this->item(
            'Havale/EFT',
            $details['configured'],
            $details['configured'] ? 'Banka bilgileri checkout ve bildirimlerde kullanılabilir.' : 'Banka bilgileri eksik; havale metni üretime hazır değil.',
            $missing,
            PaymentSettingsPage::getUrl(panel: 'admin')
        );
    }

    private function paytr(): array
    {
        $missing = [];

        if (! $this->filled(Setting::get('payment', 'paytr_merchant_id') ?: config('services.paytr.merchant_id'))) {
            $missing[] = 'Merchant ID';
        }

        if (! $this->filled(Setting::get('payment', 'paytr_merchant_key') ?: config('services.paytr.merchant_key'))) {
            $missing[] = 'Merchant Key';
        }

        if (! $this->filled(Setting::get('payment', 'paytr_merchant_salt') ?: config('services.paytr.merchant_salt'))) {
            $missing[] = 'Merchant Salt';
        }

        return $this->item(
            'PayTR',
            empty($missing),
            empty($missing) ? 'Kartla ödeme yapılandırması mevcut.' : 'Kartla ödeme kapalı kalır; checkout havale/EFT akışına düşer.',
            $missing,
            PaymentSettingsPage::getUrl(panel: 'admin')
        );
    }

    private function email(): array
    {
        $mailer = (string) config('mail.default');
        $missing = [];

        if ($mailer !== 'smtp') {
            $missing[] = 'SMTP mailer';
        }

        if (! $this->filled(config('mail.mailers.smtp.host'))) {
            $missing[] = 'SMTP host';
        }

        if (! $this->filled(config('mail.mailers.smtp.username'))) {
            $missing[] = 'SMTP kullanıcı adı';
        }

        if (! $this->filled(config('mail.mailers.smtp.password'))) {
            $missing[] = 'SMTP şifre';
        }

        if (! $this->filled(config('mail.from.address'))) {
            $missing[] = 'Gönderici e-posta';
        }

        return $this->item(
            'E-posta',
            empty($missing),
            empty($missing) ? 'Sipariş ve bildirim e-postaları gerçek SMTP üzerinden çıkabilir.' : 'E-postalar gerçek alıcıya çıkmayabilir; SMTP bilgileri tamamlanmalı.',
            $missing,
            EmailSettings::getUrl(panel: 'admin')
        );
    }

    private function sms(): array
    {
        $sms = app(SmsService::class);
        $missing = [];

        if (! $sms->isEnabled()) {
            $missing[] = 'SMS aktifliği';
        }

        if (! $this->filled(Setting::get('sms', 'api_url') ?: config('services.sms.api_url'))) {
            $missing[] = 'SMS API URL';
        }

        if (! $this->filled(Setting::get('sms', 'username') ?: config('services.sms.username'))) {
            $missing[] = 'SMS kullanıcı adı';
        }

        if (! $this->filled(Setting::get('sms', 'password') ?: config('services.sms.password'))) {
            $missing[] = 'SMS şifre';
        }

        if (! $this->filled(Setting::get('sms', 'subscriber_no') ?: config('services.sms.subscriber_no'))) {
            $missing[] = 'SMS abone no';
        }

        return $this->item(
            'SMS',
            $sms->canSend(),
            $sms->canSend() ? 'SMS gönderimi aktif ve yapılandırılmış.' : 'SMS olmadan sipariş akışı sürer; sağlayıcı bilgisi bekliyor.',
            $missing,
            SmsSettings::getUrl(panel: 'admin')
        );
    }

    private function seo(): array
    {
        $missing = [];

        if (! $this->filled(Setting::get('seo', 'canonical_domain', ''))) {
            $missing[] = 'Canonical domain';
        }

        if (! $this->filled(Setting::get('seo', 'meta_description_default', ''))) {
            $missing[] = 'Varsayılan meta açıklama';
        }

        if (! $this->filled(Setting::get('seo', 'google_search_console_code', ''))) {
            $missing[] = 'Search Console kodu';
        }

        return $this->item(
            'SEO',
            empty($missing),
            empty($missing) ? 'Canonical, meta ve Google doğrulama girdileri hazır.' : 'Robots/sitemap çalışır; üretim SEO girdileri tamamlanmalı.',
            $missing,
            SeoSettings::getUrl(panel: 'admin')
        );
    }

    private function item(string $label, bool $ready, string $message, array $missing, string $url): array
    {
        return [
            'label' => $label,
            'ready' => $ready,
            'status' => $ready ? 'Hazır' : 'Eksik',
            'tone' => $ready ? 'success' : 'warning',
            'message' => $message,
            'missing' => $missing,
            'url' => $url,
        ];
    }

    private function filled(mixed $value): bool
    {
        return trim((string) $value) !== '';
    }
}
