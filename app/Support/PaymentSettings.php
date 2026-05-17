<?php

namespace App\Support;

use App\Models\Setting;

class PaymentSettings
{
    public static function bankTransferDetails(): array
    {
        $bankName = trim((string) Setting::get('payment', 'bank_name', ''));
        $iban = preg_replace('/\s+/', '', (string) Setting::get('payment', 'bank_iban', ''));
        $accountHolder = trim((string) Setting::get('payment', 'bank_account_holder', ''));
        $timeoutHours = (int) Setting::get('payment', 'transfer_timeout_hours', 72);

        return [
            'bank_name' => $bankName,
            'bank_iban' => $iban,
            'bank_account_holder' => $accountHolder,
            'transfer_timeout_hours' => $timeoutHours > 0 ? $timeoutHours : 72,
            'configured' => $bankName !== '' && $iban !== '' && $accountHolder !== '',
        ];
    }

    public static function isPaytrConfigured(): bool
    {
        $merchantId = trim((string) (Setting::get('payment', 'paytr_merchant_id') ?: config('services.paytr.merchant_id', '')));
        $merchantKey = trim((string) (Setting::get('payment', 'paytr_merchant_key') ?: config('services.paytr.merchant_key', '')));
        $merchantSalt = trim((string) (Setting::get('payment', 'paytr_merchant_salt') ?: config('services.paytr.merchant_salt', '')));

        return $merchantId !== '' && $merchantKey !== '' && $merchantSalt !== '';
    }
}
