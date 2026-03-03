<?php

namespace App\Filament\Pages;

use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaLibrary extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-photo';
    protected static ?string $navigationLabel = 'Medya Kütüphanesi';
    protected static ?string $title = 'Medya Kütüphanesi';
    protected static ?int $navigationSort = 16;
    protected static string $view = 'filament.pages.media-library';

    public string $viewMode = 'grid';
    public string $search = '';
    public bool $showOrphaned = false;

    public function getMediaItems(): \Illuminate\Support\Collection
    {
        $query = Media::query()
            ->when($this->search, fn ($q) => $q->where('file_name', 'like', '%' . $this->search . '%'))
            ->orderByDesc('created_at');

        if ($this->showOrphaned) {
            $query->where(function ($q) {
                $q->whereNull('model_type')
                    ->orWhereNull('model_id')
                    ->orWhereDoesntHave('model');
            });
        }

        return $query->get()->map(function (Media $media) {
            return [
                'id'         => $media->id,
                'file_name'  => $media->file_name,
                'size'       => $this->humanFileSize($media->size),
                'collection' => $media->collection_name,
                'model_type' => $media->model_type ? class_basename($media->model_type) : '—',
                'model_id'   => $media->model_id,
                'thumb_url'  => $media->hasGeneratedConversion('thumb')
                    ? $media->getUrl('thumb')
                    : ($media->type === 'image' ? $media->getUrl() : null),
                'created_at' => $media->created_at?->format('d.m.Y H:i'),
                'mime_type'  => $media->mime_type,
            ];
        });
    }

    public function deleteMedia(int $id): void
    {
        $media = Media::find($id);

        if (!$media) {
            Notification::make()->danger()->title('Medya bulunamadı')->send();
            return;
        }

        $media->delete();
        Notification::make()->success()->title('Medya silindi')->send();
    }

    public function setViewMode(string $mode): void
    {
        $this->viewMode = $mode;
    }

    public function toggleOrphaned(): void
    {
        $this->showOrphaned = !$this->showOrphaned;
    }

    private function humanFileSize(int $bytes): string
    {
        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 1) . ' MB';
        }
        if ($bytes >= 1024) {
            return round($bytes / 1024, 1) . ' KB';
        }
        return $bytes . ' B';
    }
}
