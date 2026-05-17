{{-- Checkout payment continuation view. Entry is checkout.index/CheckoutWizard; this surface is controller-owned. --}}
@php
    $metaTitle = __('Ödeme');
@endphp
@extends('layouts.checkout')

@section('content')
    <div class="mx-auto max-w-4xl space-y-6">
        <div class="space-y-2">
            <p class="rg-kicker">{{ __('Ödeme') }}</p>
            <h1 class="font-display text-3xl font-semibold tracking-tight text-rg-deepPurple dark:text-white md:text-4xl">{{ __('Siparişinizi tamamlayın') }}</h1>
            <p class="max-w-2xl text-sm text-rg-grayText dark:text-white/76">{{ __('Sipariş no :order ve toplam tutar aşağıda. Ödeme alanı tek işe odaklanır.', ['order' => $order->order_number]) }}</p>
        </div>

        <div class="grid gap-3 sm:grid-cols-2">
            <div class="rounded-2xl border border-rg-lightLavender/80 bg-white p-4 text-sm shadow-sm dark:border-white/10 dark:bg-rg-deepPurple/40">
                <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-rg-midPurple dark:text-rg-lavender/80">{{ __('Sipariş no') }}</p>
                <p class="mt-2 font-mono font-semibold text-rg-deepPurple dark:text-white">{{ $order->order_number }}</p>
            </div>
            <div class="rounded-2xl border border-rg-lightLavender/80 bg-white p-4 text-sm shadow-sm dark:border-white/10 dark:bg-rg-deepPurple/40">
                <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-rg-midPurple dark:text-rg-lavender/80">{{ __('Toplam') }}</p>
                <p class="mt-2 text-xl font-semibold text-rg-deepPurple dark:text-white">₺{{ number_format($order->total, 2, ',', '.') }}</p>
            </div>
        </div>

        <p class="flex items-center gap-2 text-xs font-medium text-emerald-800 dark:text-emerald-200">
            <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
            {{ __('Kart bilgileriniz güvenli bağlantı üzerinden işlenir.') }}
        </p>

        @if (!empty($iframeUrl))
            <div class="overflow-hidden rounded-[1.4rem] border border-rg-lightLavender/80 bg-white shadow-[0_18px_46px_rgba(34,24,40,0.08)] dark:border-white/10 dark:bg-rg-deepPurple/40">
                <iframe src="{{ $iframeUrl }}" id="paytriframe" title="{{ __('Güvenli ödeme') }}" frameborder="0" class="min-h-[640px] w-full" scrolling="no"></iframe>
            </div>
        @else
            <div class="rounded-[1.4rem] border border-amber-200 bg-amber-50 p-6 text-center shadow-sm dark:border-amber-500/30 dark:bg-amber-950/30">
                <p class="text-sm text-amber-900 dark:text-amber-100">{{ __('Ödeme sistemi şu an yapılandırılıyor. Lütfen daha sonra tekrar deneyin.') }}</p>
            </div>
        @endif
    </div>

    @push('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/iframe-resizer/4.3.9/iframeResizer.min.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                if (typeof iFrameResize === 'function') {
                    iFrameResize({ log: false, checkOrigin: false }, '#paytriframe');
                }
            });
        </script>
    @endpush
@endsection
