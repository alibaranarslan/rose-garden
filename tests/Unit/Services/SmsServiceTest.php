<?php

namespace Tests\Unit\Services;

use App\Models\Setting;
use App\Services\SmsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SmsServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_setting_can_enable_sms_even_when_env_flag_is_disabled(): void
    {
        config()->set('services.sms.enabled', false);
        config()->set('services.sms.api_url', 'https://sms.example.test');

        Setting::set('sms', 'username', 'panel-user');
        Setting::set('sms', 'password', 'panel-pass');
        Setting::set('sms', 'subscriber_no', '12345');
        Setting::set('sms', 'sender_title', 'ROSEGARDEN');
        Setting::set('sms', 'enabled', '1');

        $service = app(SmsService::class);

        $this->assertTrue($service->isEnabled());
        $this->assertTrue($service->isConfigured());
        $this->assertTrue($service->canSend());
    }

    public function test_admin_setting_can_disable_sms_even_when_env_flag_is_enabled(): void
    {
        config()->set('services.sms.enabled', true);
        config()->set('services.sms.api_url', 'https://sms.example.test');

        Setting::set('sms', 'username', 'panel-user');
        Setting::set('sms', 'password', 'panel-pass');
        Setting::set('sms', 'subscriber_no', '12345');
        Setting::set('sms', 'sender_title', 'ROSEGARDEN');
        Setting::set('sms', 'enabled', '0');

        $service = app(SmsService::class);

        $this->assertFalse($service->isEnabled());
        $this->assertTrue($service->isConfigured());
        $this->assertFalse($service->canSend());
    }
}
