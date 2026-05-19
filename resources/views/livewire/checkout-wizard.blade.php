@php
    $field = 'w-full rounded-lg border border-rg-lightLavender bg-white px-3 py-2.5 text-sm text-rg-darkText shadow-sm outline-none transition focus:border-rg-purple focus:ring-2 focus:ring-rg-purple/40 dark:border-white/15 dark:bg-rg-deepPurple/40 dark:text-white dark:placeholder:text-white/58';
    $label = 'mb-1.5 block text-xs font-semibold uppercase tracking-wide text-rg-midPurple dark:text-rg-lavender';
    $errorText = 'mt-1.5 text-xs font-medium text-red-600 dark:text-red-300';
@endphp

<div class="rg-checkout-wizard mx-auto max-w-3xl">
    @if (session('status'))
        <p class="mb-4 rounded-lg bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:bg-emerald-950/40 dark:text-emerald-200">{{ session('status') }}</p>
    @endif

    @if ($errors->has('cart'))
        <p class="mb-4 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700 dark:bg-red-950/40 dark:text-red-200">{{ $errors->first('cart') }}</p>
    @endif

    @if ($errors->isNotEmpty())
        @php
            $stepErrorLabels = [
                1 => [
                    'senderName' => __('Gönderici adı'),
                    'senderPhone' => __('Gönderici telefonu'),
                    'senderEmail' => __('Gönderici e-posta'),
                    'recipientName' => __('Alıcı adı'),
                    'recipientPhone' => __('Alıcı telefonu'),
                    'recipientAddress' => __('Açık adres'),
                    'recipientDistrict' => __('İlçe'),
                ],
                2 => [
                    'deliveryConfiguration' => __('Teslimat ayarları'),
                    'deliveryDate' => __('Teslimat tarihi'),
                    'deliveryTimeSlotId' => __('Saat aralığı'),
                    'deliveryZoneId' => __('Teslimat bölgesi'),
                ],
                3 => [
                    'deliveryConfiguration' => __('Teslimat ayarları'),
                    'paymentMethod' => __('Ödeme yöntemi'),
                    'loyaltyPointsToUse' => __('Kullanılacak tutar'),
                    'distanceSalesAgreement' => __('Mesafeli Satış Sözleşmesi'),
                    'kvkkAcknowledgement' => __('KVKK Aydınlatma Metni'),
                    'explicitConsent' => __('Açık rıza'),
                ],
            ];
            $currentStepErrors = collect($stepErrorLabels[$step] ?? [])
                ->filter(fn ($label, $field) => $errors->has($field))
                ->values()
                ->all();
        @endphp
        <div
            id="checkout-validation-summary"
            class="mb-4 rounded-lg border border-red-200 bg-red-50/90 px-4 py-3 text-sm text-red-700 dark:border-red-500/30 dark:bg-red-950/35 dark:text-red-200"
            aria-live="polite"
            x-data
            x-init="$nextTick(() => window.scrollTo({ top: Math.max(0, $el.getBoundingClientRect().top + window.scrollY - 128), behavior: 'auto' }))"
        >
            <p class="font-semibold">{{ __('Devam etmeden önce şu alanları kontrol edin:') }}</p>
            <p class="mt-1">{{ $currentStepErrors ? implode(', ', $currentStepErrors) : __('İşaretli alanlar') }}</p>
        </div>
    @endif

    <div class="mb-7 flex gap-2 overflow-x-auto pb-1 text-center text-xs font-semibold md:mb-8">
        <div class="min-w-[6.75rem] shrink-0 rounded-full border px-3 py-2 {{ $step === 1 ? 'border-rg-purple bg-rg-purple text-white shadow-sm dark:border-rg-lavender dark:bg-rg-lavender dark:text-rg-deepPurple' : 'border-rg-lightLavender bg-white text-rg-grayText dark:border-white/10 dark:bg-white/5 dark:text-white/70' }}">
            {{ __('1. Bilgiler') }}
        </div>
        <div class="min-w-[6.75rem] shrink-0 rounded-full border px-3 py-2 {{ $step === 2 ? 'border-rg-purple bg-rg-purple text-white shadow-sm dark:border-rg-lavender dark:bg-rg-lavender dark:text-rg-deepPurple' : 'border-rg-lightLavender bg-white text-rg-grayText dark:border-white/10 dark:bg-white/5 dark:text-white/70' }}">
            {{ __('2. Teslimat') }}
        </div>
        <div class="min-w-[6.75rem] shrink-0 rounded-full border px-3 py-2 {{ $step === 3 ? 'border-rg-purple bg-rg-purple text-white shadow-sm dark:border-rg-lavender dark:bg-rg-lavender dark:text-rg-deepPurple' : 'border-rg-lightLavender bg-white text-rg-grayText dark:border-white/10 dark:bg-white/5 dark:text-white/70' }}">
            {{ __('3. Ödeme') }}
        </div>
    </div>

    @if ($step === 1)
        <div class="space-y-6">
            <section class="rounded-[1.4rem] border border-rg-lightLavender/80 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-rg-deepPurple/40 md:p-6">
                <h2 class="mb-4 font-display text-lg font-semibold text-rg-deepPurple dark:text-white">{{ __('Gönderici') }}</h2>

                @if ($savedAddresses->isNotEmpty())
                    <div class="mb-4">
                        <label for="savedAddressId" class="{{ $label }}">{{ __('Kayıtlı adres seçin') }}</label>
                        <select id="savedAddressId" wire:model.live="savedAddressId" class="{{ $field }}">
                            <option value="">{{ __('Elle gireceğim') }}</option>
                            @foreach ($savedAddresses as $address)
                                <option value="{{ $address->id }}">{{ $address->label ?: $address->recipient_name }} - {{ $address->district }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <div class="grid gap-4 md:grid-cols-2">
                    <div class="md:col-span-2">
                        <label for="senderName" class="{{ $label }}">{{ __('Gönderici adı') }}</label>
                        <input id="senderName" wire:model.blur="senderName" type="text" class="{{ $field }}" placeholder="{{ __('Ad Soyad') }}">
                        @error('senderName') <p class="{{ $errorText }}">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="senderPhone" class="{{ $label }}">{{ __('Gönderici telefonu') }}</label>
                        <input id="senderPhone" wire:model.blur="senderPhone" type="text" class="{{ $field }}" placeholder="{{ __('Telefon') }}">
                        @error('senderPhone') <p class="{{ $errorText }}">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="senderEmail" class="{{ $label }}">{{ __('Gönderici e-posta') }}</label>
                        <input id="senderEmail" wire:model.blur="senderEmail" type="email" class="{{ $field }}" placeholder="{{ __('E-posta') }}">
                        @error('senderEmail') <p class="{{ $errorText }}">{{ $message }}</p> @enderror
                    </div>
                </div>
            </section>

            <section class="rounded-[1.4rem] border border-rg-lightLavender/80 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-rg-deepPurple/40 md:p-6">
                <h2 class="mb-1 font-display text-lg font-semibold text-rg-deepPurple dark:text-white">{{ __('Teslimat adresi') }}</h2>
                <p class="mb-4 text-xs text-rg-grayText dark:text-white/72">{{ __('Adres faturada kullanılır.') }}</p>

                <div class="grid gap-4 md:grid-cols-2">
                    <div class="md:col-span-2">
                        <label for="recipientName" class="{{ $label }}">{{ __('Alıcı adı') }}</label>
                        <input id="recipientName" wire:model.blur="recipientName" type="text" class="{{ $field }}" placeholder="{{ __('Alıcı adı') }}">
                        @error('recipientName') <p class="{{ $errorText }}">{{ $message }}</p> @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label for="recipientPhone" class="{{ $label }}">{{ __('Alıcı telefonu') }}</label>
                        <input id="recipientPhone" wire:model.blur="recipientPhone" type="text" class="{{ $field }}" placeholder="{{ __('Telefon') }}">
                        @error('recipientPhone') <p class="{{ $errorText }}">{{ $message }}</p> @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label for="recipientAddress" class="{{ $label }}">{{ __('Açık adres') }}</label>
                        <textarea id="recipientAddress" wire:model.blur="recipientAddress" rows="3" class="{{ $field }}" placeholder="{{ __('Mahalle, sokak, bina no, daire...') }}"></textarea>
                        @error('recipientAddress') <p class="{{ $errorText }}">{{ $message }}</p> @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label for="recipientDistrict" class="{{ $label }}">{{ __('İlçe') }}</label>
                        <input id="recipientDistrict" wire:model.blur="recipientDistrict" type="text" class="{{ $field }}" placeholder="{{ __('İlçe') }}">
                        @error('recipientDistrict') <p class="{{ $errorText }}">{{ $message }}</p> @enderror
                    </div>
                </div>
            </section>
        </div>
    @elseif ($step === 2)
        <section class="rounded-[1.4rem] border border-rg-lightLavender/80 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-rg-deepPurple/40 md:p-6">
            <h2 class="mb-4 font-display text-lg font-semibold text-rg-deepPurple dark:text-white">{{ __('Teslimat') }}</h2>

            @if ($errors->has('deliveryConfiguration'))
                <div class="mb-4 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900 dark:border-amber-500/30 dark:bg-amber-950/30 dark:text-amber-100" aria-live="polite">
                    <p class="font-semibold">{{ $errors->first('deliveryConfiguration') }}</p>
                    <ul class="mt-2 list-disc space-y-1 pl-5">
                        @if ($deliveryZones->isEmpty())
                            <li>{{ __('Aktif teslimat bölgesi tanımlanmalı.') }}</li>
                        @endif
                        @if ($deliveryTimeSlots->isEmpty())
                            <li>{{ __('Aktif saat aralığı tanımlanmalı.') }}</li>
                        @endif
                    </ul>
                </div>
            @elseif ($deliveryZones->isEmpty() || $deliveryTimeSlots->isEmpty())
                <div class="mb-4 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900 dark:border-amber-500/30 dark:bg-amber-950/30 dark:text-amber-100" aria-live="polite">
                    <p class="font-semibold">{{ __('Teslimat seçenekleri hazır değil.') }}</p>
                    <ul class="mt-2 list-disc space-y-1 pl-5">
                        @if ($deliveryZones->isEmpty())
                            <li>{{ __('Aktif teslimat bölgesi tanımlanmalı.') }}</li>
                        @endif
                        @if ($deliveryTimeSlots->isEmpty())
                            <li>{{ __('Aktif saat aralığı tanımlanmalı.') }}</li>
                        @endif
                    </ul>
                </div>
            @endif

            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label for="deliveryDate" class="{{ $label }}">{{ __('Teslimat tarihi') }}</label>
                    <input id="deliveryDate" wire:model.blur="deliveryDate" type="date" class="{{ $field }}">
                    @error('deliveryDate') <p class="{{ $errorText }}">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="deliveryTimeSlotId" class="{{ $label }}">{{ __('Saat aralığı') }}</label>
                    <select id="deliveryTimeSlotId" wire:model.live="deliveryTimeSlotId" class="{{ $field }}">
                        <option value="">{{ __('Teslimat saat aralığı') }}</option>
                        @foreach ($deliveryTimeSlots as $slot)
                            <option value="{{ $slot->id }}">{{ $slot->label }}</option>
                        @endforeach
                    </select>
                    @error('deliveryTimeSlotId') <p class="{{ $errorText }}">{{ $message }}</p> @enderror
                </div>
                <div class="md:col-span-2">
                    <label for="deliveryZoneId" class="{{ $label }}">{{ __('Teslimat bölgesi') }}</label>
                    <select id="deliveryZoneId" wire:model.live="deliveryZoneId" class="{{ $field }}">
                        <option value="">{{ __('Bölge seçin') }}</option>
                        @foreach ($deliveryZones as $zone)
                            <option value="{{ $zone->id }}">{{ $zone->name }}</option>
                        @endforeach
                    </select>
                    @error('deliveryZoneId') <p class="{{ $errorText }}">{{ $message }}</p> @enderror
                </div>
                <div class="md:col-span-2">
                    <label for="deliveryNote" class="{{ $label }}">{{ __('Teslimat notu') }}</label>
                    <textarea id="deliveryNote" wire:model.blur="deliveryNote" rows="2" class="{{ $field }}" placeholder="{{ __('Kapı şifresi, yönlendirme vb. (isteğe bağlı)') }}"></textarea>
                </div>
            </div>
        </section>
    @else
        <div class="space-y-6">
            <section class="rounded-[1.4rem] border border-rg-lightLavender/80 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-rg-deepPurple/40 md:p-6">
                <h2 class="mb-4 font-display text-lg font-semibold text-rg-deepPurple dark:text-white">{{ __('Ödeme yöntemi') }}</h2>
                <p class="mb-4 text-xs text-rg-grayText dark:text-white/72">{{ __('Kart ile ödeme, sipariş oluşunca güvenli sayfada tamamlanır.') }}</p>

                <div class="space-y-3">
                    <label class="flex cursor-pointer items-center gap-3 rounded-lg border border-rg-lightLavender/80 p-3 transition hover:border-rg-purple/40 dark:border-white/10 dark:hover:border-rg-lavender/40">
                        <input wire:model.live="paymentMethod" value="credit_card" type="radio" name="payment" class="h-4 w-4 border-rg-lightLavender text-rg-purple focus:ring-rg-purple dark:border-white/30" @disabled(! $paytrConfigured)>
                        <span class="text-sm font-medium text-rg-darkText dark:text-white">{{ __('Kredi / banka kartı') }}</span>
                        @if (! $paytrConfigured)
                            <span class="ml-auto rounded-full bg-amber-100 px-2 py-0.5 text-[11px] font-semibold text-amber-800 dark:bg-amber-500/20 dark:text-amber-100">{{ __('Kapalı') }}</span>
                        @endif
                    </label>
                    <label class="flex cursor-pointer items-center gap-3 rounded-lg border border-rg-lightLavender/80 p-3 transition hover:border-rg-purple/40 dark:border-white/10 dark:hover:border-rg-lavender/40">
                        <input wire:model.live="paymentMethod" value="bank_transfer" type="radio" name="payment" class="h-4 w-4 border-rg-lightLavender text-rg-purple focus:ring-rg-purple dark:border-white/30">
                        <span class="text-sm font-medium text-rg-darkText dark:text-white">{{ __('Havale / EFT') }}</span>
                    </label>
                </div>
                @error('paymentMethod') <p class="{{ $errorText }}">{{ $message }}</p> @enderror
            </section>

            @guest
                @if ($guestLoyaltyEstimate > 0)
                    <section class="rounded-[1.4rem] border border-rg-lavender/70 bg-rg-lavender/10 p-5 shadow-sm dark:border-rg-lavender/20 dark:bg-white/6 md:p-6">
                        <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-rg-midPurple dark:text-rg-lavender">{{ __('Üyelik ve puan') }}</p>
                        <h2 class="mt-2 font-display text-base font-semibold text-rg-deepPurple dark:text-white">{{ __('Üye ol, puan biriktir') }}</h2>
                        <p class="mt-2 text-sm leading-relaxed text-rg-darkText dark:text-white/88">
                            {{ __('Bu siparişle yaklaşık :points Paraçiçek Puan kazanabilirsiniz. Üye olursanız puanlar hesabınızda birikir.', ['points' => number_format($guestLoyaltyEstimate, 0, ',', '.')]) }}
                        </p>
                        <div class="mt-4 flex flex-col gap-2 sm:flex-row sm:items-center">
                            <a href="{{ \App\Support\StorefrontLocale::route('register') }}" class="inline-flex items-center justify-center rounded-xl bg-rg-purple px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-rg-darkPlum hover:shadow-md focus:outline-none focus-visible:ring-2 focus-visible:ring-rg-purple focus-visible:ring-offset-2 dark:focus-visible:ring-offset-rg-deepPurple">
                                {{ __('Üye ol') }}
                            </a>
                            <p class="text-xs text-rg-grayText dark:text-white/72">{{ __('Sipariş akışı kesilmez. Puanlar yalnızca üyelikle birikir.') }}</p>
                        </div>
                    </section>
                @endif
            @endguest

            @if (! $paytrConfigured)
                <div class="rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900 shadow-sm dark:border-amber-500/30 dark:bg-amber-950/30 dark:text-amber-100" aria-live="polite">
                    <p class="font-semibold">{{ __('Kart ile ödeme canlı değil.') }}</p>
                    <p class="mt-1">{{ __('Bu ortamda varsayılan seçim havale / EFT. Kart seçeneği aktif olunca tekrar görünür.') }}</p>
                </div>
            @endif

            <section class="rounded-[1.4rem] border border-rg-lightLavender/80 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-rg-deepPurple/40 md:p-6">
                <h2 class="mb-3 font-display text-base font-semibold text-rg-deepPurple dark:text-white">{{ __('Havale / EFT bilgileri') }}</h2>
                @if ($bankInfo['configured'])
                    <p class="text-sm text-rg-grayText dark:text-white/86">{{ __('Banka') }}: <strong class="text-rg-darkText dark:text-white">{{ $bankInfo['bank_name'] }}</strong></p>
                    <p class="mt-1 text-sm text-rg-grayText dark:text-white/86">{{ __('IBAN') }}: <strong class="text-rg-darkText dark:text-white">{{ $bankInfo['bank_iban'] }}</strong></p>
                    <p class="mt-1 text-sm text-rg-grayText dark:text-white/86">{{ __('Hesap sahibi') }}: <strong class="text-rg-darkText dark:text-white">{{ $bankInfo['bank_account_holder'] }}</strong></p>
                    <p class="mt-3 text-xs leading-relaxed text-rg-grayText dark:text-white/72">
                        {{ __('Açıklamaya sipariş numaranızı ekleyin. :hours saat içinde ödeme gelmezse sipariş beklemede kalır.', ['hours' => $bankInfo['transfer_timeout_hours']]) }}
                    </p>
                @else
                    <p class="text-sm text-rg-grayText dark:text-white/86">
                        {{ __('Banka bilgileri henüz hazır değil. Siparişiniz oluşturulur; detaylar manuel paylaşılır.') }}
                    </p>
                @endif
            </section>

            @auth
                @if ($availableLoyaltyBalance > 0)
                    <section class="rounded-[1.4rem] border border-rg-lightLavender/80 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-rg-deepPurple/40 md:p-6">
                        <h2 class="mb-3 font-display text-base font-semibold text-rg-deepPurple dark:text-white">{{ __('Puan kullanımı') }}</h2>
                        <p class="text-sm text-rg-grayText dark:text-white/86">{{ __('Bakiyeniz') }}: <strong class="text-rg-darkText dark:text-white">{{ number_format($availableLoyaltyBalance, 2, ',', '.') }} TL</strong></p>
                        <label class="mt-3 flex cursor-pointer items-center gap-2 text-sm dark:text-white/90">
                            <input wire:model.live="useLoyaltyPoints" type="checkbox" class="h-4 w-4 rounded border-rg-lightLavender text-rg-purple focus:ring-rg-purple dark:border-white/30">
                            {{ __('Bu siparişte puan kullan') }}
                        </label>
                        @if ($useLoyaltyPoints)
                            <div class="mt-3">
                                <label class="{{ $label }}">{{ __('Kullanılacak tutar') }}</label>
                                <input
                                    wire:model.blur="loyaltyPointsToUse"
                                    type="number"
                                    min="0"
                                    max="{{ $availableLoyaltyBalance }}"
                                    step="0.01"
                                    placeholder="{{ __('Tutar') }}"
                                    class="{{ $field }}"
                                >
                                <p class="mt-1 text-xs text-rg-grayText dark:text-white/72">{{ __('Toplamdan düşülecek') }}: {{ number_format((float) $loyaltyPointsToUse, 2, ',', '.') }} TL</p>
                                @if ($minimumLoyaltyOrderAmount > 0)
                                    <p class="mt-1 text-xs text-rg-grayText dark:text-white/72">{{ __('Puan kullanımı için minimum sipariş tutarı') }}: {{ number_format($minimumLoyaltyOrderAmount, 2, ',', '.') }} TL</p>
                                @endif
                                @error('loyaltyPointsToUse') <p class="{{ $errorText }}">{{ $message }}</p> @enderror
                            </div>
                        @endif
                    </section>
                @endif
            @endauth

            <section class="rounded-[1.4rem] border border-rg-lightLavender/80 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-rg-deepPurple/40 md:p-6">
                <h2 class="mb-4 font-display text-base font-semibold text-rg-deepPurple dark:text-white">{{ __('Onaylar') }}</h2>
                <div class="space-y-3 text-sm text-rg-darkText dark:text-white/90">
                    <label class="flex cursor-pointer items-start gap-3">
                        <input wire:model.live="distanceSalesAgreement" type="checkbox" class="mt-0.5 h-4 w-4 rounded border-rg-lightLavender text-rg-purple focus:ring-rg-purple dark:border-white/30">
                        <span>
                            <a href="{{ \App\Support\StorefrontLocale::route('page.show', ['slug' => 'mesafeli-satis-sozlesmesi']) }}" class="font-medium text-rg-purple underline hover:text-rg-darkPlum dark:text-rg-lavender" target="_blank" rel="noopener">{{ __('Mesafeli Satış Sözleşmesi') }}</a>
                            {{ __('metnini okudum ve kabul ediyorum.') }}
                        </span>
                    </label>
                    @error('distanceSalesAgreement') <p class="{{ $errorText }}">{{ $message }}</p> @enderror

                    <label class="flex cursor-pointer items-start gap-3">
                        <input wire:model.live="kvkkAcknowledgement" type="checkbox" class="mt-0.5 h-4 w-4 rounded border-rg-lightLavender text-rg-purple focus:ring-rg-purple dark:border-white/30">
                        <span>
                            <a href="{{ \App\Support\StorefrontLocale::route('page.show', ['slug' => 'kvkk-aydinlatma']) }}" class="font-medium text-rg-purple underline dark:text-rg-lavender" target="_blank" rel="noopener">{{ __('KVKK Aydınlatma Metni') }}</a>
                            {{ __('ni okudum.') }}
                        </span>
                    </label>
                    @error('kvkkAcknowledgement') <p class="{{ $errorText }}">{{ $message }}</p> @enderror

                    <label class="flex cursor-pointer items-start gap-3">
                        <input wire:model.live="explicitConsent" type="checkbox" class="mt-0.5 h-4 w-4 rounded border-rg-lightLavender text-rg-purple focus:ring-rg-purple dark:border-white/30">
                        <span>{{ __('Kişisel verilerimin işlenmesine açık rıza veriyorum.') }}</span>
                    </label>
                    @error('explicitConsent') <p class="{{ $errorText }}">{{ $message }}</p> @enderror
                </div>

                <p class="mt-4 text-xs text-rg-grayText dark:text-white/70">{{ __('Kart ödemesi, sipariş oluşunca güvenli sayfada tamamlanır.') }}</p>

                @if ($orderNumber)
                    <p class="mt-3 text-sm font-semibold text-rg-deepPurple dark:text-rg-lavender">{{ __('Oluşan sipariş no') }}: {{ $orderNumber }}</p>
                @endif
            </section>
        </div>
    @endif

    <div class="rg-checkout-actions mt-8 flex flex-col-reverse gap-3 sm:flex-row sm:justify-between">
        <div>
            @if ($step > 1)
                <button type="button" wire:click="prevStep" wire:loading.attr="disabled" wire:target="prevStep,nextStep,createOrder" class="rounded-xl border-2 border-rg-lightLavender bg-white px-5 py-3 text-sm font-semibold text-rg-darkPlum shadow-sm transition hover:bg-rg-cream disabled:cursor-not-allowed disabled:opacity-70 dark:border-white/15 dark:bg-rg-deepPurple/50 dark:text-white dark:hover:bg-white/10">
                    {{ __('Geri') }}
                </button>
            @endif
        </div>

        @if ($step < 3)
            <button type="button" wire:click="nextStep" wire:loading.attr="disabled" wire:target="nextStep,createOrder" class="rounded-xl bg-rg-purple px-6 py-3 text-sm font-semibold text-white shadow-md transition hover:bg-rg-darkPlum hover:shadow-lg focus:outline-none focus-visible:ring-2 focus-visible:ring-rg-purple focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-70 dark:focus-visible:ring-offset-rg-deepPurple">
                {{ __('Devam et') }}
            </button>
        @else
            <button type="button" wire:click="createOrder" wire:loading.attr="disabled" wire:target="createOrder" class="rounded-xl bg-rg-purple px-6 py-3 text-sm font-semibold text-white shadow-md transition hover:bg-rg-darkPlum hover:shadow-lg focus:outline-none focus-visible:ring-2 focus-visible:ring-rg-purple focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-70 dark:focus-visible:ring-offset-rg-deepPurple">
                {{ __('Siparişi tamamla') }}
            </button>
        @endif
    </div>

    <div class="rg-checkout-mobile-actionbar md:hidden" aria-label="{{ __('Mobil ödeme adımı kısayolu') }}">
        <div class="min-w-0">
            <p class="truncate text-[11px] font-semibold uppercase tracking-[0.16em] text-rg-midPurple dark:text-rg-lavender">
                {{ __('Adım :step / 3', ['step' => $step]) }}
            </p>
            <p class="text-sm font-bold text-rg-deepPurple dark:text-white">
                @if ($step === 1)
                    {{ __('Bilgileri tamamla') }}
                @elseif ($step === 2)
                    {{ __('Teslimatı seç') }}
                @else
                    {{ __('Siparişi onayla') }}
                @endif
            </p>
        </div>

        <div class="flex shrink-0 items-center gap-2">
            @if ($step > 1)
                <button type="button" wire:click="prevStep" wire:loading.attr="disabled" wire:target="prevStep,nextStep,createOrder" class="inline-flex h-10 items-center justify-center rounded-full border border-rg-lightLavender bg-white px-4 text-xs font-semibold text-rg-darkPlum shadow-sm transition hover:bg-rg-cream disabled:cursor-not-allowed disabled:opacity-70 dark:border-white/15 dark:bg-white/10 dark:text-white dark:hover:bg-white/15">
                    {{ __('Geri') }}
                </button>
            @endif

            @if ($step < 3)
                <button type="button" wire:click="nextStep" wire:loading.attr="disabled" wire:target="nextStep,createOrder" class="inline-flex h-10 items-center justify-center rounded-full bg-rg-purple px-5 text-sm font-semibold text-white shadow-sm transition hover:bg-rg-darkPlum disabled:cursor-not-allowed disabled:opacity-70">
                    {{ __('Devam et') }}
                </button>
            @else
                <button type="button" wire:click="createOrder" wire:loading.attr="disabled" wire:target="createOrder" class="inline-flex h-10 items-center justify-center rounded-full bg-rg-purple px-5 text-sm font-semibold text-white shadow-sm transition hover:bg-rg-darkPlum disabled:cursor-not-allowed disabled:opacity-70">
                    {{ __('Tamamla') }}
                </button>
            @endif
        </div>
    </div>
</div>


