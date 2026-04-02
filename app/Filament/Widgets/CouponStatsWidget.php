<?php

namespace App\Filament\Widgets;

use App\Models\Coupon;
use App\Models\CouponUsage;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class CouponStatsWidget extends BaseWidget
{
    protected static ?string $heading = 'Bu Ay Kupon İstatistikleri';
    protected static ?int $sort = 8;
    protected int | string | array $columnSpan = 'full';

    public function getStats(): array
    {
        $startOfMonth = now()->startOfMonth();

        $totalUsage = CouponUsage::where('created_at', '>=', $startOfMonth)->count();
        $totalDiscount = CouponUsage::where('created_at', '>=', $startOfMonth)->sum('discount_amount');

        return compact('totalUsage', 'totalDiscount');
    }

    public function table(Table $table): Table
    {
        $startOfMonth = now()->startOfMonth();

        return $table
            ->query(
                Coupon::withCount(['usages' => fn ($q) => $q->where('created_at', '>=', $startOfMonth)])
                    ->withSum(['usages as monthly_discount' => fn ($q) => $q->where('created_at', '>=', $startOfMonth)], 'discount_amount')
                    ->having('usages_count', '>', 0)
                    ->orderByDesc('usages_count')
                    ->limit(5)
            )
            ->columns([
                TextColumn::make('code')
                    ->label('Kupon Kodu')
                    ->searchable(),

                TextColumn::make('type')
                    ->label('Tür')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'percent'       => '% İndirim',
                        'fixed'         => 'Sabit İndirim',
                        'free_delivery' => 'Ücretsiz Teslimat',
                        default         => $state,
                    })
                    ->colors([
                        'info'    => 'percent',
                        'success' => 'fixed',
                        'warning' => 'free_delivery',
                    ]),

                TextColumn::make('usages_count')
                    ->label('Bu Ay Kullanım')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('monthly_discount')
                    ->label('Bu Ay İndirim Toplamı')
                    ->money('TRY')
                    ->sortable(),
            ])
            ->heading(function () {
                $stats = $this->getStats();
                return "Bu Ay Kupon İstatistikleri — {$stats['totalUsage']} kullanım · " .
                    number_format($stats['totalDiscount'], 2, ',', '.') . ' ₺ toplam indirim';
            });
    }
}
