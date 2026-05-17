<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DeliveryTimeSlotResource\Pages;
use App\Models\DeliveryTimeSlot;
use Closure;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DeliveryTimeSlotResource extends Resource
{
    protected static ?string $model = DeliveryTimeSlot::class;
    protected static ?string $navigationIcon = 'heroicon-o-clock';
    protected static ?string $navigationGroup = 'Teslimat';
    protected static ?string $navigationLabel = 'Saat Aralıkları';
    protected static ?string $modelLabel = 'Saat Aralığı';
    protected static ?string $pluralModelLabel = 'Saat Aralıkları';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('label')
                ->label('Etiket')
                ->placeholder('09:00 - 12:00')
                ->maxLength(100)
                ->dehydrateStateUsing(fn (mixed $state): string => trim((string) $state))
                ->required(),

            TimePicker::make('start_time')
                ->label('Başlangıç')
                ->seconds(false)
                ->rule(fn (callable $get): Closure => function (string $attribute, mixed $value, Closure $fail) use ($get): void {
                    $endTime = $get('end_time');

                    if (filled($value) && filled($endTime) && $value >= $endTime) {
                        $fail('Başlangıç saati bitiş saatinden önce olmalıdır.');
                    }
                })
                ->required(),

            TimePicker::make('end_time')
                ->label('Bitiş')
                ->seconds(false)
                ->required(),

            Toggle::make('is_active')
                ->label('Aktif')
                ->default(true),

            TextInput::make('sort_order')
                ->label('Sıra')
                ->numeric()
                ->minValue(0)
                ->maxValue(9999)
                ->default(0),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('label')->label('Aralık'),
                TextColumn::make('start_time')->label('Başlangıç'),
                TextColumn::make('end_time')->label('Bitiş'),
                IconColumn::make('is_active')->label('Aktif')->boolean(),
                TextColumn::make('sort_order')->label('Sıra')->sortable(),
            ])
            ->reorderable('sort_order')
            ->defaultSort('sort_order');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDeliveryTimeSlots::route('/'),
            'create' => Pages\CreateDeliveryTimeSlot::route('/create'),
            'edit' => Pages\EditDeliveryTimeSlot::route('/{record}/edit'),
        ];
    }
}
