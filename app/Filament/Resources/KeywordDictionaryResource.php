<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KeywordDictionaryResource\Pages;
use App\Models\KeywordDictionary;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class KeywordDictionaryResource extends Resource
{
    protected static ?string $model = KeywordDictionary::class;
    protected static ?string $navigationIcon = 'heroicon-o-book-open';
    protected static ?string $navigationGroup = 'Pazarlama';
    protected static ?string $navigationLabel = 'Anahtar Kelimeler';
    protected static ?string $modelLabel = 'Anahtar Kelime';
    protected static ?string $pluralModelLabel = 'Anahtar Kelimeler';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('keyword')
                ->label('Kelime')
                ->required(),

            Select::make('event_type')
                ->label('Olay Türü')
                ->options([
                    'birthday' => 'Doğum Günü',
                    'anniversary' => 'Yıldönümü',
                    'valentines' => 'Sevgililer Günü',
                    'mothers_day' => 'Anneler Günü',
                    'custom' => 'Özel',
                ])
                ->required(),

            Toggle::make('is_active')
                ->label('Aktif')
                ->default(true),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('keyword')->label('Kelime')->searchable(),
                BadgeColumn::make('event_type')->label('Olay')->colors(['primary'])
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'birthday' => 'Doğum Günü',
                        'anniversary' => 'Yıldönümü',
                        'valentines' => 'Sevgililer Günü',
                        'mothers_day' => 'Anneler Günü',
                        'custom' => 'Özel',
                        default => $state,
                    }),
                IconColumn::make('is_active')->label('Aktif')->boolean(),
            ])
            ->defaultSort('keyword');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKeywordDictionaries::route('/'),
            'create' => Pages\CreateKeywordDictionary::route('/create'),
            'edit' => Pages\EditKeywordDictionary::route('/{record}/edit'),
        ];
    }
}
