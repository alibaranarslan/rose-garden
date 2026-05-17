@extends('layouts.app')

@php
    $noindex = true;
    $metaTitle = __('Sunucu Hatası');
@endphp

@section('content')
    <section class="rounded-card border border-rg-lightLavender bg-white p-10 text-center">
        <h1 class="mb-3 font-display text-5xl">500</h1>
        <p class="mb-6 text-lg">{{ __('Beklenmeyen bir hata oluştu.') }}</p>
        <a href="{{ \App\Support\StorefrontLocale::route('home') }}" class="rounded-btn bg-rg-purple px-4 py-2 text-white">{{ __('Anasayfaya Dön') }}</a>
    </section>
@endsection
