<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CouponResource\Pages;
use App\Models\Coupon;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class CouponResource extends Resource
{
    protected static ?string $model = Coupon::class;
    protected static ?string $navigationIcon = 'heroicon-o-ticket';
    protected static ?string $navigationGroup = 'Kampanyalar';
    protected static ?string $navigationLabel = 'Kuponlar';
    protected static ?string $modelLabel = 'Kupon';
    protected static ?string $pluralModelLabel = 'Kuponlar';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('code')
                ->label('Kupon Kodu')
                ->required()
                ->unique(Coupon::class, 'code', ignoreRecord: true)
                ->suffixAction(
                    \Filament\Forms\Components\Actions\Action::make('generate')
                        ->icon('heroicon-o-arrow-path')
                        ->action(fn (\Filament\Forms\Set $set) => $set('code', strtoupper(Str::random(8))))
                )
                ->maxLength(50),

            Select::make('type')
                ->label('Tür')
                ->options([
                    'percentage' => 'Yüzde (%)',
                    'fixed_amount' => 'Sabit Tutar (₺)',
                    'free_delivery' => 'Ücretsiz Teslimat',
                ])
                ->required(),

            TextInput::make('value')
                ->label('Değer')
                ->numeric()
                ->required(),

            TextInput::make('min_order_amount')
                ->label('Min. Sipariş Tutarı')
                ->numeric()
                ->prefix('₺'),

            TextInput::make('max_uses')
                ->label('Max. Kullanım')
                ->numeric()
                ->placeholder('Sınırsız'),

            TextInput::make('max_uses_per_user')
                ->label('Kişi Başı Limit')
                ->numeric()
                ->default(1),

            DateTimePicker::make('starts_at')
                ->label('Başlangıç')
                ->native(false),

            DateTimePicker::make('expires_at')
                ->label('Bitiş')
                ->native(false),

            Toggle::make('is_active')
                ->label('Aktif')
                ->default(true),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('Kod')
                    ->searchable()
                    ->copyable()
                    ->fontFamily('mono'),

                BadgeColumn::make('type')
                    ->label('Tür')
                    ->colors([
                        'primary' => 'percentage',
                        'success' => 'fixed_amount',
                        'info' => 'free_delivery',
                    ])
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'percentage' => 'Yüzde',
                        'fixed_amount' => 'Sabit',
                        'free_delivery' => 'Ücretsiz Teslimat',
                        default => $state,
                    }),

                TextColumn::make('value')
                    ->label('Değer')
                    ->formatStateUsing(fn ($record) => $record->type === 'percentage' ? "%{$record->value}" : "₺{$record->value}"),

                TextColumn::make('usage')
                    ->label('Kullanım')
                    ->getStateUsing(fn ($record) => "{$record->used_count}" . ($record->max_uses ? "/{$record->max_uses}" : '')),

                TextColumn::make('expires_at')
                    ->label('Geçerlilik')
                    ->dateTime('d.m.Y')
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
            ])
            ->actions([
                EditAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCoupons::route('/'),
            'create' => Pages\CreateCoupon::route('/create'),
            'edit' => Pages\EditCoupon::route('/{record}/edit'),
        ];
    }
}
