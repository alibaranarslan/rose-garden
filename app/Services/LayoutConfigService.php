<?php

namespace App\Services;

use App\Models\LayoutModule;
use App\Models\LayoutRevision;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;

class LayoutConfigService
{
    public const AREA_HOME = 'home';

    public function getModuleDefinitions(): array
    {
        return [
            'announcement_bar' => [
                'name' => 'Announcement Bar',
                'description' => 'Header üstündeki kısa operasyon bandı.',
                'settings' => $this->defaultModuleSettings([
                    'variant' => 'notice',
                    'content_limit' => 1,
                    'padding_scale' => 'compact',
                ]),
            ],
            'hero' => [
                'name' => 'Hero Spotlight',
                'description' => 'Anasayfa vitrininin açılış yüzeyi.',
                'settings' => $this->defaultModuleSettings([
                    'variant' => 'spotlight',
                    'content_limit' => 1,
                    'columns_desktop' => 12,
                    'image_ratio' => '4:5',
                ]),
            ],
            'category_showcase' => [
                'name' => 'Kategori Keşfi',
                'description' => 'Kısa kategori geçişleriyle ürünlere hızlı giriş.',
                'settings' => $this->defaultModuleSettings([
                    'variant' => 'category-grid',
                    'content_limit' => 6,
                    'columns_mobile' => 2,
                    'columns_tablet' => 3,
                    'columns_desktop' => 3,
                ]),
            ],
            'featured_showcase' => [
                'name' => 'Editoryal Vitrin',
                'description' => 'Tek güçlü ürün spotlight alanı.',
                'settings' => $this->defaultModuleSettings([
                    'variant' => 'featured-product',
                    'content_limit' => 1,
                    'image_ratio' => '5:6',
                ]),
            ],
            'occasion_spotlight' => [
                'name' => 'Özel Gün Spotlight',
                'description' => 'Yaklaşan tarih ve ilgili ürünler.',
                'settings' => $this->defaultModuleSettings([
                    'variant' => 'occasion',
                    'content_limit' => 4,
                ]),
            ],
            'new_arrivals' => [
                'name' => 'Yeni Gelenler',
                'description' => 'Yeni ürünleri kompakt katalog gridinde gösterir.',
                'settings' => $this->defaultModuleSettings([
                    'variant' => 'grid',
                    'content_limit' => 8,
                    'columns_mobile' => 2,
                    'columns_tablet' => 3,
                    'columns_desktop' => 4,
                ]),
            ],
            'best_sellers' => [
                'name' => 'Çok Satanlar',
                'description' => 'Ana sayfada erken görünen satış odaklı ürün gridi.',
                'settings' => $this->defaultModuleSettings([
                    'variant' => 'grid',
                    'content_limit' => 8,
                    'columns_mobile' => 2,
                    'columns_tablet' => 3,
                    'columns_desktop' => 4,
                ]),
            ],
            'trust_badges' => [
                'name' => 'Güven Rozetleri',
                'description' => 'Teslim, kalite ve deneyim güvenceleri.',
                'settings' => $this->defaultModuleSettings([
                    'variant' => 'trust-strip',
                    'content_limit' => 4,
                ]),
            ],
            'instagram_preview' => [
                'name' => 'Instagram Önizleme',
                'description' => 'Sosyal kanıt ve takip CTA alanı.',
                'settings' => $this->defaultModuleSettings([
                    'variant' => 'social-proof',
                    'content_limit' => 1,
                    'cta_enabled' => true,
                ]),
            ],
            'blog_preview' => [
                'name' => 'Blog Seçkisi',
                'description' => 'Marka notları ve içerik girişi.',
                'settings' => $this->defaultModuleSettings([
                    'variant' => 'editorial-cards',
                    'content_limit' => 3,
                ]),
            ],
        ];
    }

    public function defaultAppearance(): array
    {
        return [
            'primary_color' => '#3d2645',
            'accent_color' => '#c97a9b',
            'background_color' => '#faf6f1',
            'font_family' => 'inter',
            'radius_preset' => 'rounded',
            'shadow_preset' => 'soft',
            'container_width' => '1280px',
            'default_theme_mode' => 'system',
        ];
    }

    public function getDraftState(): array
    {
        $modules = $this->getDraftModules()
            ->values()
            ->map(fn (LayoutModule $module, int $index) => $this->normalizeModule($module, $index))
            ->all();

        return [
            'area' => self::AREA_HOME,
            'name' => 'Storefront Taslagi',
            'modules' => $modules,
            'appearance' => $this->getAppearanceSettings(),
        ];
    }

