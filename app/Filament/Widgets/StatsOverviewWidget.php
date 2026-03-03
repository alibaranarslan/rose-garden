<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Payment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;
    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        $todayOrders = Order::whereDate('created_at', today())->count();
        $todayRevenue = Order::whereDate('created_at', today())
            ->whereNotIn('status', ['cancelled', 'refunded'])->sum('total');
        $pendingOrders = Order::pending()->count();
        $awaitingBankTransfer = Order::awaitingBankTransfer()->count();

        return [
            Stat::make('Bugünkü Sipariş', $todayOrders)
                ->description('Bugün verilen sipariş sayısı')
                ->icon('heroicon-o-shopping-cart')
                ->color('primary'),

            Stat::make('Bugünkü Ciro', '₺' . number_format($todayRevenue, 2))
                ->description('Bugünkü toplam gelir')
                ->icon('heroicon-o-banknotes')
                ->color('success'),

            Stat::make('Bekleyen Sipariş', $pendingOrders)
                ->description('İşlem bekleyen siparişler')
                ->icon('heroicon-o-clock')
                ->color($pendingOrders > 10 ? 'danger' : 'warning'),

            Stat::make('Havale Onayı', $awaitingBankTransfer)
                ->description('Onay bekleyen havale ödemeleri')
                ->icon('heroicon-o-banknotes')
                ->color($awaitingBankTransfer > 0 ? 'danger' : 'success'),
        ];
    }
}
