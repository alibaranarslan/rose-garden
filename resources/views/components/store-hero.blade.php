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
    $headingText = $heading ?: __('Adıyaman’da taze çiçek ve butik hediyeler');
    $subheadingText = $subheading ?: __('Sevdiklerinize taze çiçek, butik çikolata ve özenli hediye seçenekleriyle hızlıca ulaşın.');
    $ctaText = $ctaLabel ?: __('Koleksiyonu Keşfet');
    $ctaHref = $ctaUrl ?: \App\Support\StorefrontLocale::route('products.index');
    $secondaryText = $secondaryCtaLabel ?: __('WhatsApp ile Sipariş');
    $secondaryHref = $secondaryCtaUrl ?: 'https://api.whatsapp.com/send?phone='.$whatsAppPhone;
    $spotlightPrice = $featuredProduct?->cardPriceDisplay();
@endphp

@if ($heroImageOptimized !== $heroImage)
    @push('head')
        <link rel="preload" as="image" href="{{ $heroImageOptimized }}" @if ($heroImageSrcset !== '') imagesrcset="{{ $heroImageSrcset }}" imagesizes="(min-width: 1024px) 48vw, 100vw" @endif>
    @endpush
@endif

<section class="rg-store-hero relative isolate overflow-hidden border-b border-black/5 bg-[#fffaf7] text-rg-deepPurple dark:border-white/8 dark:bg-[#150f1c] dark:text-white">
    <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_18%_0%,rgba(241,218,225,0.72),transparent_28rem),radial-gradient(circle_at_82%_18%,rgba(201,122,155,0.18),transparent_24rem),linear-gradient(180deg,rgba(255,255,255,0.96),rgba(255,250,247,0.98))] dark:hidden"></div>
    <div class="pointer-events-none absolute inset-0 hidden bg-[radial-gradient(circle_at_18%_0%,rgba(125,88,150,0.26),transparent_25rem),radial-gradient(circle_at_82%_18%,rgba(201,122,155,0.18),transparent_24rem),linear-gradient(180deg,rgba(27,19,36,0.98),rgba(18,12,24,1))] dark:block"></div>

    <div class="relative mx-auto max-w-7xl px-4 py-4 sm:px-6 md:py-5">
        <div class="rg-sales-hero-card rg-store-hero-intro-card" style="--rg-sales-hero-image: url('{{ e($heroImageMobileOptimized) }}'); --rg-store-hero-mobile-image: url('{{ e($heroImageMobileOptimized) }}');">
            <div class="rg-sales-hero-copy">
                <p class="rg-sales-hero-kicker">{{ __('Taze çiçek · butik hediyeler') }}</p>
                <h1 class="rg-sales-hero-title">{{ $headingText }}</h1>
                <p class="rg-sales-hero-subtitle">{{ $subheadingText }}</p>

                <div class="rg-sales-hero-actions">
                    <a href="{{ $ctaHref }}" class="rg-sales-hero-primary">
                        <span>{{ $ctaText }}</span>
                        <span aria-hidden="true">→</span>
                    </a>
                    <a href="{{ $secondaryHref }}" target="_blank" rel="noopener" class="rg-sales-hero-secondary">
                        <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                        <span>{{ $secondaryText }}</span>
                    </a>
                </div>

                <div class="rg-sales-hero-chips">
                    <span>{{ __('Adıyaman içi aynı gün teslimat') }}</span>
                    <span>{{ __('Gerçek ürün fotoğrafları') }}</span>
                </div>
            </div>

            <div class="rg-sales-hero-visual" aria-hidden="true">
                <img
                    src="{{ $heroImageOptimized }}"
                    @if ($heroImageSrcset !== '') srcset="{{ $heroImageSrcset }}" sizes="(min-width: 1280px) 42rem, (min-width: 1024px) 48vw, 100vw" @endif
                    alt=""
                    class="{{ $heroImageIsPlaceholder ? 'object-contain p-6' : 'object-cover' }}"
                    loading="eager"
                    fetchpriority="high"
                    decoding="async"
                >

                @if ($featuredProduct)
                    <a href="{{ \App\Support\StorefrontLocale::route('products.show', ['slug' => $featuredProduct->slug]) }}" class="rg-sales-hero-product" aria-label="{{ $featuredProduct->name }}">
                        <span class="rg-sales-hero-product-label">{{ $spotlightEyebrow ?: __('Öne çıkan ürün') }}</span>
                        <span class="rg-sales-hero-product-name">{{ $featuredProduct->name }}</span>
                        @if ($spotlightPrice)
                            <span class="rg-sales-hero-product-price">₺ {{ number_format($spotlightPrice['current'], 0, ',', '.') }}</span>
                        @endif
                    </a>
                @endif
            </div>
        </div>
    </div>
</section>
