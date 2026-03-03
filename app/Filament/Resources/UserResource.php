<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Müşteriler';
    protected static ?string $modelLabel = 'Müşteri';
    protected static ?string $pluralModelLabel = 'Müşteriler';
    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(User::customers()->withCount('orders'))
            ->columns([
                TextColumn::make('name')
                    ->label('Ad')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->label('E-posta')
                    ->searchable(),

                TextColumn::make('phone')
                    ->label('Telefon')
                    ->searchable(),

                TextColumn::make('orders_count')
                    ->label('Sipariş')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('total_spent')
                    ->label('Toplam Harcama')
                    ->getStateUsing(fn ($record) => '₺' . number_format($record->orders()->where('status', '!=', 'cancelled')->sum('total'), 2)),

                TextColumn::make('loyaltyPoints.balance')
                    ->label('Puan')
                    ->numeric()
                    ->prefix('₺'),

                IconColumn::make('marketing_consent')
                    ->label('Pazarlama İzni')
                    ->boolean(),

                TextColumn::make('created_at')
                    ->label('Kayıt')
                    ->date('d.m.Y')
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('marketing_consent')
                    ->label('Pazarlama İzni'),

                Filter::make('has_orders')
                    ->label('Sipariş Verenler')
                    ->query(fn ($q) => $q->has('orders')),
            ])
            ->actions([
                ViewAction::make()->label('Görüntüle'),
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
            'index' => Pages\ListUsers::route('/'),
            'view' => Pages\ViewUser::route('/{record}'),
        ];
    }
}
