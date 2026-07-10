{{-- Checkout entry shell only: order creation belongs to App\Livewire\CheckoutWizard. --}}
{{-- Payment continuation and result surfaces stay in CheckoutController + checkout/payment|success|fail views. --}}
@php
    $metaTitle = __('Ödeme');
    $metaDescription = __('Teslimat bilgilerinizi, notunuzu ve ödeme tercihinizi tamamlayarak siparişinizi oluşturun.');
@endphp
@extends('layouts.checkout')

@section('content')
    <div class="mb-6 flex flex-col gap-2 md:mb-8">
        <h1 class="font-display text-3xl font-semibold tracking-tight text-rg-deepPurple dark:text-white md:text-4xl">{{ __('Ödeme') }}</h1>
        <p class="max-w-2xl text-sm text-rg-grayText dark:text-white/72">{{ __('Teslimat adresi, alıcı bilgisi ve ödeme tercihi burada tamamlanır.') }}</p>
    </div>
    @if (session('error'))
        <p class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800 dark:border-red-500/30 dark:bg-red-950/40 dark:text-red-200">{{ session('error') }}</p>
    @endif
    <livewire:checkout-wizard />
@endsection
