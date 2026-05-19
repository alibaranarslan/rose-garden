<?php

namespace App\Filament\Pages;

use App\Models\LayoutRevision;
use App\Services\LayoutConfigService;
use App\Support\AdminActionLogger;
use App\Support\AdminPrivileges;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class LayoutStudio extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-swatch';

    protected static ?string $navigationLabel = 'Yerleşim Stüdyosu';

    protected static ?string $navigationGroup = 'Ayarlar';

    protected static ?string $title = 'Yerleşim Stüdyosu';

    protected static ?int $navigationSort = 19;

    protected static string $view = 'filament.pages.layout-studio-storefront';

    public array $modules = [];

    public array $appearance = [];

    public ?string $selectedModuleKey = null;

    public ?string $restoreRevisionId = null;

    public bool $hasUnsavedChanges = false;

    public function mount(LayoutConfigService $layoutConfigService): void
    {
        $state = $layoutConfigService->getDraftState();

        $this->modules = $state['modules'];
        $this->appearance = $state['appearance'];
        $this->selectedModuleKey = $this->modules[0]['key'] ?? null;
    }

    public static function canAccess(): bool
    {
        return AdminPrivileges::canAccessAdminPanel(auth()->user());
    }

    public function updated(string $property): void
    {
        if (str_starts_with($property, 'modules') || str_starts_with($property, 'appearance')) {
            $this->hasUnsavedChanges = true;
        }
    }

    public function updateModuleOrder(array $order): void
    {
        $orderedIds = array_map('strval', $order);

        $this->modules = collect($this->modules)
            ->sortBy(function (array $module) use ($orderedIds): int {
                $position = array_search((string) ($module['id'] ?? ''), $orderedIds, true);

                return $position === false ? PHP_INT_MAX : $position;
            })
            ->values()
            ->map(function (array $module, int $index): array {
                $module['sort_order'] = $index + 1;

                return $module;
            })
            ->all();
        $this->hasUnsavedChanges = true;

        Notification::make()->success()->title('Sıralama taslakta güncellendi')->send();
    }

    public function selectModule(string $key): void
    {
        $this->selectedModuleKey = $key;
    }

    public function toggleModule(int $id): void
    {
        foreach ($this->modules as $index => $module) {
            if ((int) ($module['id'] ?? 0) !== $id) {
                continue;
            }

            $this->modules[$index]['is_active'] = ! (bool) ($module['is_active'] ?? true);
            $this->selectedModuleKey = $module['key'] ?? $this->selectedModuleKey;
            $this->hasUnsavedChanges = true;

            Notification::make()->success()->title('Modül taslakta güncellendi')->send();

            return;
        }
    }

    public function moveModuleUp(int $id): void
    {
        $this->moveModule($id, -1);
    }

    public function moveModuleDown(int $id): void
    {
        $this->moveModule($id, 1);
    }

    public function applyModulePreset(string $preset): void
    {
        $index = $this->getSelectedModuleIndex();

        if ($index === null) {
            return;
        }

        $module = $this->modules[$index];
        $definition = app(LayoutConfigService::class)->getModuleDefinitions()[$module['key']] ?? [];
        $baseSettings = $definition['settings'] ?? [];
        $currentSettings = $module['settings'] ?? [];
        $currentLimit = max(1, (int) data_get($currentSettings, 'content_limit', 6));

        $overrides = match ($preset) {
            'balanced' => [
                'background_tone' => 'surface',
                'accent_mode' => 'brand',
                'padding_scale' => 'regular',
                'card_density' => 'comfortable',
                'container_width' => 'content',
            ],
            'showcase' => [
                'background_tone' => 'contrast',
                'accent_mode' => 'brand',
                'padding_scale' => 'relaxed',
                'card_density' => 'airy',
                'container_width' => in_array($module['key'], ['hero', 'featured_showcase'], true) ? 'wide' : 'content',
                'content_limit' => min($currentLimit, in_array($module['key'], ['hero', 'featured_showcase'], true) ? 4 : 6),
            ],
            'compact' => [
                'background_tone' => 'muted',
                'accent_mode' => 'soft',
                'padding_scale' => 'compact',
                'card_density' => 'compact',
                'container_width' => 'content',
                'content_limit' => max(1, min($currentLimit, 4)),
            ],
            default => [],
        };

        if ($overrides === []) {
            return;
        }

        $this->modules[$index]['settings'] = array_replace_recursive($baseSettings, $currentSettings, $overrides);
        $this->hasUnsavedChanges = true;

        Notification::make()
            ->success()
            ->title('Modül ön ayarı uygulandı')
            ->body('Seçilen blok için hızlı vitrin kararı taslağa işlendi.')
            ->send();
    }

    public function resetSelectedModuleSettings(): void
    {
        $index = $this->getSelectedModuleIndex();

        if ($index === null) {
            return;
        }

        $module = $this->modules[$index];
        $definition = app(LayoutConfigService::class)->getModuleDefinitions()[$module['key']] ?? [];

        if (! isset($definition['settings'])) {
            return;
        }

        $this->modules[$index]['settings'] = $definition['settings'];
        $this->hasUnsavedChanges = true;

        Notification::make()
            ->success()
            ->title('Modül varsayılanına döndü')
            ->body('Seçilen bloğun ayarları başlangıç değerlerine alındı.')
            ->send();
    }

    public function applyAppearancePreset(string $preset): void
    {
        $defaults = app(LayoutConfigService::class)->defaultAppearance();

        $overrides = match ($preset) {
            'romantik' => [
                'primary_color' => '#3d2645',
                'accent_color' => '#c97a9b',
                'background_color' => '#faf6f1',
                'font_family' => 'playfair',
                'radius_preset' => 'rounded',
                'shadow_preset' => 'soft',
                'container_width' => '1280px',
                'default_theme_mode' => 'light',
            ],
            'modern' => [
                'primary_color' => '#42275a',
                'accent_color' => '#e89ab4',
                'background_color' => '#fffafc',
                'font_family' => 'inter',
                'radius_preset' => 'soft',
                'shadow_preset' => 'elevated',
                'container_width' => '1240px',
                'default_theme_mode' => 'light',
            ],
            'minimal' => [
                'primary_color' => '#2f2a33',
                'accent_color' => '#b87b95',
                'background_color' => '#ffffff',
                'font_family' => 'inter',
                'radius_preset' => 'sharp',
                'shadow_preset' => 'none',
                'container_width' => '1180px',
                'default_theme_mode' => 'light',
            ],
            default => [],
        };

        if ($overrides === []) {
            return;
        }

        $this->appearance = array_replace($defaults, $this->appearance, $overrides);
        $this->hasUnsavedChanges = true;

        Notification::make()
            ->success()
            ->title('Görünüm ön ayarı uygulandı')
            ->body('Sayfanın genel vitrin kararı taslağa işlendi.')
            ->send();
    }

    public function getSelectedModuleWarnings(): array
    {
        $module = $this->getSelectedModuleState();
        $settings = $module['settings'] ?? [];
        $warnings = [];

        if (! $module) {
            return $warnings;
        }

        if (
            ! data_get($settings, 'show_on_mobile', true)
            && ! data_get($settings, 'show_on_tablet', true)
            && ! data_get($settings, 'show_on_desktop', true)
        ) {
            $warnings[] = 'Bu modül hiçbir cihazda görünmeyecek.';
        }

        if (data_get($settings, 'cta_enabled') && blank(data_get($settings, 'cta_url'))) {
            $warnings[] = 'Buton açık ancak bağlantı girilmemiş.';
        }

        if (
            data_get($settings, 'cta_enabled')
            && blank(data_get($settings, 'cta_label.tr'))
            && blank(data_get($settings, 'cta_label.en'))
            && blank(data_get($settings, 'cta_label.ku'))
        ) {
            $warnings[] = 'Buton açık ancak hiçbir dil için etiket yazılmamış.';
        }

        if ((int) data_get($settings, 'content_limit', 0) < 1) {
            $warnings[] = 'İçerik limiti en az 1 olmalı.';
        }

        return $warnings;
    }

    public function saveDraft(): void
    {
        app(LayoutConfigService::class)->storeDraftState($this->modules, $this->appearance, auth()->user());
        $this->refreshFromDraft(app(LayoutConfigService::class));

        AdminActionLogger::record('layout.save_draft', null, [
            'module_count' => count($this->modules),
            'selected_module' => $this->selectedModuleKey,
        ]);

        Notification::make()
            ->success()
            ->title('Taslak kaydedildi')
            ->body('Vitrin düzeni önizleme ve yayınlama için hazır.')
            ->send();
    }

    public function publishDraft(): void
    {
        if (! $this->canPublishLayout()) {
            Notification::make()
                ->warning()
                ->title('Yayın yetkisi gerekli')
                ->body('Canlıya alma işlemi yalnızca süper yönetici yetkisiyle yapılabilir.')
                ->send();

            return;
        }

        $service = app(LayoutConfigService::class);
        $service->storeDraftState($this->modules, $this->appearance, auth()->user());
        $revision = $service->publishDraft(auth()->user());
        $this->refreshFromDraft($service);

        AdminActionLogger::record('layout.publish_draft', $revision, [
            'revision_name' => $revision->name,
        ]);

        Notification::make()
            ->success()
            ->title('Vitrin canlıya alındı')
            ->body($revision->name)
            ->send();
    }

    public function restoreRevision(): void
    {
        if (! $this->canPublishLayout()) {
            Notification::make()
                ->warning()
                ->title('Yayın yetkisi gerekli')
                ->body('Geri alma işlemi yalnızca süper yönetici yetkisiyle yapılabilir.')
                ->send();

            return;
        }

        if (! filled($this->restoreRevisionId)) {
            Notification::make()->warning()->title('Revizyon seçin')->send();

            return;
        }

        $revision = LayoutRevision::query()->find($this->restoreRevisionId);

        if (! $revision) {
            Notification::make()->danger()->title('Revizyon bulunamadı')->send();

            return;
        }

        $service = app(LayoutConfigService::class);
        $service->restoreRevisionToDraft($revision, auth()->user());
        $this->refreshFromDraft($service);
        $this->restoreRevisionId = null;

        AdminActionLogger::record('layout.restore_revision', $revision, [
            'revision_name' => $revision->name,
        ]);

        Notification::make()->success()->title('Taslak geri yüklendi')->send();
    }

    public function canPublishLayout(): bool
    {
        return AdminPrivileges::canPublishConfiguration(auth()->user());
    }

    public function isPublishRestricted(): bool
    {
        return self::canAccess() && ! $this->canPublishLayout();
    }

    public function getSelectedModuleIndex(): ?int
    {
        foreach ($this->modules as $index => $module) {
            if (($module['key'] ?? null) === $this->selectedModuleKey) {
                return $index;
            }
        }

        return null;
    }

    public function getSelectedModuleState(): ?array
    {
        $index = $this->getSelectedModuleIndex();

        return $index === null ? null : $this->modules[$index];
    }

    public function getPublishedRevision(): ?LayoutRevision
    {
        return app(LayoutConfigService::class)->getPublishedRevision();
    }

    public function getDraftRevision(): LayoutRevision
    {
        return app(LayoutConfigService::class)->getDraftRevision();
    }

    public function getPreviewUrls(): array
    {
        $service = app(LayoutConfigService::class);
        $draft = $service->getDraftRevision();

        return [
            'tr' => $service->getPreviewUrl($draft, 'tr'),
            'en' => $service->getPreviewUrl($draft, 'en'),
            'ku' => $service->getPreviewUrl($draft, 'ku'),
        ];
    }

    public function getRevisionOptions(): array
    {
        return app(LayoutConfigService::class)->getRevisionOptions();
    }

    private function refreshFromDraft(LayoutConfigService $layoutConfigService): void
    {
        $state = $layoutConfigService->getDraftState();
        $this->modules = $state['modules'];
        $this->appearance = $state['appearance'];
        $this->selectedModuleKey = $this->selectedModuleKey ?: ($this->modules[0]['key'] ?? null);
        $this->hasUnsavedChanges = false;
    }

    private function moveModule(int $id, int $direction): void
    {
        $index = collect($this->modules)->search(
            fn (array $module): bool => (int) ($module['id'] ?? 0) === $id
        );

        if ($index === false) {
            return;
        }

        $targetIndex = $index + $direction;

        if ($targetIndex < 0 || $targetIndex >= count($this->modules)) {
            return;
        }

        $modules = $this->modules;
        [$modules[$index], $modules[$targetIndex]] = [$modules[$targetIndex], $modules[$index]];

        $this->modules = collect($modules)
            ->values()
            ->map(function (array $module, int $position): array {
                $module['sort_order'] = $position + 1;

                return $module;
            })
            ->all();
        $this->hasUnsavedChanges = true;

        Notification::make()->success()->title('Modül sırası taslakta güncellendi')->send();
    }
}

