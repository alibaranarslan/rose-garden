<?php

namespace App\Filament\Resources\BlogCategoryResource\Pages;

use App\Filament\Resources\BlogCategoryResource;
use App\Models\Setting;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\CreateRecord\Concerns\Translatable;

class CreateBlogCategory extends CreateRecord
{
    use Translatable;

    protected static string $resource = BlogCategoryResource::class;

    protected function afterCreate(): void
    {
        Setting::forgetStorefrontCaches();
        Setting::bumpStorefrontContentVersion();
    }
}
