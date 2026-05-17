<?php

namespace App\Filament\Resources\BlogCategoryResource\Pages;

use App\Filament\Resources\BlogCategoryResource;
use App\Models\Setting;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\EditRecord\Concerns\Translatable;

class EditBlogCategory extends EditRecord
{
    use Translatable;

    protected static string $resource = BlogCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->after(fn () => $this->refreshStorefrontSurface()),
        ];
    }

    protected function afterSave(): void
    {
        $this->refreshStorefrontSurface();
    }

    private function refreshStorefrontSurface(): void
    {
        Setting::forgetStorefrontCaches();
        Setting::bumpStorefrontContentVersion();
    }
}
