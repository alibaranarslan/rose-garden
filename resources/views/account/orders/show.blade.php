@extends('layouts.account')

@section('account')
    <header class="mb-8">
        <nav class="mb-3 text-xs font-medium text-rg-grayText dark:text-white/72">
            <a href="{{ \App\Support\StorefrontLocale::route('account.orders') }}" class="hover:text-rg-purple dark:hover:text-rg-lavender">{{ __('Siparişlerim') }}</a>
            <span class="mx-1.5">/</span>
            <span class="text-rg-darkText dark:text-white">{{ $order->order_number }}</span>
        </nav>
        <div class="flex flex-col gap-4 sm:flex-row sm:flex-wrap sm:items-center sm:justify-between">
            <div>
                <h1 class="font-display text-2xl font-semibold tracking-tight text-rg-deepPurple dark:text-white md:text-3xl">{{ __('Sipariş detayı') }}</h1>
                <p class="mt-1 font-mono text-sm text-rg-grayText dark:text-white/78">{{ $order->order_number }}</p>
            </div>
            <span class="inline-flex w-fit rounded-full px-3 py-1 text-xs font-bold {{ \App\Support\OrderStatus::badgeClass((string) $order->status) }}">
                {{ \App\Support\OrderStatus::label((string) $order->status) }}
            </span>
        </div>
    </header>

    <div class="space-y-6">
        <section class="rounded-2xl border border-rg-lightLavender bg-white p-5 shadow-sm dark:border-white/10 dark:bg-rg-deepPurple/40 md:p-6">
            <h2 class="text-sm font-bold uppercase tracking-wide text-rg-midPurple dark:text-rg-lavender">{{ __('Özet') }}</h2>
            <dl class="mt-4 grid gap-3 text-sm sm:grid-cols-2">
                <div>
                    <dt class="text-rg-grayText dark:text-white/72">{{ __('Tarih') }}</dt>
                    <dd class="font-medium text-rg-darkText dark:text-white">{{ $order->created_at->format('d.m.Y H:i') }}</dd>
                </div>
                <div>
                    <dt class="text-rg-grayText dark:text-white/72">{{ __('Toplam') }}</dt>
                    <dd class="text-lg font-bold text-rg-deepPurple dark:text-rg-lavender">₺ {{ number_format($order->total, 2, ',', '.') }}</dd>
                </div>
                @if($order->deliveryTimeSlot)
                    <div>
                        <dt class="text-rg-grayText dark:text-white/72">{{ __('Teslimat saati') }}</dt>
                        <dd class="font-medium text-rg-darkText dark:text-white">{{ $order->deliveryTimeSlot->label }}</dd>
                    </div>
                @endif
                @if($order->deliveryZone)
                    <div>
                        <dt class="text-rg-grayText dark:text-white/72">{{ __('Bölge') }}</dt>
                        <dd class="font-medium text-rg-darkText dark:text-white">{{ $order->deliveryZone->name }}</dd>
                    </div>
                @endif
            </dl>
        </section>

        <section class="rounded-2xl border border-rg-lightLavender bg-white p-5 shadow-sm dark:border-white/10 dark:bg-rg-deepPurple/40 md:p-6">
            <h2 class="text-sm font-bold uppercase tracking-wide text-rg-midPurple dark:text-rg-lavender">{{ __('Ürünler') }}</h2>
            <ul class="mt-4 divide-y divide-rg-lightLavender/80 dark:divide-white/10">
                @foreach ($order->items as $item)
                    <li class="flex flex-wrap items-center justify-between gap-3 py-3 first:pt-0">
                        <span class="text-sm font-medium text-rg-darkText dark:text-white">{{ $item->product_name }} × {{ $item->quantity }}</span>
                        <span class="text-sm font-semibold tabular-nums text-rg-deepPurple dark:text-rg-lavender">₺ {{ number_format($item->total_price, 2, ',', '.') }}</span>
                    </li>
                @endforeach
            </ul>
        </section>

        <form method="POST" action="{{ \App\Support\StorefrontLocale::route('account.order.reorder', ['orderNumber' => $order->order_number]) }}">
            @csrf
            <button type="submit" class="w-full rounded-xl bg-rg-purple py-3.5 text-sm font-semibold text-white shadow-md transition hover:bg-rg-darkPlum hover:shadow-lg sm:w-auto sm:px-8">
                {{ __('Tekrar sipariş ver') }}
            </button>
        </form>
    </div>
@endsection
