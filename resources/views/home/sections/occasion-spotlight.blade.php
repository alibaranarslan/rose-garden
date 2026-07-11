@if ($activeOccasion)
    @php
        $occasionProducts = collect($occasionProducts ?? []);
        $leadProduct = $occasionProducts->first();
        $occasionCategory = $activeOccasion->category;
        $occasionCategoryName = $occasionCategory?->getTranslation('name', app()->getLocale());
        $occasionTitle = $activeOccasion->getTranslation('name', app()->getLocale());
        $occasionDateLabel = $activeOccasion->nextOccurrence()
            ->locale(app()->getLocale())
            ->translatedFormat('d F');
        $occasionStatusLabel = $activeOccasion->isToday()
            ? __('Bugün')
            : __(':count gün kaldı', ['count' => $activeOccasion->daysUntil()]);
        $occasionProductCount = $occasionProducts->count();
        $leadProductImage = $leadProduct
            ? \App\Support\StorefrontImage::publicImgSrc(\App\Support\StorefrontImage::resolveProduct(
                data_get($leadProduct, 'image') ?? data_get($leadProduct, 'images.0.image_path'),
                data_get($leadProduct, 'slug'),
                data_get($leadProduct, 'name'),
            ))
            : null;
        $leadProductImageSrc = $leadProductImage ? \App\Support\StorefrontImage::optimizedImgSrc($leadProductImage, 420) : null;
        $leadProductPrice = $leadProduct?->cardPriceDisplay();
        $primaryHref = \App\Support\StorefrontLocale::route('special-occasions.show', ['slug' => $activeOccasion->slug]);
        $secondaryHref = $occasionCategory
            ? \App\Support\StorefrontLocale::route('products.category', ['slug' => $occasionCategory->slug])
            : \App\Support\StorefrontLocale::route('products.index');
    @endphp

    <section class="rg-section">
        <div class="rg-occasion-compact">
            <div class="rg-occasion-compact-copy">
                <span class="rg-occasion-compact-date">{{ $occasionDateLabel }} · {{ $occasionStatusLabel }}</span>
                <h2>{{ $occasionTitle }}</h2>
                <p>
                    {{ $occasionProductCount > 0
                        ? __('Bu güne uygun çiçek ve hediye seçeneklerini hızlıca inceleyin.')
                        : __('Bu özel gün için uygun kategoriye geçin veya WhatsApp üzerinden destek alın.') }}
                </p>
            </div>

            @if ($leadProduct)
                <a href="{{ \App\Support\StorefrontLocale::route('products.show', ['slug' => $leadProduct->slug]) }}" class="rg-occasion-compact-product">
                    <img src="{{ $leadProductImageSrc }}" alt="{{ $leadProduct->name }}" loading="lazy" decoding="async">
                    <span>
                        <strong>{{ $leadProduct->name }}</strong>
                        @if ($leadProductPrice)
                            <small>₺ {{ number_format($leadProductPrice['current'], 0, ',', '.') }}</small>
                        @endif
                    </span>
                </a>
            @else
                <a href="{{ $secondaryHref }}" class="rg-occasion-compact-product rg-occasion-compact-product--empty">
                    <span>
                        <strong>{{ $occasionCategoryName ?: __('Tüm ürünler') }}</strong>
                        <small>{{ __('Uygun seçenekleri gör') }}</small>
                    </span>
                </a>
            @endif

            <div class="rg-occasion-compact-actions">
                <a href="{{ $primaryHref }}" class="rg-occasion-compact-primary">{{ __('Seçkiyi Aç') }}</a>
                <a href="{{ $secondaryHref }}" class="rg-occasion-compact-secondary">{{ __('Ürünlere Git') }}</a>
            </div>
        </div>
    </section>
@endif
