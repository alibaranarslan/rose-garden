@props(['product'])

@php
    if ($product instanceof \App\Models\Product) {
        $d = $product->cardPriceDisplay();
        $current = $d['current'];
        $compare = $d['compare'];
        $showFrom = $d['show_from'];
    } else {
        $current = (float) data_get($product, 'price', 0);
        $salePrice = data_get($product, 'sale_price');
        $compare = ! empty($salePrice) ? $current : null;
        $current = ! empty($salePrice) ? (float) $salePrice : $current;
        $showFrom = false;
    }
@endphp

<div class="mt-2 flex flex-wrap items-baseline gap-2">
    <span class="text-lg font-bold tabular-nums text-rg-darkText dark:text-zinc-50">₺ {{ number_format($current, 0, ',', '.') }}</span>
    @if ($compare !== null && $compare > $current)
        <span class="text-sm text-rg-grayText line-through dark:text-zinc-500">₺ {{ number_format($compare, 0, ',', '.') }}</span>
    @endif
    @if ($showFrom)
        <span class="text-xs font-semibold text-rg-grayText dark:text-zinc-400">{{ __('Başlayan fiyatlarla') }}</span>
    @endif
</div>
