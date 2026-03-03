<?php

namespace App\Filament\Resources\SpecialOccasionResource\Pages;

use App\Filament\Resources\SpecialOccasionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\EditRecord\Concerns\Translatable;

class EditSpecialOccasion extends EditRecord
{
    use Translatable;

    protected static string $resource = SpecialOccasionResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
