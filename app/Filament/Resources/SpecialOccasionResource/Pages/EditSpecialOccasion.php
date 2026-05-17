<?php

namespace App\Filament\Resources\SpecialOccasionResource\Pages;

use App\Filament\Resources\SpecialOccasionResource;
use App\Models\Setting;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\EditRecord\Concerns\Translatable;

class EditSpecialOccasion extends EditRecord
{
    use Translatable;

    protected static string $resource = SpecialOccasionResource::class;

    protected function afterSave(): void
    {
        $this->refreshStorefrontSurface();
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->after(fn () => $this->refreshStorefrontSurface()),
        ];
    }

    private function refreshStorefrontSurface(): void
    {
        Setting::forgetStorefrontCaches();
        Setting::bumpStorefrontContentVersion();
    }
}
