{{-- Checkout success result view. Rendered after checkout entry/order creation has completed. --}}
@php
    $metaTitle = __('Siparişiniz alındı');
@endphp
@extends('layouts.checkout')

@section('content')
    <div class="mx-auto max-w-2xl py-4 md:py-8">
        <div class="flex items-start gap-4">
            <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-700 ring-1 ring-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-200 dark:ring-emerald-500/30 md:h-16 md:w-16">
                <svg class="h-7 w-7 md:h-8 md:w-8" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <div>
                <p class="rg-kicker">{{ __('Sipariş alındı') }}</p>
                <h1 class="mt-1 font-display text-3xl font-semibold tracking-tight text-emerald-900 dark:text-emerald-100 md:text-4xl">{{ __('Teşekkürler') }}</h1>
                <p class="mt-2 text-sm text-rg-grayText dark:text-white/80">{{ __('Siparişiniz kaydedildi. Aşağıdaki referansla takip edebilirsiniz.') }}</p>
            </div>
        </div>

        <div class="mt-6 rounded-2xl border border-rg-lightLavender/80 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-rg-deepPurple/50 md:p-6">
            @if ($order)
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.22em] text-rg-midPurple dark:text-rg-lavender/80">{{ __('Sipariş numarası') }}</p>
                        <p class="mt-1 font-mono text-lg font-bold text-rg-deepPurple dark:text-white">{{ $order->order_number }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.22em] text-rg-midPurple dark:text-rg-lavender/80">{{ __('Toplam') }}</p>
                        <p class="mt-1 text-lg font-semibold text-rg-deepPurple dark:text-white">₺{{ number_format($order->total, 2, ',', '.') }}</p>
                    </div>
                </div>

                @if ($order->payment_method === 'bank_transfer')
                    <div class="mt-5 rounded-xl border border-amber-200 bg-amber-50 p-4 text-left dark:border-amber-500/30 dark:bg-amber-950/35">
                        <h2 class="font-display text-base font-semibold text-amber-900 dark:text-amber-100">{{ __('Havale / EFT') }}</h2>
                        @if ($bankInfo['configured'])
                            <p class="mt-2 text-sm text-amber-800 dark:text-amber-200">{{ __('Banka') }}: {{ $bankInfo['bank_name'] }}</p>
                            <p class="text-sm text-amber-800 dark:text-amber-200">{{ __('IBAN') }}: {{ $bankInfo['bank_iban'] }}</p>
                            <p class="text-sm text-amber-800 dark:text-amber-200">{{ __('Hesap sahibi') }}: {{ $bankInfo['bank_account_holder'] }}</p>
                            <p class="mt-3 text-xs text-amber-900 dark:text-amber-100">{{ __('Açıklamaya sipariş numaranızı ekleyin.') }}</p>
                        @else
                            <p class="mt-2 text-sm text-amber-800 dark:text-amber-200">{{ __('Banka bilgileri henüz hazır değil. Siparişiniz oluşturuldu; ödeme detayları ekip tarafından manuel paylaşılacak.') }}</p>
                        @endif
                    </div>
                @endif
            @else
                <p class="text-sm text-rg-grayText dark:text-white/86">{{ __('Siparişiniz kaydedildi.') }}</p>
            @endif

            @guest
                <div class="mt-5 rounded-xl border border-rg-lightLavender/80 bg-rg-cream/80 p-4 text-sm text-rg-darkText dark:border-white/10 dark:bg-white/10 dark:text-white/90">
                    <p>{{ __('Sipariş takibi için hesap oluşturabilirsiniz.') }}</p>
                    <a href="{{ \App\Support\StorefrontLocale::route('register') }}" class="mt-2 inline-flex text-sm font-semibold text-rg-purple hover:text-rg-darkPlum dark:text-rg-lavender">
                        {{ __('Kayıt ol') }}
                    </a>
                </div>
            @endguest

            <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:justify-start">
                @if ($order)
                    <a href="{{ \App\Support\StorefrontLocale::route('order.track') }}" class="inline-flex items-center justify-center rounded-xl bg-rg-deepPurple px-6 py-3 text-sm font-semibold text-white shadow-md transition hover:bg-rg-purple hover:shadow-lg">
                        {{ __('Sipariş takibi') }}
                    </a>
                @endif
                <a href="{{ \App\Support\StorefrontLocale::route('home') }}" class="inline-flex items-center justify-center rounded-xl border-2 border-rg-lightLavender px-6 py-3 text-sm font-semibold text-rg-darkPlum transition hover:bg-rg-cream dark:border-white/15 dark:text-white dark:hover:bg-white/10">
                    {{ __('Alışverişe devam') }}
                </a>
            </div>
        </div>
    </div>
@endsection
