<div class="space-y-4">
    @if($variants->isNotEmpty())
        <div>
            <label class="block text-sm font-semibold text-rg-darkPlum mb-2">{{ __('Seçenek') }}</label>
            <div class="flex flex-wrap gap-2">
                @foreach($variants as $variant)
                    <button type="button"
                        wire:click="selectVariant({{ $variant->id }})"
                        class="px-4 py-2 text-sm rounded-btn border transition-colors duration-150
                            {{ $variantId === $variant->id
                                ? 'bg-rg-purple text-white border-rg-purple'
                                : 'bg-white text-rg-darkPlum border-rg-lightLavender hover:border-rg-purple' }}">
                        {{ $variant->name }}
                        <span class="ml-1 font-semibold">
                            {{ number_format($variant->sale_price ?? $variant->price, 2) }} ₺
                        </span>
                    </button>
                @endforeach
            </div>
        </div>
    @endif

    <div>
        <label class="block text-sm font-semibold text-rg-darkPlum mb-2">{{ __('Adet') }}</label>
        <div class="inline-flex items-center border border-rg-lightLavender rounded-btn overflow-hidden">
            <button type="button" wire:click="decrementQty" class="px-3 py-2 hover:bg-rg-lightLavender transition-colors">−</button>
            <span class="px-4 py-2 text-sm font-medium min-w-[40px] text-center">{{ $quantity }}</span>
            <button type="button" wire:click="incrementQty" class="px-3 py-2 hover:bg-rg-lightLavender transition-colors">+</button>
        </div>
    </div>

    <div>
        <label for="cardMessage" class="block text-sm font-semibold text-rg-darkPlum mb-2">{{ __('Kart Mesajı') }} <span class="text-xs text-rg-grayText font-normal">({{ __('İsteğe bağlı') }})</span></label>
        <textarea
            id="cardMessage"
            wire:model="cardMessage"
            maxlength="500"
            rows="3"
            placeholder="{{ __('Kart mesajınızı yazın...') }}"
            class="w-full border border-rg-lightLavender rounded-btn px-3 py-2 text-sm focus:ring-2 focus:ring-rg-purple focus:border-rg-purple outline-none resize-none"></textarea>
        <p class="text-xs text-rg-grayText mt-1">{{ mb_strlen($cardMessage) }}/500</p>
    </div>

    @if(($product?->stock_status ?? 'out_of_stock') === 'in_stock')
        <button
            wire:click="addToCart"
            type="button"
            aria-label="{{ __('Sepete ekle') }}"
            class="w-full bg-rg-purple hover:bg-rg-darkPlum text-white text-sm font-semibold px-4 py-3 rounded-btn transition-colors duration-200 focus:ring-2 focus:ring-offset-2 focus:ring-rg-purple"
        >
            {{ __('Sepete Ekle') }}
        </button>
    @else
        <button
            type="button"
            disabled
            aria-label="{{ __('Stokta yok') }}"
            class="w-full bg-gray-300 text-gray-500 text-sm font-semibold px-4 py-3 rounded-btn cursor-not-allowed"
        >
            {{ __('Stokta Yok') }}
        </button>
    @endif
</div>
