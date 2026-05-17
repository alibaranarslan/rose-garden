<?php

namespace App\Filament\Resources\DataRequestResource\Pages;

use App\Filament\Resources\DataRequestResource;
use Filament\Resources\Pages\EditRecord;

class EditDataRequest extends EditRecord
{
    protected static string $resource = DataRequestResource::class;

    protected function afterSave(): void
    {
        if ($this->record->status === 'completed' && ! $this->record->completed_at) {
            $this->record->forceFill(['completed_at' => now()])->saveQuietly();

            return;
        }

        if ($this->record->status !== 'completed' && $this->record->completed_at) {
            $this->record->forceFill(['completed_at' => null])->saveQuietly();
        }
    }
}
