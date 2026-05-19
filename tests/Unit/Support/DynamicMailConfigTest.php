<?php

namespace Tests\Unit\Support;

use App\Models\Setting;
use App\Support\DynamicMailConfig;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DynamicMailConfigTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_applies_database_backed_mail_configuration(): void
    {
        config([
            'mail.default' => 'log',
            'mail.mailers.smtp.host' => 'env-host',
            'mail.mailers.smtp.port' => 2525,
            'mail.from.address' => 'env@example.com',
            'mail.from.name' => 'Env Sender',
        ]);

        Setting::set('email', 'smtp_host', 'smtp.rose.test');
        Setting::set('email', 'smtp_port', '465');
        Setting::set('email', 'smtp_username', 'rose-user');
        Setting::set('email', 'smtp_password', 'rose-pass');
        Setting::set('email', 'smtp_encryption', 'ssl');
        Setting::set('email', 'from_name', 'Rose Sender');
        Setting::set('email', 'from_email', 'hello@rose.test');

        DynamicMailConfig::apply();

        $this->assertSame('smtp', config('mail.default'));
        $this->assertSame('smtp.rose.test', config('mail.mailers.smtp.host'));
        $this->assertSame(465, config('mail.mailers.smtp.port'));
        $this->assertSame('rose-user', config('mail.mailers.smtp.username'));
        $this->assertSame('hello@rose.test', config('mail.from.address'));
        $this->assertSame('Rose Sender', config('mail.from.name'));
    }

    public function test_sender_only_database_settings_do_not_switch_mailer_to_smtp(): void
    {
        config([
            'mail.default' => 'log',
            'mail.mailers.smtp.host' => '127.0.0.1',
            'mail.mailers.smtp.port' => 2525,
            'mail.mailers.smtp.username' => null,
            'mail.mailers.smtp.password' => null,
            'mail.from.address' => 'env@example.com',
            'mail.from.name' => 'Env Sender',
        ]);

        Setting::set('email', 'from_name', 'Rose Garden Çiçek Çikolata');
        Setting::set('email', 'from_email', 'info@rosegardencicekcilik.com.tr');

        DynamicMailConfig::apply();

        $this->assertSame('log', config('mail.default'));
        $this->assertSame('info@rosegardencicekcilik.com.tr', config('mail.from.address'));
        $this->assertSame('Rose Garden Çiçek Çikolata', config('mail.from.name'));
    }
}
