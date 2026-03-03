<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';
    protected static ?string $title = 'Ürünler';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('product_name')
                    ->label('Ürün'),

                TextColumn::make('variant_name')
                    ->label('Varyant'),

                TextColumn::make('quantity')
                    ->label('Adet')
                    ->numeric(),

                TextColumn::make('unit_price')
                    ->label('Birim Fiyat')
                    ->money('TRY'),

                TextColumn::make('total_price')
                    ->label('Toplam')
                    ->money('TRY'),

                TextColumn::make('card_message')
                    ->label('Kart Mesajı')
                    ->limit(30),
            ]);
    }
}
