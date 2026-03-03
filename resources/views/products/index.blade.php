@extends('layouts.app')

@section('content')
    <x-breadcrumb :items="[['label' => __('Anasayfa'), 'url' => route('home')], ['label' => __('Ürünler')]]" />

    <section class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <aside class="bg-white border border-rg-lightLavender rounded-card p-4 h-fit">
            <h2 class="font-semibold mb-3">{{ __('Filtreler') }}</h2>
            <form method="GET" class="space-y-3 text-sm">
                <label class="block">{{ __('Min Fiyat') }}</label>
                <input type="number" name="min_price" value="{{ request('min_price') }}" class="w-full border rounded-btn px-3 py-2">
                <label class="block">{{ __('Max Fiyat') }}</label>
                <input type="number" name="max_price" value="{{ request('max_price') }}" class="w-full border rounded-btn px-3 py-2">
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="stock" value="1" @checked(request()->boolean('stock'))>
                    {{ __('Stokta Olanlar') }}
                </label>
                <button type="submit" class="w-full bg-rg-purple text-white px-4 py-2 rounded-btn">{{ __('Uygula') }}</button>
            </form>
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
                    <select name="sort" class="border rounded-btn px-3 py-2 text-sm" onchange="this.form.submit()">
                        <option value="recommended" @selected(($sort ?? 'recommended') === 'recommended')>{{ __('Önerilen') }}</option>
                        <option value="price_asc" @selected(($sort ?? '') === 'price_asc')>{{ __('Fiyat: Düşükten Yükseğe') }}</option>
                        <option value="price_desc" @selected(($sort ?? '') === 'price_desc')>{{ __('Fiyat: Yüksekten Düşüğe') }}</option>
                        <option value="newest" @selected(($sort ?? '') === 'newest')>{{ __('En Yeniler') }}</option>
                    </select>
                </form>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach ($products as $product)
                    <x-product-card :product="$product" />
                @endforeach
            </div>
            <div class="mt-6">
                {{ $products->links() }}
            </div>
        </div>
    </section>
@endsection
