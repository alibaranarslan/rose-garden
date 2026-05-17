@extends('layouts.app')

@php
    $noindex = true;
    $metaTitle = __('Sayfa Bulunamadı');
@endphp

@section('content')
    <section class="rounded-card border border-rg-lightLavender bg-white p-10 text-center">
        <h1 class="mb-3 font-display text-5xl">404</h1>
        <p class="mb-6 text-lg">{{ __('Aradığınız sayfa bulunamadı.') }}</p>
        <a href="{{ \App\Support\StorefrontLocale::route('products.index') }}" class="rounded-btn bg-rg-purple px-4 py-2 text-white">{{ __('Ürünleri Keşfet') }}</a>
    </section>
@endsection
