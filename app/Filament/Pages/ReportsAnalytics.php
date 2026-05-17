<?php

namespace App\Filament\Pages;

use App\Models\AnalyticsPageView;
use App\Models\Order;
use App\Models\Product;
use Filament\Pages\Page;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportsAnalytics extends Page
{
    private const REVENUE_EXCLUDED_STATUSES = ['cancelled', 'refunded'];

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationLabel = 'Raporlar ve Analitik';

    protected static ?string $title = 'Raporlar ve Analitik';
    protected static ?string $navigationGroup = 'Analiz';

    protected static ?int $navigationSort = 18;

    protected static string $view = 'filament.pages.reports-analytics-studio';

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
        $this->period = in_array($period, ['today', '7days', '30days'], true) ? $period : '30days';
        $this->dateFrom = match ($period) {
            'today' => now()->format('Y-m-d'),
            '7days' => now()->subDays(7)->format('Y-m-d'),
            '30days' => now()->subDays(30)->format('Y-m-d'),
            default => now()->subDays(30)->format('Y-m-d'),
        };
        $this->dateTo = now()->format('Y-m-d');
    }

    public function updatedDateFrom(mixed $value): void
    {
        $this->dateFrom = $this->normalizeDateInput($value, now()->subDays(30)->format('Y-m-d'));
    }

    public function updatedDateTo(mixed $value): void
    {
        $this->dateTo = $this->normalizeDateInput($value, now()->format('Y-m-d'));
    }

    public function getViewData(): array
    {
        [$from, $to] = $this->resolveSelectedRange();

        $current = $this->buildSnapshot($from, $to);
        $previousRange = $this->resolvePreviousRange($from, $to);
        $previous = $this->buildSnapshot($previousRange['from'], $previousRange['to']);

        return array_merge($current, [
            'comparison' => [
                'revenue' => $this->calculateDelta($current['totalRevenue'], $previous['totalRevenue']),
                'orders' => $this->calculateDelta($current['totalOrders'], $previous['totalOrders']),
                'aov' => $this->calculateDelta($current['avgOrderValue'], $previous['avgOrderValue']),
            ],
            'previousPeriodLabel' => $previousRange['from']->format('d.m.Y') . ' - ' . $previousRange['to']->format('d.m.Y'),
            'periodLabel' => $from->format('d.m.Y') . ' - ' . $to->format('d.m.Y'),
        ]);
    }

    public function exportCsv(): StreamedResponse
    {
        $data = $this->getViewData();

        return response()->streamDownload(function () use ($data) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, ['metric', 'value']);
            fputcsv($handle, ['total_revenue', number_format((float) $data['totalRevenue'], 2, '.', '')]);
            fputcsv($handle, ['total_orders', $data['totalOrders']]);
            fputcsv($handle, ['average_order_value', number_format((float) $data['avgOrderValue'], 2, '.', '')]);
            fputcsv($handle, ['repeat_customer_rate', $data['repeatCustomerRate']]);
            fputcsv($handle, ['coupon_usage_rate', $data['couponUsageRate']]);
            fputcsv($handle, []);

            fputcsv($handle, ['top_products']);
            fputcsv($handle, ['product_name', 'revenue']);
            foreach ($data['topProducts'] as $product) {
                fputcsv($handle, [
                    $product->getTranslation('name', 'tr'),
                    number_format((float) ($product->revenue ?? 0), 2, '.', ''),
                ]);
            }

            fputcsv($handle, []);
            fputcsv($handle, ['status_breakdown']);
            fputcsv($handle, ['status', 'count']);
            foreach ($data['statusBreakdown'] as $status => $count) {
                fputcsv($handle, [$status, $count]);
            }

            fputcsv($handle, []);
            fputcsv($handle, ['daily_revenue']);
            fputcsv($handle, ['date', 'orders', 'revenue']);
            foreach ($data['dailyRevenue'] as $row) {
                fputcsv($handle, [$row->date, $row->orders, number_format((float) $row->revenue, 2, '.', '')]);
            }

            fclose($handle);
        }, 'rose-garden-analytics.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    private function buildSnapshot(Carbon $from, Carbon $to): array
    {
        $revenueOrders = $this->revenueOrdersBetween($from, $to);
        $totalRevenue = (clone $revenueOrders)->sum('total');

        $totalOrders = Order::whereBetween('created_at', [$from, $to])->count();
        $revenueOrderCount = (clone $revenueOrders)->count();
        $avgOrderValue = $revenueOrderCount > 0 ? round($totalRevenue / $revenueOrderCount, 2) : 0;

        $topProducts = Product::withSum(
            ['orderItems as revenue' => fn ($query) => $query->whereHas('order', fn ($orderQuery) => $orderQuery
                ->whereBetween('created_at', [$from, $to])
                ->whereNotIn('status', self::REVENUE_EXCLUDED_STATUSES)
            )],
            'total_price'
        )
            ->orderByDesc('revenue')
            ->limit(10)
            ->get()
            ->filter(fn (Product $product): bool => (float) ($product->revenue ?? 0) > 0)
            ->values();

        $dailyRevenue = $this->revenueOrdersBetween($from, $to)
            ->selectRaw('DATE(created_at) as date, sum(total) as revenue, count(*) as orders')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $statusBreakdown = Order::whereBetween('created_at', [$from, $to])
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        $repeatCustomerRate = Order::whereBetween('created_at', [$from, $to])
            ->whereNotNull('user_id')
            ->selectRaw('user_id, count(*) as orders_count')
            ->groupBy('user_id')
            ->get()
            ->pipe(function ($rows) {
                $total = $rows->count();

                if ($total === 0) {
                    return 0;
                }

                return round(($rows->where('orders_count', '>', 1)->count() / $total) * 100, 1);
            });

        $couponUsageRate = $this->revenueOrdersBetween($from, $to)
            ->whereNotNull('coupon_id')
            ->count();
        $couponUsageRate = $revenueOrderCount > 0 ? round(($couponUsageRate / $revenueOrderCount) * 100, 1) : 0;

        $deviceDistribution = AnalyticsPageView::whereBetween('viewed_at', [$from, $to])
            ->selectRaw('COALESCE(device_type, "unknown") as device_type, count(*) as count')
            ->groupBy('device_type')
            ->pluck('count', 'device_type');

        $refererDistribution = AnalyticsPageView::whereBetween('viewed_at', [$from, $to])
            ->get(['referer'])
            ->groupBy(fn (AnalyticsPageView $view): string => $this->resolveTrafficSource($view->referer))
            ->map(fn ($group) => $group->count())
            ->sortDesc();

        return compact(
            'totalRevenue',
            'totalOrders',
            'avgOrderValue',
            'topProducts',
            'dailyRevenue',
            'statusBreakdown',
            'repeatCustomerRate',
            'couponUsageRate',
            'deviceDistribution',
            'refererDistribution'
        );
    }

    private function resolveSelectedRange(): array
    {
        try {
            $from = Carbon::parse($this->dateFrom)->startOfDay();
        } catch (\Throwable) {
            $from = now()->subDays(30)->startOfDay();
            $this->dateFrom = $from->format('Y-m-d');
        }

        try {
            $to = Carbon::parse($this->dateTo)->endOfDay();
        } catch (\Throwable) {
            $to = now()->endOfDay();
            $this->dateTo = $to->format('Y-m-d');
        }

        if ($from->greaterThan($to)) {
            [$from, $to] = [$to->copy()->startOfDay(), $from->copy()->endOfDay()];
            $this->dateFrom = $from->format('Y-m-d');
            $this->dateTo = $to->format('Y-m-d');
        }

        $todayEnd = now()->endOfDay();
        if ($to->greaterThan($todayEnd)) {
            $to = $todayEnd;
            $this->dateTo = $to->format('Y-m-d');
        }

        if ($from->diffInDays($to) > 365) {
            $from = $to->copy()->subDays(365)->startOfDay();
            $this->dateFrom = $from->format('Y-m-d');
        }

        return [$from, $to];
    }

    private function normalizeDateInput(mixed $value, string $fallback): string
    {
        try {
            return Carbon::parse((string) $value)->format('Y-m-d');
        } catch (\Throwable) {
            return $fallback;
        }
    }

    private function revenueOrdersBetween(Carbon $from, Carbon $to)
    {
        return Order::whereBetween('created_at', [$from, $to])
            ->whereNotIn('status', self::REVENUE_EXCLUDED_STATUSES);
    }

    private function resolvePreviousRange(Carbon $from, Carbon $to): array
    {
        $daySpan = max($from->copy()->startOfDay()->diffInDays($to->copy()->endOfDay()) + 1, 1);
        $previousTo = $from->copy()->subDay()->endOfDay();
        $previousFrom = $previousTo->copy()->subDays($daySpan - 1)->startOfDay();

        return [
            'from' => $previousFrom,
            'to' => $previousTo,
        ];
    }

    private function calculateDelta(int|float $current, int|float $previous): array
    {
        $difference = $current - $previous;
        $percentage = $previous > 0 ? round(($difference / $previous) * 100, 1) : null;

        return [
            'difference' => $difference,
            'percentage' => $percentage,
            'direction' => $difference >= 0 ? 'up' : 'down',
        ];
    }

    private function resolveTrafficSource(?string $referer): string
    {
        if (blank($referer)) {
            return 'direct';
        }

        $host = strtolower((string) parse_url($referer, PHP_URL_HOST));

        if ($host === '') {
            return 'referral';
        }

        foreach (['google.', 'bing.', 'yandex.', 'duckduckgo.'] as $needle) {
            if (str_contains($host, $needle)) {
                return 'search';
            }
        }

        foreach (['facebook.', 'instagram.', 'twitter.', 'x.com', 't.co', 'linkedin.', 'youtube.', 'tiktok.'] as $needle) {
            if (str_contains($host, $needle)) {
                return 'social';
            }
        }

        return 'referral';
    }
}
