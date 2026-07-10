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
    <x-product-grid variant="catalog" class="rg-home-product-grid">
        @foreach ($items as $product)
            <x-product-card :product="$product" :interactive="$interactive" :eager-image="$loop->index < 4" :image-alternate-products="$items" />
        @endforeach
    </x-product-grid>
@endif
