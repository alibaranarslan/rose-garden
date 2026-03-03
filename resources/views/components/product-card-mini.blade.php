@props(['product'])

<article class="flex gap-3 bg-white border border-rg-lightLavender rounded-card p-2">
    <img src="{{ $product['image'] }}" alt="{{ $product['name'] }}" loading="lazy" class="w-20 h-20 rounded object-cover">
    <div>
        <a href="{{ route('products.show', ['slug' => $product['slug']]) }}" class="font-medium hover:text-rg-purple">{{ $product['name'] }}</a>
        <x-price-tag :product="$product" />
    </div>
</article>
