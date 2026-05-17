<?php

namespace App\Support;

use App\Models\HeaderTheme;
use App\Models\SpecialOccasion;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class HeaderThemeVisuals
{
    public static function present(HeaderTheme $theme, string $locale, bool $isPreview = false): array
    {
        $message = $theme->translatedBannerMessage($locale);
        $headline = $theme->translatedHeadline($locale) ?: $theme->translatedName($locale);
        $subline = $theme->translatedSubline($locale) ?: $message;
        $ctaLabel = $theme->translatedCtaLabel($locale) ?: self::defaultCtaLabel($locale);
        $ctaUrl = self::ctaUrl($theme);
        $visuals = self::campaignVisuals($theme);
        $style = $theme->style_variant ?: self::defaultStyleVariant($theme);

        return [
            'id' => $theme->slug,
            'is_active' => true,
            'is_preview' => $isPreview,
            'style_variant' => $style,
            'seasonal_style' => $style,
            'tone' => self::tone($theme),
            'header_class' => self::headerClass($theme),
            'banner_message' => $message,
            'message' => $message,
            'seasonal_message' => $message,
            'headline' => $headline,
            'subline' => $subline,
            'cta_label' => $ctaLabel,
            'cta_url' => $ctaUrl,
            'seasonal_cta' => [
                'label' => $ctaLabel,
                'url' => $ctaUrl,
            ],
            'seasonal_cta_label' => $ctaLabel,
            'seasonal_cta_url' => $ctaUrl,
            'campaign_visuals' => $visuals,
            'campaign_image' => $visuals[0] ?? null,
            'seasonal_visual' => $visuals[0] ?? null,
            'banner_layout' => 'header_skin',
            'illustration_mode' => $theme->illustration_mode ?: 'inline_svg',
            'illustration_asset' => $theme->illustration_asset,
            'visual' => null,
            'visual_markup' => null,
            'show_flag' => false,
            'show_ataturk' => false,
            'decor_intensity' => $theme->decor_intensity ?: 'medium',
            'date_range' => [
                'starts_at' => optional($theme->starts_at)?->toDateString(),
                'ends_at' => optional($theme->ends_at)?->toDateString(),
            ],
            'preview_state' => $isPreview ? 'preview' : 'live',
            'flags' => [
                'show_flag' => false,
                'show_ataturk' => false,
            ],
        ];
    }

    public static function headerClass(HeaderTheme $theme): string
    {
        $slugClass = match ($theme->slug) {
            'ramazan-bayrami', 'kurban-bayrami' => 'theme-bayram',
            default => 'theme-'.$theme->slug,
        };

        $style = $theme->style_variant ?: self::defaultStyleVariant($theme);

        return trim(sprintf('%s rg-header-theme rg-header-campaign theme-style-%s decor-%s', $slugClass, $style, $theme->decor_intensity ?: 'medium'));
    }

    private static function tone(HeaderTheme $theme): string
    {
        return match ($theme->slug) {
            'sevgililer-gunu' => 'romantic',
            'kadinlar-gunu', 'anneler-gunu' => 'warm',
            'tip-bayrami' => 'fresh',
            'babalar-gunu' => 'tailored',
            'ogretmenler-gunu' => 'tribute',
            'yilbasi' => 'winter',
            'ramazan-bayrami', 'kurban-bayrami' => 'bayram',
            default => 'seasonal',
        };
    }

    private static function defaultStyleVariant(HeaderTheme $theme): string
    {
        return match ($theme->slug) {
            'sevgililer-gunu' => 'romantic',
            'anneler-gunu', 'kadinlar-gunu' => 'warm',
            'tip-bayrami' => 'fresh',
            'babalar-gunu' => 'tailored',
            'yilbasi' => 'winter',
            'ramazan-bayrami', 'kurban-bayrami' => 'bayram',
            default => 'tribute',
        };
    }

    /**
     * @return list<string>
     */
    private static function campaignVisuals(HeaderTheme $theme): array
    {
        $urls = [];
        $append = function (?string $url) use (&$urls): void {
            $url = trim((string) $url);
            if ($url === '' || in_array($url, $urls, true)) {
                return;
            }

            $urls[] = $url;
        };

        if (filled($theme->campaign_image)) {
            $append(StorefrontImage::resolvePath($theme->campaign_image, 'images/product-placeholder.svg'));
        }

        if ($theme->illustration_mode === 'custom_asset' && filled($theme->illustration_asset)) {
            $append(self::assetUrl($theme->illustration_asset));
        }

        $occasion = self::specialOccasion($theme);
        if ($occasion && count($urls) < 3) {
            $name = $occasion->getTranslation('name', app()->getLocale(), false) ?: $occasion->getTranslation('name', 'tr', false);
            foreach (StorefrontImage::specialOccasionGallery(
                $occasion->slug,
                $name,
                $occasion->category?->getTranslation('name', app()->getLocale(), false),
                $occasion->category?->slug,
            ) as $url) {
                $append($url);

                if (count($urls) >= 3) {
                    break;
                }
            }
        }

        foreach (StorefrontImage::decorativeOrProductStrip(3) as $url) {
            $append($url);

            if (count($urls) >= 3) {
                break;
            }
        }

        return array_slice($urls, 0, 3);
    }

    private static function ctaUrl(HeaderTheme $theme): string
    {
        $custom = trim((string) $theme->cta_url);
        if ($custom !== '') {
            if (Str::startsWith($custom, ['http://', 'https://', '/', '#'])) {
                return $custom;
            }

            return url('/'.ltrim($custom, '/'));
        }

        $occasion = self::specialOccasion($theme);

        if ($occasion) {
            return StorefrontLocale::route('special-occasions.show', ['slug' => $occasion->slug]);
        }

        return StorefrontLocale::route('special-occasions.index');
    }

    private static function specialOccasion(HeaderTheme $theme): ?SpecialOccasion
    {
        $slug = trim((string) $theme->special_occasion_slug);
        if ($slug === '') {
            $slug = $theme->slug;
        }

        return SpecialOccasion::query()
            ->active()
            ->with('category')
            ->where('slug', $slug)
            ->first();
    }

    private static function defaultCtaLabel(string $locale): string
    {
        return match ($locale) {
            'en' => 'Explore the collection',
            'ku' => 'Koleksiyonê bibîne',
            default => 'Koleksiyonu keşfet',
        };
    }

    private static function assetUrl(string $path): string
    {
        $path = trim($path);

        if ($path === '') {
            return '';
        }

        if (Str::startsWith($path, ['http://', 'https://', '//'])) {
            return $path;
        }

        if (Str::startsWith($path, '/')) {
            return url($path);
        }

        if (Str::startsWith($path, ['images/', 'storage/'])) {
            return asset($path);
        }

        return Storage::disk(config('filament.default_filesystem_disk', 'public'))->url($path);
    }
}
