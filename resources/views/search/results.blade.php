@extends('layouts.app')

@section('content')
    <section class="bg-white border border-rg-lightLavender rounded-card p-5 md:p-7">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="font-display text-2xl md:text-3xl font-semibold text-rg-darkText">{{ __('Arama Sonuçları') }}</h1>
                @if ($query)
                    <p class="text-sm text-rg-grayText mt-1">
                        "<span class="font-medium text-rg-darkText">{{ $query }}</span>"
                        {{ __('için') }}
                        @if ($results instanceof \Illuminate\Pagination\LengthAwarePaginator)
                            <span class="font-medium text-rg-purple">{{ $results->total() }}</span> {{ __('ürün bulundu') }}
                        @endif
                    </p>
                @endif
            </div>
            {{-- Inline search --}}
            <form action="{{ route('search') }}" method="GET" class="hidden sm:flex items-center gap-2">
                <input type="text" name="q" value="{{ $query }}"
                       placeholder="{{ __('Yeniden ara...') }}"
                       class="border border-rg-lightLavender focus:border-rg-purple rounded-btn px-3 py-1.5 text-sm outline-none w-48 transition-all">
                <button type="submit" class="bg-rg-purple text-white px-3 py-1.5 rounded-btn text-sm hover:bg-rg-darkPlum transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </button>
            </form>
        </div>

        @if ($results instanceof \Illuminate\Pagination\LengthAwarePaginator && $results->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                @foreach ($results as $product)
                    <x-product-card :product="$product" />
                @endforeach
            </div>
            <div class="mt-8">{{ $results->links() }}</div>

        @elseif (mb_strlen((string) $query) < 2)
            <div class="py-16 text-center">
                <svg class="w-16 h-16 mx-auto mb-4 text-rg-lightLavender" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <p class="text-rg-grayText">{{ __('Aramak için en az 2 karakter girin.') }}</p>
            </div>

        @else
            {{-- Empty state --}}
            <div class="py-16 text-center">
                <div class="w-20 h-20 rounded-full bg-rg-lightLavender/50 flex items-center justify-center mx-auto mb-5">
                    <svg class="w-10 h-10 text-rg-midPurple" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h2 class="font-display text-xl font-semibold text-rg-darkText mb-2">{{ __('Sonuç Bulunamadı') }}</h2>
                <p class="text-sm text-rg-grayText mb-6 max-w-xs mx-auto">
                    "{{ $query }}" {{ __('için bir ürün bulunamadı. Farklı bir anahtar kelime deneyin.') }}
                </p>
                <div class="flex flex-wrap gap-2 justify-center mb-8">
                    @foreach (['gül', 'orkide', 'çikolata', 'buket', 'hediye'] as $suggestion)
                        <a href="{{ route('search', ['q' => $suggestion]) }}"
                           class="text-sm bg-rg-lightLavender/50 hover:bg-rg-lightLavender text-rg-darkPlum px-4 py-1.5 rounded-full transition-colors duration-200">
                            {{ $suggestion }}
                        </a>
                    @endforeach
                </div>
                <a href="{{ route('products.index') }}"
                   class="inline-flex items-center gap-2 bg-rg-purple text-white text-sm font-semibold px-6 py-2.5 rounded-btn hover:bg-rg-darkPlum transition-colors duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/>
                    </svg>
                    {{ __('Tüm Ürünleri Görüntüle') }}
                </a>
            </div>
        @endif
    </section>
@endsection
