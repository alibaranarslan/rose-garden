<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use App\Support\AdminPrivileges;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Müşteriler';
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
            ->query(
                User::customers()
                    ->withCount(['orders', 'favorites', 'customerEvents'])
                    ->withSum(['orders as completed_orders_total' => fn (Builder $query) => $query->where('status', '!=', 'cancelled')], 'total')
            )
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
                    ->getStateUsing(fn ($record) => 'TL '.number_format((float) ($record->completed_orders_total ?? 0), 2, ',', '.'))
                    ->sortable(query: fn (Builder $query, string $direction) => $query->orderBy('completed_orders_total', $direction)),

                TextColumn::make('loyaltyPoints.balance')
                    ->label('Puan')
                    ->numeric(decimalPlaces: 2)
                    ->prefix('TL '),

                TextColumn::make('customer_events_count')
                    ->label('Olay')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('favorites_count')
                    ->label('Favori')
                    ->numeric()
                    ->sortable(),

                IconColumn::make('marketing_consent')
                    ->label('Pazarlama İzni')
                    ->boolean(),

                TextColumn::make('preferred_language')
                    ->label('Dil')
                    ->badge(),

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
                    ->query(fn (Builder $query) => $query->has('orders')),

                Filter::make('vip_customers')
                    ->label('VIP Müşteriler')
                    ->query(fn (Builder $query) => $query->having('completed_orders_total', '>=', 5000)),

                Filter::make('upcoming_events')
                    ->label('Yaklaşan Olayı Olanlar')
                    ->query(fn (Builder $query) => $query->whereHas('customerEvents', fn (Builder $eventQuery) => $eventQuery->where('is_active', true))),

                Filter::make('recoverable_abandoned_carts')
                    ->label('Terk Edilmiş Sepeti Olanlar')
                    ->query(fn (Builder $query) => $query->whereHas('abandonedCarts', fn (Builder $cartQuery) => $cartQuery->where('recovered', false))),

                SelectFilter::make('preferred_language')
                    ->label('Tercih Edilen Dil')
                    ->options([
                        'tr' => 'TR',
                        'en' => 'EN',
                        'ku' => 'KU',
                    ]),
            ])
            ->actions([
                ViewAction::make()->label('Görüntüle'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('export_contacts')
                        ->label('Seçilenleri CSV Aktar')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->action(fn (Collection $records) => static::streamCustomerExport($records, 'customer-segment.csv')),
                    BulkAction::make('export_marketing_contacts')
                        ->label('Pazarlama Listesi CSV')
                        ->icon('heroicon-o-megaphone')
                        ->action(function (Collection $records) {
                            $filtered = $records->filter(fn (User $user) => $user->marketing_consent);

                            return static::streamCustomerExport($filtered, 'marketing-ready-segment.csv');
                        }),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canViewAny(): bool
    {
        return AdminPrivileges::canPublishConfiguration(auth()->user());
    }

    public static function canView($record): bool
    {
        return static::canViewAny() && ! (bool) $record->is_admin;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'view' => Pages\ViewUser::route('/{record}'),
        ];
    }

    private static function streamCustomerExport(Collection $records, string $filename)
    {
        return response()->streamDownload(function () use ($records) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['name', 'email', 'phone', 'preferred_language', 'marketing_consent', 'orders_count', 'total_spent']);

            foreach ($records as $record) {
                fputcsv($handle, [
                    $record->name,
                    $record->email,
                    $record->phone,
                    $record->preferred_language,
                    $record->marketing_consent ? 'yes' : 'no',
                    $record->orders_count,
                    number_format((float) ($record->completed_orders_total ?? 0), 2, '.', ''),
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
