@extends('layouts.app')

@push('schema')
    <x-schema-product :product="$product" />
    <x-schema-breadcrumb :items="[
        ['name' => __('Anasayfa'), 'url' => route('home')],
        ['name' => __('Ürünler'), 'url' => route('products.index')],
        ['name' => $product->name, 'url' => url()->current()],
    ]" />
@endpush

@section('content')
    <x-breadcrumb :items="[
        ['label' => __('Anasayfa'), 'url' => route('home')],
        ['label' => __('Ürünler'), 'url' => route('products.index')],
        ['label' => $product->name],
    ]" />

    <section class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div
            x-data="{
                images: @js($product->images->pluck('image_path')->values()),
                activeImage: @js($product->images->first()?->image_path ?? asset('images/product-placeholder.svg')),
                lightbox: false,
                lightboxImage: '',
                openLightbox(src) { this.lightboxImage = src; this.lightbox = true; },
            }"
        >
            <div
                class="relative aspect-square rounded-card overflow-hidden cursor-zoom-in mb-3"
                @click="openLightbox(activeImage)"
            >
                <img
                    :src="activeImage"
                    alt="{{ $product->name }}"
                    class="w-full h-full object-cover hover:scale-110 transition-transform duration-300"
                    loading="lazy"
                >
            </div>

            <div class="flex gap-2 overflow-x-auto pb-2">
                @forelse($product->images as $image)
                    <button
                        type="button"
                        @click="activeImage = '{{ $image->image_path }}'"
                        class="flex-shrink-0 w-16 h-16 rounded-lg overflow-hidden border-2 transition-colors"
                        :class="activeImage === '{{ $image->image_path }}' ? 'border-rg-purple' : 'border-transparent hover:border-rg-lavender'"
                        aria-label="{{ __('Görsel seç') }}"
                    >
                        <img src="{{ $image->image_path }}" alt="{{ $product->name }}" class="w-full h-full object-cover" loading="lazy">
                    </button>
                @empty
                    <button
                        type="button"
                        @click="activeImage = '{{ asset('images/product-placeholder.svg') }}'"
                        class="flex-shrink-0 w-16 h-16 rounded-lg overflow-hidden border-2 border-rg-lavender"
                        aria-label="{{ __('Varsayılan görsel') }}"
                    >
                        <img src="{{ asset('images/product-placeholder.svg') }}" alt="{{ $product->name }}" class="w-full h-full object-cover" loading="lazy">
                    </button>
                @endforelse
            </div>

            <div
                x-show="lightbox"
                x-transition.opacity
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 p-4"
                @click.self="lightbox = false"
                @keydown.escape.window="lightbox = false"
                x-cloak
            >
                <button
                    type="button"
                    @click="lightbox = false"
                    aria-label="{{ __('Işık kutusunu kapat') }}"
                    class="absolute top-4 right-4 text-white text-3xl hover:text-gray-300 z-50"
                >&times;</button>
                <img :src="lightboxImage" alt="{{ $product->name }}" class="max-w-full max-h-[90vh] object-contain rounded-lg">
            </div>
        </div>
        <div class="space-y-4">
            <h1 class="font-display text-4xl">{{ $product->name }}</h1>
            <x-price-tag :product="$product" />
            <div class="text-rg-grayText prose prose-sm">{!! $product->description !!}</div>
            <livewire:add-to-cart :product-id="$product->id" />
            <a href="https://api.whatsapp.com/send?phone={{ data_get($siteSettings, 'contact.whatsapp_phone', '905420000000') }}&text={{ urlencode(__('Merhaba, bu ürünü sipariş vermek istiyorum: ') . $product->name . ' - ' . url()->current()) }}"
               target="_blank" rel="noopener"
               class="flex items-center justify-center gap-2 w-full mt-1 py-3 bg-green-500 hover:bg-green-600 text-white font-semibold rounded-btn transition-colors duration-200">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                </svg>
                {{ __('WhatsApp ile Sipariş') }}
            </a>
            <x-share-buttons />
        </div>
    </section>

    <section class="mt-10">
        <h2 class="font-display text-2xl mb-4">{{ __('İlgili Ürünler') }}</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach ($related as $item)
                <x-product-card :product="$item" />
            @endforeach
        </div>
    </section>
@endsection
