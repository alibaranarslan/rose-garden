@props([
    'heading' => null,
    'subheading' => null,
    'ctaLabel' => null,
    'ctaUrl' => null,
    'secondaryCtaLabel' => null,
    'secondaryCtaUrl' => null,
    'highlights' => [],
    'featuredProduct' => null,
    'spotlightEyebrow' => null,
    'spotlightSummary' => null,
])

@php
    $contact = $siteSettings?->get('contact', collect()) ?? collect();
    $whatsAppPhone = \App\Support\ContactLinks::phoneForWhatsApp($contact);
    $heroImage = \App\Support\StorefrontImage::publicImgSrc(\App\Support\StorefrontImage::resolveProduct(
        data_get($featuredProduct, 'images.0.image_path'),
        data_get($featuredProduct, 'slug'),
        data_get($featuredProduct, 'name'),
    ));
    $heroImageOptimized = \App\Support\StorefrontImage::optimizedImgSrc($heroImage, 1280);
    $heroImageMobileOptimized = \App\Support\StorefrontImage::optimizedImgSrc($heroImage, 640);
    $heroImageSrcset = \App\Support\StorefrontImage::optimizedImgSrcset($heroImage, [640, 960, 1280]);
    $heroImageIsPlaceholder = \App\Support\StorefrontImage::isResolvedProductPlaceholder($heroImage);
    $headingText = $heading ?: __('Adıyaman’da taze çiçek ve hediyelik bitkiler.');
    $subheadingText = $subheading ?: __('Gerçek ürün fotoğrafları, net fiyatlar ve Adıyaman içi teslimat desteğiyle hızlıca sipariş verin.');
    $ctaText = $ctaLabel ?: __('Koleksiyonu Keşfet');
    $ctaHref = $ctaUrl ?: \App\Support\StorefrontLocale::route('products.index');
    $secondaryText = $secondaryCtaLabel ?: __('WhatsApp ile İletişim');
    $secondaryHref = $secondaryCtaUrl ?: 'https://api.whatsapp.com/send?phone='.$whatsAppPhone;

    $highlightItems = collect($highlights)
        ->filter(fn ($item) => filled(data_get($item, 'label')) && filled(data_get($item, 'value')))
        ->take(1)
        ->values();

    if ($highlightItems->isEmpty()) {
        $highlightItems = collect([
            ['label' => __('Adıyaman içi teslimat'), 'value' => __('Uygun saatlerde aynı gün hazırlık ve teslimat desteği.')],
        ]);
    }

    $spotlightPrice = $featuredProduct?->cardPriceDisplay();
    $heroServiceTags = collect([
        __('Gerçek ürün fotoğrafları'),
        __('Aynı gün teslimat desteği'),
    ]);
@endphp

@if ($heroImageOptimized !== $heroImage)
    @push('head')
        <link rel="preload" as="image" href="{{ $heroImageOptimized }}" @if ($heroImageSrcset !== '') imagesrcset="{{ $heroImageSrcset }}" imagesizes="(min-width: 1024px) 46vw, 100vw" @endif>
    @endpush
@endif

