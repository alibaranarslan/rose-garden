<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DeliveryZoneResource\Pages;
use App\Models\DeliveryZone;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DeliveryZoneResource extends Resource
{
    protected static ?string $model = DeliveryZone::class;
    protected static ?string $navigationIcon = 'heroicon-o-map-pin';
    protected static ?string $navigationGroup = 'Teslimat';
    protected static ?string $navigationLabel = 'Bölgeler';
    protected static ?string $modelLabel = 'Teslimat Bölgesi';
    protected static ?string $pluralModelLabel = 'Teslimat Bölgeleri';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')
                ->label('Bölge Adı')
                ->required(),

            TextInput::make('fee')
                ->label('Teslimat Ücreti')
                ->numeric()
                ->prefix('₺')
                ->required(),

            TextInput::make('min_free_amount')
                ->label('Ücretsiz Teslimat Üstü')
                ->numeric()
                ->prefix('₺')
                ->helperText('Bu tutarın üzeri ücretsiz teslimat'),

            TimePicker::make('cutoff_time')
                ->label('Son Sipariş Saati')
                ->helperText('Aynı gün teslimat için son saat'),

            Toggle::make('is_active')
                ->label('Aktif')
                ->default(true),

            TextInput::make('sort_order')
                ->label('Sıra')
                ->numeric()
                ->default(0),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Bölge')
                    ->sortable(),

                TextColumn::make('fee')
                    ->label('Ücret')
                    ->money('TRY')
                    ->sortable(),

                TextColumn::make('min_free_amount')
                    ->label('Ücretsiz Üstü')
                    ->money('TRY'),

                TextColumn::make('cutoff_time')
                    ->label('Son Saat'),

                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
            ])
            ->reorderable('sort_order')
            ->defaultSort('sort_order');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDeliveryZones::route('/'),
            'create' => Pages\CreateDeliveryZone::route('/create'),
            'edit' => Pages\EditDeliveryZone::route('/{record}/edit'),
        ];
    }
}
