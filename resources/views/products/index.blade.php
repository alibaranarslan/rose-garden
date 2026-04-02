@extends('layouts.app')

@section('content')
    <x-breadcrumb :items="[['label' => __('Anasayfa'), 'url' => route('home')], ['label' => __('Ürünler')]]" />

    <section class="grid grid-cols-1 lg:grid-cols-4 gap-6" x-data="{ filtersOpen: false }">
        <aside class="bg-white border border-rg-lightLavender rounded-card p-4 h-fit">
            <button
                type="button"
                class="w-full lg:hidden flex items-center justify-between font-semibold mb-3"
                @click="filtersOpen = !filtersOpen"
                aria-label="{{ __('Filtreleri aç/kapat') }}"
            >
                <span>{{ __('Filtreler') }}</span>
                <span x-text="filtersOpen ? '−' : '+'"></span>
            </button>

            <h2 class="hidden lg:block font-semibold mb-3">{{ __('Filtreler') }}</h2>
            <form method="GET" class="space-y-3 text-sm" x-show="filtersOpen || window.innerWidth >= 1024">
                <label for="min_price" class="block">{{ __('Min Fiyat') }}</label>
                <input id="min_price" type="number" name="min_price" value="{{ request('min_price') }}" class="w-full border rounded-btn px-3 py-2" aria-label="{{ __('Minimum fiyat') }}">
                <label for="max_price" class="block">{{ __('Max Fiyat') }}</label>
                <input id="max_price" type="number" name="max_price" value="{{ request('max_price') }}" class="w-full border rounded-btn px-3 py-2" aria-label="{{ __('Maksimum fiyat') }}">
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="stock" value="1" @checked(request()->boolean('stock'))>
                    {{ __('Stokta Olanlar') }}
                </label>
                <button type="submit" class="w-full bg-rg-purple text-white px-4 py-2 rounded-btn focus:ring-2 focus:ring-offset-2 focus:ring-rg-purple">{{ __('Uygula') }}</button>
            </form>
            <noscript>
                <style>[x-cloak]{display:block !important;}</style>
            </noscript>
        </aside>

        <div class="lg:col-span-3">
            <div class="flex items-center justify-between mb-4">
                <h1 class="font-display text-3xl">{{ $category?->name ?? __('Tüm Ürünler') }}</h1>
                <form method="GET">
                    <input type="hidden" name="min_price" value="{{ request('min_price') }}">
                    <input type="hidden" name="max_price" value="{{ request('max_price') }}">
                    @if (request()->boolean('stock'))
                        <input type="hidden" name="stock" value="1">
                    @endif
                    <select name="sort" aria-label="{{ __('Sıralama') }}" class="border rounded-btn px-3 py-2 text-sm" onchange="this.form.submit()">
                        <option value="recommended" @selected(($sort ?? 'recommended') === 'recommended')>{{ __('Önerilen') }}</option>
                        <option value="price_asc" @selected(($sort ?? '') === 'price_asc')>{{ __('Fiyat: Düşükten Yükseğe') }}</option>
                        <option value="price_desc" @selected(($sort ?? '') === 'price_desc')>{{ __('Fiyat: Yüksekten Düşüğe') }}</option>
                        <option value="newest" @selected(($sort ?? '') === 'newest')>{{ __('En Yeniler') }}</option>
                        <option value="best_sellers" @selected(($sort ?? '') === 'best_sellers')>{{ __('En Çok Satan') }}</option>
                    </select>
                </form>
            </div>
            @if($products->isEmpty())
            <div class="text-center py-12 text-rg-grayText">
                <svg class="w-16 h-16 mx-auto mb-4 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <p class="text-lg font-medium">{{ __('Ürün bulunamadı') }}</p>
                <p class="text-sm mt-1">{{ __('Farklı filtreler deneyebilir veya tüm ürünlere göz atabilirsiniz.') }}</p>
                <a href="{{ route('products.index') }}" class="inline-block mt-4 bg-rg-purple text-white px-4 py-2 rounded-btn text-sm hover:bg-rg-deepPurple transition-colors">{{ __('Tüm Ürünler') }}</a>
            </div>
            @else
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach ($products as $product)
                    <x-product-card :product="$product" />
                @endforeach
            </div>
            @endif
            <div class="mt-6">
                {{ $products->links() }}
            </div>
        </div>
    </section>
@endsection
