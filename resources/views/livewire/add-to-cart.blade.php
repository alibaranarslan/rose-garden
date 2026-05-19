@php
    $isCard = $layout === 'card';
    $gapClass = $isCard ? 'gap-2' : 'space-y-4';
@endphp

<div @class([
    'rg-add-to-cart flex flex-col flex-1 min-h-0 w-full',
    'rg-add-to-cart--card' => $isCard,
    'rg-add-to-cart--detail' => ! $isCard,
])>
    <div class="flex min-h-0 flex-1 flex-col {{ $gapClass }}">
    {{-- Fiyat (varyantlı / varyantsız tek yerden) --}}
    @if($product)
        <div class="flex flex-wrap items-baseline gap-2 {{ $isCard ? 'mt-1' : '' }}">
            <span class="font-bold tabular-nums text-rg-deepPurple dark:text-white {{ $isCard ? 'text-xl' : 'text-lg' }}">
                ₺ {{ number_format($priceAmount, 0, ',', '.') }}
            </span>
            @if($compareAmount !== null && $compareAmount > $priceAmount)
                <span class="text-sm tabular-nums text-rg-grayText line-through decoration-2 decoration-rg-mauve/70 opacity-90 dark:text-zinc-400">₺ {{ number_format($compareAmount, 0, ',', '.') }}</span>
            @endif
        </div>
    @endif

    @if($variants->isNotEmpty())
        <div class="{{ $isCard ? '' : 'mt-1' }}" @if($isCard) data-rg-card-variant @endif>
            <label for="variant-{{ $productId }}" class="{{ $isCard ? 'sr-only' : 'block text-sm font-semibold text-rg-darkText dark:text-white/90 mb-2' }}">
                {{ __('Boyut / Seçenek') }}
            </label>
            <select
                id="variant-{{ $productId }}"
                wire:model.live="variantId"
                class="w-full text-sm border border-rg-lightLavender dark:border-white/20 dark:bg-white/10 dark:text-white rounded-btn px-2.5 py-1.5 outline-none focus:ring-2 focus:ring-rg-purple focus:border-rg-purple bg-white"
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

    {{-- Adet: kompakt yatay --}}
    <div class="flex flex-row items-center gap-2 {{ $isCard ? 'mt-0.5' : '' }}" @if($isCard) data-rg-card-quantity @endif>
        <span class="text-xs font-medium text-rg-grayText dark:text-white/78 shrink-0">{{ __('Adet') }}</span>
        <div class="inline-flex items-stretch border border-rg-lightLavender dark:border-white/20 rounded-btn overflow-hidden h-8 flex-1 max-w-[6.5rem]">
            <button
                type="button"
                wire:click="decrementQty"
                class="px-2.5 text-base leading-none hover:bg-rg-lightLavender/80 dark:hover:bg-white/10 transition-colors flex items-center justify-center"
                aria-label="{{ __('Azalt') }}"
            >−</button>
            <span class="min-w-[2rem] px-1 flex items-center justify-center text-sm font-semibold tabular-nums dark:text-white">{{ $quantity }}</span>
            <button
                type="button"
                wire:click="incrementQty"
                class="px-2.5 text-base leading-none hover:bg-rg-lightLavender/80 dark:hover:bg-white/10 transition-colors flex items-center justify-center"
                aria-label="{{ __('Artır') }}"
            >+</button>
        </div>
    </div>

    @if($layout === 'detail')
        <div>
            <label for="cardMessage-{{ $productId }}" class="block text-sm font-semibold text-rg-darkText dark:text-white/90 mb-2">
                {{ __('Kart Mesajı') }} <span class="text-xs text-rg-grayText font-normal">({{ __('İsteğe bağlı') }})</span>
            </label>
            <textarea
                id="cardMessage-{{ $productId }}"
                wire:model="cardMessage"
                maxlength="500"
                rows="3"
                placeholder="{{ __('Kart mesajınızı yazın...') }}"
                class="w-full border border-rg-lightLavender dark:border-white/20 dark:bg-white/10 dark:text-white rounded-btn px-3 py-2 text-sm focus:ring-2 focus:ring-rg-purple focus:border-rg-purple outline-none resize-none"
            ></textarea>
            <p class="text-xs text-rg-grayText mt-1">{{ mb_strlen($cardMessage) }}/500</p>
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
                class="w-full bg-rg-purple hover:bg-rg-darkPlum text-white text-sm font-semibold px-3 py-2.5 rounded-btn transition-colors duration-200 focus:ring-2 focus:ring-offset-2 focus:ring-rg-purple disabled:opacity-60"
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
                class="w-full bg-gray-300 text-gray-500 text-sm font-semibold px-3 py-2.5 rounded-btn cursor-not-allowed"
            >
                {{ __('Stokta Yok') }}
            </button>
            @unless($isCard)
                <div class="rg-pdp-mobile-purchasebar rg-pdp-mobile-purchasebar--disabled md:hidden" aria-label="{{ __('Mobil satın alma kısayolu') }}">
                    <div class="min-w-0">
                        <p class="truncate text-[11px] font-semibold uppercase tracking-[0.16em] text-rg-grayText dark:text-white/60">{{ __('Stok bilgisi') }}</p>
                        <p class="text-sm font-bold text-rg-deepPurple dark:text-white">{{ __('Stokta Yok') }}</p>
                    </div>
                    <button
                        type="button"
                        disabled
                        class="inline-flex shrink-0 cursor-not-allowed items-center justify-center rounded-full bg-gray-300 px-5 py-2.5 text-sm font-semibold text-gray-600"
                    >
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
        class="fixed inset-0 z-[60] flex items-end sm:items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
        wire:click.self="closeCartMessageModal"
        wire:keydown.escape.window="closeCartMessageModal"
    >
        <div
            class="w-full max-w-md bg-white dark:bg-rg-deepPurple rounded-card border border-rg-lightLavender dark:border-white/15 shadow-xl p-5 sm:p-6"
            role="dialog"
            aria-modal="true"
            aria-labelledby="cart-modal-title-{{ $productId }}"
            @click.stop
        >
            <div class="flex items-start justify-between gap-3 mb-4">
                <h2 id="cart-modal-title-{{ $productId }}" class="font-display text-lg font-semibold text-rg-darkText dark:text-white">
                    {{ __('Kart mesajı') }}
                </h2>
                <button
                    type="button"
                    wire:click="closeCartMessageModal"
                    class="text-rg-grayText hover:text-rg-darkPlum dark:text-white/78 dark:hover:text-white p-1 rounded-btn"
                    aria-label="{{ __('Kapat') }}"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <p class="text-sm text-rg-grayText dark:text-white/86 mb-3">
                {{ __('İsterseniz sepete eklenen ürün için kart mesajı yazabilirsiniz. Boş bırakabilirsiniz.') }}
            </p>
            <textarea
                wire:model="cardMessage"
                maxlength="500"
                rows="4"
                placeholder="{{ __('Kart mesajınızı yazın...') }}"
                class="w-full border border-rg-lightLavender dark:border-white/20 dark:bg-white/10 dark:text-white rounded-btn px-3 py-2 text-sm focus:ring-2 focus:ring-rg-purple outline-none resize-none mb-2"
            ></textarea>
            <p class="text-xs text-rg-grayText mb-4">{{ mb_strlen($cardMessage) }}/500</p>
            <div class="flex flex-col-reverse sm:flex-row gap-2 sm:justify-end">
                <button
                    type="button"
                    wire:click="closeCartMessageModal"
                    class="w-full sm:w-auto px-4 py-2.5 rounded-btn border border-rg-lightLavender dark:border-white/20 text-sm font-medium text-rg-darkPlum dark:text-white hover:bg-rg-cream dark:hover:bg-white/10"
                >
                    {{ __('İptal') }}
                </button>
                <button
                    type="button"
                    wire:click="addToCart"
                    wire:loading.attr="disabled"
                    class="w-full sm:w-auto px-4 py-2.5 rounded-btn bg-rg-purple hover:bg-rg-darkPlum text-white text-sm font-semibold focus:ring-2 focus:ring-offset-2 focus:ring-rg-purple disabled:opacity-60"
                >
                    <span wire:loading.remove wire:target="addToCart">{{ __('Sepete ekle') }}</span>
                    <span wire:loading wire:target="addToCart">{{ __('Ekleniyor…') }}</span>
                </button>
            </div>
        </div>
    </div>
@endif
</div>
