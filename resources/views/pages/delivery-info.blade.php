@extends('layouts.app')

@section('content')
    <div class="space-y-8 md:space-y-10">
        <x-page-hero
            class="rg-page-hero--compact"
            :eyebrow="__('Teslimat Bilgileri')"
            :title="__('Siparişinizin hazırlanma, yönlendirilme ve teslim edilme akışı tek bakışta')"
            :description="__('Teslimat sayfası; hız, bölge, ücret ve özel gün yoğunluğu gibi karar verdiren bilgileri daha profesyonel ve daha hızlı okunan bir düzende sunar.')"
            compact
        >
            <x-slot:stats>
                <div class="rg-page-stat">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-rg-midPurple dark:text-rg-lavender/80">{{ __('Aynı gün kesim') }}</p>
                    <p class="mt-2 text-sm font-semibold text-rg-deepPurple dark:text-white">{{ __('14:00') }}</p>
                </div>
                <div class="rg-page-stat">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-rg-midPurple dark:text-rg-lavender/80">{{ __('Operasyon notu') }}</p>
                    <p class="mt-2 text-sm leading-relaxed text-rg-grayText dark:text-white/84">{{ __('Özel dönemlerde erken sipariş, standart günlerde ise aynı gün teslimat ritmi korunur.') }}</p>
                </div>
            </x-slot:stats>
        </x-page-hero>

        <div class="grid gap-6 xl:grid-cols-[minmax(0,1.04fr)_minmax(0,0.96fr)]">
            <div class="space-y-6">
                <div class="rg-surface p-6">
                    <div class="flex items-center gap-3">
                        <div class="flex h-11 w-11 items-center justify-center rounded-full bg-rg-lightLavender text-rg-purple dark:bg-white/10 dark:text-rg-lavender">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /></svg>
                        </div>
                        <div>
                            <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-rg-midPurple dark:text-rg-lavender/80">{{ __('Teslimat bölgeleri') }}</p>
                            <h2 class="mt-1 font-display text-2xl text-rg-deepPurple dark:text-white">{{ __('Adıyaman merkezden yakın ilçelere uzanan servis alanı') }}</h2>
                        </div>
                    </div>

                    <div class="mt-5 grid grid-cols-2 gap-3 md:grid-cols-3">
                        @foreach (['Adıyaman Merkez', 'Kahta', 'Besni', 'Gölbaşı', 'Çelikhan', 'Gerger'] as $zone)
                            <div class="rounded-[1.2rem] border border-black/6 bg-rg-cream/75 px-4 py-3 text-sm text-rg-darkText dark:border-white/10 dark:bg-white/8 dark:text-white/88">
                                {{ $zone }}
                            </div>
                        @endforeach
                    </div>

                    <p class="mt-4 text-sm leading-7 text-rg-copy-muted dark:text-white/84">{{ __('Yukarıdaki bölgeler dışında teslimat için lütfen önce bizimle iletişime geçin; bazı siparişlerde önceden planlı yönlendirme yapılabilir.') }}</p>
                </div>

                <div class="rg-surface p-6">
                    <div class="flex items-center gap-3">
                        <div class="flex h-11 w-11 items-center justify-center rounded-full bg-rg-lightLavender text-rg-purple dark:bg-white/10 dark:text-rg-lavender">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </div>
                        <div>
                            <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-rg-midPurple dark:text-rg-lavender/80">{{ __('Teslimat saatleri') }}</p>
                            <h2 class="mt-1 font-display text-2xl text-rg-deepPurple dark:text-white">{{ __('Günlük akış ve sipariş kesim saatleri') }}</h2>
                        </div>
                    </div>

                    <div class="mt-5 space-y-3">
                        <div class="rounded-[1.3rem] border border-rg-lightLavender bg-rg-lightLavender/25 p-4 dark:border-white/10 dark:bg-white/8">
                            <p class="text-sm font-semibold text-rg-deepPurple dark:text-white">{{ __('Aynı gün teslimat') }}</p>
                            <p class="mt-1 text-sm leading-7 text-rg-copy-muted dark:text-white/84">{{ __("Saat 14:00'e kadar verilen siparişler aynı gün 17:00-21:00 arasında teslim edilir.") }}</p>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="border-b border-rg-lightLavender dark:border-white/10">
                                        <th class="py-2 text-left font-semibold text-rg-darkText dark:text-white">{{ __('Gün') }}</th>
                                        <th class="py-2 text-left font-semibold text-rg-darkText dark:text-white">{{ __('Sipariş kesim') }}</th>
                                        <th class="py-2 text-left font-semibold text-rg-darkText dark:text-white">{{ __('Teslimat aralığı') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-rg-lightLavender/60 text-rg-copy-muted dark:divide-white/10 dark:text-white/82">
                                    <tr><td class="py-3">Pazartesi – Cumartesi</td><td>14:00</td><td>10:00 – 21:00</td></tr>
                                    <tr><td class="py-3">Pazar</td><td>12:00</td><td>12:00 – 18:00</td></tr>
                                    <tr><td class="py-3">Resmî Tatiller</td><td colspan="2">{{ __('Önceden bilgi alın') }}</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="rg-surface p-6">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-rg-midPurple dark:text-rg-lavender/80">{{ __('Teslimat ücretleri') }}</p>
                    <h2 class="mt-2 font-display text-2xl text-rg-deepPurple dark:text-white">{{ __('Bölgeye ve sipariş tutarına göre net ücretlendirme') }}</h2>
                    <div class="mt-5 space-y-3">
                        <div class="flex items-center justify-between rounded-[1.2rem] border border-black/6 bg-rg-cream/75 px-4 py-3 dark:border-white/10 dark:bg-white/8">
                            <span class="text-sm text-rg-darkText dark:text-white/88">{{ __('Adıyaman Merkez') }}</span>
                            <span class="text-sm font-semibold text-rg-leafGreen">{{ __('Ücretsiz') }}</span>
                        </div>
                        <div class="flex items-center justify-between rounded-[1.2rem] border border-black/6 bg-rg-cream/75 px-4 py-3 dark:border-white/10 dark:bg-white/8">
                            <span class="text-sm text-rg-darkText dark:text-white/88">{{ __('İlçeler (Kahta, Besni vb.)') }}</span>
                            <span class="text-sm font-semibold text-rg-deepPurple dark:text-white">₺ 50,00</span>
                        </div>
                        <div class="flex items-center justify-between rounded-[1.2rem] border border-black/6 bg-rg-cream/75 px-4 py-3 dark:border-white/10 dark:bg-white/8">
                            <span class="text-sm text-rg-darkText dark:text-white/88">{{ __('500 TL ve üzeri siparişlerde') }}</span>
                            <span class="text-sm font-semibold text-rg-leafGreen">{{ __('Tüm bölgelere ücretsiz') }}</span>
                        </div>
                    </div>
                </div>

                <div class="rg-surface p-6">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-rg-midPurple dark:text-rg-lavender/80">{{ __('Özel gün teslimatı') }}</p>
                    <h2 class="mt-2 font-display text-2xl text-rg-deepPurple dark:text-white">{{ __('Yoğun dönemlerde daha sağlıklı planlama için notlar') }}</h2>
                    <ul class="mt-5 space-y-3">
                        @foreach ([
                            'Özel günlerde siparişlerinizi en az 3-5 gün önceden verin.',
                            'Belirli bir saat diliminde teslimat isteniyorsa sipariş notuna yazın.',
                            'Kapıda bulunamama durumunda komşuya bırakma veya arama yapılır.',
                            'Adres değişikliği için teslimat gününden 1 gün önce bildirmeniz gerekir.',
                        ] as $tip)
                            <li class="flex items-start gap-3 rounded-[1.2rem] border border-black/6 bg-rg-cream/75 px-4 py-3 text-sm leading-7 text-rg-copy-muted dark:border-white/10 dark:bg-white/8 dark:text-white/84">
                                <span class="mt-1 inline-flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-rg-lightLavender text-rg-purple dark:bg-white/10 dark:text-rg-lavender">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                </span>
                                <span>{{ $tip }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
