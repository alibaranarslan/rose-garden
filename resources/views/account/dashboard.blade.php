@extends('layouts.account')

@section('account')
    <div class="space-y-8">
        <x-page-hero
            compact
            :eyebrow="__('Hesabım')"
            :title="__('Merhaba, :name', ['name' => \Illuminate\Support\Str::before($user->name, ' ') ?: $user->name])"
            :description="__('Siparişler, favoriler, puan bakiyesi ve hesap ayarları artık daha sakin ve okunabilir bir panel akışıyla sunuluyor.')"
        >
            <x-slot:stats>
                <div class="rg-page-stat">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-rg-midPurple dark:text-rg-lavender/80">{{ __('Son siparişler') }}</p>
                    <p class="mt-2 text-2xl font-semibold text-rg-deepPurple dark:text-white">{{ number_format($latestOrders->count(), 0, ',', '.') }}</p>
                </div>
                <div class="rg-page-stat">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-rg-midPurple dark:text-rg-lavender/80">{{ __('Paraçiçek bakiyesi') }}</p>
                    <p class="mt-2 text-2xl font-semibold text-rg-deepPurple dark:text-white">{{ number_format($loyaltyPoint?->balance ?? 0, 0, ',', '.') }}</p>
                </div>
            </x-slot:stats>
        </x-page-hero>

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            <a href="{{ \App\Support\StorefrontLocale::route('account.orders') }}" class="group rounded-[1.6rem] border border-rg-lightLavender bg-white p-6 shadow-[0_18px_42px_rgba(34,24,40,0.08)] transition hover:-translate-y-1 hover:border-rg-purple/35 hover:shadow-[0_24px_54px_rgba(34,24,40,0.12)] dark:border-white/10 dark:bg-rg-deepPurple/40 dark:hover:border-rg-lavender/40">
                <p class="text-xs font-bold uppercase tracking-wide text-rg-midPurple dark:text-rg-lavender">{{ __('Siparişlerim') }}</p>
                <p class="mt-3 font-display text-3xl font-semibold text-rg-deepPurple dark:text-white">{{ $latestOrders->count() }}</p>
                <p class="mt-1 text-sm text-rg-grayText dark:text-white/72">{{ __('En güncel siparişlerinize hızlı geçiş') }}</p>
                <span class="mt-4 inline-flex text-sm font-semibold text-rg-purple group-hover:underline dark:text-rg-lavender">{{ __('Görüntüle') }} →</span>
            </a>
            <a href="{{ \App\Support\StorefrontLocale::route('account.loyalty') }}" class="group rounded-[1.6rem] border border-rg-lightLavender bg-white p-6 shadow-[0_18px_42px_rgba(34,24,40,0.08)] transition hover:-translate-y-1 hover:border-rg-purple/35 hover:shadow-[0_24px_54px_rgba(34,24,40,0.12)] dark:border-white/10 dark:bg-rg-deepPurple/40 dark:hover:border-rg-lavender/40">
                <p class="text-xs font-bold uppercase tracking-wide text-rg-midPurple dark:text-rg-lavender">{{ __('Paraçiçek puanı') }}</p>
                <p class="mt-3 font-display text-3xl font-semibold text-rg-deepPurple dark:text-white">{{ number_format($loyaltyPoint?->balance ?? 0, 0, ',', '.') }}</p>
                <p class="mt-1 text-sm text-rg-grayText dark:text-white/72">{{ __('Bakiye ve hareketler') }}</p>
                <span class="mt-4 inline-flex text-sm font-semibold text-rg-purple group-hover:underline dark:text-rg-lavender">{{ __('Hareketler') }} →</span>
            </a>
            <a href="{{ \App\Support\StorefrontLocale::route('account.favorites') }}" class="group rounded-[1.6rem] border border-rg-lightLavender bg-white p-6 shadow-[0_18px_42px_rgba(34,24,40,0.08)] transition hover:-translate-y-1 hover:border-rg-purple/35 hover:shadow-[0_24px_54px_rgba(34,24,40,0.12)] dark:border-white/10 dark:bg-rg-deepPurple/40 dark:hover:border-rg-lavender/40 sm:col-span-2 xl:col-span-1">
                <p class="text-xs font-bold uppercase tracking-wide text-rg-midPurple dark:text-rg-lavender">{{ __('Favorilerim') }}</p>
                <p class="mt-3 text-sm leading-7 text-rg-grayText dark:text-white/86">{{ __('Kaydettiğiniz ürünleri yeniden gözden geçirin ve siparişe daha hızlı dönün.') }}</p>
                <span class="mt-4 inline-flex text-sm font-semibold text-rg-purple group-hover:underline dark:text-rg-lavender">{{ __('Listeye git') }} →</span>
            </a>
        </div>

        @if ($latestOrders->isNotEmpty())
            <section class="space-y-4">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <span class="rg-kicker">{{ __('Son hareketler') }}</span>
                        <h2 class="mt-3 font-display text-2xl font-semibold text-rg-deepPurple dark:text-white">{{ __('Son siparişleriniz') }}</h2>
                    </div>
                    <a href="{{ \App\Support\StorefrontLocale::route('account.orders') }}" class="text-sm font-semibold text-rg-purple hover:underline dark:text-rg-lavender">{{ __('Tümünü gör') }}</a>
                </div>
                <ul class="space-y-3">
                    @foreach ($latestOrders as $order)
                        <li>
                            <a href="{{ \App\Support\StorefrontLocale::route('account.order.show', ['orderNumber' => $order->order_number]) }}" class="flex flex-wrap items-center justify-between gap-3 rounded-[1.3rem] border border-rg-lightLavender bg-white px-4 py-4 shadow-[0_14px_32px_rgba(34,24,40,0.06)] transition hover:border-rg-purple/30 dark:border-white/10 dark:bg-rg-deepPurple/35">
                                <span class="font-mono text-sm font-semibold text-rg-darkText dark:text-white">{{ $order->order_number }}</span>
                                <span class="rounded-full px-2.5 py-0.5 text-xs font-semibold {{ \App\Support\OrderStatus::badgeClass((string) $order->status) }}">
                                    {{ \App\Support\OrderStatus::label((string) $order->status) }}
                                </span>
                                <span class="text-sm text-rg-grayText dark:text-white/72">{{ $order->created_at->format('d.m.Y') }}</span>
                                <span class="text-sm font-bold text-rg-deepPurple dark:text-rg-lavender">₺ {{ number_format($order->total, 2, ',', '.') }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </section>
        @endif
    </div>
@endsection
