<?php

namespace App\Services;

use App\Models\NotificationLog;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    private string $apiUrl;

    private string $username;

    private string $password;

    private string $subscriberNo;

    private string $senderTitle;

    private bool $enabled;

    public function __construct()
    {
        // Admin panelde kaydedilen SMS bilgileri .env/config değerlerini override etsin.
        $this->apiUrl = Setting::get('sms', 'api_url') ?: config('services.sms.api_url', '');
        $this->username = Setting::get('sms', 'username') ?: config('services.sms.username', '');
        $this->password = Setting::get('sms', 'password') ?: config('services.sms.password', '');
        $this->subscriberNo = Setting::get('sms', 'subscriber_no') ?: config('services.sms.subscriber_no', '');
        $this->senderTitle = Setting::get('sms', 'sender_title') ?: config('services.sms.sender_title', 'ROSEGARDEN');
        $this->enabled = $this->resolveEnabledFlag();
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function isConfigured(): bool
    {
        return $this->apiUrl !== ''
            && $this->username !== ''
            && $this->password !== ''
            && $this->subscriberNo !== '';
    }

    public function canSend(): bool
    {
        return $this->isEnabled() && $this->isConfigured();
    }

    public function send(string $phone, string $message): bool
    {
        $phone = $this->formatPhone($phone);

        if (! $this->enabled) {
            Log::info('SMS gönderimi devre dışı (SMS_ENABLED=false)', [
                'phone' => $phone,
                'message' => $message,
            ]);
            $this->logNotification($phone, $message, 'skipped', null, 'SMS devre dışı');

            return true;
        }

        if (! $this->isConfigured()) {
            Log::warning('SMS yapılandırması eksik');

            return false;
        }

        try {
            $response = Http::timeout(15)->post($this->apiUrl, [
                'username' => $this->username,
                'password' => $this->password,
                'subscriber_no' => $this->subscriberNo,
                'header' => $this->senderTitle,
                'message' => $message,
                'numbers' => $phone,
            ]);

            $success = $response->successful();

            $this->logNotification(
                $phone,
                $message,
                $success ? 'sent' : 'failed',
                null,
                $success ? null : $response->body()
            );

            if (! $success) {
                Log::warning('SMS gönderimi başarısız', [
                    'phone' => $phone,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
            }

            return $success;
        } catch (\Exception $e) {
            Log::error('SMS servisi hatası', ['phone' => $phone, 'message' => $e->getMessage()]);
            $this->logNotification($phone, $message, 'failed', null, $e->getMessage());

            return false;
        }
    }

    public function sendBulk(array $phones, string $message): array
    {
        $results = [];
        foreach ($phones as $phone) {
            $results[$phone] = $this->send($phone, $message);
        }

        return $results;
    }

    public function formatPhone(string $phone): string
    {
        $phone = preg_replace('/\D/', '', $phone);

        if (str_starts_with($phone, '90') && strlen($phone) === 12) {
            return '+'.$phone;
        }

        if (str_starts_with($phone, '0') && strlen($phone) === 11) {
            return '+9'.$phone;
        }

        if (strlen($phone) === 10) {
            return '+90'.$phone;
        }

        return '+'.$phone;
    }

    private function resolveEnabledFlag(): bool
    {
        $setting = Setting::get('sms', 'enabled');

        if ($setting !== null && $setting !== '') {
            return (bool) filter_var($setting, FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE);
        }

        return (bool) config('services.sms.enabled', false);
    }

    private function logNotification(
        string $phone,
        string $message,
        string $status,
        ?int $userId = null,
        ?string $errorMessage = null
    ): void {
        try {
            NotificationLog::create([
                'channel' => 'sms',
                'recipient' => $phone,
                'user_id' => $userId,
                'body' => $message,
                'status' => $status,
                'error_message' => $errorMessage,
                'sent_at' => $status === 'sent' ? now() : null,
                'created_at' => now(),
            ]);
        } catch (\Exception $e) {
            Log::warning('SMS log kaydedilemedi', ['message' => $e->getMessage()]);
        }
    }
}
