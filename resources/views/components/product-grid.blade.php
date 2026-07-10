@props([
    'variant' => 'default',
])

@php
    $gridClasses = $variant === 'catalog'
        ? 'rg-product-grid rg-catalog-grid grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-2.5 sm:gap-4 md:gap-5 lg:gap-6 items-stretch'
        : 'rg-product-grid grid grid-cols-1 min-[480px]:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 md:gap-5 lg:gap-6 items-stretch';
@endphp

<div {{ $attributes->merge(['class' => $gridClasses]) }}>
    {{ $slot }}
</div>
