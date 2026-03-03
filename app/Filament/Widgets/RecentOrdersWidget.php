<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\OrderResource;
use App\Models\Order;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentOrdersWidget extends BaseWidget
{
    protected static ?string $heading = 'Son Siparişler';
    protected static ?int $sort = 5;
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(Order::latest()->limit(5))
            ->columns([
                TextColumn::make('order_number')
                    ->label('No')
                    ->url(fn ($record) => OrderResource::getUrl('edit', ['record' => $record])),
                TextColumn::make('sender_name')->label('Müşteri'),
                TextColumn::make('total')->label('Tutar')->money('TRY'),
                BadgeColumn::make('status')
                    ->label('Durum')
                    ->colors([
                        'gray' => 'pending', 'warning' => 'awaiting_payment',
                        'info' => 'paid', 'primary' => 'preparing',
                        'success' => fn ($s) => in_array($s, ['on_the_way', 'delivered']),
                        'danger' => fn ($s) => in_array($s, ['cancelled', 'refunded']),
                    ])
                    ->formatStateUsing(fn ($s) => match ($s) {
                        'pending' => 'Bekliyor', 'awaiting_payment' => 'Ödeme Bekleniyor',
                        'paid' => 'Ödendi', 'preparing' => 'Hazırlanıyor',
                        'on_the_way' => 'Yolda', 'delivered' => 'Teslim',
                        'cancelled' => 'İptal', 'refunded' => 'İade', default => $s,
                    }),
                TextColumn::make('created_at')->label('Tarih')->dateTime('d.m.Y H:i')->since(),
            ]);
    }
}
