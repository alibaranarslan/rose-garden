<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 bg-white border border-rg-lightLavender rounded-card p-4">
        <h2 class="font-semibold mb-4">Urunler</h2>
        <div class="space-y-3">
            @forelse ($items as $item)
                <div class="flex items-center justify-between border-b pb-2">
                    <div>
                        <p class="font-medium">{{ $item->product?->name }}</p>
                        <p class="text-xs text-rg-grayText">Birim: ₺ {{ number_format($item->variant?->price ?? $item->product?->current_price ?? 0, 2, ',', '.') }}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <button wire:click="updateQuantity({{ $item->id }}, {{ max(1, $item->quantity - 1) }})" type="button" class="px-2 py-1 border rounded-btn">-</button>
                        <span>{{ $item->quantity }}</span>
                        <button wire:click="updateQuantity({{ $item->id }}, {{ $item->quantity + 1 }})" type="button" class="px-2 py-1 border rounded-btn">+</button>
                        <span class="w-24 text-right">₺ {{ number_format($item->subtotal, 2, ',', '.') }}</span>
                        <button wire:click="removeItem({{ $item->id }})" type="button" class="text-xs text-red-600">Sil</button>
                    </div>
                </div>
            @empty
                <p class="text-sm text-rg-grayText">Sepetiniz bos.</p>
            @endforelse
        </div>
    </div>
    <aside class="bg-white border border-rg-lightLavender rounded-card p-4">
        <h3 class="font-semibold mb-3">Siparis Ozeti</h3>
        <div class="space-y-2 text-sm mb-3">
            <p>Ara Toplam: <strong>₺ {{ number_format($this->subtotal, 2, ',', '.') }}</strong></p>
            <p>Indirim: <strong>₺ {{ number_format($this->discount, 2, ',', '.') }}</strong></p>
            <p>Toplam: <strong>₺ {{ number_format($this->total, 2, ',', '.') }}</strong></p>
        </div>
        <div class="space-y-2">
            <input wire:model="couponCode" type="text" placeholder="Kupon kodu" class="w-full border rounded-btn px-3 py-2 text-sm">
            <button wire:click="applyCoupon" type="button" class="w-full border border-rg-purple text-rg-purple px-4 py-2 rounded-btn text-sm">Kupon Uygula</button>
        </div>
        @if ($couponMessage)
            <p class="text-xs text-rg-grayText mt-2">{{ $couponMessage }}</p>
        @endif
        <a href="{{ route('checkout') }}" class="mt-4 inline-block bg-rg-purple text-white px-4 py-2 rounded-btn">Odemeye Gec</a>
    </aside>
</div>
