<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\ProductResource;
use App\Models\OrderItem;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class TopProductsWidget extends BaseWidget
{
    protected static ?string $heading = 'En Çok Satan Ürünler (Son 30 Gün)';
    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                \App\Models\Product::withSum(['orderItems as total_revenue' => fn (Builder $q) =>
                    $q->whereHas('order', fn ($q) => $q->whereDate('created_at', '>=', now()->subDays(30)))
                ], 'total_price')
                ->withCount(['orderItems as units_sold' => fn (Builder $q) =>
                    $q->whereHas('order', fn ($q) => $q->whereDate('created_at', '>=', now()->subDays(30)))
                ])
                ->orderByDesc('units_sold')
                ->limit(5)
            )
            ->columns([
                TextColumn::make('name')
                    ->label('Ürün')
                    ->formatStateUsing(fn ($record) => $record->getTranslation('name', 'tr'))
                    ->url(fn ($record) => ProductResource::getUrl('edit', ['record' => $record])),
                TextColumn::make('units_sold')->label('Satış Adedi')->numeric(),
                TextColumn::make('total_revenue')->label('Gelir')->money('TRY'),
            ]);
    }
}
