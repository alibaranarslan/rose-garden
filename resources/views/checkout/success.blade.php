@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto py-12 text-center">
    <div class="bg-white rounded-card border border-rg-lightLavender p-8">
        <div class="text-green-500 text-6xl mb-4">&#10003;</div>
        <h1 class="text-3xl font-display text-rg-darkPlum mb-4">{{ __('Siparişiniz Alındı!') }}</h1>

        @if ($order)
            <p class="text-lg text-rg-grayText mb-2">
                {{ __('Sipariş Numaranız') }}: <strong class="text-rg-darkText">{{ $order->order_number }}</strong>
            </p>
            <p class="text-rg-grayText mb-6">
                {{ __('Toplam') }}: <strong class="text-rg-darkText">₺{{ number_format($order->total, 2, ',', '.') }}</strong>
            </p>

            @if ($order->payment_method === 'bank_transfer')
                <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 mb-6 text-left">
                    <h3 class="font-semibold text-amber-800 mb-2">{{ __('Havale/EFT Bilgileri') }}</h3>
                    <p class="text-sm text-amber-700 mb-1">{{ __('Banka') }}: {{ $bankInfo['bank_name'] }}</p>
                    <p class="text-sm text-amber-700 mb-1">{{ __('IBAN') }}: {{ $bankInfo['bank_iban'] }}</p>
                    <p class="text-sm text-amber-700 mb-1">{{ __('Hesap Sahibi') }}: {{ $bankInfo['bank_holder'] }}</p>
                    <p class="text-sm text-amber-700 mt-2 font-medium">
                        {{ __('Açıklama kısmına sipariş numaranızı yazmayı unutmayın.') }}
                    </p>
                    <p class="text-sm text-amber-700 mt-1">
                        {{ __('72 saat içinde ödeme yapılmadığında siparişiniz otomatik iptal edilecektir.') }}
                    </p>
                </div>
            @endif
        @else
            <p class="text-rg-grayText mb-6">{{ __('Siparişiniz başarıyla oluşturuldu.') }}</p>
        @endif

        @guest
            <div class="mt-6 rounded-lg border border-rg-lightLavender bg-rg-cream p-4 text-left">
                <p class="text-sm text-rg-darkText">{{ __('Siparişinizi takip etmek için hesap oluşturun.') }}</p>
                <a href="{{ route('register') }}" class="mt-2 inline-block text-sm text-rg-purple underline">
                    {{ __('Hemen kayıt olun') }}
                </a>
            </div>
        @endguest

        <div class="flex justify-center gap-4">
            @if ($order)
                <a href="{{ route('order.track') }}" class="bg-rg-purple hover:bg-rg-darkPlum text-white px-6 py-2 rounded-btn transition">
                    {{ __('Sipariş Takibi') }}
                </a>
            @endif
            <a href="{{ route('home') }}" class="border border-rg-purple text-rg-purple hover:bg-rg-lavender px-6 py-2 rounded-btn transition">
                {{ __('Alışverişe Devam Et') }}
            </a>
        </div>
    </div>
</div>
@endsection
