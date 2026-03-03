@props(['product'])

@php
    $price = (float) data_get($product, 'price', 0);
    $salePrice = data_get($product, 'sale_price');
@endphp

<div class="flex items-center gap-2 mt-2">
    @if (!empty($salePrice))
        <span class="text-rg-darkPlum font-bold text-lg">₺ {{ number_format((float) $salePrice, 0, ',', '.') }}</span>
        <span class="text-rg-grayText line-through text-sm">₺ {{ number_format($price, 0, ',', '.') }}</span>
    @else
        <span class="text-rg-darkPlum font-bold text-lg">₺ {{ number_format($price, 0, ',', '.') }}</span>
    @endif
</div>
