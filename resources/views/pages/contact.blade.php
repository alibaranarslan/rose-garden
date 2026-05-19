@extends('layouts.app')

@section('content')
    @php
        $field = 'w-full rounded-xl border border-rg-lightLavender bg-white px-4 py-3 text-sm text-rg-darkText shadow-sm outline-none transition focus:border-rg-purple focus:ring-2 focus:ring-rg-purple/45 dark:border-white/15 dark:bg-rg-deepPurple/40 dark:text-white dark:placeholder:text-white/40 dark:focus:border-rg-lavender dark:focus:ring-rg-lavender/35';
        $label = 'mb-1.5 block text-xs font-semibold uppercase tracking-wide text-rg-midPurple dark:text-rg-lavender';
        $storePhone = $siteSettings->get('contact', collect())->get('contact_phone', '0552 271 70 67');
        $storeEmail = $siteSettings->get('contact', collect())->get('contact_email', '');
        $storeAddress = $siteSettings->get('contact', collect())->get('address', 'Yeni Sanayi Mah. 2819 Sk. No: 70/2B K.A.06 Adıyaman Merkez');
        $contactSettings = $siteSettings->get('contact', collect());
        $storeWa = \App\Support\ContactLinks::phoneForWhatsApp($contactSettings);
        $phoneRawStore = \App\Support\ContactLinks::phoneForTel($contactSettings);
        $rgMapQuery = rawurlencode('Yeni Sanayi Mah. 2819 Sk. No 70/2B K.A.06 Adıyaman Merkez');
    @endphp

    <div class="rg-content-shell space-y-8 md:space-y-10">
        <x-page-hero
            class="rg-page-hero--compact"
            :eyebrow="__('İletişim')"
            :title="__('Mağaza, teslimat ve ürün seçimi için doğrudan ulaşılabilir bir destek hattı')"
            :description="__('Rose Garden ile WhatsApp, telefon veya iletişim formu üzerinden hızlıca bağlantı kurabilirsiniz.')"
            compact
        >
            <x-slot:actions>
                <a href="https://api.whatsapp.com/send?phone={{ $storeWa }}" target="_blank" rel="noopener" class="inline-flex items-center justify-center rounded-full bg-emerald-500 px-5 py-3 text-sm font-semibold text-white transition-colors duration-200 hover:bg-emerald-600">
                    WhatsApp
                </a>
                <a href="tel:+{{ $phoneRawStore }}" class="rg-button-secondary">
                    {{ __('Hemen ara') }}
                </a>
            </x-slot:actions>

            <x-slot:stats>
                <div class="rg-page-stat">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-rg-midPurple dark:text-rg-lavender/80">{{ __('Mağaza hattı') }}</p>
                    <p class="mt-2 text-sm font-semibold text-rg-deepPurple dark:text-white">{{ $storePhone }}</p>
                </div>
                <div class="rg-page-stat">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-rg-midPurple dark:text-rg-lavender/80">{{ __('Teslimat odağı') }}</p>
                    <p class="mt-2 text-sm leading-relaxed text-rg-grayText dark:text-white/84">{{ __('Aynı gün teslimata uygun siparişler ve butik yönlendirme için hızlı geri dönüş odaklı çalışırız.') }}</p>
                </div>
            </x-slot:stats>
        </x-page-hero>

        <div class="grid grid-cols-1 items-start gap-8 lg:grid-cols-[minmax(0,0.88fr)_minmax(0,1.12fr)] lg:gap-10 xl:gap-12">
            <div class="space-y-6">
                <div class="rg-surface p-5 md:p-6">
                    <h2 class="font-display text-xl font-semibold text-rg-deepPurple dark:text-white md:text-2xl">{{ __('Bize ulaşın') }}</h2>
                    <ul class="mt-6 space-y-6">
                        <li class="flex gap-4">
                            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-rg-lightLavender/90 text-rg-purple dark:bg-white/10 dark:text-rg-lavender" aria-hidden="true">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </span>
                            <div>
                                <p class="text-xs font-bold uppercase tracking-wide text-rg-midPurple dark:text-rg-lavender">{{ __('Adres') }}</p>
                                <p class="mt-1 text-sm leading-relaxed text-rg-darkText dark:text-white/90">{{ $storeAddress }}</p>
                            </div>
                        </li>
                        <li class="flex gap-4">
                            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-rg-lightLavender/90 text-rg-purple dark:bg-white/10 dark:text-rg-lavender" aria-hidden="true">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                            </span>
                            <div>
                                <p class="text-xs font-bold uppercase tracking-wide text-rg-midPurple dark:text-rg-lavender">{{ __('Telefon') }}</p>
                                <a href="tel:+{{ $phoneRawStore }}" class="mt-1 inline-block text-sm font-medium text-rg-darkText hover:text-rg-purple dark:text-white dark:hover:text-rg-lavender">{{ $storePhone }}</a>
                            </div>
                        </li>
                        @if(filled($storeEmail))
                            <li class="flex gap-4">
                                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-rg-lightLavender/90 text-rg-purple dark:bg-white/10 dark:text-rg-lavender" aria-hidden="true">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                </span>
                                <div>
                                    <p class="text-xs font-bold uppercase tracking-wide text-rg-midPurple dark:text-rg-lavender">{{ __('E-posta') }}</p>
                                    <a href="mailto:{{ $storeEmail }}" class="mt-1 inline-block text-sm font-medium text-rg-purple hover:underline dark:text-rg-lavender">{{ $storeEmail }}</a>
                                </div>
                            </li>
                        @endif
                        <li class="flex gap-4">
                            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 dark:bg-emerald-950/50 dark:text-emerald-300" aria-hidden="true">
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                </svg>
                            </span>
                            <div>
                                <p class="text-xs font-bold uppercase tracking-wide text-rg-midPurple dark:text-rg-lavender">WhatsApp</p>
                                <a href="https://api.whatsapp.com/send?phone={{ $storeWa }}" target="_blank" rel="noopener" class="mt-1 inline-block text-sm font-medium text-emerald-700 hover:underline dark:text-emerald-300">{{ $storePhone }}</a>
                            </div>
                        </li>
                    </ul>
                </div>

                <div class="grid gap-6 md:grid-cols-2">
                    <div class="rg-surface-soft p-5">
                        <div class="mb-4 flex items-center gap-2">
                            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-rg-lightLavender/90 text-rg-purple dark:bg-white/10 dark:text-rg-lavender" aria-hidden="true">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </span>
                            <h3 class="font-display text-lg font-semibold text-rg-deepPurple dark:text-white">{{ __('Çalışma saatleri') }}</h3>
                        </div>
                        <ul class="space-y-2 text-sm">
                            @foreach ([
                                [__('Pazartesi – Cuma'), '09:00 – 20:00'],
                                [__('Cumartesi'), '09:00 – 21:00'],
                                [__('Pazar'), '10:00 – 18:00'],
                            ] as [$day, $hours])
                                <li class="flex items-center justify-between border-b border-rg-lightLavender/60 py-2 last:border-0 dark:border-white/10">
                                    <span class="text-rg-grayText dark:text-white/82">{{ $day }}</span>
                                    <span class="font-semibold text-rg-darkText dark:text-white">{{ $hours }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="rg-surface-soft p-5">
                        <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-rg-midPurple dark:text-rg-lavender/80">{{ __('İlk temas') }}</p>
                        <h3 class="mt-3 font-display text-xl text-rg-deepPurple dark:text-white">{{ __('Hızlı, sakin ve butik iletişim') }}</h3>
                        <p class="mt-3 text-sm leading-[1.85] text-rg-copy-muted dark:text-white/84">{{ __('Telefon ve WhatsApp girişleri, ürün seçimi ve teslimat planlamasında hız isteyen ziyaretçiler için doğrudan aksiyon alanı sunar.') }}</p>
                    </div>
                </div>

                <div class="rg-surface overflow-hidden rounded-[1.75rem]">
                    <div class="aspect-video min-h-[240px] w-full">
                        <iframe
                            title="{{ __('Mağaza konumu') }}"
                            src="https://maps.google.com/maps?q={{ $rgMapQuery }}&hl=tr&z=16&output=embed"
                            class="h-full w-full border-0"
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"
                            allowfullscreen
                        ></iframe>
                    </div>
                    <div class="border-t border-rg-lightLavender bg-rg-cream/90 px-4 py-3 text-center dark:border-white/10 dark:bg-[#241b2b]/90">
                        <a href="https://maps.app.goo.gl/NGPknVNMNcqYVmDc8" target="_blank" rel="noopener" class="text-sm font-semibold text-rg-purple hover:underline dark:text-rg-lavender">
                            {{ __('Google Haritalarda aç') }} →
                        </a>
                    </div>
                </div>
            </div>

            <div class="rg-surface p-5 md:p-6">
                <h2 class="font-display text-xl font-semibold text-rg-deepPurple dark:text-white md:text-2xl">{{ __('Bize yazın') }}</h2>
                <p class="mt-3 text-sm leading-[1.85] text-rg-grayText dark:text-white/84">{{ __('Formu doldurduğunuzda ekibimiz size mümkün olan en kısa sürede geri döner.') }}</p>
                <form id="contact-form" method="POST" action="{{ \App\Support\StorefrontLocale::route('contact.submit') }}" class="mt-6 space-y-5">
                    @csrf
                    <div>
                        <label for="contact-name" class="{{ $label }}">{{ __('Ad Soyad') }} <span class="text-red-500">*</span></label>
                        <input type="text" id="contact-name" name="name" value="{{ old('name') }}" required placeholder="{{ __('Adınız Soyadınız') }}" class="{{ $field }}">
                        @error('name') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="contact-email" class="{{ $label }}">{{ __('E-posta') }} <span class="text-red-500">*</span></label>
                        <input type="email" id="contact-email" name="email" value="{{ old('email') }}" required placeholder="ornek@email.com" autocomplete="email" class="{{ $field }}">
                        @error('email') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="contact-subject" class="{{ $label }}">{{ __('Konu') }}</label>
                        <input type="text" id="contact-subject" name="subject" value="{{ old('subject') }}" placeholder="{{ __('Konunuzu belirtin') }}" class="{{ $field }}">
                    </div>
                    <div>
                        <label for="contact-message" class="{{ $label }}">{{ __('Mesaj') }} <span class="text-red-500">*</span></label>
                        <textarea id="contact-message" name="message" rows="6" required placeholder="{{ __('Mesajınızı buraya yazın...') }}" class="{{ $field }} resize-y">{{ old('message') }}</textarea>
                        @error('message') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                    </div>
                    @if (session('success'))
                        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-500/30 dark:bg-emerald-950/40 dark:text-emerald-200">
                            {{ session('success') }}
                        </div>
                    @endif
                    <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-xl bg-rg-purple py-3.5 text-sm font-semibold text-white shadow-md transition hover:bg-rg-darkPlum hover:shadow-lg focus:outline-none focus-visible:ring-2 focus-visible:ring-rg-purple focus-visible:ring-offset-2 dark:focus-visible:ring-offset-rg-deepPurple">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                        {{ __('Gönder') }}
                    </button>
                </form>
            </div>
        </div>
    </div>

    @if ($errors->any() || session('success'))
        <script>
            (function () {
                function focusContactForm() {
                    window.setTimeout(function () {
                        var target = document.getElementById('contact-form');

                        if (target) {
                            window.scrollTo({
                                top: target.getBoundingClientRect().top + window.scrollY - 140,
                                behavior: 'auto'
                            });
                        }
                    }, 80);
                }

                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', focusContactForm, { once: true });
                } else {
                    focusContactForm();
                }
            })();
        </script>
    @endif
@endsection
