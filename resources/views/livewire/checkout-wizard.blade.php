<div class="bg-white border border-rg-lightLavender rounded-card p-6">
    @if (session('status'))
        <p class="mb-4 rounded bg-green-50 text-green-700 px-3 py-2 text-sm">{{ session('status') }}</p>
    @endif
    @if ($errors->has('cart'))
        <p class="mb-4 rounded bg-red-50 text-red-700 px-3 py-2 text-sm">{{ $errors->first('cart') }}</p>
    @endif
    <div class="mb-6 text-sm text-rg-grayText">
        <span class="{{ $step === 1 ? 'font-bold text-rg-purple' : '' }}">1 {{ __('Bilgiler') }}</span> -
        <span class="{{ $step === 2 ? 'font-bold text-rg-purple' : '' }}">2 {{ __('Teslimat') }}</span> -
        <span class="{{ $step === 3 ? 'font-bold text-rg-purple' : '' }}">3 {{ __('Ödeme') }}</span>
    </div>

    @if ($step === 1)
        <div class="space-y-3">
            <input wire:model.defer="senderName" type="text" placeholder="{{ __('Gönderici Adı') }}" class="w-full border rounded-btn px-3 py-2">
            <input wire:model.defer="senderPhone" type="text" placeholder="{{ __('Gönderici Telefonu') }}" class="w-full border rounded-btn px-3 py-2">
            <input wire:model.defer="senderEmail" type="email" placeholder="{{ __('Gönderici E-posta') }}" class="w-full border rounded-btn px-3 py-2">
            <input wire:model.defer="recipientName" type="text" placeholder="{{ __('Alıcı Adı') }}" class="w-full border rounded-btn px-3 py-2">
            <input wire:model.defer="recipientPhone" type="text" placeholder="{{ __('Alıcı Telefonu') }}" class="w-full border rounded-btn px-3 py-2">
            <textarea wire:model.defer="recipientAddress" rows="3" placeholder="{{ __('Alıcı Adresi') }}" class="w-full border rounded-btn px-3 py-2"></textarea>
            <input wire:model.defer="recipientDistrict" type="text" placeholder="{{ __('İlçe') }}" class="w-full border rounded-btn px-3 py-2">
        </div>
    @elseif ($step === 2)
        <div class="space-y-3">
            <input wire:model.defer="deliveryDate" type="date" class="w-full border rounded-btn px-3 py-2">
            <select wire:model.defer="deliveryTimeSlotId" class="w-full border rounded-btn px-3 py-2">
                <option value="">{{ __('Teslimat Saat Aralığı') }}</option>
                @foreach ($deliveryTimeSlots as $slot)
                    <option value="{{ $slot->id }}">{{ $slot->label }}</option>
                @endforeach
            </select>
            <select wire:model.defer="deliveryZoneId" class="w-full border rounded-btn px-3 py-2">
                <option value="">{{ __('Teslimat Bölgesi') }}</option>
                @foreach ($deliveryZones as $zone)
                    <option value="{{ $zone->id }}">{{ $zone->name }}</option>
                @endforeach
            </select>
            <textarea wire:model.defer="deliveryNote" rows="2" placeholder="{{ __('Teslimat Notu') }}" class="w-full border rounded-btn px-3 py-2"></textarea>
        </div>
    @else
        <div class="space-y-3">
            <label class="block"><input wire:model.defer="paymentMethod" value="credit_card" type="radio" name="payment"> {{ __('Kart ile Ödeme') }}</label>
            <label class="block"><input wire:model.defer="paymentMethod" value="bank_transfer" type="radio" name="payment"> {{ __('Havale/EFT') }}</label>
            {{-- Cash payment removed: DB enum only supports credit_card and bank_transfer (K39) --}}

            @auth
                @if ($availableLoyaltyBalance > 0)
                    <div class="rounded border border-rg-lightLavender p-3">
                        <p class="text-sm mb-2">Paracicek Puanlariniz: <strong>{{ number_format($availableLoyaltyBalance, 2, ',', '.') }} TL</strong></p>
                        <label class="block text-sm mb-2">
                            <input wire:model="useLoyaltyPoints" type="checkbox">
                            Bu sipariste puan kullan
                        </label>
                        @if ($useLoyaltyPoints)
                            <input
                                wire:model="loyaltyPointsToUse"
                                type="number"
                                min="0"
                                max="{{ $availableLoyaltyBalance }}"
                                step="0.01"
                                placeholder="Kullanilacak puan miktari"
                                class="w-full border rounded-btn px-3 py-2 text-sm"
                            >
                            <p class="text-xs text-rg-grayText mt-1">Toplamdan dusulecek: {{ number_format((float) $loyaltyPointsToUse, 2, ',', '.') }} TL</p>
                            @error('loyaltyPointsToUse')
                                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        @endif
                    </div>
                @endif
            @endauth

            <label class="block text-sm">
                <input wire:model.defer="distanceSalesAgreement" type="checkbox">
                <a href="{{ route('page.show', ['slug' => 'mesafeli-satis-sozlesmesi']) }}" class="underline" target="_blank" rel="noopener">{{ __('Mesafeli Satış Sözleşmesi') }}</a>
                {{ __("Mesafeli Satış Sözleşmesi'ni okudum ve kabul ediyorum") }}
            </label>
            <label class="block text-sm">
                <input wire:model.defer="kvkkAcknowledgement" type="checkbox">
                <a href="{{ route('page.show', ['slug' => 'kvkk-aydinlatma']) }}" class="underline" target="_blank" rel="noopener">{{ __('KVKK Aydınlatma Metni') }}</a>
                {{ __("KVKK Aydınlatma Metni'ni okudum") }}
            </label>
            <label class="block text-sm">
                <input wire:model.defer="explicitConsent" type="checkbox">
                {{ __('Kişisel verilerimin işlenmesine açık rıza veriyorum') }}
            </label>
            <p class="text-xs text-rg-grayText">Online ödeme (PayTR iFrame) yakında aktif olacak.</p>
            @if ($orderNumber)
                <p class="text-sm font-semibold">{{ __('Oluşan Sipariş No') }}: {{ $orderNumber }}</p>
            @endif
        </div>
    @endif

    <div class="mt-6 flex justify-between">
        <button wire:click="prevStep" class="px-4 py-2 border rounded-btn">{{ __('Geri') }}</button>
        @if ($step < 3)
            <button wire:click="nextStep" class="px-4 py-2 bg-rg-purple text-white rounded-btn">{{ __('Devam Et') }}</button>
        @else
            <button wire:click="createOrder" class="px-4 py-2 bg-rg-purple text-white rounded-btn">{{ __('Siparişi Tamamla') }}</button>
        @endif
    </div>
</div>
