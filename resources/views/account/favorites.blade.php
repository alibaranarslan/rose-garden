@extends('layouts.account')

@section('account')
    <header class="mb-8">
        <h1 class="font-display text-2xl font-semibold tracking-tight text-rg-deepPurple dark:text-white md:text-3xl">{{ __('Favorilerim') }}</h1>
        <p class="mt-2 text-sm text-rg-grayText dark:text-white/78">{{ __('Beğendiğiniz ürünler, vitrin ile aynı kart düzeninde burada durur.') }}</p>
    </header>

    @if ($favorites->isEmpty())
        <div class="rounded-2xl border border-dashed border-rg-lightLavender bg-white/60 px-6 py-14 text-center dark:border-white/15 dark:bg-rg-deepPurple/20">
            <p class="text-sm text-rg-grayText dark:text-white/82">{{ __('Henüz favori ürününüz yok.') }}</p>
            <a href="{{ \App\Support\StorefrontLocale::route('products.index') }}" class="mt-4 inline-flex rounded-xl bg-rg-purple px-5 py-2.5 text-sm font-semibold text-white hover:bg-rg-darkPlum">{{ __('Ürünleri keşfet') }}</a>
        </div>
    @else
        <x-product-grid>
            @foreach ($favorites as $favorite)
                <x-product-card :product="$favorite->product" />
            @endforeach
        </x-product-grid>
        {{ $favorites->links('vendor.pagination.rg-account') }}
    @endif
@endsection
