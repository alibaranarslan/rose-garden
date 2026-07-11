@php
    $isCard = $layout === 'card';
    $gapClass = $isCard ? 'gap-2' : 'space-y-4';
@endphp

<div @class([
    'rg-add-to-cart flex min-h-0 w-full flex-1 flex-col',
    'rg-add-to-cart--card' => $isCard,
    'rg-add-to-cart--detail' => ! $isCard,
])>
    <div class="flex min-h-0 flex-1 flex-col {{ $gapClass }}">
        @if($product)
            <div class="{{ $isCard ? 'rg-card-purchase-line' : 'flex flex-wrap items-baseline gap-2' }}">
                <div class="flex flex-wrap items-baseline gap-x-2 gap-y-0.5">
                    <span class="font-bold tabular-nums text-rg-deepPurple dark:text-white {{ $isCard ? 'text-[1.05rem] sm:text-xl' : 'text-lg' }}">
                        ₺ {{ number_format($priceAmount, 0, ',', '.') }}
                    </span>
                    @if($compareAmount !== null && $compareAmount > $priceAmount)
                        <span class="text-xs tabular-nums text-rg-grayText line-through decoration-2 decoration-rg-mauve/70 opacity-90 dark:text-zinc-400 sm:text-sm">₺ {{ number_format($compareAmount, 0, ',', '.') }}</span>
                    @endif
                </div>

                @if($isCard)
                    <span class="rg-card-delivery-chip">{{ __('Aynı gün teslimat') }}</span>
                @endif
            </div>
        @endif

        @if($variants->isNotEmpty() && ! $isCard)
            <div class="mt-1">
                <label for="variant-{{ $productId }}" class="mb-2 block text-sm font-semibold text-rg-darkText dark:text-white/90">
                    {{ __('Boyut / Seçenek') }}
                </label>
                <select
                    id="variant-{{ $productId }}"
                    wire:model.live="variantId"
                    class="w-full rounded-btn border border-rg-lightLavender bg-white px-2.5 py-1.5 text-sm outline-none focus:border-rg-purple focus:ring-2 focus:ring-rg-purple dark:border-white/20 dark:bg-white/10 dark:text-white"
                >
                    @foreach($variants as $variant)
                        @php
                            $vPrice = (float) ($variant->sale_price ?? $variant->price);
                            $vLabel = $variant->name . ' — ₺ ' . number_format($vPrice, 0, ',', '.');
                        @endphp
                        <option value="{{ $variant->id }}">{{ $vLabel }}</option>
                    @endforeach
                </select>
            </div>
        @endif

        @unless($isCard)
            <div class="flex flex-row items-center gap-2">
                <span class="shrink-0 text-xs font-medium text-rg-grayText dark:text-white/78">{{ __('Adet') }}</span>
                <div class="inline-flex h-8 max-w-[6.5rem] flex-1 items-stretch overflow-hidden rounded-btn border border-rg-lightLavender dark:border-white/20">
                    <button
                        type="button"
                        wire:click="decrementQty"
                        class="flex items-center justify-center px-2.5 text-base leading-none transition-colors hover:bg-rg-lightLavender/80 dark:hover:bg-white/10"
                        aria-label="{{ __('Azalt') }}"
                    >−</button>
                    <span class="flex min-w-[2rem] items-center justify-center px-1 text-sm font-semibold tabular-nums dark:text-white">{{ $quantity }}</span>
                    <button
                        type="button"
                        wire:click="incrementQty"
                        class="flex items-center justify-center px-2.5 text-base leading-none transition-colors hover:bg-rg-lightLavender/80 dark:hover:bg-white/10"
                        aria-label="{{ __('Artır') }}"
                    >+</button>
                </div>
            </div>
        @endunless

        @if($layout === 'detail')
            <div>
                <label for="cardMessage-{{ $productId }}" class="mb-2 block text-sm font-semibold text-rg-darkText dark:text-white/90">
                    {{ __('Kart Mesajı') }} <span class="text-xs font-normal text-rg-grayText">({{ __('İsteğe bağlı') }})</span>
                </label>
                <textarea
                    id="cardMessage-{{ $productId }}"
                    wire:model="cardMessage"
                    maxlength="500"
                    rows="3"
                    placeholder="{{ __('Kart mesajınızı yazın...') }}"
                    class="w-full resize-none rounded-btn border border-rg-lightLavender px-3 py-2 text-sm outline-none focus:border-rg-purple focus:ring-2 focus:ring-rg-purple dark:border-white/20 dark:bg-white/10 dark:text-white"
                ></textarea>
                <p class="mt-1 text-xs text-rg-grayText">{{ mb_strlen($cardMessage) }}/500</p>
            </div>
        @endif
    </div>

    <div class="mt-auto shrink-0 pt-2">
        @if(($product?->stock_status ?? 'out_of_stock') === 'in_stock')
            <button
                type="button"
                @if($isCard)
                    wire:click="openCartMessageModal"
                @else
                    wire:click="addToCart"
                @endif
                wire:loading.attr="disabled"
                aria-label="{{ __('Sepete ekle') }}"
                class="{{ $isCard ? 'rg-card-cart-button' : 'w-full rounded-btn bg-rg-purple px-3 py-2.5 text-sm font-semibold text-white transition-colors duration-200 hover:bg-rg-darkPlum focus:ring-2 focus:ring-rg-purple focus:ring-offset-2 disabled:opacity-60' }}"
            >
                <span wire:loading.remove wire:target="{{ $isCard ? 'openCartMessageModal' : 'addToCart' }}">{{ __('Sepete Ekle') }}</span>
                <span wire:loading wire:target="{{ $isCard ? 'openCartMessageModal' : 'addToCart' }}">{{ __('Bekleyin…') }}</span>
            </button>

            @unless($isCard)
                <div class="rg-pdp-mobile-purchasebar md:hidden" aria-label="{{ __('Mobil satın alma kısayolu') }}">
                    <div class="min-w-0">
                        <p class="truncate text-[11px] font-semibold uppercase tracking-[0.16em] text-rg-midPurple dark:text-rg-lavender">{{ __('Sepete hazır') }}</p>
                        <p class="text-sm font-bold tabular-nums text-rg-deepPurple dark:text-white">₺ {{ number_format($priceAmount, 0, ',', '.') }}</p>
                    </div>
                    <button
                        type="button"
                        wire:click="addToCart"
                        wire:loading.attr="disabled"
                        wire:target="addToCart"
                        class="inline-flex shrink-0 items-center justify-center rounded-full bg-rg-purple px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-rg-darkPlum focus:outline-none focus-visible:ring-2 focus-visible:ring-rg-purple focus-visible:ring-offset-2 disabled:opacity-60 dark:focus-visible:ring-offset-rg-deepPurple"
                    >
                        <span wire:loading.remove wire:target="addToCart">{{ __('Sepete Ekle') }}</span>
                        <span wire:loading wire:target="addToCart">{{ __('Bekleyin…') }}</span>
                    </button>
                </div>
            @endunless
        @else
            <button
                type="button"
                disabled
                aria-label="{{ __('Stokta yok') }}"
                class="{{ $isCard ? 'rg-card-cart-button rg-card-cart-button--disabled' : 'w-full cursor-not-allowed rounded-btn bg-gray-300 px-3 py-2.5 text-sm font-semibold text-gray-500' }}"
            >
                {{ __('Stokta Yok') }}
            </button>

            @unless($isCard)
                <div class="rg-pdp-mobile-purchasebar rg-pdp-mobile-purchasebar--disabled md:hidden" aria-label="{{ __('Mobil satın alma kısayolu') }}">
                    <div class="min-w-0">
                        <p class="truncate text-[11px] font-semibold uppercase tracking-[0.16em] text-rg-grayText dark:text-white/60">{{ __('Stok bilgisi') }}</p>
                        <p class="text-sm font-bold text-rg-deepPurple dark:text-white">{{ __('Stokta Yok') }}</p>
                    </div>
                    <button type="button" disabled class="inline-flex shrink-0 cursor-not-allowed items-center justify-center rounded-full bg-gray-300 px-5 py-2.5 text-sm font-semibold text-gray-600">
                        {{ __('Stokta Yok') }}
                    </button>
                </div>
            @endunless
        @endif

        @if($layout === 'detail' && $showAddedNotice)
            <div class="mt-3 rounded-[1.15rem] border border-emerald-200 bg-emerald-50/95 p-3 text-sm text-emerald-950 shadow-sm dark:border-emerald-400/30 dark:bg-emerald-500/12 dark:text-emerald-50">
                <p class="font-semibold">{{ __('Ürün sepete eklendi.') }}</p>
                <p class="mt-1 text-xs leading-relaxed text-emerald-800/85 dark:text-emerald-50/78">{{ __('Sepetinizi kontrol edebilir veya alışverişe devam edebilirsiniz.') }}</p>
                <div class="mt-3 flex flex-col gap-2 sm:flex-row">
                    <a href="{{ \App\Support\StorefrontLocale::route('cart', prefixDefault: true) }}" class="inline-flex items-center justify-center rounded-full bg-emerald-700 px-4 py-2 text-xs font-semibold text-white transition-colors hover:bg-emerald-800 dark:bg-emerald-400 dark:text-emerald-950 dark:hover:bg-emerald-300">
                        {{ __('Sepete git') }}
                    </a>
                    <button type="button" wire:click="$set('showAddedNotice', false)" class="inline-flex items-center justify-center rounded-full border border-emerald-300/80 bg-white/70 px-4 py-2 text-xs font-semibold text-emerald-900 transition-colors hover:bg-white dark:border-emerald-300/30 dark:bg-white/10 dark:text-emerald-50 dark:hover:bg-white/15">
                        {{ __('Alışverişe devam et') }}
                    </button>
                </div>
            </div>
        @endif
    </div>

    @if($isCard && $showCartMessageModal)
        <div
            class="fixed inset-0 z-[60] flex items-end justify-center bg-black/50 p-4 backdrop-blur-sm sm:items-center"
            wire:click.self="closeCartMessageModal"
            wire:keydown.escape.window="closeCartMessageModal"
        >
            <div
                class="w-full max-w-md rounded-card border border-rg-lightLavender bg-white p-5 shadow-xl dark:border-white/15 dark:bg-rg-deepPurple sm:p-6"
                role="dialog"
                aria-modal="true"
                aria-labelledby="cart-modal-title-{{ $productId }}"
                @click.stop
            >
                <div class="mb-4 flex items-start justify-between gap-3">
                    <h2 id="cart-modal-title-{{ $productId }}" class="font-display text-lg font-semibold text-rg-darkText dark:text-white">
                        {{ __('Kart mesajı') }}
                    </h2>
                    <button
                        type="button"
                        wire:click="closeCartMessageModal"
                        class="rounded-btn p-1 text-rg-grayText hover:text-rg-darkPlum dark:text-white/78 dark:hover:text-white"
                        aria-label="{{ __('Kapat') }}"
                    >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <p class="mb-3 text-sm text-rg-grayText dark:text-white/86">
                    {{ __('İsterseniz sepete eklenen ürün için kart mesajı yazabilirsiniz. Boş bırakabilirsiniz.') }}
                </p>
                <textarea
                    wire:model="cardMessage"
                    maxlength="500"
                    rows="4"
                    placeholder="{{ __('Kart mesajınızı yazın...') }}"
                    class="mb-2 w-full resize-none rounded-btn border border-rg-lightLavender px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-rg-purple dark:border-white/20 dark:bg-white/10 dark:text-white"
                ></textarea>
                <p class="mb-4 text-xs text-rg-grayText">{{ mb_strlen($cardMessage) }}/500</p>
                <div class="flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
                    <button
                        type="button"
                        wire:click="closeCartMessageModal"
                        class="w-full rounded-btn border border-rg-lightLavender px-4 py-2.5 text-sm font-medium text-rg-darkPlum hover:bg-rg-cream dark:border-white/20 dark:text-white dark:hover:bg-white/10 sm:w-auto"
                    >
                        {{ __('İptal') }}
                    </button>
                    <button
                        type="button"
                        wire:click="addToCart"
                        wire:loading.attr="disabled"
                        class="w-full rounded-btn bg-rg-purple px-4 py-2.5 text-sm font-semibold text-white hover:bg-rg-darkPlum focus:ring-2 focus:ring-rg-purple focus:ring-offset-2 disabled:opacity-60 sm:w-auto"
                    >
                        <span wire:loading.remove wire:target="addToCart">{{ __('Sepete ekle') }}</span>
                        <span wire:loading wire:target="addToCart">{{ __('Ekleniyor…') }}</span>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
