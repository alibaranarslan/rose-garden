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
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach ($featuredProducts as $product)
                <x-product-card :product="$product" />
            @endforeach
        </div>
    </section>

    @if ($activeOccasion)
        <section class="mb-10">
            <h2 class="font-display text-2xl mb-4">{{ $activeOccasion->name }}</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach ($occasionProducts as $product)
                    <x-product-card :product="$product" />
                @endforeach
            </div>
        </section>
    @else
        <section class="mb-10">
            <h2 class="font-display text-2xl mb-4">{{ __('Özel Günler') }}</h2>
            <div class="bg-white border border-rg-lightLavender rounded-card p-6 text-sm text-rg-grayText">
                {{ __('Aktif özel gün kampanyası şu an bulunmuyor.') }}
            </div>
        </section>
    @endif

    <section class="mb-10">
        <h2 class="font-display text-2xl mb-4">{{ __('Yeni Ürünler') }}</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach ($newProducts as $product)
                <x-product-card :product="$product" />
            @endforeach
        </div>
    </section>

    @if($bestSellers->isNotEmpty())
        <section class="mb-10">
            <h2 class="font-display text-2xl mb-4">{{ __('Çok Satanlar') }}</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach ($bestSellers as $product)
                    <x-product-card :product="$product" />
                @endforeach
            </div>
        </section>
    @endif

    <x-floral-separator />
    <x-trust-badges />

    @php
        $instagramUrl = $siteSettings?->get('social', collect())?->get('instagram', '');
    @endphp
    @if($instagramUrl)
        <section class="mt-10 mb-10">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-display text-2xl">{{ __('Instagram\'da Biz') }}</h2>
                <a href="{{ $instagramUrl }}" target="_blank" rel="noopener"
                   class="text-sm text-rg-purple font-medium hover:underline flex items-center gap-1">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                    {{ __('Takip Et') }}
                </a>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                @for($i = 1; $i <= 4; $i++)
                    <a href="{{ $instagramUrl }}" target="_blank" rel="noopener"
                       class="relative aspect-square rounded-card overflow-hidden group bg-rg-lightLavender">
                        <img src="{{ asset('images/instagram/ig-' . $i . '.jpg') }}"
                             alt="Instagram {{ $i }}"
                             loading="lazy"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                             onerror="this.src='{{ asset('images/placeholder.svg') }}'">
                        <div class="absolute inset-0 bg-rg-deepPurple/0 group-hover:bg-rg-deepPurple/40 transition-colors duration-300 flex items-center justify-center">
                            <svg class="w-8 h-8 text-white opacity-0 group-hover:opacity-100 transition-opacity duration-300" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                        </div>
                    </a>
                @endfor
            </div>
        </section>
    @else
        <section class="mt-10 mb-10">
            <h2 class="font-display text-2xl mb-4">{{ __('Instagram\'da Biz') }}</h2>
            <div class="bg-white border border-rg-lightLavender rounded-card p-6 text-sm text-rg-grayText">
                {{ __('Instagram bağlantısı henüz eklenmedi.') }}
            </div>
        </section>
    @endif

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
