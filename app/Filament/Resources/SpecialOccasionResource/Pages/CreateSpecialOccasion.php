<?php

namespace App\Filament\Resources\SpecialOccasionResource\Pages;

use App\Filament\Resources\SpecialOccasionResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\CreateRecord\Concerns\Translatable;

class CreateSpecialOccasion extends CreateRecord
{
    use Translatable;

    protected static string $resource = SpecialOccasionResource::class;
}
