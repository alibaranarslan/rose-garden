@props([
    'products' => collect(),
    'cardWidth' => 'w-[84vw] min-[480px]:w-[19.25rem] md:w-[21rem] lg:w-[22.5rem] xl:w-[23.5rem]',
    'interactive' => true,
])

@php
    $items = collect($products)
        ->filter(function ($product) {
            $name = trim((string) data_get($product, 'name'));
            $slug = trim((string) data_get($product, 'slug'));

            return $name !== '' && $slug !== '';
        })
        ->values();
@endphp

@if ($items->isNotEmpty())
    <div x-data="scrollRail()" class="space-y-4">
        <div x-ref="track" class="rg-scroll-rail">
            @foreach ($items as $product)
                <div class="rg-rail-card {{ $cardWidth }}">
                    <x-product-card :product="$product" :interactive="$interactive" :eager-image="$loop->index < 2" :image-alternate-products="$items" />
                </div>
            @endforeach
        </div>

        @if ($items->count() > 1)
            <div class="rg-rail-actions">
                <button type="button" class="rg-rail-button" @click="scrollPrev()" :disabled="!canPrev" aria-label="{{ __('Previous products') }}">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </button>
                <button type="button" class="rg-rail-button" @click="scrollNext()" :disabled="!canNext" aria-label="{{ __('Next products') }}">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
            </div>
        @endif
    </div>
@endif
