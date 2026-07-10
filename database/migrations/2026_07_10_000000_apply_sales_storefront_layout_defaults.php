<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('layout_modules')) {
            return;
        }

        $order = [
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

        $activeModules = [
            'announcement_bar',
            'hero',
            'best_sellers',
            'category_showcase',
            'occasion_spotlight',
            'new_arrivals',
            'trust_badges',
        ];

        $moduleNames = [
            'announcement_bar' => 'Announcement Bar',
            'hero' => 'Hero Spotlight',
            'best_sellers' => 'Çok Satanlar',
            'category_showcase' => 'Kategori Keşfi',
            'occasion_spotlight' => 'Özel Gün Spotlight',
            'new_arrivals' => 'Yeni Gelenler',
            'trust_badges' => 'Güven Rozetleri',
            'featured_showcase' => 'Editoryal Vitrin',
            'instagram_preview' => 'Instagram Önizleme',
            'blog_preview' => 'Blog Seçkisi',
        ];

        $moduleOverrides = [
            'hero' => [
                'padding_scale' => 'compact',
                'background_tone' => 'surface',
                'container_width' => 'content',
            ],
            'best_sellers' => [
                'variant' => 'grid',
                'padding_scale' => 'compact',
                'background_tone' => 'surface',
                'card_density' => 'compact',
                'content_limit' => 8,
                'columns_mobile' => 2,
                'columns_tablet' => 3,
                'columns_desktop' => 4,
            ],
            'category_showcase' => [
                'padding_scale' => 'compact',
                'background_tone' => 'muted',
                'card_density' => 'compact',
                'content_limit' => 6,
                'columns_mobile' => 2,
                'columns_tablet' => 3,
                'columns_desktop' => 3,
            ],
            'occasion_spotlight' => [
                'padding_scale' => 'compact',
                'background_tone' => 'surface',
                'content_limit' => 3,
            ],
            'new_arrivals' => [
                'variant' => 'grid',
                'padding_scale' => 'compact',
                'background_tone' => 'surface',
                'card_density' => 'compact',
                'content_limit' => 8,
                'columns_mobile' => 2,
                'columns_tablet' => 3,
                'columns_desktop' => 4,
            ],
            'trust_badges' => [
                'padding_scale' => 'compact',
                'background_tone' => 'muted',
                'content_limit' => 4,
            ],
            'featured_showcase' => [
                'padding_scale' => 'compact',
                'background_tone' => 'surface',
                'content_limit' => 1,
            ],
            'instagram_preview' => [
                'padding_scale' => 'compact',
            ],
            'blog_preview' => [
                'padding_scale' => 'compact',
            ],
        ];

        $now = now();

        foreach ($order as $index => $key) {
            $current = DB::table('layout_modules')->where('key', $key)->first();
            $settings = $this->decodeSettings($current?->settings);
            $settings = array_replace_recursive($settings, $moduleOverrides[$key] ?? []);

            DB::table('layout_modules')->updateOrInsert(
                ['key' => $key],
                [
                    'name' => $moduleNames[$key] ?? $key,
                    'is_active' => in_array($key, $activeModules, true),
                    'sort_order' => $index + 1,
                    'settings' => json_encode($settings, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                    'updated_at' => $now,
                    'created_at' => $current?->created_at ?? $now,
                ]
            );
        }

        if (! Schema::hasTable('layout_revisions')) {
            return;
        }

        DB::table('layout_revisions')
            ->where('area', 'home')
            ->whereIn('status', ['draft', 'published'])
            ->orderBy('id')
            ->each(function (object $revision) use ($order, $activeModules, $moduleNames, $moduleOverrides, $now): void {
                $payload = $this->decodePayload($revision->payload);
                $modules = collect($payload['modules'] ?? [])
                    ->filter(fn ($module): bool => is_array($module) && filled($module['key'] ?? null))
                    ->keyBy(fn (array $module): string => (string) $module['key']);

                $payload['modules'] = collect($order)
                    ->map(function (string $key, int $index) use ($modules, $activeModules, $moduleNames, $moduleOverrides): array {
                        $module = $modules->get($key, ['key' => $key]);
                        $settings = is_array($module['settings'] ?? null) ? $module['settings'] : [];

                        $module['key'] = $key;
                        $module['name'] = $moduleNames[$key] ?? ($module['name'] ?? $key);
                        $module['is_active'] = in_array($key, $activeModules, true);
                        $module['sort_order'] = $index + 1;
                        $module['settings'] = array_replace_recursive($settings, $moduleOverrides[$key] ?? []);

                        return $module;
                    })
                    ->all();

                $payload['appearance'] = array_replace($payload['appearance'] ?? [], [
                    'primary_color' => '#3d2645',
                    'accent_color' => '#b87b95',
                    'background_color' => '#fffaf7',
                    'font_family' => 'playfair',
                    'radius_preset' => 'soft',
                    'shadow_preset' => 'soft',
                    'container_width' => '1240px',
                    'default_theme_mode' => 'light',
                ]);

                DB::table('layout_revisions')
                    ->where('id', $revision->id)
                    ->update([
                        'payload' => json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                        'updated_at' => $now,
                    ]);
            });
    }

    public function down(): void
    {
        // Intentionally not reversible: this migration updates merchandising defaults
        // while preserving existing content and per-module text overrides.
    }

    private function decodeSettings(mixed $settings): array
    {
        if (is_array($settings)) {
            return $settings;
        }

        if (! is_string($settings) || $settings === '') {
            return [];
        }

        $decoded = json_decode($settings, true);

        return is_array($decoded) ? $decoded : [];
    }

    private function decodePayload(mixed $payload): array
    {
        if (is_array($payload)) {
            return $payload;
        }

        if (! is_string($payload) || $payload === '') {
            return ['area' => 'home', 'name' => 'Storefront Taslagi', 'modules' => [], 'appearance' => []];
        }

        $decoded = json_decode($payload, true);

        return is_array($decoded) ? $decoded : ['area' => 'home', 'name' => 'Storefront Taslagi', 'modules' => [], 'appearance' => []];
    }
};
