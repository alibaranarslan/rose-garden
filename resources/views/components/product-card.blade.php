@props(['product'])

@php
    $id = data_get($product, 'id');
    $slug = data_get($product, 'slug');
    $name = data_get($product, 'name', '');
    $price = (float) data_get($product, 'price', 0);
    $salePrice = data_get($product, 'sale_price');
    $isNew = (bool) data_get($product, 'is_new', false);
    $image = data_get($product, 'image')
        ?? data_get($product, 'images.0.image_path')
        ?? asset('images/placeholder.svg');
@endphp

<article class="bg-white border border-rg-lightLavender rounded-card overflow-hidden hover:border-rg-midPurple hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 group relative">
    <div class="absolute top-2 right-2 z-10">
        <livewire:favorite-toggle :product-id="$id" :key="'fav-'.$id" />
    </div>
    <a href="{{ route('products.show', ['slug' => $slug]) }}" class="block relative aspect-square overflow-hidden">
        <img src="{{ $image }}" alt="{{ $name }}" loading="lazy" class="w-full h-full object-cover group-hover:scale-105 transition-transform">
        @if(data_get($product, 'stock_status') !== 'in_stock')
            <span class="absolute top-2 left-2 bg-gray-500 text-white text-xs font-semibold px-2 py-1 rounded-full z-10">
                {{ __('Tükendi') }}
            </span>
        @endif
        @if(!empty($salePrice))
            @php
                $discount = $price > 0 ? max(1, round((($price - $salePrice) / $price) * 100)) : 0;
            @endphp
            <span class="absolute top-2 left-2 bg-rg-rosePink text-rg-deepPurple text-xs font-semibold px-2 py-1 rounded-full">%{{ $discount }}</span>
        @endif
        @if($isNew)
            <span class="absolute top-10 right-2 bg-rg-leafGreen text-white text-xs px-2 py-1 rounded-full">{{ __('Yeni') }}</span>
        @endif
    </a>
    <div class="p-4">
        <h3 class="font-semibold text-rg-darkText line-clamp-2">{{ $name }}</h3>
        <x-price-tag :product="$product" />
        <livewire:add-to-cart :product-id="$id" :key="'cart-'.$id" />
    </div>
</article>
