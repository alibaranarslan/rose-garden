<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = [
        'group',
        'key',
        'value',
    ];

    public static function get(string $group, string $key, mixed $default = null): mixed
    {
        $setting = static::where('group', $group)->where('key', $key)->first();

        return $setting ? $setting->value : $default;
    }

    public static function set(string $group, string $key, mixed $value): void
    {
        static::updateOrCreate(
            ['group' => $group, 'key' => $key],
            ['value' => $value]
        );
    }

    public static function bumpStorefrontContentVersion(): void
    {
        static::set('system', 'storefront_content_version', (string) now()->format('U.u'));
    }

    public static function forgetStorefrontCaches(): void
    {
        foreach (\App\Support\StorefrontLocale::codes() as $locale) {
            Cache::forget('rg_site_settings_'.$locale);
            Cache::forget('rg_site_branding_'.$locale);
        }

        Cache::forget('rg_nav_categories');
        Cache::forget('rg_nav_special_occasions');
        Cache::forget('rg_footer_promo_visuals');
    }

    public static function getGroup(string $group): array
    {
        return static::where('group', $group)
            ->pluck('value', 'key')
            ->toArray();
    }
}
