@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto py-12 text-center">
    <div class="bg-white rounded-card border border-rg-lightLavender p-8">
        <div class="text-red-500 text-6xl mb-4">&#10007;</div>
        <h1 class="text-3xl font-display text-rg-darkPlum mb-4">{{ __('Ödeme Başarısız') }}</h1>
        <p class="text-rg-grayText mb-6">
            {{ __('Ödeme işlemi tamamlanamadı. Lütfen tekrar deneyin veya farklı bir ödeme yöntemi seçin.') }}
        </p>

        @if ($order)
            <p class="text-sm text-rg-grayText mb-6">
                {{ __('Sipariş Numarası') }}: <strong>{{ $order->order_number }}</strong>
            </p>
        @endif

        <div class="flex justify-center gap-4">
            <a href="{{ route('cart') }}" class="bg-rg-purple hover:bg-rg-darkPlum text-white px-6 py-2 rounded-btn transition">
                {{ __('Sepete Dön') }}
            </a>
            <a href="{{ route('home') }}" class="border border-rg-purple text-rg-purple hover:bg-rg-lavender px-6 py-2 rounded-btn transition">
                {{ __('Anasayfa') }}
            </a>
        </div>
    </div>
</div>
@endsection
