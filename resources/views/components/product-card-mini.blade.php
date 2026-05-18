@props(['product'])

@php
    $miniImage = \App\Support\StorefrontImage::publicImgSrc(\App\Support\StorefrontImage::resolveProduct(
        data_get($product, 'image') ?? data_get($product, 'images.0.image_path'),
        data_get($product, 'slug'),
        data_get($product, 'name'),
    ));
    $miniImageSrc = \App\Support\StorefrontImage::optimizedImgSrc($miniImage, 320);
    $miniImageSrcset = \App\Support\StorefrontImage::optimizedImgSrcset($miniImage, [160, 240, 320]);
@endphp

<article class="flex gap-3 rounded-card border border-rg-lightLavender bg-white p-2 dark:border-white/10 dark:bg-[#252030]">
    <img
        src="{{ $miniImageSrc }}"
        alt="{{ $product['name'] }}"
        loading="lazy"
        decoding="async"
        @if ($miniImageSrcset !== '') srcset="{{ $miniImageSrcset }}" sizes="80px" @endif
        class="h-20 w-20 rounded object-cover"
    >
    <div>
        <a href="{{ \App\Support\StorefrontLocale::route('products.show', ['slug' => $product['slug']]) }}" class="font-medium text-rg-darkText hover:text-rg-purple dark:text-zinc-50 dark:hover:text-rg-lavender">{{ $product['name'] }}</a>
        <x-price-tag :product="$product" />
    </div>
</article>
