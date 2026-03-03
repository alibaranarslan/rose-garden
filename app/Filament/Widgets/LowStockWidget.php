<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\ProductResource;
use App\Models\Product;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LowStockWidget extends BaseWidget
{
    protected static ?string $heading = 'Tükenen Ürünler';
    protected static ?int $sort = 8;
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(Product::active()->where('stock_status', 'out_of_stock'))
            ->columns([
                TextColumn::make('name')
                    ->label('Ürün')
                    ->formatStateUsing(fn ($record) => $record->getTranslation('name', 'tr'))
                    ->url(fn ($record) => ProductResource::getUrl('edit', ['record' => $record])),
                TextColumn::make('sku')->label('SKU'),
                TextColumn::make('price')->label('Fiyat')->money('TRY'),
            ]);
    }
}
