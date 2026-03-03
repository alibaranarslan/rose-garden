<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class SalesChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Son 30 Gün — Günlük Ciro';
    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $data = [];
        $labels = [];

        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $labels[] = $date->format('d.m');
            $data[] = Order::whereDate('created_at', $date)
                ->whereNotIn('status', ['cancelled', 'refunded'])
                ->sum('total');
        }

        return [
            'datasets' => [
                [
                    'label' => 'Ciro (₺)',
                    'data' => $data,
                    'backgroundColor' => 'rgba(16, 185, 129, 0.5)',
                    'borderColor' => '#10b981',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
