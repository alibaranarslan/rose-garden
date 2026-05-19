@php
$summarySupport = [
        __('Kart mesajı her ürün için ayrı kaydedilir.'),
        __('Teslimat ücreti ve saat bilgisi ödemede netleşir.'),
    ];
@endphp

<div class="rg-cart-commerce-shell grid grid-cols-1 gap-8 lg:grid-cols-12 lg:items-start lg:gap-10">
    <div class="lg:col-span-7 xl:col-span-8">
        <div class="rg-surface overflow-hidden">
            <div class="border-b border-rg-lightLavender/80 px-5 py-4 dark:border-white/10 md:px-8 md:py-5">
                <h2 class="font-display text-lg font-semibold text-rg-deepPurple dark:text-white md:text-xl">{{ __('Sepetinizdeki ürünler') }}</h2>
                <p class="mt-1 text-sm text-rg-grayText dark:text-white/78">{{ __('Satırları kontrol edip ödemeye geçebilirsiniz.') }}</p>
            </div>

            <div class="divide-y divide-rg-lightLavender/70 dark:divide-white/10">
                @forelse ($items as $item)
                    @php
                        $primary = $item->product?->images->firstWhere('is_primary', true) ?? $item->product?->images->first();
                        $cartImg = \App\Support\StorefrontImage::publicImgSrc(\App\Support\StorefrontImage::resolveProduct(
                            $primary?->image_path,
                            $item->product?->slug,
                            $item->product?->name,
                        ));
                        $unit = (float) ($item->variant?->price ?? $item->product?->current_price ?? 0);
                    @endphp

                    <div class="hidden flex-col gap-5 p-6 md:flex md:flex-row md:items-start md:px-8 md:py-7">
                        <a href="{{ $item->product ? \App\Support\StorefrontLocale::route('products.show', ['slug' => $item->product->slug]) : '#' }}" class="block shrink-0 overflow-hidden rounded-xl border border-zinc-200/80 bg-rg-cream dark:border-white/10 dark:bg-white/10">
                            <img src="{{ $cartImg }}" alt="" class="aspect-square h-28 w-28 object-cover sm:h-32 sm:w-32" loading="lazy">
                        </a>
                        <div class="min-w-0 flex-1 space-y-4">
                            <div class="flex flex-wrap items-start justify-between gap-3">
                                <div>
                                    <h3 class="font-semibold text-rg-darkText dark:text-white">
                                        <a href="{{ $item->product ? \App\Support\StorefrontLocale::route('products.show', ['slug' => $item->product->slug]) : '#' }}" class="hover:text-rg-purple dark:hover:text-rg-lavender">
                                            {{ $item->product?->name }}
                                        </a>
                                    </h3>
                                    @if($item->variant)
                                        <p class="rg-copy-soft mt-0.5 text-sm">{{ $item->variant->name }}</p>
                                    @endif
                                    <p class="rg-copy-soft mt-1 text-sm">
                                        {{ __('Birim') }}: <span class="font-medium text-rg-darkText dark:text-white">₺ {{ number_format($unit, 2, ',', '.') }}</span>
                                    </p>
                                </div>
                                <div class="flex flex-col items-end gap-2">
                                    <div class="inline-flex items-center rounded-lg border border-rg-lightLavender bg-white dark:border-white/15 dark:bg-rg-deepPurple/50">
                                        <button wire:click="updateQuantity({{ $item->id }}, {{ max(1, $item->quantity - 1) }})" type="button" class="px-3 py-2 text-lg leading-none text-rg-darkPlum transition hover:bg-rg-lightLavender/80 dark:text-white dark:hover:bg-white/10" aria-label="{{ __('Azalt') }}">−</button>
                                        <span class="min-w-[2.25rem] px-2 py-2 text-center text-sm font-semibold tabular-nums dark:text-white">{{ $item->quantity }}</span>
                                        <button wire:click="updateQuantity({{ $item->id }}, {{ $item->quantity + 1 }})" type="button" class="px-3 py-2 text-lg leading-none text-rg-darkPlum transition hover:bg-rg-lightLavender/80 dark:text-white dark:hover:bg-white/10" aria-label="{{ __('Artır') }}">+</button>
                                    </div>
                                    <p class="text-base font-bold tabular-nums text-rg-deepPurple dark:text-rg-lavender">₺ {{ number_format($item->subtotal, 2, ',', '.') }}</p>
                                    <button wire:click="removeItem({{ $item->id }})" type="button" class="text-sm font-medium text-red-600 underline-offset-2 hover:underline dark:text-red-400">
                                        {{ __('Kaldır') }}
                                    </button>
                                </div>
                            </div>
                            <div class="rounded-xl border border-rg-lightLavender/80 bg-rg-cream/60 p-4 dark:border-white/10 dark:bg-white/10">
                                <label for="card-message-{{ $item->id }}" class="mb-2 block text-xs font-semibold uppercase tracking-wide text-rg-midPurple dark:text-rg-lavender">{{ __('Kart mesajı') }}</label>
                                <textarea
                                    id="card-message-{{ $item->id }}"
                                    wire:model.defer="cardMessages.{{ $item->id }}"
                                    rows="2"
                                    maxlength="500"
                                    class="w-full resize-none rounded-lg border border-rg-lightLavender bg-white px-3 py-2.5 text-sm outline-none transition focus:border-rg-purple focus:ring-2 focus:ring-rg-purple/30 dark:border-white/15 dark:bg-rg-deepPurple/40 dark:text-white"
                                    placeholder="{{ __('Kartınıza yazılacak notu buradan düzenleyebilirsiniz') }}"
                                ></textarea>
                                <div class="mt-2 flex items-center justify-between gap-2">
                                    <p class="rg-copy-soft text-[11px]">{{ mb_strlen($cardMessages[$item->id] ?? '') }}/500</p>
                                    <button
                                        wire:click="saveCardMessage({{ $item->id }})"
                                        type="button"
                                        class="text-xs font-semibold text-rg-purple hover:text-rg-darkPlum dark:text-rg-lavender dark:hover:text-white"
                                    >
                                        {{ __('Mesajı kaydet') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="rg-cart-mobile-line flex flex-col gap-4 p-5 md:hidden">
                        <div class="flex gap-4">
                            <a href="{{ $item->product ? \App\Support\StorefrontLocale::route('products.show', ['slug' => $item->product->slug]) : '#' }}" class="block aspect-square h-24 w-24 shrink-0 overflow-hidden rounded-xl border border-zinc-200/80 dark:border-white/10">
                                <img src="{{ $cartImg }}" alt="" class="h-full w-full object-cover" loading="lazy">
                            </a>
                            <div class="min-w-0 flex-1">
                                <h3 class="font-semibold leading-snug text-rg-darkText dark:text-white">
                                    <a href="{{ $item->product ? \App\Support\StorefrontLocale::route('products.show', ['slug' => $item->product->slug]) : '#' }}" class="hover:text-rg-purple">{{ $item->product?->name }}</a>
                                </h3>
                                @if($item->variant)
                                    <p class="rg-copy-soft mt-0.5 text-xs">{{ $item->variant->name }}</p>
                                @endif
                                <p class="mt-2 text-sm font-bold text-rg-deepPurple dark:text-rg-lavender">₺ {{ number_format($item->subtotal, 2, ',', '.') }}</p>
                            </div>
                        </div>
                        <div class="flex items-center justify-between gap-3">
                            <div class="inline-flex items-center rounded-lg border border-rg-lightLavender bg-white dark:border-white/15 dark:bg-rg-deepPurple/50">
                                <button wire:click="updateQuantity({{ $item->id }}, {{ max(1, $item->quantity - 1) }})" type="button" class="px-3 py-2 text-lg leading-none" aria-label="{{ __('Azalt') }}">−</button>
                                <span class="min-w-[2rem] px-2 py-2 text-center text-sm font-semibold tabular-nums dark:text-white">{{ $item->quantity }}</span>
                                <button wire:click="updateQuantity({{ $item->id }}, {{ $item->quantity + 1 }})" type="button" class="px-3 py-2 text-lg leading-none" aria-label="{{ __('Artır') }}">+</button>
                            </div>
                            <button wire:click="removeItem({{ $item->id }})" type="button" class="text-sm font-medium text-red-600 dark:text-red-400">{{ __('Kaldır') }}</button>
                        </div>
                        <details class="rg-cart-mobile-card-message rounded-xl border border-rg-lightLavender/80 bg-rg-cream/60 p-4 dark:border-white/10 dark:bg-white/10">
                            <summary class="cursor-pointer list-none text-xs font-semibold text-rg-midPurple dark:text-rg-lavender">{{ __('Kart mesajı') }}</summary>
                            <div class="mt-3">
                            <label for="card-message-m-{{ $item->id }}" class="mb-2 block text-xs font-semibold text-rg-midPurple dark:text-rg-lavender">{{ __('Kart mesajı') }}</label>
                            <textarea
                                id="card-message-m-{{ $item->id }}"
                                wire:model.defer="cardMessages.{{ $item->id }}"
                                rows="2"
                                maxlength="500"
                                class="w-full resize-none rounded-lg border border-rg-lightLavender bg-white px-3 py-2 text-sm outline-none focus:border-rg-purple focus:ring-2 focus:ring-rg-purple/30 dark:border-white/15 dark:bg-rg-deepPurple/40 dark:text-white"
                                placeholder="{{ __('Kart mesajı (isteğe bağlı)') }}"
                            ></textarea>
                            <div class="mt-2 flex items-center justify-between">
                                <p class="rg-copy-soft text-[11px]">{{ mb_strlen($cardMessages[$item->id] ?? '') }}/500</p>
                                <button wire:click="saveCardMessage({{ $item->id }})" type="button" class="text-xs font-semibold text-rg-purple">{{ __('Kaydet') }}</button>
                            </div>
                            </div>
                        </details>
                    </div>
                @empty
                    <div class="px-6 py-10 md:px-8 md:py-12">
                        <div class="empty-cart-card mx-auto max-w-2xl rounded-[1.8rem] border border-rg-lightLavender/80 bg-rg-cream/55 px-6 py-8 text-center dark:border-white/10 dark:bg-white/8 md:px-8">
                            <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-rg-midPurple dark:text-rg-lavender/80">{{ __('Sepet boş') }}</p>
                            <h3 class="mt-3 font-display text-3xl text-rg-deepPurple dark:text-white">{{ __('Ürün seçerek devam edin') }}</h3>
                            <p class="mt-4 text-sm leading-relaxed text-rg-grayText dark:text-white/80">{{ __('Katalogdan seçim yapın; sepet ve ödeme akışı sonra otomatik devam eder.') }}</p>
                            <div class="empty-cart-actions mt-6 flex flex-wrap justify-center gap-3">
                                <a href="{{ \App\Support\StorefrontLocale::route('products.index') }}" class="inline-flex items-center justify-center rounded-xl bg-rg-purple px-5 py-3 text-sm font-semibold text-white shadow-md transition hover:bg-rg-darkPlum hover:shadow-lg">
                                    {{ __('Ürünlere dön') }}
                                </a>
                                <a href="{{ \App\Support\StorefrontLocale::route('special-occasions.index') }}" class="rg-button-secondary">
                                    {{ __('Özel gün seçkileri') }}
                                </a>
                            </div>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <aside class="rg-cart-summary-column lg:col-span-5 xl:col-span-4">
        <div class="sticky top-28 rounded-2xl border border-black/6 bg-[#f5efe8] p-6 shadow-sm dark:border-white/10 dark:bg-[#241b2b]/88 dark:shadow-black/25 md:p-7">
            <h3 class="font-display text-lg font-semibold text-rg-deepPurple dark:text-white">{{ __('Sipariş özeti') }}</h3>
            <div class="mt-5 space-y-3 text-sm">
                <div class="flex justify-between gap-4 text-rg-grayText dark:text-white/76">
                    <span>{{ __('Ara toplam') }}</span>
                    <span class="font-semibold tabular-nums text-rg-darkText dark:text-white">₺ {{ number_format($this->subtotal, 2, ',', '.') }}</span>
                </div>
                <div class="flex justify-between gap-4 text-rg-grayText dark:text-white/76">
                    <span>{{ __('Teslimat') }}</span>
                    <span class="text-right text-xs font-medium text-rg-darkText dark:text-white/85">{{ __('Ödeme adımında hesaplanır') }}</span>
                </div>
                <div class="flex justify-between gap-4 text-rg-grayText dark:text-white/76">
                    <span>{{ __('İndirim') }}</span>
                    <span class="font-semibold tabular-nums text-rg-darkText dark:text-white">₺ {{ number_format($this->discount, 2, ',', '.') }}</span>
                </div>
                <div class="border-t border-gray-200 pt-3 dark:border-white/10"></div>
                <div class="flex justify-between gap-4 text-base font-bold text-rg-deepPurple dark:text-rg-lavender">
                    <span>{{ __('Toplam') }}</span>
                    <span class="tabular-nums">₺ {{ number_format($this->total, 2, ',', '.') }}</span>
                </div>
            </div>

            <div class="mt-6 space-y-3">
                <label class="sr-only" for="cart-coupon-code">{{ __('Kupon kodu') }}</label>
                <input
                    id="cart-coupon-code"
                    wire:model="couponCode"
                    type="text"
                    placeholder="{{ __('Kupon kodu') }}"
                    class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-rg-purple focus:ring-2 focus:ring-rg-purple/25 dark:border-white/15 dark:bg-rg-deepPurple/40 dark:text-white"
                >
                <button
                    wire:click="applyCoupon"
                    type="button"
                    class="w-full rounded-xl border-2 border-rg-purple bg-white py-3 text-sm font-semibold text-rg-purple shadow-sm transition hover:bg-rg-lightLavender/50 dark:bg-rg-deepPurple/60 dark:text-rg-lavender dark:hover:bg-rg-deepPurple"
                >
                    {{ __('Kupon uygula') }}
                </button>
            </div>
            @if ($couponMessage)
                <p class="rg-copy-soft mt-3 text-xs">{{ $couponMessage }}</p>
            @endif

            <a
                href="{{ \App\Support\StorefrontLocale::route('checkout', prefixDefault: true) }}"
                class="{{ $items->isEmpty() ? 'pointer-events-none opacity-50' : '' }} mt-6 flex w-full items-center justify-center rounded-xl bg-rg-deepPurple py-3.5 text-sm font-semibold text-white shadow-lg transition hover:bg-rg-purple hover:shadow-xl focus:outline-none focus-visible:ring-2 focus-visible:ring-rg-purple focus-visible:ring-offset-2 dark:focus-visible:ring-offset-rg-deepPurple"
            >
                {{ __('Ödemeye geç') }}
            </a>
            <a href="{{ \App\Support\StorefrontLocale::route('products.index') }}" class="mt-3 block text-center text-sm font-medium text-rg-purple underline-offset-2 hover:underline dark:text-rg-lavender">
                {{ __('Alışverişe devam et') }}
            </a>

            <div class="mt-6 grid gap-3">
                @foreach ($summarySupport as $note)
                    <div class="rounded-[1.15rem] border border-black/6 bg-white/72 px-4 py-3 text-sm leading-relaxed text-rg-grayText dark:border-white/10 dark:bg-white/8 dark:text-white/82">
                        {{ $note }}
                    </div>
                @endforeach
            </div>
        </div>

        @if ($items->isNotEmpty())
            <div class="rg-cart-mobile-checkoutbar md:hidden" aria-label="{{ __('Mobil sepet ödeme kısayolu') }}">
                <div class="min-w-0">
                    <p class="truncate text-[11px] font-semibold uppercase tracking-[0.16em] text-rg-midPurple dark:text-rg-lavender">{{ __('Sepet toplamı') }}</p>
                    <p class="text-sm font-bold tabular-nums text-rg-deepPurple dark:text-white">₺ {{ number_format($this->total, 2, ',', '.') }}</p>
                </div>
                <a
                    href="{{ \App\Support\StorefrontLocale::route('checkout', prefixDefault: true) }}"
                    class="inline-flex shrink-0 items-center justify-center rounded-full bg-rg-purple px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-rg-darkPlum"
                >
                    {{ __('Ödemeye geç') }}
                </a>
            </div>
        @endif
    </aside>
</div>
