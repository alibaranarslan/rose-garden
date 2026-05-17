<?php

namespace App\Filament\Pages;

use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaLibrary extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-photo';
    protected static ?string $navigationLabel = 'Medya Kütüphanesi';
    protected static ?string $title = 'Medya Kütüphanesi';
    protected static ?string $navigationGroup = 'İçerik';
    protected static ?int $navigationSort = 4;
    protected static string $view = 'filament.pages.media-library';

    public string $viewMode = 'grid';
    public string $search = '';
    public bool $showOrphaned = false;

    public function getMediaItems(): \Illuminate\Support\Collection
    {
        $search = $this->normalizedSearch();

        $query = Media::query()
            ->when($search, fn ($q) => $q->where('file_name', 'like', '%' . $search . '%'))
            ->orderByDesc('created_at');

        if ($this->showOrphaned) {
            $query->where(function ($q) {
                $q->whereNull('model_type')
                    ->orWhereNull('model_id')
                    ->orWhereDoesntHave('model');
            });
        }

        return $query->limit(200)->get()->map(function (Media $media) {
            $isOrphaned = $this->isOrphaned($media);

            return [
                'id' => $media->id,
                'file_name' => $media->file_name,
                'size' => $this->humanFileSize($media->size),
                'collection' => $media->collection_name,
                'model_type' => $media->model_type ? class_basename($media->model_type) : '—',
                'model_id' => $media->model_id,
                'thumb_url' => $media->hasGeneratedConversion('thumb')
                    ? $media->getUrl('thumb')
                    : (Str::startsWith((string) $media->mime_type, 'image/') ? $media->getUrl() : null),
                'created_at' => $media->created_at?->format('d.m.Y H:i'),
                'mime_type' => $media->mime_type,
                'is_orphaned' => $isOrphaned,
            ];
        });
    }

    public function deleteMedia(int $id): void
    {
        $media = Media::find($id);

        if (! $media) {
            Notification::make()->danger()->title('Medya bulunamadı')->send();

            return;
        }

        if (! $this->isOrphaned($media)) {
            Notification::make()
                ->danger()
                ->title('Bu medya aktif bir kayda bağlı')
                ->body('Ürün, blog veya sayfa görselini kırmamak için önce ilgili kayıttan bağlantıyı kaldırın.')
                ->send();

            return;
        }

        $media->delete();
        Notification::make()->success()->title('Medya silindi')->send();
    }

    public function setViewMode(string $mode): void
    {
        $this->viewMode = in_array($mode, ['grid', 'list'], true) ? $mode : 'grid';
    }

    public function toggleOrphaned(): void
    {
        $this->showOrphaned = ! $this->showOrphaned;
    }

    public function updatedSearch(mixed $value): void
    {
        $this->search = $this->normalizedSearch($value);
    }

    private function normalizedSearch(mixed $value = null): string
    {
        return Str::limit(trim((string) ($value ?? $this->search)), 100, '');
    }

    private function isOrphaned(Media $media): bool
    {
        if (blank($media->model_type) || blank($media->model_id)) {
            return true;
        }

        $model = $media->model;

        return ! $model instanceof Model || ! $model->exists;
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
