@extends('layouts.app')

@section('content')
    {{-- Hero --}}
    <div class="bg-gradient-to-r from-rg-deepPurple to-rg-darkPlum text-white rounded-card p-8 mb-8 text-center">
        <h1 class="font-display text-3xl md:text-4xl font-bold mb-2">{{ __('Teslimat Bilgileri') }}</h1>
        <p class="text-white/75">{{ __('Siparişiniz güvenle ve taze olarak kapınıza geliyor.') }}</p>
    </div>

    <div class="max-w-4xl mx-auto space-y-6">
        {{-- Delivery zones --}}
        <div class="bg-white border border-rg-lightLavender rounded-card p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-full bg-rg-lightLavender flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-rg-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <h2 class="font-display text-xl font-semibold text-rg-darkText">{{ __('Teslimat Bölgeleri') }}</h2>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                @foreach (['Adıyaman Merkez', 'Kahta', 'Besni', 'Gölbaşı', 'Çelikhan', 'Gerger'] as $zone)
                <div class="flex items-center gap-2 bg-rg-cream rounded-btn px-3 py-2">
                    <div class="w-2 h-2 rounded-full bg-rg-leafGreen flex-shrink-0"></div>
                    <span class="text-sm text-rg-darkText">{{ $zone }}</span>
                </div>
                @endforeach
            </div>
            <p class="mt-3 text-xs text-rg-grayText">{{ __('Yukarıdaki bölgeler dışında teslimat için lütfen bizi arayın.') }}</p>
        </div>

        {{-- Delivery hours --}}
        <div class="bg-white border border-rg-lightLavender rounded-card p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-full bg-rg-lightLavender flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-rg-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h2 class="font-display text-xl font-semibold text-rg-darkText">{{ __('Teslimat Saatleri') }}</h2>
            </div>
            <div class="space-y-3">
                <div class="flex items-start gap-3 p-3 bg-rg-lightLavender/30 rounded-btn">
                    <span class="text-lg">⚡</span>
                    <div>
                        <p class="font-semibold text-rg-darkText text-sm">{{ __('Aynı Gün Teslimat') }}</p>
                        <p class="text-sm text-rg-grayText">{{ __('Saat 14:00\'e kadar verilen siparişler aynı gün 17:00-21:00 arasında teslim edilir.') }}</p>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-rg-lightLavender">
                                <th class="text-left py-2 font-semibold text-rg-darkText">{{ __('Gün') }}</th>
                                <th class="text-left py-2 font-semibold text-rg-darkText">{{ __('Sipariş Kesme') }}</th>
                                <th class="text-left py-2 font-semibold text-rg-darkText">{{ __('Teslimat Aralığı') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-rg-lightLavender/50 text-rg-grayText">
                            <tr><td class="py-2">Pazartesi – Cumartesi</td><td>14:00</td><td>10:00–21:00</td></tr>
                            <tr><td class="py-2">Pazar</td><td>12:00</td><td>12:00–18:00</td></tr>
                            <tr><td class="py-2">Resmi Tatiller</td><td colspan="2" class="py-2 italic">{{ __('Önceden arayınız') }}</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Delivery fees --}}
        <div class="bg-white border border-rg-lightLavender rounded-card p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-full bg-rg-lightLavender flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-rg-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h2 class="font-display text-xl font-semibold text-rg-darkText">{{ __('Teslimat Ücretleri') }}</h2>
            </div>
            <div class="space-y-2">
                <div class="flex justify-between items-center p-3 bg-rg-cream rounded-btn">
                    <span class="text-sm text-rg-darkText">{{ __('Adıyaman Merkez') }}</span>
                    <span class="font-semibold text-rg-leafGreen">{{ __('Ücretsiz') }}</span>
                </div>
                <div class="flex justify-between items-center p-3 bg-rg-cream rounded-btn">
                    <span class="text-sm text-rg-darkText">{{ __('İlçeler (Kahta, Besni vb.)') }}</span>
                    <span class="font-semibold text-rg-darkText">₺ 50,00</span>
                </div>
                <div class="flex justify-between items-center p-3 bg-rg-cream rounded-btn">
                    <span class="text-sm text-rg-darkText">{{ __('500 TL ve üzeri siparişlerde') }}</span>
                    <span class="font-semibold text-rg-leafGreen">{{ __('Tüm bölgelere Ücretsiz') }}</span>
                </div>
            </div>
        </div>

        {{-- Special occasions --}}
        <div class="bg-white border border-rg-lightLavender rounded-card p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-full bg-rg-lightLavender flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-rg-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                    </svg>
                </div>
                <h2 class="font-display text-xl font-semibold text-rg-darkText">{{ __('Özel Gün Teslimatı') }}</h2>
            </div>
            <p class="text-sm text-rg-grayText mb-3">{{ __('Sevgililer Günü, Anneler Günü ve Yılbaşı gibi özel dönemlerde yoğunluk yaşandığından aşağıdaki uyarıları dikkate almanızı öneririz:') }}</p>
            <ul class="space-y-2">
                @foreach ([
                    'Özel günlerde siparişlerinizi en az 3-5 gün önceden verin.',
                    'Belirli bir saat diliminde teslimat isteniyorsa sipariş notuna yazın.',
                    'Kapıda bulunamama durumunda komşuya bırakma veya arama yapılır.',
                    'Adres değişikliği için teslimat gününden 1 gün önce bildirmeniz gerekmektedir.',
                ] as $tip)
                <li class="flex items-start gap-2 text-sm text-rg-grayText">
                    <svg class="w-4 h-4 mt-0.5 text-rg-purple flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"/>
                    </svg>
                    {{ $tip }}
                </li>
                @endforeach
            </ul>
        </div>
    </div>
@endsection