    public function getPublishedState(): array
    {
        return $this->resolveState();
    }

    public function resolveState(?LayoutRevision $revision = null): array
    {
        $revision ??= $this->getPublishedRevision();

        if (! $revision) {
            return $this->getDraftState();
        }

        return $this->normalizeStatePayload($revision->payload ?? []);
    }

    public function getDraftRevision(): LayoutRevision
    {
        $draft = LayoutRevision::query()
            ->area(self::AREA_HOME)
            ->draft()
            ->latest('updated_at')
            ->first();

        return $draft ?: $this->syncDraftRevision();
    }

    public function syncDraftRevision(?User $user = null): LayoutRevision
    {
        return LayoutRevision::query()->updateOrCreate(
            ['area' => self::AREA_HOME, 'status' => LayoutRevision::STATUS_DRAFT],
            [
                'name' => 'Aktif Taslak',
                'payload' => $this->getDraftState(),
                'created_by' => $user?->id,
            ]
        );
    }

    public function storeDraftState(array $modules, array $appearance, ?User $user = null): LayoutRevision
    {
        $normalizedModules = $this->normalizeIncomingModules($modules);

        DB::transaction(function () use ($normalizedModules, $appearance): void {
            foreach ($normalizedModules as $moduleState) {
                LayoutModule::query()->updateOrCreate(
                    ['key' => $moduleState['key']],
                    [
                        'name' => $moduleState['name'],
                        'is_active' => (bool) ($moduleState['is_active'] ?? true),
                        'sort_order' => (int) $moduleState['sort_order'],
                        'settings' => $moduleState['settings'],
                    ]
                );
            }

            foreach ($this->normalizeAppearance($appearance) as $key => $value) {
                Setting::set('appearance', $key, is_bool($value) ? ($value ? '1' : '0') : $value);
            }
        });

        return $this->syncDraftRevision($user);
    }

    public function publishDraft(?User $user = null): LayoutRevision
    {
        $draft = $this->syncDraftRevision($user);

        return DB::transaction(function () use ($draft, $user) {
            LayoutRevision::query()
                ->area(self::AREA_HOME)
                ->published()
                ->update(['status' => LayoutRevision::STATUS_ARCHIVED]);

            $published = LayoutRevision::query()->create([
                'area' => self::AREA_HOME,
                'name' => 'Canli Storefront '.now()->format('d.m.Y H:i'),
                'payload' => $draft->payload,
                'status' => LayoutRevision::STATUS_PUBLISHED,
                'created_by' => $draft->created_by,
                'published_by' => $user?->id,
                'published_at' => now(),
            ]);

            Setting::set('system', 'layout_version', (string) now()->timestamp);
            Setting::forgetStorefrontCaches();
            Setting::bumpStorefrontContentVersion();

            return $published;
        });
    }

    public function restoreRevisionToDraft(LayoutRevision $revision, ?User $user = null): LayoutRevision
    {
        $state = $this->normalizeStatePayload($revision->payload ?? []);

        DB::transaction(function () use ($state): void {
            foreach ($state['modules'] as $index => $moduleState) {
                LayoutModule::query()->updateOrCreate(
                    ['key' => $moduleState['key']],
                    [
                        'name' => $moduleState['name'],
                        'is_active' => (bool) ($moduleState['is_active'] ?? true),
                        'sort_order' => $index + 1,
                        'settings' => $moduleState['settings'],
                    ]
                );
            }

            foreach ($state['appearance'] as $key => $value) {
                Setting::set('appearance', $key, is_bool($value) ? ($value ? '1' : '0') : $value);
            }
        });

        return $this->syncDraftRevision($user);
    }

    public function getRevisionOptions(): array
    {
        return LayoutRevision::query()
            ->area(self::AREA_HOME)
            ->whereIn('status', [LayoutRevision::STATUS_PUBLISHED, LayoutRevision::STATUS_ARCHIVED])
            ->latest('published_at')
            ->get()
            ->mapWithKeys(function (LayoutRevision $revision): array {
                return [
                    $revision->id => trim(implode(' • ', array_filter([
                        ucfirst($revision->status),
                        $revision->published_at?->format('d.m.Y H:i'),
                        $revision->name,
                    ]))),
                ];
            })
            ->all();
    }

    public function getPublishedRevision(): ?LayoutRevision
    {
        return LayoutRevision::query()
            ->area(self::AREA_HOME)
            ->published()
            ->latest('published_at')
            ->first();
    }

