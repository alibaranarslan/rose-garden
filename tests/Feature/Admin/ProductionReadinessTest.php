<?php

namespace Tests\Feature\Admin;

use App\Models\Setting;
use App\Services\SmsService;
use App\Support\ProductionReadiness;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductionReadinessTest extends TestCase
{
    use RefreshDatabase;

    public function test_missing_production_inputs_are_reported(): void
    {
        config([
            'mail.default' => 'log',
            'mail.mailers.smtp.host' => '127.0.0.1',
            'mail.mailers.smtp.username' => null,
            'mail.mailers.smtp.password' => null,
            'services.sms.api_url' => '',
            'services.sms.enabled' => false,
            'services.paytr.merchant_id' => '',
            'services.paytr.merchant_key' => '',
            'services.paytr.merchant_salt' => '',
        ]);

        $snapshot = app(ProductionReadiness::class)->snapshot();

        $this->assertSame(0, $snapshot['ready_count']);
        $this->assertSame(5, $snapshot['total_count']);
        $this->assertSame('0/5 başlık hazır', $snapshot['state']);
        $this->assertContains('IBAN', collect($snapshot['items'])->firstWhere('label', 'Havale/EFT')['missing']);
        $this->assertContains('SMTP mailer', collect($snapshot['items'])->firstWhere('label', 'E-posta')['missing']);
        $this->assertContains('SMS API URL', collect($snapshot['items'])->firstWhere('label', 'SMS')['missing']);
    }

    public function test_ready_production_inputs_are_reported(): void
    {
        config([
            'mail.default' => 'smtp',
            'mail.mailers.smtp.host' => 'smtp.example.test',
            'mail.mailers.smtp.username' => 'mailer',
            'mail.mailers.smtp.password' => 'secret',
            'mail.from.address' => 'info@adiyamancicekcisi.com.tr',
            'services.paytr.merchant_id' => '',
            'services.paytr.merchant_key' => '',
            'services.paytr.merchant_salt' => '',
        ]);

        Setting::set('payment', 'bank_name', 'Test Bank');
        Setting::set('payment', 'bank_iban', 'TR120000000000000000000001');
        Setting::set('payment', 'bank_account_holder', 'Rose Garden');
        Setting::set('payment', 'paytr_merchant_id', 'merchant-id');
        Setting::set('payment', 'paytr_merchant_key', 'merchant-key');
        Setting::set('payment', 'paytr_merchant_salt', 'merchant-salt');
        Setting::set('sms', 'api_url', 'https://sms.example.test/send');
        Setting::set('sms', 'username', 'sms-user');
        Setting::set('sms', 'password', 'sms-pass');
        Setting::set('sms', 'subscriber_no', '12345');
        Setting::set('sms', 'enabled', '1');
        Setting::set('seo', 'canonical_domain', 'https://adiyamancicekcisi.com.tr');
        Setting::set('seo', 'meta_description_default', 'Adıyaman çiçek ve çikolata siparişleri.');
        Setting::set('seo', 'google_search_console_code', 'verification-code');
        app()->forgetInstance(SmsService::class);

        $snapshot = app(ProductionReadiness::class)->snapshot();

        $this->assertSame(5, $snapshot['ready_count']);
        $this->assertSame('Canlıya hazır', $snapshot['state']);
        $this->assertTrue(collect($snapshot['items'])->every(fn (array $item): bool => $item['ready'] === true));
    }
}
