<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;

class OrderStatusWidget extends ChartWidget
{
    protected static ?string $heading = 'Sipariş Durum Dağılımı';
    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $statuses = Order::selectRaw('status, count(*) as count')
            ->whereDate('created_at', '>=', now()->subDays(30))
            ->groupBy('status')
            ->pluck('count', 'status');

        $labels = [
            'pending' => 'Bekliyor',
            'awaiting_payment' => 'Ödeme Bekleniyor',
            'paid' => 'Ödendi',
            'preparing' => 'Hazırlanıyor',
            'on_the_way' => 'Yolda',
            'delivered' => 'Teslim',
            'cancelled' => 'İptal',
            'refunded' => 'İade',
        ];

        return [
            'datasets' => [
                [
                    'data' => $statuses->values()->toArray(),
                    'backgroundColor' => ['#6b7280', '#f59e0b', '#3b82f6', '#8b5cf6', '#06b6d4', '#10b981', '#ef4444', '#f97316'],
                ],
            ],
            'labels' => $statuses->keys()->map(fn ($s) => $labels[$s] ?? $s)->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