    public function getPreviewUrl(LayoutRevision $revision, string $locale = 'tr'): string
    {
        return URL::temporarySignedRoute(
            'layout.preview.home',
            now()->addMinutes(30),
            [
                'revision' => $revision->id,
                'locale' => $locale,
            ]
        );
    }

    public function getAppearanceSettings(): array
    {
        $defaults = $this->defaultAppearance();

        foreach (array_keys($defaults) as $key) {
            $defaults[$key] = Setting::get('appearance', $key, $defaults[$key]);
        }

        return $this->normalizeAppearance($defaults);
    }

    public function getAppearanceCssVariables(?array $appearance = null): array
    {
        $appearance ??= $this->getPublishedState()['appearance'] ?? $this->defaultAppearance();

        $radiusMap = [
            'soft' => '1rem',
            'rounded' => '1.5rem',
            'sharp' => '0.75rem',
        ];

        $shadowMap = [
            'none' => 'none',
            'soft' => '0 20px 54px -28px rgba(61, 38, 69, 0.28)',
            'elevated' => '0 24px 64px -24px rgba(61, 38, 69, 0.38)',
        ];

        return [
            '--rg-brand-primary' => (string) ($appearance['primary_color'] ?? '#3d2645'),
            '--rg-brand-accent' => (string) ($appearance['accent_color'] ?? '#c97a9b'),
            '--rg-surface-bg' => (string) ($appearance['background_color'] ?? '#faf6f1'),
            '--rg-radius' => $radiusMap[$appearance['radius_preset'] ?? 'rounded'] ?? $radiusMap['rounded'],
            '--rg-shadow' => $shadowMap[$appearance['shadow_preset'] ?? 'soft'] ?? $shadowMap['soft'],
            '--rg-content-width' => (string) ($appearance['container_width'] ?? '1280px'),
            '--rg-font-family' => match ($appearance['font_family'] ?? 'inter') {
                'playfair' => '"Playfair Display", serif',
                'poppins' => '"Poppins", sans-serif',
                default => '"Inter", sans-serif',
            },
        ];
    }

    private function getDraftModules(): Collection
    {
        $this->ensureDefaultModules();

        return LayoutModule::query()->orderBy('sort_order')->get();
    }

    private function ensureDefaultModules(): void
    {
        foreach ($this->getModuleDefinitions() as $key => $definition) {
            LayoutModule::query()->firstOrCreate(
                ['key' => $key],
                [
                    'name' => $definition['name'],
                    'is_active' => true,
                    'sort_order' => $this->defaultSortOrder($key),
                    'settings' => $definition['settings'],
                ]
            );
        }
    }

    private function normalizeModule(LayoutModule $module, int $index): array
    {
        $definition = $this->getModuleDefinitions()[$module->key] ?? [
            'name' => $module->name,
            'description' => '',
            'settings' => $this->defaultModuleSettings(),
        ];

        return [
            'id' => $module->id,
            'key' => $module->key,
            'name' => $module->name ?: $definition['name'],
            'description' => $definition['description'],
            'is_active' => (bool) $module->is_active,
            'sort_order' => $module->sort_order ?: ($index + 1),
            'settings' => $this->normalizeModuleSettings(
                array_replace_recursive($definition['settings'], $module->settings ?? []),
                $definition['settings']
            ),
        ];
    }

    private function normalizeStatePayload(array $payload): array
    {
        $definitions = $this->getModuleDefinitions();
        $providedModules = collect($payload['modules'] ?? [])
            ->filter(fn ($module) => is_array($module) && array_key_exists((string) ($module['key'] ?? ''), $definitions))
            ->sortBy(fn (array $module, int $index): int => (int) ($module['sort_order'] ?? ($index + 1)))
            ->values();

        $seen = [];
        $modules = $providedModules
            ->map(function (array $module, int $index) use (&$seen): array {
                $key = (string) $module['key'];
                $seen[$key] = true;

                return $this->normalizeModuleState($key, $module, $index);
            })
            ->values()
            ->all();

        foreach (array_keys($definitions) as $key) {
            if (isset($seen[$key])) {
                continue;
            }

            $modules[] = $this->normalizeModuleState($key, ['key' => $key], count($modules));
        }

        return [
            'area' => $payload['area'] ?? self::AREA_HOME,
            'name' => $payload['name'] ?? 'Storefront Taslagi',
            'modules' => $modules,
            'appearance' => $this->normalizeAppearance(
                array_replace($this->defaultAppearance(), Arr::wrap($payload['appearance'] ?? []))
            ),
        ];
    }