<section class="rg-store-hero relative isolate overflow-hidden border-b border-black/5 bg-[#fbf3ee] text-rg-deepPurple dark:border-white/8 dark:bg-[#150f1c] dark:text-white">
    <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,rgba(238,218,225,0.88),transparent_34rem),radial-gradient(circle_at_85%_18%,rgba(206,188,223,0.42),transparent_24rem),linear-gradient(180deg,rgba(255,255,255,0.92),rgba(251,243,238,0.98))] dark:hidden"></div>
    <div class="absolute inset-0 hidden bg-[radial-gradient(circle_at_top_left,rgba(125,88,150,0.3),transparent_26rem),radial-gradient(circle_at_85%_18%,rgba(93,73,128,0.22),transparent_20rem),linear-gradient(180deg,rgba(27,19,36,0.98),rgba(18,12,24,1))] dark:block"></div>
    <div class="absolute inset-0 opacity-[0.07] [background-image:linear-gradient(to_right,rgba(77,58,82,0.8)_1px,transparent_1px),linear-gradient(to_bottom,rgba(77,58,82,0.8)_1px,transparent_1px)] [background-size:42px_42px] dark:opacity-[0.1] dark:[background-image:linear-gradient(to_right,rgba(255,255,255,0.14)_1px,transparent_1px),linear-gradient(to_bottom,rgba(255,255,255,0.14)_1px,transparent_1px)]"></div>

    <div class="relative mx-auto max-w-7xl px-4 pb-6 pt-6 sm:px-6 md:pb-8 md:pt-8">
        <div class="rg-store-hero-grid grid gap-6 lg:grid-cols-[minmax(0,0.95fr)_minmax(0,0.82fr)] lg:items-center">
            <div class="rg-store-hero-copy max-w-2xl">
                <div class="rg-store-hero-intro-card" style="--rg-store-hero-mobile-image: url('{{ e($heroImageMobileOptimized) }}');">
                    <div class="rg-store-hero-lockup inline-flex items-center gap-4 rounded-full border border-black/8 bg-white/80 px-4 py-2.5 shadow-[0_12px_32px_rgba(72,45,70,0.08)] backdrop-blur dark:border-white/14 dark:bg-white/10 dark:shadow-[0_14px_36px_rgba(0,0,0,0.35)]">
                        <x-site-logo variant="adaptive" type="lockup" class="h-8 w-auto sm:h-9" />
                        <span class="hidden h-8 w-px bg-black/10 dark:bg-white/18 sm:block"></span>
                        <span class="hidden text-[11px] font-semibold uppercase tracking-[0.26em] text-rg-deepPurple/62 dark:text-white/82 sm:block">{{ __('Rose Garden Atelier') }}</span>
                    </div>

                    <h1 class="rg-store-hero-title mt-5 max-w-3xl text-balance font-display text-4xl font-semibold leading-[1.04] tracking-[-0.02em] text-rg-deepPurple dark:text-white sm:text-5xl lg:text-[3rem] lg:leading-[1.02]">
                        {{ $headingText }}
                    </h1>
                    <p class="rg-store-hero-subheading mt-3.5 max-w-xl text-pretty text-base leading-relaxed text-rg-grayText dark:text-white/84 sm:text-lg">
                        {{ $subheadingText }}
                    </p>

                    <div class="rg-store-hero-delivery mt-4 flex flex-wrap gap-2">
                        <span class="inline-flex items-center gap-2 rounded-full border border-black/8 bg-white/78 px-3.5 py-2 text-xs font-semibold text-rg-deepPurple shadow-sm dark:border-white/12 dark:bg-white/10 dark:text-white/88">
                            <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                            {{ __('Adıyaman içi aynı gün teslimat') }}
                        </span>
                        <span class="inline-flex items-center rounded-full border border-black/8 bg-white/62 px-3.5 py-2 text-xs font-semibold text-rg-grayText dark:border-white/12 dark:bg-white/8 dark:text-white/76">
                            {{ __('Gerçek ürün fotoğrafları') }}
                        </span>
                    </div>

                    <div class="rg-store-hero-actions mt-4 flex flex-col gap-3 sm:flex-row">
                        <a href="{{ $ctaHref }}"
                            class="inline-flex items-center justify-center rounded-full bg-rg-deepPurple px-6 py-3.5 text-sm font-semibold text-white shadow-[0_16px_35px_rgba(55,34,59,0.18)] transition-all duration-200 hover:-translate-y-0.5 hover:bg-rg-purple dark:bg-white dark:text-rg-deepPurple dark:hover:bg-white/92">
                            {{ $ctaText }}
                        </a>
                        <a href="{{ $secondaryHref }}" target="_blank" rel="noopener"
                            class="inline-flex items-center justify-center rounded-full border border-black/10 bg-white/80 px-6 py-3.5 text-sm font-semibold text-rg-deepPurple transition-colors duration-200 hover:bg-white dark:border-white/20 dark:bg-white/10 dark:text-white dark:hover:bg-white/14">
                            {{ $secondaryText }}
                        </a>
                    </div>
                </div>

                <div class="rg-store-hero-highlights mt-4 grid gap-3 sm:grid-cols-1">
                    @foreach ($highlightItems as $item)
                        <div class="rounded-[1.5rem] border border-black/7 bg-white/72 px-4 py-4 shadow-[0_14px_30px_rgba(72,45,70,0.05)] backdrop-blur dark:border-white/12 dark:bg-white/10 dark:shadow-[0_14px_32px_rgba(0,0,0,0.2)]">
                            <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-rg-midPurple dark:text-[#d4bdf0]">{{ data_get($item, 'label') }}</p>
                            <p class="mt-2 text-sm leading-relaxed text-rg-grayText dark:text-white/84">{{ data_get($item, 'value') }}</p>
                        </div>
                    @endforeach
                </div>

                <div class="rg-store-hero-tags mt-4 flex flex-wrap gap-2 text-xs font-semibold uppercase tracking-[0.18em] text-rg-midPurple dark:text-rg-lavender/80">
                    @foreach ($heroServiceTags as $tag)
                        <span class="rounded-full border border-black/8 bg-white/70 px-3 py-2 dark:border-white/10 dark:bg-white/8">{{ $tag }}</span>
                    @endforeach
                </div>
            </div>

            <div class="rg-store-hero-visual relative lg:pl-8">
                <div class="rounded-[2rem] border border-white/55 bg-white/58 p-3 shadow-[0_28px_76px_rgba(56,35,54,0.14)] ring-1 ring-white/35 backdrop-blur-md dark:border-white/14 dark:bg-white/10 dark:ring-white/10">
                    <div class="relative aspect-[4/3] overflow-hidden rounded-[1.75rem] bg-[#efe6ea] ring-1 ring-black/[0.04] dark:bg-[#241b2c] dark:ring-white/[0.06]">
                        <img
                            src="{{ $heroImageOptimized }}"
                            @if ($heroImageSrcset !== '') srcset="{{ $heroImageSrcset }}" sizes="(min-width: 1280px) 42rem, (min-width: 1024px) 46vw, 100vw" @endif
                            alt="{{ \Illuminate\Support\Str::limit(strip_tags((string) $headingText), 160) }}"
                            class="h-full w-full object-center {{ $heroImageIsPlaceholder ? 'object-contain p-6 sm:p-8' : 'object-cover' }}"
                            loading="eager"
                            fetchpriority="high"
                            decoding="async"
                        >
                        <div class="pointer-events-none absolute inset-0 bg-[linear-gradient(180deg,rgba(255,255,255,0.02),rgba(20,10,22,0.18))]"></div>

                        <div class="absolute left-4 top-4 inline-flex items-center gap-2 rounded-full border border-black/8 bg-white/88 px-3.5 py-2 text-[11px] font-semibold uppercase tracking-[0.24em] text-rg-deepPurple shadow-md backdrop-blur-md dark:border-white/15 dark:bg-[#1a1420]/90 dark:text-white">
                            <span class="h-2 w-2 rounded-full bg-rg-rosePink"></span>
                            {{ __('Canlı Katalog') }}
                        </div>
                    </div>
                </div>

                @if ($featuredProduct)
                    <div class="rg-store-hero-spotlight mt-4 rounded-[1.35rem] border border-black/8 bg-white/88 p-4 shadow-[0_18px_42px_rgba(60,38,56,0.09)] backdrop-blur dark:border-white/12 dark:bg-[#1c1522]/92 dark:shadow-[0_24px_60px_rgba(0,0,0,0.38)] md:absolute md:-bottom-5 md:left-2 md:mt-0 md:max-w-[18rem]">
                        <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-rg-midPurple dark:text-[#d4bdf0]">{{ $spotlightEyebrow ?: __('Öne çıkan ürün') }}</p>
                        <h2 class="mt-2.5 font-display text-[1.55rem] leading-tight text-rg-deepPurple dark:text-white">{{ $featuredProduct->name }}</h2>
                        <p class="mt-2 text-sm leading-relaxed text-rg-grayText dark:text-white/82">{{ $spotlightSummary ?: __('Popüler seçeneklerden biri; detayını inceleyip siparişe geçebilirsiniz.') }}</p>

                        @if ($spotlightPrice)
                            <div class="mt-4 flex flex-wrap items-baseline gap-x-2 gap-y-1 border-t border-black/6 pt-4 dark:border-white/12">
                                <span class="text-2xl font-semibold tabular-nums text-rg-deepPurple dark:text-white">₺ {{ number_format($spotlightPrice['current'], 0, ',', '.') }}</span>
                                @if (! empty($spotlightPrice['compare']))
                                    <span class="text-sm text-rg-grayText line-through dark:text-zinc-500">₺ {{ number_format($spotlightPrice['compare'], 0, ',', '.') }}</span>
                                @endif
                                @if (! empty($spotlightPrice['show_from']))
                                    <span class="text-xs font-semibold uppercase tracking-[0.18em] text-rg-grayText dark:text-white/72">{{ __('Başlayan fiyatlarla') }}</span>
                                @endif
                            </div>
                        @endif

                        <a href="{{ \App\Support\StorefrontLocale::route('products.show', ['slug' => $featuredProduct->slug]) }}" class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-rg-deepPurple transition-colors duration-200 hover:text-rg-purple dark:text-[#e8ddf7] dark:hover:text-white">
                            {{ __('Ürünü İncele') }}
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
