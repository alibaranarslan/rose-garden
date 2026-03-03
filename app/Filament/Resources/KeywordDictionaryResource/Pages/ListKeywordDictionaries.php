<?php

namespace App\Filament\Resources\KeywordDictionaryResource\Pages;

use App\Filament\Resources\KeywordDictionaryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListKeywordDictionaries extends ListRecords
{
    protected static string $resource = KeywordDictionaryResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()->label('Yeni Kelime')];
    }
}