    private function defaultModuleSettings(array $overrides = []): array
    {
        return array_replace([
            'variant' => 'default',
            'background_tone' => 'surface',
            'accent_mode' => 'brand',
            'padding_scale' => 'regular',
            'image_ratio' => '16:9',
            'card_density' => 'comfortable',
            'container_width' => 'content',
            'columns_mobile' => 1,
            'columns_tablet' => 2,
            'columns_desktop' => 3,
            'content_limit' => 6,
            'title_override' => ['tr' => '', 'en' => '', 'ku' => ''],
            'subtitle_override' => ['tr' => '', 'en' => '', 'ku' => ''],
            'cta_enabled' => false,
            'cta_label' => ['tr' => '', 'en' => '', 'ku' => ''],
            'cta_url' => '',
            'show_on_mobile' => true,
            'show_on_tablet' => true,
            'show_on_desktop' => true,
        ], $overrides);
    }

    private function defaultSortOrder(string $key): int
    {
        $salesFirstOrder = [
            'announcement_bar',
            'hero',
            'best_sellers',
            'new_arrivals',
            'category_showcase',
            'occasion_spotlight',
            'trust_badges',
            'featured_showcase',
            'instagram_preview',
            'blog_preview',
        ];

        $position = array_search($key, $salesFirstOrder, true);

        return $position === false ? array_search($key, array_keys($this->getModuleDefinitions()), true) + 1 : $position + 1;
    }

    private function normalizeAppearance(array $appearance): array
    {
        $normalized = array_replace($this->defaultAppearance(), $appearance);
        $normalized['primary_color'] = $this->normalizeHexColor($normalized['primary_color'] ?? null, '#3d2645');
        $normalized['accent_color'] = $this->normalizeHexColor($normalized['accent_color'] ?? null, '#c97a9b');
        $normalized['background_color'] = $this->normalizeHexColor($normalized['background_color'] ?? null, '#faf6f1');
        $normalized['font_family'] = $this->normalizeEnum($normalized['font_family'] ?? null, ['inter', 'playfair', 'poppins'], 'inter');
        $normalized['radius_preset'] = $this->normalizeEnum($normalized['radius_preset'] ?? null, ['soft', 'rounded', 'sharp'], 'rounded');
        $normalized['shadow_preset'] = $this->normalizeEnum($normalized['shadow_preset'] ?? null, ['none', 'soft', 'elevated'], 'soft');
        $normalized['container_width'] = $this->normalizeAppearanceWidth($normalized['container_width'] ?? null, '1280px');
        $normalized['default_theme_mode'] = $this->normalizeEnum($normalized['default_theme_mode'] ?? null, ['system', 'light', 'dark'], 'system');

        return $normalized;
    }

    private function normalizeIncomingModules(array $modules): array
    {
        $definitions = $this->getModuleDefinitions();
        $submitted = collect($modules)->values();
        $normalized = [];
        $seen = [];

        foreach ($submitted as $module) {
            if (! is_array($module)) {
                continue;
            }

            $key = (string) ($module['key'] ?? '');

            if ($key === '' || isset($seen[$key]) || ! array_key_exists($key, $definitions)) {
                continue;
            }

            $seen[$key] = true;
            $normalized[] = $this->normalizeModuleState($key, $module, count($normalized));
        }

        foreach (array_keys($definitions) as $key) {
            if (isset($seen[$key])) {
                continue;
            }

            $normalized[] = $this->normalizeModuleState($key, ['key' => $key], count($normalized));
        }

        return $normalized;
    }

    private function normalizeModuleState(string $key, array $module, int $index): array
    {
        $definition = $this->getModuleDefinitions()[$key] ?? [
            'name' => $module['name'] ?? $key,
            'description' => '',
            'settings' => $this->defaultModuleSettings(),
        ];

        return [
            'id' => $module['id'] ?? null,
            'key' => $key,
            'name' => $module['name'] ?? $definition['name'],
            'description' => $definition['description'],
            'is_active' => (bool) ($module['is_active'] ?? true),
            'sort_order' => $index + 1,
            'settings' => $this->normalizeModuleSettings(
                array_replace_recursive($definition['settings'], Arr::wrap($module['settings'] ?? [])),
                $definition['settings']
            ),
        ];
    }

