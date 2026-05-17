{{-- Checkout failure result view. Rendered from CheckoutController for payment continuation failures or declines. --}}
@php
    $metaTitle = __('Ödeme başarısız');
@endphp
@extends('layouts.checkout')

@section('content')
    <div class="mx-auto max-w-2xl py-4 md:py-8">
        <div class="flex items-start gap-4">
            <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-rose-100 text-rose-700 ring-1 ring-rose-200 dark:bg-rose-950/40 dark:text-rose-200 dark:ring-rose-500/30 md:h-16 md:w-16">
                <svg class="h-7 w-7 md:h-8 md:w-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </div>
            <div>
                <p class="rg-kicker">{{ __('Ödeme başarısız') }}</p>
                <h1 class="mt-1 font-display text-3xl font-semibold tracking-tight text-rg-deepPurple dark:text-white md:text-4xl">{{ __('Ödeme tamamlanamadı') }}</h1>
                <p class="mt-2 text-sm text-rg-grayText dark:text-white/80">{{ __('İşlem onaylanmadı. Farklı bir yöntemle tekrar deneyebilirsiniz.') }}</p>
            </div>
        </div>

        <div class="mt-6 rounded-2xl border border-rg-lightLavender/80 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-rg-deepPurple/50 md:p-6">
            @if ($order)
                <p class="text-xs font-semibold uppercase tracking-[0.22em] text-rg-midPurple dark:text-rg-lavender/80">{{ __('Sipariş / referans') }}</p>
                <p class="mt-1 font-mono text-lg font-semibold text-rg-darkText dark:text-white">{{ $order->order_number }}</p>
            @endif

            <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:justify-start">
                <a href="{{ \App\Support\StorefrontLocale::route('cart') }}" class="inline-flex items-center justify-center rounded-xl bg-rg-deepPurple px-6 py-3 text-sm font-semibold text-white shadow-md transition hover:bg-rg-purple hover:shadow-lg">
                    {{ __('Sepete dön') }}
                </a>
                <a href="{{ \App\Support\StorefrontLocale::route('home') }}" class="inline-flex items-center justify-center rounded-xl border-2 border-rg-lightLavender px-6 py-3 text-sm font-semibold text-rg-darkPlum transition hover:bg-rg-cream dark:border-white/15 dark:text-white dark:hover:bg-white/10">
                    {{ __('Anasayfa') }}
                </a>
            </div>
        </div>
    </div>
@endsection
