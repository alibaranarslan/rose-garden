<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PageResource\Pages;
use App\Models\Page;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class PageResource extends Resource
{
    use Translatable;

    protected static ?string $model = Page::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'İçerik';
    protected static ?string $navigationLabel = 'Sayfalar';
    protected static ?string $modelLabel = 'Sayfa';
    protected static ?string $pluralModelLabel = 'Sayfalar';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('title')
                ->label('Başlık')
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
                ->unique(Page::class, 'slug', ignoreRecord: true),

            RichEditor::make('content')
                ->label('İçerik')
                ->live(debounce: 500)
                ->required(fn (callable $get): bool => (bool) $get('is_published'))
                ->columnSpanFull(),

            TextInput::make('meta_title')
                ->label('Meta Başlık')
                ->maxLength(70)
                ->dehydrateStateUsing(fn ($state): string => trim((string) $state)),
            Textarea::make('meta_description')
                ->label('Meta Açıklama')
                ->maxLength(160)
                ->dehydrateStateUsing(fn ($state): string => trim((string) $state)),

            Toggle::make('is_published')->label('Yayında')->default(true),
            TextInput::make('sort_order')->label('Sıra')->numeric()->minValue(0)->maxValue(9999)->default(0),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Başlık')
                    ->formatStateUsing(fn ($record) => $record->getTranslation('title', 'tr')),
                TextColumn::make('slug')->label('Slug'),
                IconColumn::make('is_published')->label('Yayında')->boolean(),
                TextColumn::make('sort_order')->label('Sıra')->sortable(),
            ])
            ->reorderable('sort_order')
            ->defaultSort('sort_order');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPages::route('/'),
            'create' => Pages\CreatePage::route('/create'),
            'edit' => Pages\EditPage::route('/{record}/edit'),
        ];
    }

    public static function getTranslatableLocales(): array
    {
        return ['tr', 'en', 'ku'];
    }
}
