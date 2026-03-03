@extends('layouts.app')

@section('content')
    {{-- Hero --}}
    <div class="bg-gradient-to-r from-rg-deepPurple to-rg-darkPlum text-white rounded-card p-8 mb-8 text-center">
        <h1 class="font-display text-3xl md:text-4xl font-bold mb-2">{{ __('İletişim') }}</h1>
        <p class="text-white/75">{{ __('Size yardımcı olmaktan mutluluk duyarız.') }}</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-5xl mx-auto">
        {{-- Contact form --}}
        <div class="bg-white border border-rg-lightLavender rounded-card p-6">
            <h2 class="font-display text-2xl font-semibold text-rg-darkText mb-5">{{ __('Bize Yazın') }}</h2>
            <form method="POST" action="{{ route('contact.submit') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-rg-darkText mb-1.5">{{ __('Ad Soyad') }} <span class="text-red-400">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           placeholder="{{ __('Adınız Soyadınız') }}"
                           class="w-full border border-rg-lightLavender focus:border-rg-purple focus:ring-2 focus:ring-rg-purple/20 rounded-btn px-4 py-2.5 text-sm outline-none transition-all duration-200">
                    @error('name') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-rg-darkText mb-1.5">{{ __('E-posta') }} <span class="text-red-400">*</span></label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                           placeholder="ornek@email.com"
                           class="w-full border border-rg-lightLavender focus:border-rg-purple focus:ring-2 focus:ring-rg-purple/20 rounded-btn px-4 py-2.5 text-sm outline-none transition-all duration-200">
                    @error('email') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-rg-darkText mb-1.5">{{ __('Konu') }}</label>
                    <input type="text" name="subject" value="{{ old('subject') }}"
                           placeholder="{{ __('Konunuzu belirtin') }}"
                           class="w-full border border-rg-lightLavender focus:border-rg-purple focus:ring-2 focus:ring-rg-purple/20 rounded-btn px-4 py-2.5 text-sm outline-none transition-all duration-200">
                </div>
                <div>
                    <label class="block text-sm font-medium text-rg-darkText mb-1.5">{{ __('Mesaj') }} <span class="text-red-400">*</span></label>
                    <textarea name="message" rows="5" required
                              placeholder="{{ __('Mesajınızı buraya yazın...') }}"
                              class="w-full border border-rg-lightLavender focus:border-rg-purple focus:ring-2 focus:ring-rg-purple/20 rounded-btn px-4 py-2.5 text-sm outline-none transition-all duration-200 resize-none">{{ old('message') }}</textarea>
                    @error('message') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                @if (session('success'))
                    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-btn text-sm">
                        {{ session('success') }}
                    </div>
                @endif
                <button type="submit"
                        class="w-full bg-rg-purple hover:bg-rg-darkPlum text-white font-semibold px-6 py-3 rounded-btn transition-colors duration-200 flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                    {{ __('Gönder') }}
                </button>
            </form>
        </div>

        {{-- Store info --}}
        <div class="space-y-4">
            {{-- Quick contact --}}
            <div class="bg-white border border-rg-lightLavender rounded-card p-6">
                <h2 class="font-display text-2xl font-semibold text-rg-darkText mb-4">{{ __('Mağaza Bilgileri') }}</h2>
                <ul class="space-y-4">
                    <li class="flex items-start gap-3">
                        <div class="w-9 h-9 rounded-full bg-rg-lightLavender flex items-center justify-center flex-shrink-0 mt-0.5">
                            <svg class="w-4.5 h-4.5 text-rg-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-rg-grayText mb-0.5 uppercase tracking-wide">{{ __('Adres') }}</p>
                            <p class="text-sm text-rg-darkText">Adıyaman Merkez, Türkiye</p>
                        </div>
                    </li>
                    <li class="flex items-start gap-3">
                        <div class="w-9 h-9 rounded-full bg-rg-lightLavender flex items-center justify-center flex-shrink-0 mt-0.5">
                            <svg class="w-4 h-4 text-rg-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-rg-grayText mb-0.5 uppercase tracking-wide">{{ __('Telefon') }}</p>
                            <a href="tel:+905420000000" class="text-sm text-rg-darkText hover:text-rg-purple transition-colors">+90 542 000 00 00</a>
                        </div>
                    </li>
                    <li class="flex items-start gap-3">
                        <div class="w-9 h-9 rounded-full bg-green-50 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-rg-grayText mb-0.5 uppercase tracking-wide">WhatsApp</p>
                            <a href="https://api.whatsapp.com/send?phone=905420000000" target="_blank" rel="noopener"
                               class="text-sm text-rg-darkText hover:text-green-600 transition-colors">+90 542 000 00 00</a>
                        </div>
                    </li>
                </ul>
            </div>

            {{-- Working hours --}}
            <div class="bg-white border border-rg-lightLavender rounded-card p-6">
                <div class="flex items-center gap-2 mb-4">
                    <svg class="w-5 h-5 text-rg-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <h3 class="font-semibold text-rg-darkText">{{ __('Çalışma Saatleri') }}</h3>
                </div>
                <ul class="space-y-2 text-sm">
                    @foreach ([
                        ['Pazartesi – Cuma',   '09:00 – 20:00'],
                        ['Cumartesi',          '09:00 – 21:00'],
                        ['Pazar',              '10:00 – 18:00'],
                    ] as [$day, $hours])
                    <li class="flex justify-between items-center py-1.5 border-b border-rg-lightLavender/50 last:border-0">
                        <span class="text-rg-grayText">{{ $day }}</span>
                        <span class="font-medium text-rg-darkText">{{ $hours }}</span>
                    </li>
                    @endforeach
                </ul>
            </div>

            {{-- Map placeholder --}}
            <div class="bg-rg-lightLavender/30 border border-rg-lightLavender rounded-card h-40 flex items-center justify-center">
                <div class="text-center text-rg-grayText">
                    <svg class="w-8 h-8 mx-auto mb-2 text-rg-midPurple" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                    </svg>
                    <p class="text-sm">{{ __('Google Harita yakında eklenecek') }}</p>
                </div>
            </div>
        </div>
    </div>
@endsection
