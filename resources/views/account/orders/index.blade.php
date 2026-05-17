@extends('layouts.account')

@section('account')
    <header class="mb-8">
        <h1 class="font-display text-2xl font-semibold tracking-tight text-rg-deepPurple dark:text-white md:text-3xl">{{ __('Siparişlerim') }}</h1>
        <p class="mt-2 text-sm text-rg-grayText dark:text-white/78">{{ __('Geçmiş siparişleriniz ve durumları.') }}</p>
    </header>

    <div class="space-y-4">
        @forelse ($orders as $order)
            <article class="rounded-2xl border border-rg-lightLavender bg-white p-5 shadow-sm transition hover:shadow-md dark:border-white/10 dark:bg-rg-deepPurple/40 md:p-6">
                <div class="flex flex-col gap-4 sm:flex-row sm:flex-wrap sm:items-center sm:justify-between">
                    <div class="min-w-0">
                        <p class="text-xs font-semibold uppercase tracking-wide text-rg-midPurple dark:text-rg-lavender">{{ __('Sipariş no') }}</p>
                        <a href="{{ \App\Support\StorefrontLocale::route('account.order.show', ['orderNumber' => $order->order_number]) }}" class="mt-1 inline-block font-mono text-lg font-semibold text-rg-darkText hover:text-rg-purple dark:text-white dark:hover:text-rg-lavender">
                            {{ $order->order_number }}
                        </a>
                        <p class="mt-1 text-sm text-rg-grayText dark:text-white/72">{{ $order->created_at->format('d.m.Y') }}</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-3">
                        <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold {{ \App\Support\OrderStatus::badgeClass((string) $order->status) }}">
                            {{ \App\Support\OrderStatus::label((string) $order->status) }}
                        </span>
                        <span class="text-lg font-bold tabular-nums text-rg-deepPurple dark:text-rg-lavender">₺ {{ number_format($order->total, 2, ',', '.') }}</span>
                    </div>
                </div>
                <div class="mt-4 flex flex-wrap gap-2 border-t border-rg-lightLavender/80 pt-4 dark:border-white/10">
                    <a href="{{ \App\Support\StorefrontLocale::route('account.order.show', ['orderNumber' => $order->order_number]) }}" class="inline-flex items-center rounded-xl bg-rg-purple px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-rg-darkPlum dark:shadow-none">
                        {{ __('Detay') }}
                    </a>
                </div>
            </article>
        @empty
            <div class="rounded-2xl border border-dashed border-rg-lightLavender bg-white/60 px-6 py-14 text-center dark:border-white/15 dark:bg-rg-deepPurple/20">
                <p class="text-sm text-rg-grayText dark:text-white/82">{{ __('Henüz siparişiniz yok.') }}</p>
                <a href="{{ \App\Support\StorefrontLocale::route('products.index') }}" class="mt-4 inline-flex rounded-xl bg-rg-purple px-5 py-2.5 text-sm font-semibold text-white hover:bg-rg-darkPlum">{{ __('Alışverişe başla') }}</a>
            </div>
        @endforelse
    </div>

    {{ $orders->links('vendor.pagination.rg-account') }}
@endsection
