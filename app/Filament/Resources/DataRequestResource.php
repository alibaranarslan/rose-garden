<?php

namespace App\Filament\Resources;

use Closure;
use App\Filament\Resources\DataRequestResource\Pages;
use App\Models\DataRequest;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class DataRequestResource extends Resource
{
    protected static ?string $model = DataRequest::class;
    protected static ?string $navigationIcon = 'heroicon-o-shield-check';
    protected static ?string $navigationGroup = 'Müşteriler';
    protected static ?string $navigationLabel = 'KVKK Talepleri';
    protected static ?string $modelLabel = 'Veri Talebi';
    protected static ?string $pluralModelLabel = 'Veri Talepleri';
    protected static ?int $navigationSort = 30;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Select::make('status')
                ->label('Durum')
                ->options([
                    'pending' => 'Bekliyor',
                    'processing' => 'İşleniyor',
                    'completed' => 'Tamamlandı',
                    'rejected' => 'Reddedildi',
                ])
                ->rule(fn (?DataRequest $record): Closure => function (string $attribute, mixed $value, Closure $fail) use ($record): void {
                    if ($record && in_array($record->getOriginal('status'), ['completed', 'rejected'], true) && $value !== $record->getOriginal('status')) {
                        $fail('Tamamlanmış veya reddedilmiş veri taleplerinin durumu tekrar değiştirilemez.');
                    }
                })
                ->required(),
            Textarea::make('admin_notes')
                ->label('Admin Notu')
                ->rows(4)
                ->maxLength(1000)
                ->dehydrateStateUsing(fn ($state): string => trim((string) $state)),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('ID')->sortable(),
                TextColumn::make('user.name')->label('Kullanıcı')->searchable()->sortable(),
                BadgeColumn::make('type')
                    ->label('Tür')
                    ->colors(['primary'])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'view' => 'Görüntüleme',
                        'export' => 'Dışa Aktarma',
                        'delete' => 'Silme',
                        'consent_withdraw' => 'İzin Geri Çekme',
                        default => $state,
                    }),
                BadgeColumn::make('status')
                    ->label('Durum')
                    ->colors([
                        'warning' => 'pending',
                        'info' => 'processing',
                        'success' => 'completed',
                        'danger' => 'rejected',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Bekliyor',
                        'processing' => 'İşleniyor',
                        'completed' => 'Tamamlandı',
                        'rejected' => 'Reddedildi',
                        default => $state,
                    }),
                TextColumn::make('created_at')->label('Tarih')->dateTime('d.m.Y H:i')->sortable(),
                TextColumn::make('completed_at')->label('Tamamlanma')->dateTime('d.m.Y H:i'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Durum')
                    ->options([
                        'pending' => 'Bekliyor',
                        'processing' => 'İşleniyor',
                        'completed' => 'Tamamlandı',
                        'rejected' => 'Reddedildi',
                    ]),
                SelectFilter::make('type')
                    ->label('Tür')
                    ->options([
                        'view' => 'Görüntüleme',
                        'export' => 'Dışa Aktarma',
                        'delete' => 'Silme',
                        'consent_withdraw' => 'İzin Geri Çekme',
                    ]),
            ])
            ->actions([
                Action::make('mark_processing')
                    ->label('İşleniyor')
                    ->color('info')
                    ->visible(fn (DataRequest $record): bool => $record->status === 'pending')
                    ->action(fn (DataRequest $record) => $record->update(['status' => 'processing'])),
                Action::make('mark_completed')
                    ->label('Tamamla')
                    ->color('success')
                    ->visible(fn (DataRequest $record): bool => in_array($record->status, ['pending', 'processing'], true))
                    ->action(fn (DataRequest $record) => $record->update([
                        'status' => 'completed',
                        'completed_at' => now(),
                    ])),
                Action::make('mark_rejected')
                    ->label('Reddet')
                    ->color('danger')
                    ->visible(fn (DataRequest $record): bool => in_array($record->status, ['pending', 'processing'], true))
                    ->action(fn (DataRequest $record) => $record->update(['status' => 'rejected'])),
                EditAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDataRequests::route('/'),
            'edit' => Pages\EditDataRequest::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::query()->where('status', 'pending')->count();

        return $count > 0 ? (string) $count : null;
    }
}