    private function normalizeModuleSettings(array $settings, array $defaults = []): array
    {
        $normalized = array_replace_recursive($this->defaultModuleSettings(), $defaults, $settings);

        $normalized['variant'] = $this->normalizeEnum(
            $normalized['variant'] ?? null,
            ['default', 'notice', 'spotlight', 'category-grid', 'featured-product', 'occasion', 'product-rail', 'trust-strip', 'social-proof', 'editorial-cards', 'showcase', 'grid', 'stack'],
            $defaults['variant'] ?? 'default'
        );
        $normalized['background_tone'] = $this->normalizeEnum($normalized['background_tone'] ?? null, ['surface', 'muted', 'contrast'], 'surface');
        $normalized['accent_mode'] = $this->normalizeEnum($normalized['accent_mode'] ?? null, ['brand', 'neutral', 'soft'], 'brand');
        $normalized['padding_scale'] = $this->normalizeEnum($normalized['padding_scale'] ?? null, ['compact', 'regular', 'relaxed'], 'regular');
        $normalized['image_ratio'] = $this->normalizeEnum($normalized['image_ratio'] ?? null, ['16:9', '16:10', '4:3', '1:1', '4:5', '5:6', '3:4', '21:9'], $defaults['image_ratio'] ?? '16:9');
        $normalized['card_density'] = $this->normalizeEnum($normalized['card_density'] ?? null, ['compact', 'comfortable', 'airy'], 'comfortable');
        $normalized['container_width'] = $this->normalizeEnum($normalized['container_width'] ?? null, ['content', 'wide', 'full'], $defaults['container_width'] ?? 'content');
        $normalized['columns_mobile'] = $this->clampInteger($normalized['columns_mobile'] ?? 1, 1, 4);
        $normalized['columns_tablet'] = $this->clampInteger($normalized['columns_tablet'] ?? 2, 1, 6);
        $normalized['columns_desktop'] = $this->clampInteger($normalized['columns_desktop'] ?? 3, 1, 12);
        $normalized['content_limit'] = $this->clampInteger($normalized['content_limit'] ?? 6, 1, 24);
        $normalized['title_override'] = $this->normalizeTranslations($normalized['title_override'] ?? [], 120);
        $normalized['subtitle_override'] = $this->normalizeTranslations($normalized['subtitle_override'] ?? [], 240);
        $normalized['cta_enabled'] = (bool) ($normalized['cta_enabled'] ?? false);
        $normalized['cta_label'] = $this->normalizeTranslations($normalized['cta_label'] ?? [], 80);
        $normalized['cta_url'] = $this->normalizeActionUrl($normalized['cta_url'] ?? '');
        $normalized['show_on_mobile'] = (bool) ($normalized['show_on_mobile'] ?? true);
        $normalized['show_on_tablet'] = (bool) ($normalized['show_on_tablet'] ?? true);
        $normalized['show_on_desktop'] = (bool) ($normalized['show_on_desktop'] ?? true);

        return $normalized;
    }

    private function normalizeTranslations(mixed $translations, int $limit): array
    {
        $translations = is_array($translations) ? $translations : [];
        $normalized = [];

        foreach (['tr', 'en', 'ku'] as $locale) {
            $value = trim((string) ($translations[$locale] ?? ''));
            $normalized[$locale] = mb_substr($value, 0, $limit);
        }

        return $normalized;
    }

    private function normalizeActionUrl(mixed $value): string
    {
        $value = trim((string) $value);

        if ($value === '') {
            return '';
        }

        foreach (['/', '#', 'http://', 'https://', 'mailto:', 'tel:'] as $allowedPrefix) {
            if (str_starts_with($value, $allowedPrefix)) {
                return mb_substr($value, 0, 255);
            }
        }

        return '';
    }

    private function normalizeHexColor(mixed $value, string $fallback): string
    {
        $value = trim((string) $value);

        return preg_match('/^#[0-9a-fA-F]{6}$/', $value) ? strtolower($value) : $fallback;
    }

    private function normalizeAppearanceWidth(mixed $value, string $fallback): string
    {
        $value = trim((string) $value);

        if (preg_match('/^(9[6-9]\d|1\d{3}|1600)px$/', $value)) {
            return $value;
        }

        return $fallback;
    }

    private function normalizeEnum(mixed $value, array $allowed, string $fallback): string
    {
        $value = (string) $value;

        return in_array($value, $allowed, true) ? $value : $fallback;
    }

    private function clampInteger(mixed $value, int $min, int $max): int
    {
        return max($min, min($max, (int) $value));
    }
}
