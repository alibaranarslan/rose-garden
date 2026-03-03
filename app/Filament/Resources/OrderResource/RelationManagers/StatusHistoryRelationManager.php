<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class StatusHistoryRelationManager extends RelationManager
{
    protected static string $relationship = 'statusHistory';
    protected static ?string $title = 'Durum Geçmişi';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label('Tarih')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Durum')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'pending' => 'Bekliyor',
                        'awaiting_payment' => 'Ödeme Bekleniyor',
                        'paid' => 'Ödendi',
                        'preparing' => 'Hazırlanıyor',
                        'on_the_way' => 'Yolda',
                        'delivered' => 'Teslim Edildi',
                        'cancelled' => 'İptal',
                        'refunded' => 'İade',
                        default => $state,
                    }),

                TextColumn::make('note')
                    ->label('Not'),

                TextColumn::make('changedBy.name')
                    ->label('Değiştiren'),
            ])
            ->defaultSort('created_at', 'asc');
    }
}
