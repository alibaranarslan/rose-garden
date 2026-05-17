<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NotificationLogResource\Pages;
use App\Models\NotificationLog;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class NotificationLogResource extends Resource
{
    protected static ?string $model = NotificationLog::class;
    protected static ?string $navigationIcon = 'heroicon-o-inbox';
    protected static ?string $navigationGroup = 'İletişim';
    protected static ?string $navigationLabel = 'Bildirim Geçmişi';
    protected static ?string $modelLabel = 'Bildirim Kaydı';
    protected static ?string $pluralModelLabel = 'Bildirim Geçmişi';
    protected static ?int $navigationSort = 6;

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->with(['template', 'order']))
            ->columns([
                TextColumn::make('template.name')
                    ->label('Template')
                    ->default('-')
                    ->toggleable(),

                BadgeColumn::make('channel')
                    ->label('Kanal')
                    ->colors(['primary' => 'sms', 'info' => 'email'])
                    ->formatStateUsing(fn ($state) => $state === 'sms' ? 'SMS' : 'E-posta'),

                TextColumn::make('recipient')->label('Alıcı'),

                TextColumn::make('subject')->label('Konu')->limit(40),

                BadgeColumn::make('status')
                    ->label('Durum')
                    ->colors(['success' => 'sent', 'danger' => 'failed', 'warning' => 'queued'])
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'sent' => 'Gönderildi', 'failed' => 'Hata', 'queued' => 'Kuyrukta', default => $state,
                    }),

                TextColumn::make('order.order_number')
                    ->label('Sipariş')
                    ->default('-')
                    ->toggleable(),

                TextColumn::make('error_message')
                    ->label('Hata')
                    ->limit(48)
                    ->tooltip(fn ($record) => $record->error_message)
                    ->toggleable(),

                TextColumn::make('sent_at')
                    ->label('Gönderim')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Kayıt')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('channel')->label('Kanal')
                    ->options(['sms' => 'SMS', 'email' => 'E-posta']),
                SelectFilter::make('status')->label('Durum')
                    ->options(['sent' => 'Gönderildi', 'failed' => 'Hata', 'queued' => 'Kuyrukta']),
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
            'index' => Pages\ListNotificationLogs::route('/'),
        ];
    }
}
