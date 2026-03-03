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

class NotificationLogResource extends Resource
{
    protected static ?string $model = NotificationLog::class;
    protected static ?string $navigationIcon = 'heroicon-o-inbox';
    protected static ?string $navigationLabel = 'Bildirim Logları';
    protected static ?string $modelLabel = 'Bildirim Logu';
    protected static ?string $pluralModelLabel = 'Bildirim Logları';
    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
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

                TextColumn::make('sent_at')
                    ->label('Gönderim')
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
