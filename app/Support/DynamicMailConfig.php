<?php

namespace App\Support;

use App\Models\Setting;
use Illuminate\Mail\MailManager;
use Illuminate\Support\Facades\Schema;

class DynamicMailConfig
{
    public static function apply(): void
    {
        try {
            if (! Schema::hasTable('settings')) {
                return;
            }
        } catch (\Throwable) {
            return;
        }

        $storedHost = Setting::get('email', 'smtp_host', '');
        $storedPort = Setting::get('email', 'smtp_port', '');
        $storedUsername = Setting::get('email', 'smtp_username', '');
        $storedPassword = Setting::get('email', 'smtp_password', '');
        $storedEncryption = Setting::get('email', 'smtp_encryption', '');

        $host = $storedHost ?: config('mail.mailers.smtp.host');
        $port = $storedPort ?: config('mail.mailers.smtp.port');
        $username = $storedUsername ?: config('mail.mailers.smtp.username');
        $password = $storedPassword ?: config('mail.mailers.smtp.password');
        $encryption = $storedEncryption ?: config('mail.mailers.smtp.encryption');
        $fromName = Setting::get('email', 'from_name', config('mail.from.name'));
        $fromAddress = Setting::get('email', 'from_email', config('mail.from.address'));
        $hasPanelSmtpConfig = filled($storedHost) || filled($storedUsername) || filled($storedPassword);
        $usesEnvSmtpConfig = config('mail.default') === 'smtp';

        if ($encryption === 'none') {
            $encryption = null;
        }

        $config = [
            'mail.from.address' => $fromAddress,
            'mail.from.name' => $fromName,
        ];

        if ($hasPanelSmtpConfig || $usesEnvSmtpConfig) {
            $config = array_merge($config, [
                'mail.default' => 'smtp',
                'mail.mailers.smtp.transport' => 'smtp',
                'mail.mailers.smtp.host' => $host,
                'mail.mailers.smtp.port' => is_numeric($port) ? (int) $port : $port,
                'mail.mailers.smtp.username' => $username,
                'mail.mailers.smtp.password' => $password,
                'mail.mailers.smtp.encryption' => $encryption,
            ]);
        }

        config($config);

        try {
            app(MailManager::class)->purge();
        } catch (\Throwable) {
        }
    }
}
