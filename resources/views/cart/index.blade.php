{{-- Cart entry shell only: route ownership stops here, runtime behavior continues in App\Livewire\CartPage. --}}
@php
    $metaTitle = __('Sepetim');
    $metaDescription = __('Sepetinizdeki ürünleri, adetleri, kart mesajını ve kupon indiriminizi kontrol ederek Rose Garden ödeme adımına güvenle ilerleyin.');
@endphp
@extends('layouts.app')

@section('content')
    <div class="mb-8 md:mb-10">
        <x-breadcrumb
            :items="[
                ['label' => __('Anasayfa'), 'url' => \App\Support\StorefrontLocale::route('home')],
                ['label' => __('Sepetim'), 'url' => null],
            ]"
            class="mb-4 text-xs text-rg-grayText dark:text-white/72"
        />
        <h1 class="font-display text-3xl font-semibold tracking-tight text-rg-deepPurple dark:text-white md:text-4xl">{{ __('Sepetim') }}</h1>
        <p class="mt-2 max-w-2xl text-sm text-rg-grayText dark:text-white/78">{{ __('Ürünleri kontrol edip ödemeye geçin.') }}</p>
    </div>
    <div class="rg-cart-shell">
        <livewire:cart-page />
    </div>
@endsection
