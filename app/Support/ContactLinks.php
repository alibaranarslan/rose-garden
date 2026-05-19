<?php

namespace App\Support;

use Illuminate\Support\Collection;

class ContactLinks
{
    public static function phoneForTel(mixed $contact, string $fallback = '0552 271 70 67'): string
    {
        $phone = preg_replace('/\D+/', '', self::contactValue($contact, 'contact_phone', $fallback));

        if ($phone === '') {
            $phone = preg_replace('/\D+/', '', $fallback);
        }

        return str_starts_with($phone, '0') ? '90'.substr($phone, 1) : $phone;
    }

    public static function phoneForWhatsApp(mixed $contact, string $fallback = '0552 271 70 67'): string
    {
        $contactPhone = self::contactValue($contact, 'contact_phone', $fallback);
        $whatsAppPhone = self::contactValue($contact, 'whatsapp_phone', $contactPhone);

        $phone = preg_replace('/\D+/', '', $whatsAppPhone !== '' ? $whatsAppPhone : $contactPhone);

        if ($phone === '') {
            $phone = preg_replace('/\D+/', '', $fallback);
        }

        if (str_starts_with($phone, '00')) {
            $phone = substr($phone, 2);
        }

        if (str_starts_with($phone, '0')) {
            return '90'.substr($phone, 1);
        }

        if (strlen($phone) === 10 && str_starts_with($phone, '5')) {
            return '90'.$phone;
        }

        return $phone;
    }

    private static function contactValue(mixed $contact, string $key, string $fallback = ''): string
    {
        if ($contact instanceof Collection) {
            return trim((string) $contact->get($key, $fallback));
        }

        return trim((string) data_get($contact, $key, $fallback));
    }
}
