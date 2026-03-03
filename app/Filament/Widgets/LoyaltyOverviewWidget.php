<?php

namespace App\Filament\Widgets;

use App\Models\LoyaltyPoint;
use App\Models\LoyaltyTransaction;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class LoyaltyOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 6;

    protected function getStats(): array
    {
        $totalDistributed = LoyaltyTransaction::where('type', 'earned')->sum('amount');
        $totalUsed = LoyaltyTransaction::where('type', 'spent')->sum('amount');
        $pending = LoyaltyPoint::sum('balance');

        return [
            Stat::make('Dağıtılan Puan', '₺' . number_format($totalDistributed, 2))
                ->icon('heroicon-o-star')
                ->color('primary'),

            Stat::make('Kullanılan Puan', '₺' . number_format($totalUsed, 2))
                ->icon('heroicon-o-arrow-down')
                ->color('warning'),

            Stat::make('Bekleyen Puan', '₺' . number_format($pending, 2))
                ->icon('heroicon-o-clock')
                ->color('info'),
        ];
    }
}
