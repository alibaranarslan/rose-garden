<?php

namespace App\Filament\Pages;

use App\Models\AnalyticsPageView;
use App\Models\Order;
use App\Models\Product;
use Filament\Pages\Page;
use Illuminate\Support\Carbon;

class ReportsAnalytics extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationLabel = 'Raporlar & Analitik';
    protected static ?string $title = 'Raporlar & Analitik';
    protected static ?int $navigationSort = 18;
    protected static string $view = 'filament.pages.reports-analytics';

    public string $period = '30days';
    public ?string $dateFrom = null;
    public ?string $dateTo = null;

    public function mount(): void
    {
        $this->dateFrom = now()->subDays(30)->format('Y-m-d');
        $this->dateTo = now()->format('Y-m-d');
    }

    public function setPeriod(string $period): void
    {
        $this->period = $period;
        $this->dateFrom = match ($period) {
            'today' => now()->format('Y-m-d'),
            '7days' => now()->subDays(7)->format('Y-m-d'),
            '30days' => now()->subDays(30)->format('Y-m-d'),
            default => $this->dateFrom,
        };
        $this->dateTo = now()->format('Y-m-d');
    }

    public function getViewData(): array
    {
        $from = Carbon::parse($this->dateFrom)->startOfDay();
        $to = Carbon::parse($this->dateTo)->endOfDay();

        $totalRevenue = Order::whereBetween('created_at', [$from, $to])
            ->whereNotIn('status', ['cancelled', 'refunded'])->sum('total');

        $totalOrders = Order::whereBetween('created_at', [$from, $to])->count();

        $avgOrderValue = $totalOrders > 0 ? round($totalRevenue / $totalOrders, 2) : 0;

        $topProducts = Product::withSum(
            ['orderItems as revenue' => fn ($q) => $q->whereHas('order',
                fn ($q) => $q->whereBetween('created_at', [$from, $to]))],
            'total_price'
        )
        ->orderByDesc('revenue')
        ->limit(10)
        ->get();

        $dailyRevenue = Order::whereBetween('created_at', [$from, $to])
            ->whereNotIn('status', ['cancelled', 'refunded'])
            ->selectRaw('DATE(created_at) as date, sum(total) as revenue, count(*) as orders')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return compact('totalRevenue', 'totalOrders', 'avgOrderValue', 'topProducts', 'dailyRevenue');
    }
}
