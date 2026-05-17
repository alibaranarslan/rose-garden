@props([
    'variant' => 'default',
])

@php
    $gridClasses = $variant === 'catalog'
        ? 'grid grid-cols-1 min-[520px]:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-4 md:gap-5 lg:gap-6 items-stretch'
        : 'grid grid-cols-1 min-[480px]:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 md:gap-5 lg:gap-6 items-stretch';
@endphp

<div {{ $attributes->merge(['class' => $gridClasses]) }}>
    {{ $slot }}
</div>
