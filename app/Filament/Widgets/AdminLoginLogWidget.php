<?php

namespace App\Filament\Widgets;

use App\Models\AdminLoginLog;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class AdminLoginLogWidget extends BaseWidget
{
    protected static ?string $heading = 'Son Admin Girişleri';
    protected static ?int $sort = 9;
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                AdminLoginLog::with('user')
                    ->orderByDesc('created_at')
                    ->limit(15)
            )
            ->columns([
                TextColumn::make('user.name')
                    ->label('Kullanıcı')
                    ->searchable(),

                BadgeColumn::make('action')
                    ->label('İşlem')
                    ->colors([
                        'success' => 'login',
                        'warning' => 'logout',
                        'danger'  => 'failed_login',
                    ])
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'login'        => 'Giriş',
                        'logout'       => 'Çıkış',
                        'failed_login' => 'Başarısız Giriş',
                        default        => $state,
                    }),

                TextColumn::make('ip_address')
                    ->label('IP Adresi'),

                TextColumn::make('user_agent')
                    ->label('Tarayıcı')
                    ->limit(40)
                    ->tooltip(fn ($record) => $record->user_agent),

                TextColumn::make('created_at')
                    ->label('Tarih')
                    ->dateTime('d.m.Y H:i:s')
                    ->sortable(),
            ]);
    }
}
