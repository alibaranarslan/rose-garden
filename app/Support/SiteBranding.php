<?php

namespace App\Support;

use App\Models\Setting;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SiteBranding
{
    public static function current(): array
    {
        $fallbackBrandName = __('Rose Garden Çiçek Çikolata');
        $fallbackBrandTagline = __('Butik çiçek ve çikolata deneyimi.');

        $siteName = self::resolveLocalizedText(
            Setting::get('general', 'site_name', ''),
            $fallbackBrandName,
        );

        if ($siteName === '' || Str::lower($siteName) === 'laravel') {
            $siteName = $fallbackBrandName;
        }

        $siteTagline = self::resolveLocalizedText(
            Setting::get('general', 'site_tagline', ''),
            $fallbackBrandTagline,
        );
        $customLogo = trim((string) Setting::get('general', 'logo_path', ''));
        $customFavicon = trim((string) Setting::get('general', 'favicon_path', ''));
        $socialLinks = self::socialLinks();

        return [
            'site_name' => $siteName,
            'site_tagline' => $siteTagline,
            'footer_description' => $siteTagline !== ''
                ? $siteTagline
                : __('Adıyaman’da butik floral tasarımları ve kontrollü teslim deneyimini aynı çizgide buluşturur.'),
            'custom_logo_url' => $customLogo !== '' ? self::resolveMediaUrl($customLogo, '') : null,
            'logo_light_url' => $customLogo !== ''
                ? self::resolveMediaUrl($customLogo, '')
                : self::resolveMediaUrl('', 'images/branding/rg-logo-dark.png'),
            'logo_dark_url' => $customLogo !== ''
                ? self::resolveMediaUrl($customLogo, '')
                : self::resolveMediaUrl('', 'images/branding/rg-logo-light.png'),
            'favicon_url' => $customFavicon !== ''
                ? self::resolveMediaUrl($customFavicon, '')
                : self::resolveMediaUrl('', 'images/branding/favicon.png'),
            'uses_custom_favicon' => $customFavicon !== '',
            'social_links' => $socialLinks,
            'social_profiles' => collect($socialLinks)
                ->map(fn (string $url, string $platform) => [
                    'platform' => $platform,
                    'label' => self::socialLabel($platform),
                    'url' => $url,
                ])
                ->filter(fn (array $profile) => filled($profile['url']))
                ->values()
                ->all(),
        ];
    }

    private static function socialLinks(): array
    {
        $defaults = [
            'instagram' => '',
            'facebook' => '',
            'twitter' => '',
            'youtube' => '',
        ];

        $configured = json_decode((string) Setting::get('social', 'links', '[]'), true) ?? [];

        foreach ($configured as $item) {
            $platform = Str::lower(trim((string) data_get($item, 'platform', '')));
            $url = trim((string) data_get($item, 'url', ''));

            if ($platform === '' || ! array_key_exists($platform, $defaults)) {
                continue;
            }

            $defaults[$platform] = $url;
        }

        return $defaults;
    }

    private static function socialLabel(string $platform): string
    {
        return match ($platform) {
            'twitter' => 'X',
            'facebook' => 'Facebook',
            'instagram' => 'Instagram',
            'youtube' => 'YouTube',
            default => Str::headline($platform),
        };
    }

    private static function resolveMediaUrl(?string $value, string $fallback): string
    {
        $path = trim((string) $value);

        if ($path === '') {
            return $fallback !== '' ? asset($fallback) : '';
        }

        if (Str::startsWith($path, ['http://', 'https://', '//'])) {
            return $path;
        }

        if (Str::startsWith($path, ['/'])) {
            return url($path);
        }

        if (Str::startsWith($path, ['images/', 'storage/', 'favicon'])) {
            return asset($path);
        }

        return Storage::disk(config('filament.default_filesystem_disk', 'public'))->url($path);
    }

    private static function resolveLocalizedText(mixed $value, string $fallback): string
    {
        if (is_array($value)) {
            return self::pickLocaleValue($value, $fallback);
        }

        $text = trim((string) $value);

        if ($text === '') {
            return $fallback;
        }

        $decoded = json_decode($text, true);

        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return self::pickLocaleValue($decoded, $fallback);
        }

        return $text;
    }

    private static function pickLocaleValue(array $values, string $fallback): string
    {
        $locale = app()->getLocale();

        foreach ([$locale, 'tr', 'en', 'ku'] as $candidate) {
            $value = trim((string) ($values[$candidate] ?? ''));

            if ($value !== '') {
                return $value;
            }
        }

        foreach ($values as $value) {
            $resolved = trim((string) $value);

            if ($resolved !== '') {
                return $resolved;
            }
        }

        return $fallback;
    }
}
