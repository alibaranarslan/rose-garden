@extends('layouts.app')

@section('content')
    <section class="rounded-card overflow-hidden mb-10 shadow-lg">
        <div class="relative h-[420px] md:h-[500px] text-white flex items-end">
            {{-- Background image --}}
            <img src="{{ asset('images/hero/hero-main.jpg') }}"
                 alt="{{ __('Rose Garden') }}"
                 class="absolute inset-0 w-full h-full object-cover object-center"
                 loading="eager">
            {{-- Gradient overlay --}}
            <div class="absolute inset-0 bg-gradient-to-t from-rg-deepPurple/90 via-rg-deepPurple/40 to-transparent"></div>
            {{-- Content --}}
            <div class="relative z-10 p-8 md:p-12 max-w-xl">
                <p class="font-script text-4xl md:text-5xl mb-2 text-rg-rosePink drop-shadow-md">{{ __('Sevdiklerinize Özel') }}</p>
                <h1 class="font-display text-3xl md:text-5xl font-bold mb-3 leading-tight drop-shadow-md">
                    {{ __('Taze Çiçek &') }}<br>
                    <span class="text-rg-lavender">{{ __('El Yapımı Çikolata') }}</span>
                </h1>
                <p class="text-white/80 text-sm md:text-base mb-6 max-w-sm">{{ __('Aynı gün teslimat ile sevdiklerinizi mutlu edin.') }}</p>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('products.index') }}"
                       class="bg-rg-purple hover:bg-rg-darkPlum text-white font-semibold px-6 py-3 rounded-btn transition-colors duration-200 shadow-lg">
                        {{ __('Alışverişe Başla') }}
                    </a>
                    <a href="{{ route('products.category', ['slug' => 'hediye-setleri']) }}"
                       class="bg-white/20 hover:bg-white/30 backdrop-blur-sm text-white font-medium px-6 py-3 rounded-btn transition-colors duration-200 border border-white/30">
                        {{ __('Hediye Setleri') }}
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section class="mb-10">
        <h2 class="font-display text-2xl mb-4">{{ __('Kategoriler') }}</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach ($categories as $category)
                <x-category-card :category="$category" />
            @endforeach
        </div>
    </section>

    <section class="mb-10">
        <h2 class="font-display text-2xl mb-4">{{ __('Editörün Seçimi') }}</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach ($featuredProducts as $product)
                <x-product-card :product="$product" />
            @endforeach
        </div>
    </section>

    <section class="mb-10">
        <h2 class="font-display text-2xl mb-4">{{ __('Yeni Ürünler') }}</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach ($newProducts as $product)
                <x-product-card :product="$product" />
            @endforeach
        </div>
    </section>

    @if ($activeOccasion)
        <section class="mb-10">
            <h2 class="font-display text-2xl mb-4">{{ $activeOccasion->name }}</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach ($occasionProducts as $product)
                    <x-product-card :product="$product" />
                @endforeach
            </div>
        </section>
    @endif

    <x-floral-separator />
    <x-trust-badges />

    <section class="mt-10">
        <h2 class="font-display text-2xl mb-4">{{ __('Blog') }}</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @foreach ($blogPosts as $post)
                <article class="bg-white border border-rg-lightLavender rounded-card overflow-hidden">
                    <img src="{{ $post->featured_image ?? asset('images/placeholder.svg') }}" alt="{{ $post->title }}" loading="lazy" class="w-full h-40 object-cover">
                    <div class="p-4">
                        <h3 class="font-semibold mb-2">{{ $post->title }}</h3>
                        <p class="text-sm text-rg-grayText">{{ $post->excerpt }}</p>
                        <a href="{{ route('blog.show', ['slug' => $post->slug]) }}" class="text-sm text-rg-purple mt-2 block">{{ __('Devamını Oku') }}</a>
                    </div>
                </article>
            @endforeach
        </div>
    </section>
@endsection
