<?php

namespace App\Filament\Resources\SpecialOccasionResource\Pages;

use App\Filament\Resources\SpecialOccasionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSpecialOccasions extends ListRecords
{
    protected static string $resource = SpecialOccasionResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()->label('Yeni Özel Gün')];
    }
}
