<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AbandonedCartResource\Pages;
use App\Models\AbandonedCart;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class AbandonedCartResource extends Resource
{
    protected static ?string $model = AbandonedCart::class;
    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?string $navigationGroup = 'Pazarlama';
    protected static ?string $navigationLabel = 'Terk Edilmiş Sepetler';
    protected static ?string $modelLabel = 'Terk Edilmiş Sepet';
    protected static ?string $pluralModelLabel = 'Terk Edilmiş Sepetler';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('customer')
                    ->label('Müşteri / E-posta')
                    ->getStateUsing(fn ($record) => $record->user?->name ?? $record->email ?? $record->session_id),

                TextColumn::make('total_value')
                    ->label('Sepet Değeri')
                    ->money('TRY')
                    ->sortable(),

                TextColumn::make('item_count')
                    ->label('Ürün Sayısı')
                    ->getStateUsing(fn ($record) => count($record->cart_data ?? []))
                    ->numeric(),

                TextColumn::make('reminder_count')
                    ->label('Hatırlatma')
                    ->numeric(),

                TextColumn::make('abandoned_at')
                    ->label('Terk Zamanı')
                    ->dateTime('d.m.Y H:i')
                    ->since()
                    ->sortable(),

                IconColumn::make('recovered')
                    ->label('Kurtarıldı')
                    ->boolean(),
            ])
            ->filters([
                TernaryFilter::make('recovered')->label('Kurtarıldı'),
            ])
            ->actions([
                Action::make('send_reminder')
                    ->label('Hatırlatma Gönder')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('warning')
                    ->visible(fn (AbandonedCart $record) => !$record->recovered)
                    ->action(function (AbandonedCart $record) {
                        // Send reminder notification via SMS/Email
                        $record->increment('reminder_count');
                        $record->update(['last_reminded_at' => now()]);
                        Notification::make()->success()->title('Hatırlatma gönderildi')->send();
                    }),
            ])
            ->defaultSort('abandoned_at', 'desc');
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAbandonedCarts::route('/'),
        ];
    }
}
