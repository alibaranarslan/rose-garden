<?php

namespace App\Support;

class OrderStatus
{
    public static function label(string $status): string
    {
        return match ($status) {
            'pending' => __('Bekliyor'),
            'awaiting_payment' => __('Ödeme Bekleniyor'),
            'paid' => __('Ödendi'),
            'preparing' => __('Hazırlanıyor'),
            'on_the_way' => __('Yolda'),
            'delivered' => __('Teslim Edildi'),
            'cancelled' => __('İptal Edildi'),
            'refunded' => __('İade Edildi'),
            default => ucfirst($status),
        };
    }

    public static function badgeClass(string $status): string
    {
        return match ($status) {
            'pending' => 'bg-yellow-100 text-yellow-800',
            'awaiting_payment' => 'bg-amber-100 text-amber-800',
            'paid' => 'bg-blue-100 text-blue-800',
            'preparing' => 'bg-violet-100 text-violet-800',
            'on_the_way' => 'bg-indigo-100 text-indigo-800',
            'delivered' => 'bg-green-100 text-green-800',
            'cancelled' => 'bg-red-100 text-red-800',
            'refunded' => 'bg-rose-100 text-rose-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }
}
