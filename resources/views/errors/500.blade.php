@extends('layouts.app')

@php $noindex = true; $metaTitle = 'Sunucu Hatası'; @endphp

@section('content')
    <section class="bg-white border border-rg-lightLavender rounded-card p-10 text-center">
        <h1 class="font-display text-5xl mb-3">500</h1>
        <p class="text-lg mb-6">Beklenmeyen bir hata olustu.</p>
        <a href="{{ route('home') }}" class="bg-rg-purple text-white px-4 py-2 rounded-btn">Anasayfaya Don</a>
    </section>
@endsection
