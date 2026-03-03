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
        <div>
            <img src="{{ $product->images->first()?->image_path ?? asset('images/product-placeholder.svg') }}" alt="{{ $product->name }}" loading="lazy" class="w-full h-[480px] object-cover rounded-card mb-4">
            <div class="grid grid-cols-2 gap-3">
                @foreach ($product->images as $image)
                    <img src="{{ $image->image_path }}" alt="{{ $product->name }}" loading="lazy" class="w-full h-36 object-cover rounded-card">
                @endforeach
            </div>
        </div>
        <div class="space-y-4">
            <h1 class="font-display text-4xl">{{ $product->name }}</h1>
            <x-price-tag :product="$product" />
            <div class="text-rg-grayText prose prose-sm">{!! $product->description !!}</div>
            <x-quantity-selector />
            <livewire:add-to-cart :product-id="$product->id" />
            <button class="w-full border border-rg-purple text-rg-purple px-4 py-2 rounded-btn">{{ __('WhatsApp Sipariş') }}</button>
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
