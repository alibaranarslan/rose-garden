<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentResource\Pages;
use App\Models\Payment;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;
    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    protected static ?string $navigationLabel = 'Ödemeler';
    protected static ?string $modelLabel = 'Ödeme';
    protected static ?string $pluralModelLabel = 'Ödemeler';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order.order_number')
                    ->label('Sipariş No')
                    ->searchable(),

                BadgeColumn::make('payment_method')
                    ->label('Yöntem')
                    ->colors([
                        'info' => 'credit_card',
                        'warning' => 'bank_transfer',
                    ])
                    ->formatStateUsing(fn ($state) => $state === 'credit_card' ? 'Kredi Kartı' : 'Havale'),

                TextColumn::make('amount')
                    ->label('Tutar')
                    ->money('TRY')
                    ->sortable(),

                BadgeColumn::make('status')
                    ->label('Durum')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'completed',
                        'danger' => 'failed',
                        'gray' => 'refunded',
                    ])
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'pending' => 'Bekliyor',
                        'completed' => 'Tamamlandı',
                        'failed' => 'Başarısız',
                        'refunded' => 'İade',
                        default => $state,
                    }),

                TextColumn::make('transaction_id')
                    ->label('İşlem ID')
                    ->limit(20),

                TextColumn::make('created_at')
                    ->label('Tarih')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Durum')
                    ->options([
                        'pending' => 'Bekliyor',
                        'completed' => 'Tamamlandı',
                        'failed' => 'Başarısız',
                        'refunded' => 'İade',
                    ]),

                SelectFilter::make('payment_method')
                    ->label('Yöntem')
                    ->options([
                        'credit_card' => 'Kredi Kartı',
                        'bank_transfer' => 'Havale',
                    ]),

                Filter::make('awaiting_confirmation')
                    ->label('Havale Onayı Bekleyen')
                    ->query(fn (Builder $q) => $q->where('status', 'pending')->where('payment_method', 'bank_transfer')),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPayments::route('/'),
        ];
    }
}
