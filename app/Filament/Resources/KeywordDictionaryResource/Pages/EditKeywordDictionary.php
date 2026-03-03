<?php

namespace App\Filament\Resources\KeywordDictionaryResource\Pages;

use App\Filament\Resources\KeywordDictionaryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditKeywordDictionary extends EditRecord
{
    protected static string $resource = KeywordDictionaryResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
