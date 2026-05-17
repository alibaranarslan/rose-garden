@extends('layouts.account')

@section('account')
    <header class="mb-8">
        <h1 class="font-display text-2xl font-semibold tracking-tight text-rg-deepPurple dark:text-white md:text-3xl">{{ __('Paraçiçek puanlarım') }}</h1>
        <p class="mt-2 text-sm text-rg-grayText dark:text-white/78">{{ __('Bakiyeniz ve son hareketleriniz.') }}</p>
    </header>

    <div class="mb-8 rounded-2xl bg-gradient-to-br from-rg-purple via-rg-midPurple to-rg-darkPlum p-8 text-white shadow-lg">
        <p class="text-sm font-medium text-white/80">{{ __('Toplam bakiye') }}</p>
        <p class="mt-2 font-display text-4xl font-bold tabular-nums">{{ number_format($loyaltyPoint?->balance ?? 0, 0, ',', '.') }} <span class="text-xl font-semibold text-white/90">{{ __('puan') }}</span></p>
    </div>

    <section class="rounded-2xl border border-rg-lightLavender bg-white shadow-sm dark:border-white/10 dark:bg-rg-deepPurple/40">
        <h2 class="border-b border-rg-lightLavender px-5 py-4 text-sm font-bold uppercase tracking-wide text-rg-midPurple dark:border-white/10 dark:text-rg-lavender md:px-6">{{ __('Hareketler') }}</h2>
        <ul class="divide-y divide-rg-lightLavender/70 dark:divide-white/10">
            @forelse ($transactions as $transaction)
                <li class="flex flex-wrap items-center justify-between gap-3 px-5 py-4 md:px-6">
                    <div>
                        <p class="text-sm font-medium text-rg-darkText dark:text-white">{{ $transaction->type }}</p>
                        <p class="text-xs text-rg-grayText dark:text-white/70">{{ $transaction->created_at?->format('d.m.Y H:i') }}</p>
                    </div>
                    <span class="text-sm font-bold tabular-nums text-rg-deepPurple dark:text-rg-lavender">{{ number_format($transaction->amount, 2, ',', '.') }}</span>
                </li>
            @empty
                <li class="px-5 py-10 text-center text-sm text-rg-grayText dark:text-white/78 md:px-6">{{ __('Hareket bulunamadı.') }}</li>
            @endforelse
        </ul>
    </section>
@endsection
