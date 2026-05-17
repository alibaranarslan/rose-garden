{{--
  Site temasi (html sinifi):
  - Acik tema: siyah logo -> *-dark.png dosyalari
  - Koyu tema: beyaz logo -> *-light.png dosyalari
  variant="light"|"dark" = o anki sayfa zeminine gore tek logo
--}}
@props([
    'variant' => 'light',
    'type' => 'wordmark',
    'placement' => null,
    'alt' => 'Rose Garden — Çiçek ve Çikolata',
])

@php
    $branding = \App\Support\SiteBranding::current();
    $assets = [
        'wordmark' => [
            'light' => 'images/branding/rg-logo-dark.png',
            'dark' => 'images/branding/rg-logo-light.png',
        ],
        'lockup' => [
            'light' => 'images/branding/rg-lockup-dark.png',
            'dark' => 'images/branding/rg-lockup-light.png',
        ],
    ];

    $defaultType = $assets[$type] ?? $assets['wordmark'];
    $logoLightUi = $branding['custom_logo_url'] ?? $defaultType['light'];
    $logoDarkUi = $branding['custom_logo_url'] ?? $defaultType['dark'];
    $resolvedAlt = $alt !== 'Rose Garden — Çiçek ve Çikolata'
        ? $alt
        : trim(($branding['site_name'] ?? 'Rose Garden').' - '.($branding['site_tagline'] ?? 'Çiçek ve Çikolata'));

    $placementClasses = match ($placement) {
        'header' => 'h-10 sm:h-11 md:h-[3.1rem] lg:h-[3.9rem] w-auto max-w-[min(100%,12.5rem)] sm:max-w-[14rem] md:max-w-[16.25rem] xl:max-w-[18rem] 2xl:max-w-[19rem] object-contain object-left',
        'footer' => 'h-12 sm:h-14 md:h-16 w-auto max-w-full object-contain object-left',
        'auth_dark' => 'h-14 sm:h-[3.75rem] md:h-[4.5rem] w-auto max-w-[min(100%,18rem)] sm:max-w-[20rem] object-contain mx-auto',
        'auth_light' => 'h-10 sm:h-11 md:h-12 w-auto max-w-[min(100%,12.5rem)] sm:max-w-[14rem] object-contain mx-auto',
        default => 'h-12 w-auto max-w-full object-contain',
    };

    $classList = trim($placementClasses.' '.($attributes->get('class') ?? ''));
    $extraAttrs = $attributes->except('class');

    $brandAssetUrl = static function (string $pathOrUrl): string {
        if (\Illuminate\Support\Str::startsWith($pathOrUrl, ['http://', 'https://', '//'])) {
            return $pathOrUrl;
        }

        $path = public_path($pathOrUrl);
        $v = is_file($path) ? filemtime($path) : 0;

        return $v > 0 ? asset($pathOrUrl).'?v='.$v : asset($pathOrUrl);
    };
@endphp

@if ($variant === 'adaptive')
    <img src="{{ $brandAssetUrl($logoLightUi) }}" alt="{{ $resolvedAlt }}" class="{{ $classList }} dark:hidden" {{ $extraAttrs }}>
    <img src="{{ $brandAssetUrl($logoDarkUi) }}" alt="{{ $resolvedAlt }}" class="{{ $classList }} hidden dark:block" {{ $extraAttrs }}>
@else
    <img src="{{ $brandAssetUrl($defaultType[$variant] ?? $logoLightUi) }}" alt="{{ $resolvedAlt }}" {{ $attributes->except('class')->merge(['class' => $classList]) }}>
@endif
