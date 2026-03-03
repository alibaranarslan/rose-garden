<?php

namespace App\Filament\Widgets;

use App\Models\AbandonedCart;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AbandonedCartWidget extends BaseWidget
{
    protected static ?int $sort = 7;

    protected function getStats(): array
    {
        $count = AbandonedCart::notRecovered()->count();
        $totalValue = AbandonedCart::notRecovered()->sum('total_value');

        return [
            Stat::make('Terk Edilmiş Sepet', $count)
                ->description('Kurtarılmamış sepet sayısı')
                ->icon('heroicon-o-shopping-bag')
                ->color($count > 0 ? 'warning' : 'success'),

            Stat::make('Kayıp Değer', '₺' . number_format($totalValue, 2))
                ->description('Kurtarılmamış sepetlerin toplam değeri')
                ->icon('heroicon-o-banknotes')
                ->color('danger'),
        ];
    }
}
