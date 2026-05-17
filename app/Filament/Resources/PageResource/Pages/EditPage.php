<?php

namespace App\Filament\Resources\PageResource\Pages;

use App\Filament\Resources\PageResource;
use App\Models\Setting;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\EditRecord\Concerns\Translatable;

class EditPage extends EditRecord
{
    use Translatable;

    protected static string $resource = PageResource::class;

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
