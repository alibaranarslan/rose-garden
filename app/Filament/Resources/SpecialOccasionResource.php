<?php

namespace App\Filament\Resources;

use Closure;
use App\Filament\Resources\SpecialOccasionResource\Pages;
use App\Models\SpecialOccasion;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class SpecialOccasionResource extends Resource
{
    use Translatable;

    protected static ?string $model = SpecialOccasion::class;
    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationGroup = 'Kampanyalar';
    protected static ?string $navigationLabel = 'Özel Günler';
    protected static ?string $modelLabel = 'Özel Gün';
    protected static ?string $pluralModelLabel = 'Özel Günler';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')
                ->label('Ad')
                ->required()
                ->maxLength(255)
                ->dehydrateStateUsing(fn ($state): string => trim((string) $state))
                ->live(onBlur: true)
                ->afterStateUpdated(fn (string $operation, $state, \Filament\Forms\Set $set) => $operation === 'create' && filled($state)
                    ? $set('slug', Str::slug((string) $state, '-', 'tr'))
                    : null),

            TextInput::make('slug')
                ->label('Slug')
                ->required()
                ->maxLength(255)
                ->regex('/^[A-Za-z0-9]+(?:-[A-Za-z0-9]+)*$/')
                ->dehydrateStateUsing(fn ($state): string => Str::slug((string) $state, '-', 'tr'))
                ->unique(SpecialOccasion::class, 'slug', ignoreRecord: true),

            Select::make('date_month')
                ->label('Ay')
                ->options(array_combine(range(1, 12), ['Ocak','Şubat','Mart','Nisan','Mayıs','Haziran','Temmuz','Ağustos','Eylül','Ekim','Kasım','Aralık']))
                ->live()
                ->dehydrateStateUsing(fn ($state): ?int => filled($state) ? (int) $state : null)
                ->required(),

            Select::make('date_day')
                ->label('Gün')
                ->options(array_combine(range(1, 31), range(1, 31)))
                ->live()
                ->dehydrateStateUsing(fn ($state): ?int => filled($state) ? (int) $state : null)
                ->rule(fn (callable $get): Closure => function (string $attribute, mixed $value, Closure $fail) use ($get): void {
                    if (blank($value) || blank($get('date_month'))) {
                        return;
                    }

                    if (! checkdate((int) $get('date_month'), (int) $value, 2024)) {
                        $fail('Seçilen ay ve gün birlikte geçerli olmalıdır.');
                    }
                })
                ->required(),

            Select::make('category_id')
                ->label('İlişkili Kategori')
                ->relationship('category', 'name')
                ->nullable()
                ->searchable()
                ->getOptionLabelFromRecordUsing(fn ($record) => $record->getTranslation('name', 'tr')),

            TextInput::make('loyalty_multiplier')
                ->label('Paraçiçek Çarpanı')
                ->numeric()
                ->minValue(0)
                ->maxValue(10)
                ->default(1.0)
                ->helperText('2.0 = 2 kat puan'),

            Toggle::make('is_active')
                ->label('Aktif')
                ->default(true),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Ad')
                    ->formatStateUsing(fn ($record) => $record->getTranslation('name', 'tr')),

                TextColumn::make('date')
                    ->label('Tarih')
                    ->getStateUsing(fn ($record) => "{$record->date_day}/{$record->date_month}"),

                TextColumn::make('loyalty_multiplier')
                    ->label('Puan Çarpanı'),

                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSpecialOccasions::route('/'),
            'create' => Pages\CreateSpecialOccasion::route('/create'),
            'edit' => Pages\EditSpecialOccasion::route('/{record}/edit'),
        ];
    }

    public static function getTranslatableLocales(): array
    {
        return ['tr', 'en', 'ku'];
    }
}
