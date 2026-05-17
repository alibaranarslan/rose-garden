<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use App\Models\Setting;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\CreateRecord\Concerns\Translatable;

class CreateProduct extends CreateRecord
{
    use Translatable;

    protected static string $resource = ProductResource::class;

    protected function afterCreate(): void
    {
        $this->record->ensurePrimaryImage();
        Setting::forgetStorefrontCaches();
        Setting::bumpStorefrontContentVersion();
    }
}
