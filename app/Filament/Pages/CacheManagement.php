<?php

namespace App\Filament\Pages;

use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Artisan;

class CacheManagement extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-server';

    protected static ?string $navigationLabel = 'Önbellek';

    protected static ?string $navigationGroup = 'Ayarlar';

    protected static ?string $title = 'Önbellek Yönetimi';

    protected static ?int $navigationSort = 24;

    protected static string $view = 'filament.pages.cache-management';

    public function clearConfig(): void
    {
        $this->runCommand('config:clear', 'Config cache temizlendi', 'Config cache temizlenemedi');
    }

    public function clearView(): void
    {
        $this->runCommand('view:clear', 'View cache temizlendi', 'View cache temizlenemedi');
    }

    public function clearRoute(): void
    {
        $this->runCommand('route:clear', 'Route cache temizlendi', 'Route cache temizlenemedi');
    }

    public function clearAll(): void
    {
        $commands = ['cache:clear', 'config:clear', 'view:clear', 'route:clear'];
        $failed = [];

        foreach ($commands as $command) {
            if (! $this->callCommand($command)) {
                $failed[] = $command;
            }
        }

        if ($failed !== []) {
            Notification::make()
                ->danger()
                ->title('Önbellek temizleme tamamlanamadı')
                ->body('Başarısız komutlar: ' . implode(', ', $failed))
                ->send();

            return;
        }

        Notification::make()->success()->title('Tüm cache temizlendi')->send();
    }

    public function optimizeApp(): void
    {
        $this->runCommand('optimize', 'Uygulama optimize edildi', 'Uygulama optimize edilemedi');
    }

    private function runCommand(string $command, string $successMessage, string $errorMessage): void
    {
        if (! $this->callCommand($command)) {
            Notification::make()->danger()->title($errorMessage)->send();

            return;
        }

        Notification::make()->success()->title($successMessage)->send();
    }

    private function callCommand(string $command): bool
    {
        try {
            return Artisan::call($command) === 0;
        } catch (\Throwable) {
            return false;
        }
    }
}
