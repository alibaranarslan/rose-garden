@extends('layouts.app')

@section('content')
    <section class="bg-white border border-rg-lightLavender rounded-card p-10 text-center">
        <h1 class="font-display text-5xl mb-3">404</h1>
        <p class="text-lg mb-6">Aradiginiz sayfa bulunamadi.</p>
        <a href="{{ route('products.index') }}" class="bg-rg-purple text-white px-4 py-2 rounded-btn">Urunleri Kesfet</a>
    </section>
@endsection
