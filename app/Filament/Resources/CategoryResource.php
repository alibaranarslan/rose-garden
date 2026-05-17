<?php

namespace App\Filament\Resources;

use Closure;
use App\Filament\Resources\CategoryResource\Pages;
use App\Models\Category;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class CategoryResource extends Resource
{
    use Translatable;

    protected static ?string $model = Category::class;
    protected static ?string $navigationIcon = 'heroicon-o-folder';
    protected static ?string $navigationGroup = 'Katalog';
    protected static ?string $navigationLabel = 'Kategoriler';
    protected static ?string $modelLabel = 'Kategori';
    protected static ?string $pluralModelLabel = 'Kategoriler';
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
                ->afterStateUpdated(fn (string $operation, $state, \Filament\Forms\Set $set) =>
                    $operation === 'create' ? $set('slug', Str::slug((string) $state, '-', 'tr')) : null),

            TextInput::make('slug')
                ->label('Slug')
                ->required()
                ->maxLength(255)
                ->regex('/^[A-Za-z0-9]+(?:-[A-Za-z0-9]+)*$/')
                ->dehydrateStateUsing(fn ($state): string => Str::slug((string) $state, '-', 'tr'))
                ->unique(Category::class, 'slug', ignoreRecord: true),

            Textarea::make('description')
                ->label('Açıklama')
                ->rows(3)
                ->maxLength(500)
                ->dehydrateStateUsing(fn ($state): string => trim((string) $state))
                ->columnSpanFull(),

            FileUpload::make('image')
                ->label('Görsel')
                ->image()
                ->directory('categories')
                ->maxSize(5120)
                ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/gif'])
                ->getUploadedFileNameForStorageUsing(fn (\Livewire\Features\SupportFileUploads\TemporaryUploadedFile $file): string => \Illuminate\Support\Str::random(40) . '.' . $file->getClientOriginalExtension()),

            Select::make('parent_id')
                ->label('Üst Kategori')
                ->relationship('parent', 'name')
                ->nullable()
                ->searchable()
                ->preload()
                ->rule(fn (?Category $record): Closure => function (string $attribute, mixed $value, Closure $fail) use ($record): void {
                    if ($record && filled($value) && (int) $value === (int) $record->getKey()) {
                        $fail('Kategori kendi üst kategorisi olamaz.');
                    }
                })
                ->getOptionLabelFromRecordUsing(fn ($record) => $record->getTranslation('name', app()->getLocale())),

            TextInput::make('sort_order')
                ->label('Sıra')
                ->numeric()
                ->minValue(0)
                ->maxValue(9999)
                ->default(0),

            Toggle::make('is_active')
                ->label('Aktif')
                ->default(true),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->label('Görsel')
                    ->width(40)
                    ->height(40),

                TextColumn::make('name')
                    ->label('Ad')
                    ->formatStateUsing(fn ($record) => $record->getTranslation('name', 'tr'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('parent.name')
                    ->label('Üst Kategori')
                    ->formatStateUsing(fn ($record) => $record->parent?->getTranslation('name', 'tr') ?? '-'),

                TextColumn::make('products_count')
                    ->label('Ürün Sayısı')
                    ->counts('products')
                    ->sortable(),

                TextColumn::make('sort_order')
                    ->label('Sıra')
                    ->sortable(),

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
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }

    public static function getTranslatableLocales(): array
    {
        return ['tr', 'en', 'ku'];
    }
}
