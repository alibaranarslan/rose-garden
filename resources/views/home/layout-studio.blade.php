{{-- Live storefront homepage owner: routes/web.php -> StorefrontHomeController@index -> this view. --}}
{{-- Legacy home.index remains reference-only and should not be used for active homepage edits. --}}
@extends('layouts.app')

@php
    $moduleMap = collect($layoutSections ?? [])->keyBy('key');
    $heroSection = $moduleMap->get('hero');
    $bodySections = collect($layoutSections ?? [])
        ->values()
        ->reject(fn ($section) => in_array($section['key'], ['hero', 'announcement_bar'], true))
        ->values();
    $homepageDiscoveryProducts = collect($bestSellers ?? collect())
        ->concat([$featuredShowcase ?? null])
        ->filter()
        ->concat($newProducts ?? collect())
        ->concat($occasionProducts ?? collect())
        ->reject(fn ($product) => (int) data_get($product, 'id') === (int) data_get($heroProduct ?? null, 'id'))
        ->unique('id')
        ->take(4)
        ->values();
    $featuredSupportingProducts = collect($bestSellers ?? collect())
        ->concat($newProducts ?? collect())
        ->concat($occasionProducts ?? collect())
        ->concat($homepageDiscoveryProducts)
        ->reject(function ($product) use ($heroProduct, $featuredShowcase) {
            $productId = (int) data_get($product, 'id');

            return in_array($productId, [
                (int) data_get($heroProduct ?? null, 'id'),
                (int) data_get($featuredShowcase ?? null, 'id'),
            ], true);
        })
        ->unique('id')
        ->take(2)
        ->values();
    $backgroundClasses = [
        'surface' => '',
        'muted' => 'bg-white/60 dark:bg-white/[0.04]',
        'contrast' => 'border-y border-rose-200/70 bg-rose-50/80 dark:border-rose-400/10 dark:bg-rose-950/20',
    ];
    $paddingClasses = [
        'compact' => 'py-4',
        'regular' => 'py-6',
        'relaxed' => 'py-8 md:py-10',
    ];
    $containerClasses = [
        'content' => 'mx-auto w-full max-w-7xl px-4 sm:px-6',
        'wide' => 'mx-auto w-full max-w-[92rem] px-4 sm:px-6',
        'full' => 'w-full',
    ];
@endphp

@push('head')
    @once
        <style>
            .layout-visibility {
                display: none;
            }

            @media (max-width: 639px) {
                .layout-visibility[data-mobile="1"] {
                    display: block;
                }
            }

            @media (min-width: 640px) and (max-width: 1023px) {
                .layout-visibility[data-tablet="1"] {
                    display: block;
                }
            }

            @media (min-width: 1024px) {
                .layout-visibility[data-desktop="1"] {
                    display: block;
                }
            }

            .rg-homepage-shell .rg-section {
                padding-top: 2rem;
                padding-bottom: 2rem;
            }

            .rg-homepage-shell [data-home-section="category_showcase"] .rg-section,
            .rg-homepage-shell [data-home-section="featured_showcase"] .rg-section,
            .rg-homepage-shell [data-home-section="best_sellers"] .rg-section {
                padding-top: 1.5rem;
                padding-bottom: 1.5rem;
            }

            @media (min-width: 768px) {
                .rg-homepage-shell .rg-section {
                    padding-top: 2.5rem;
                    padding-bottom: 2.5rem;
                }

                .rg-homepage-shell [data-home-section="category_showcase"] .rg-section,
                .rg-homepage-shell [data-home-section="featured_showcase"] .rg-section,
                .rg-homepage-shell [data-home-section="best_sellers"] .rg-section {
                    padding-top: 1.85rem;
                    padding-bottom: 1.85rem;
                }
            }

            @media (min-width: 1024px) {
                .rg-homepage-shell .rg-section {
                    padding-top: 3rem;
                    padding-bottom: 3rem;
                }

                .rg-homepage-shell [data-home-section="category_showcase"] .rg-section,
                .rg-homepage-shell [data-home-section="featured_showcase"] .rg-section,
                .rg-homepage-shell [data-home-section="best_sellers"] .rg-section {
                    padding-top: 2.1rem;
                    padding-bottom: 2.1rem;
                }

                .rg-homepage-shell [data-home-section="occasion_spotlight"] .rg-section {
                    padding-top: 2.25rem;
                }
            }
        </style>
    @endonce
@endpush

