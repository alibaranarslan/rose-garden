<?php

namespace App\Services;

use App\Models\NotificationLog;
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
        $this->apiUrl       = config('services.sms.api_url', '');
        $this->username     = config('services.sms.username', '');
        $this->password     = config('services.sms.password', '');
        $this->subscriberNo = config('services.sms.subscriber_no', '');
        $this->senderTitle  = config('services.sms.sender_title', 'ROSEGARDEN');
        $this->enabled      = config('services.sms.enabled', false);
    }

    public function send(string $phone, string $message): bool
    {
        $phone = $this->formatPhone($phone);

        if (!$this->enabled) {
            Log::info('SMS gönderimi devre dışı (SMS_ENABLED=false)', [
                'phone'   => $phone,
                'message' => $message,
            ]);
            $this->logNotification($phone, $message, 'skipped', null, 'SMS devre dışı');
            return true;
        }

        if (empty($this->apiUrl) || empty($this->username)) {
            Log::warning('SMS yapılandırması eksik');
            return false;
        }

        try {
            $response = Http::timeout(15)->post($this->apiUrl, [
                'username'     => $this->username,
                'password'     => $this->password,
                'subscriber_no' => $this->subscriberNo,
                'header'       => $this->senderTitle,
                'message'      => $message,
                'numbers'      => $phone,
            ]);

            $success = $response->successful();

            $this->logNotification(
                $phone,
                $message,
                $success ? 'sent' : 'failed',
                null,
                $success ? null : $response->body()
            );

            if (!$success) {
                Log::warning('SMS gönderimi başarısız', [
                    'phone'  => $phone,
                    'status' => $response->status(),
                    'body'   => $response->body(),
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
            return '+' . $phone;
        }

        if (str_starts_with($phone, '0') && strlen($phone) === 11) {
            return '+9' . $phone;
        }

        if (strlen($phone) === 10) {
            return '+90' . $phone;
        }

        return '+' . $phone;
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
                'channel'       => 'sms',
                'recipient'     => $phone,
                'user_id'       => $userId,
                'body'          => $message,
                'status'        => $status,
                'error_message' => $errorMessage,
                'sent_at'       => $status === 'sent' ? now() : null,
                'created_at'    => now(),
            ]);
        } catch (\Exception $e) {
            Log::warning('SMS log kaydedilemedi', ['message' => $e->getMessage()]);
        }
    }
}
