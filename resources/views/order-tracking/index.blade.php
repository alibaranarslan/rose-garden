@php
    $metaTitle = __('Sipariş Takibi');
@endphp
@extends('layouts.app')

@section('content')
    <div class="space-y-8 md:space-y-10">
        <x-page-hero
            compact
            :eyebrow="__('Sipariş Takibi')"
            :title="__('Sipariş numaranızla teslimat durumunu hızlıca görüntüleyin')"
            :description="__('Takip sayfası; siparişin hangi aşamada olduğunu sakin ve anlaşılır bir akışla göstermek için düzenlendi. Numaranızı girmeniz yeterli.')"
        >
            <x-slot:stats>
                <div class="rg-page-stat sm:col-span-2">
                    <form id="order-tracking-form" method="POST" action="{{ \App\Support\StorefrontLocale::route('order.track.submit') }}" class="grid gap-3 md:grid-cols-[minmax(0,1fr)_auto] md:items-end">
                        @csrf
                        <div>
                            <label for="order_number" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-rg-midPurple dark:text-rg-lavender">{{ __('Sipariş numarası') }}</label>
                            <input
                                id="order_number"
                                type="text"
                                name="order_number"
                                value="{{ old('order_number', request('order_number')) }}"
                                placeholder="{{ __('Örn. RG-2026-0001') }}"
                                class="w-full rounded-xl border {{ $errors->has('order_number') ? 'border-red-300 focus:border-red-500 focus:ring-red-200 dark:border-red-400/70 dark:focus:border-red-300 dark:focus:ring-red-400/25' : 'border-rg-lightLavender focus:border-rg-purple focus:ring-rg-purple/35 dark:border-white/15' }} bg-white px-4 py-3 text-sm text-rg-darkText shadow-sm outline-none transition placeholder:text-rg-grayText focus:ring-2 dark:bg-white/14 dark:text-white dark:placeholder:text-white/62"
                                autocomplete="off"
                                @error('order_number') aria-invalid="true" aria-describedby="order-number-error" @enderror
                            >
                            @error('order_number')
                                <p id="order-number-error" class="mt-2 text-sm font-medium text-red-600 dark:text-red-300">{{ $message }}</p>
                            @enderror
                        </div>
                        <button type="submit" class="rounded-xl bg-rg-purple px-6 py-3 text-sm font-semibold text-white shadow-md transition hover:bg-rg-darkPlum hover:shadow-lg focus:outline-none focus-visible:ring-2 focus-visible:ring-rg-purple focus-visible:ring-offset-2 dark:focus-visible:ring-offset-rg-deepPurple">
                            {{ __('Sorgula') }}
                        </button>
                    </form>

                    @if (isset($order) && $order)
                        <div class="mt-4 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900 dark:border-emerald-500/30 dark:bg-emerald-950/40 dark:text-emerald-100">
                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-emerald-700 dark:text-emerald-200">{{ __('Sipariş bulundu') }}</p>
                                    <p class="mt-1 font-mono text-sm font-semibold">{{ $order->order_number }}</p>
                                </div>
                                <span class="rounded-full px-3 py-1 text-xs font-semibold {{ \App\Support\OrderStatus::badgeClass((string) $order->status) }}">
                                    {{ \App\Support\OrderStatus::label((string) $order->status) }}
                                </span>
                            </div>
                            <p class="mt-2 text-xs leading-relaxed text-emerald-800/85 dark:text-emerald-100/80">{{ __('Detaylı durum geçmişini aşağıdaki takip kartında görebilirsiniz.') }}</p>
                        </div>
                    @endif

                    @if ((! isset($order) || ! $order) && request()->isMethod('POST'))
                        <p class="mt-3 text-sm font-medium text-red-600 dark:text-red-300">{{ __('Bu sipariş numarasıyla eşleşen bir kayıt bulunamadı.') }}</p>
                    @endif
                </div>
                <div class="rg-page-stat">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-rg-midPurple dark:text-rg-lavender/80">{{ __('Gerekli bilgi') }}</p>
                    <p class="mt-2 text-sm font-semibold text-rg-deepPurple dark:text-white">{{ __('Sipariş numarası') }}</p>
                </div>
                <div class="rg-page-stat">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-rg-midPurple dark:text-rg-lavender/80">{{ __('Takip amacı') }}</p>
                    <p class="mt-2 text-sm leading-relaxed text-rg-grayText dark:text-white/84">{{ __('Hazırlık, yola çıkış ve teslim durumunu tek ekranda göstermek.') }}</p>
                </div>
            </x-slot:stats>

            <x-slot:aside>
                @php
                    $trackingHeroVisual = \App\Support\StorefrontImage::publicImgSrc(
                        \App\Support\StorefrontImage::productVisualStrip(1)[0] ?? \App\Support\StorefrontImage::productPlaceholderImgSrc()
                    );
                @endphp
                <div class="space-y-3">
                    <div class="rg-photo-card rg-photo-card--tall">
                        <img src="{{ $trackingHeroVisual }}" alt="{{ __('Sipariş takibi') }}">
                        <div class="rg-photo-card__content">
                            <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-white/72">{{ __('Takip yüzeyi') }}</p>
                            <h2 class="mt-2 font-display text-[1.8rem] leading-tight text-white">{{ __('Durumu tek bakışta görün') }}</h2>
                        </div>
                    </div>
                    <div class="rg-mini-note">
                        <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-rg-midPurple dark:text-rg-lavender/80">{{ __('Not') }}</p>
                        <p class="mt-2 text-sm leading-7 text-rg-copy-muted dark:text-white/84">{{ __('Sipariş numaranızı bilmiyorsanız hesap alanındaki siparişlerim sayfasından veya e-posta bildiriminden kontrol edebilirsiniz.') }}</p>
                    </div>
                </div>
            </x-slot:aside>
        </x-page-hero>

        <div class="mx-auto max-w-5xl">
            @if (isset($order) && $order)
                <div class="rg-surface p-5 md:p-6">
                    <div class="flex items-center justify-between gap-4 border-b border-rg-lightLavender/80 pb-4 dark:border-white/10">
                        <div>
                            <p class="rg-copy-soft text-xs font-medium uppercase tracking-wide">{{ __('Sipariş no') }}</p>
                            <p class="mt-0.5 font-mono text-lg font-semibold text-rg-darkText dark:text-white">{{ $order->order_number }}</p>
                        </div>
                        <span class="shrink-0 rounded-full px-3 py-1 text-xs font-semibold {{ \App\Support\OrderStatus::badgeClass((string) $order->status) }}">
                            {{ \App\Support\OrderStatus::label((string) $order->status) }}
                        </span>
                    </div>

                    @if($order->statusHistory && $order->statusHistory->isNotEmpty())
                        <div class="mt-6">
                            <h2 class="mb-4 text-sm font-bold uppercase tracking-wide text-rg-midPurple dark:text-rg-lavender">{{ __('Durum geçmişi') }}</h2>
                            <div class="relative space-y-4 pl-6">
                                @foreach($order->statusHistory->sortByDesc('created_at') as $index => $history)
                                    <div class="relative">
                                        <div class="absolute -left-6 top-1.5 h-2.5 w-2.5 rounded-full {{ $index === 0 ? 'bg-rg-purple ring-2 ring-rg-purple/30' : 'bg-rg-lightLavender dark:bg-white/25' }}"></div>
                                        @if(!$loop->last)
                                            <div class="absolute -left-[17px] top-4 h-[calc(100%+0.5rem)] w-0.5 bg-rg-lightLavender dark:bg-white/10"></div>
                                        @endif
                                        <div>
                                            <span class="text-sm font-semibold text-rg-darkText dark:text-white">{{ \App\Support\OrderStatus::label((string) $history->status) }}</span>
                                            <span class="rg-copy-soft ml-2 text-xs">{{ $history->created_at?->format('d.m.Y H:i') ?? __('Zaman bilgisi bekleniyor') }}</span>
                                            @if($history->note)
                                                <p class="rg-copy-muted mt-1 text-xs">{{ $history->note }}</p>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @else
                <div class="rg-mini-note">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-rg-midPurple dark:text-rg-lavender/80">{{ __('Nasıl çalışır') }}</p>
                    <p class="mt-2 text-sm leading-7 text-rg-copy-muted dark:text-white/84">{{ __('Sipariş numarası girildiğinde hazırlık, yola çıkış ve teslim durumları bu alanda görünür. Kayıt bulunamazsa formun altında bilgi mesajı gösterilir.') }}</p>
                </div>
            @endif
        </div>
    </div>

    @if ($errors->has('order_number') || request()->isMethod('POST'))
        <script>
            (function () {
                function focusOrderTrackingForm() {
                    window.setTimeout(function () {
                        var target = document.getElementById('order-tracking-form');

                        if (target) {
                            window.scrollTo({
                                top: target.getBoundingClientRect().top + window.scrollY - 140,
                                behavior: 'auto'
                            });
                        }
                    }, 80);
                }

                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', focusOrderTrackingForm, { once: true });
                } else {
                    focusOrderTrackingForm();
                }
            })();
        </script>
    @endif
@endsection
