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

        foreach ($order as $index => $key) {
            DB::table('layout_modules')
                ->where('key', $key)
                ->update([
                    'sort_order' => $index + 1,
                    'updated_at' => now(),
                ]);
        }

        if (! Schema::hasTable('layout_revisions')) {
            return;
        }

        DB::table('layout_revisions')
            ->where('area', 'home')
            ->whereIn('status', ['draft', 'published'])
            ->orderBy('id')
            ->each(function (object $revision) use ($order): void {
                $payload = is_string($revision->payload)
                    ? json_decode($revision->payload, true)
                    : (array) $revision->payload;

                if (! is_array($payload)) {
                    return;
                }

                $modules = collect($payload['modules'] ?? [])
                    ->filter(fn ($module): bool => is_array($module) && filled($module['key'] ?? null))
                    ->keyBy(fn (array $module): string => (string) $module['key']);

                $payload['modules'] = collect($order)
                    ->map(function (string $key, int $index) use ($modules): array {
                        $module = $modules->get($key, ['key' => $key]);
                        $module['sort_order'] = $index + 1;

                        return $module;
                    })
                    ->all();

                DB::table('layout_revisions')
                    ->where('id', $revision->id)
                    ->update([
                        'payload' => json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                        'updated_at' => now(),
                    ]);
            });
    }

    public function down(): void
    {
        // Merchandising order change only; no destructive rollback.
    }
};