@push('before_main')
    @if ($layoutPreviewRevision)
        <div class="mx-auto mt-4 w-full max-w-7xl px-4 sm:px-6">
            <div class="rounded-[1.5rem] border border-amber-300 bg-amber-50 px-4 py-3 text-sm text-amber-900 shadow-sm dark:border-amber-500/30 dark:bg-amber-500/10 dark:text-amber-100">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <p class="font-semibold">{{ __('Önizleme modu aktif') }}</p>
                        <p class="mt-1 text-xs opacity-80">
                            {{ $layoutPreviewRevision->name ?? __('Taslak') }}
                            @if($layoutPreviewRevision->published_at)
                                • {{ $layoutPreviewRevision->published_at->format('d.m.Y H:i') }}
                            @endif
                        </p>
                    </div>
                    <a href="{{ \App\Support\StorefrontLocale::route('home') }}" class="inline-flex items-center rounded-full border border-current px-3 py-1.5 text-xs font-semibold transition hover:bg-amber-900/10 dark:hover:bg-white/10">
                        {{ __('Canlı sayfaya dön') }}
                    </a>
                </div>
            </div>
        </div>
    @endif

    @if ($heroSection)
        @php
            $heroSettings = $heroSection['settings'] ?? [];
            $heroWrapperClasses = trim(($backgroundClasses[data_get($heroSettings, 'background_tone', 'surface')] ?? '').' '.($paddingClasses[data_get($heroSettings, 'padding_scale', 'regular')] ?? 'py-6'));
        @endphp

        <div
            class="layout-visibility {{ $heroWrapperClasses }}"
            data-mobile="{{ data_get($heroSettings, 'show_on_mobile', true) ? 1 : 0 }}"
            data-tablet="{{ data_get($heroSettings, 'show_on_tablet', true) ? 1 : 0 }}"
            data-desktop="{{ data_get($heroSettings, 'show_on_desktop', true) ? 1 : 0 }}"
        >
            @include('home.sections.hero', [
                'settings' => $heroSettings,
                'heroProduct' => $heroProduct,
                'heroSpotlight' => $heroSpotlight,
                'homeContent' => $homeContent,
            ])
        </div>
    @endif
@endpush

@section('content')
    <div class="rg-homepage-shell space-y-4 lg:space-y-5">
        @foreach ($bodySections as $section)
            @php
                $settings = $section['settings'] ?? [];
                $wrapperClasses = trim(($backgroundClasses[data_get($settings, 'background_tone', 'surface')] ?? '').' '.($paddingClasses[data_get($settings, 'padding_scale', 'regular')] ?? 'py-6'));
                $wrapperContainerClass = $containerClasses[data_get($settings, 'container_width', 'content')] ?? $containerClasses['content'];
                $ctaLabel = data_get($settings, 'cta_label.'.app()->getLocale());
                $ctaUrl = data_get($settings, 'cta_url');
                $showSectionCta = data_get($settings, 'cta_enabled', false)
                    && filled($ctaLabel)
                    && filled($ctaUrl)
                    && ! in_array($section['key'], ['featured_showcase', 'instagram_preview', 'new_arrivals', 'best_sellers'], true);
            @endphp

            <div
                class="layout-visibility {{ $wrapperClasses }}"
                data-home-section="{{ $section['key'] }}"
                data-mobile="{{ data_get($settings, 'show_on_mobile', true) ? 1 : 0 }}"
                data-tablet="{{ data_get($settings, 'show_on_tablet', true) ? 1 : 0 }}"
                data-desktop="{{ data_get($settings, 'show_on_desktop', true) ? 1 : 0 }}"
            >
                <div class="{{ $wrapperContainerClass }}">
                    @if ($showSectionCta)
                        <div class="mb-4 flex justify-end">
                            <a href="{{ $ctaUrl }}" class="inline-flex items-center rounded-full border border-rose-200 bg-white/80 px-4 py-2 text-xs font-semibold text-rose-700 transition hover:border-rose-400 hover:bg-rose-50 dark:border-rose-400/20 dark:bg-rose-400/10 dark:text-rose-200 dark:hover:bg-rose-400/15">
                                {{ $ctaLabel }}
                            </a>
                        </div>
                    @endif

                    @switch($section['key'])
                        @case('category_showcase')
                            @include('home.sections.category-showcase', ['settings' => $settings, 'categories' => $categories, 'homeContent' => $homeContent, 'discoveryProducts' => $homepageDiscoveryProducts])
                            @break

                        @case('featured_showcase')
                            @include('home.sections.featured-showcase', ['settings' => $settings, 'featuredShowcase' => $featuredShowcase, 'homeContent' => $homeContent, 'showcaseCompanions' => $featuredSupportingProducts])
                            @break

                        @case('occasion_spotlight')
                            @include('home.sections.occasion-spotlight', ['settings' => $settings, 'activeOccasion' => $activeOccasion, 'occasionProducts' => $occasionProducts])
                            @break

                        @case('new_arrivals')
                            @include('home.sections.product-rail', ['settings' => $settings, 'title' => __('Yeni Gelenler'), 'products' => $newProducts, 'routeUrl' => \App\Support\StorefrontLocale::route('products.index', ['sort' => 'newest']), 'routeLabel' => __('Tamamını gör')])
                            @break

                        @case('best_sellers')
                            @include('home.sections.product-rail', ['settings' => $settings, 'title' => data_get($homeContent, 'best_sellers_heading') ?: __('Çok Satanlar'), 'subtitle' => data_get($homeContent, 'best_sellers_body'), 'products' => $bestSellers, 'routeUrl' => \App\Support\StorefrontLocale::route('products.index', ['sort' => 'best_sellers']), 'routeLabel' => __('Tamamını gör')])
                            @break

                        @case('trust_badges')
                            @include('home.sections.trust-badges', ['settings' => $settings, 'trustAccentProducts' => $trustAccentProducts])
                            @break

                        @case('instagram_preview')
                            @include('home.sections.instagram-preview', ['settings' => $settings, 'instagramUrl' => $instagramUrl])
                            @break

                        @case('blog_preview')
                            @include('home.sections.blog-preview', ['settings' => $settings, 'blogCards' => $blogCards])
                            @break
                    @endswitch
                </div>
            </div>
        @endforeach
    </div>
@endsection

