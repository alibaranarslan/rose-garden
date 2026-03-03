@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="grid grid-cols-1 md:grid-cols-2 rounded-card overflow-hidden shadow-lg border border-rg-lightLavender">
        {{-- Left: Brand panel --}}
        <div class="relative hidden md:flex flex-col justify-center items-center p-10 bg-gradient-to-b from-rg-deepPurple to-rg-darkPlum text-white overflow-hidden">
            <img src="{{ asset('images/hero/hero-main.jpg') }}"
                 alt=""
                 class="absolute inset-0 w-full h-full object-cover opacity-20"
                 aria-hidden="true">
            <div class="relative z-10 text-center">
                <img src="{{ asset('images/branding/rg-logo-dark.svg') }}" alt="Rose Garden" class="h-14 w-auto mx-auto mb-6">
                <p class="font-script text-4xl text-rg-rosePink mb-3">{{ __('Aramıza Katılın') }}</p>
                <ul class="space-y-2 text-sm text-white/75 text-left max-w-xs">
                    @foreach ([
                        __('Siparişlerinizi kolayca takip edin'),
                        __('Favori ürünlerinizi kaydedin'),
                        __('Özel kampanyalardan haberdar olun'),
                        __('Hızlı ödeme için adres kaydedin'),
                    ] as $benefit)
                    <li class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-rg-rosePink flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        {{ $benefit }}
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
        {{-- Right: Form --}}
        <div class="bg-white p-8 md:p-10">
            <div class="md:hidden mb-6 text-center">
                <img src="{{ asset('images/branding/rg-logo-light.svg') }}" alt="Rose Garden" class="h-12 w-auto mx-auto">
            </div>
            <h1 class="font-display text-2xl md:text-3xl font-semibold text-rg-darkText mb-1">{{ __('Kayıt Ol') }}</h1>
            <p class="text-sm text-rg-grayText mb-6">{{ __('Zaten hesabınız var mı?') }} <a href="{{ route('login') }}" class="text-rg-purple hover:underline font-medium">{{ __('Giriş Yap') }}</a></p>

            <form method="POST" action="{{ route('register.submit') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-rg-darkText mb-1.5">{{ __('Ad Soyad') }}</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           placeholder="{{ __('Adınız Soyadınız') }}"
                           class="w-full border border-rg-lightLavender focus:border-rg-purple focus:ring-2 focus:ring-rg-purple/20 rounded-btn px-4 py-2.5 text-sm outline-none transition-all duration-200">
                    @error('name') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-rg-darkText mb-1.5">{{ __('E-posta') }}</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                           placeholder="ornek@email.com"
                           class="w-full border border-rg-lightLavender focus:border-rg-purple focus:ring-2 focus:ring-rg-purple/20 rounded-btn px-4 py-2.5 text-sm outline-none transition-all duration-200">
                    @error('email') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-rg-darkText mb-1.5">{{ __('Telefon') }}</label>
                    <input type="text" name="phone" value="{{ old('phone') }}"
                           placeholder="05XX XXX XX XX"
                           class="w-full border border-rg-lightLavender focus:border-rg-purple focus:ring-2 focus:ring-rg-purple/20 rounded-btn px-4 py-2.5 text-sm outline-none transition-all duration-200">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-rg-darkText mb-1.5">{{ __('Şifre') }}</label>
                        <input type="password" name="password" required
                               placeholder="••••••••"
                               class="w-full border border-rg-lightLavender focus:border-rg-purple focus:ring-2 focus:ring-rg-purple/20 rounded-btn px-4 py-2.5 text-sm outline-none transition-all duration-200">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-rg-darkText mb-1.5">{{ __('Tekrar') }}</label>
                        <input type="password" name="password_confirmation" required
                               placeholder="••••••••"
                               class="w-full border border-rg-lightLavender focus:border-rg-purple focus:ring-2 focus:ring-rg-purple/20 rounded-btn px-4 py-2.5 text-sm outline-none transition-all duration-200">
                    </div>
                </div>
                @error('password') <p class="text-red-400 text-xs">{{ $message }}</p> @enderror
                <div class="space-y-2">
                    <label class="flex items-start gap-2 text-sm cursor-pointer">
                        <input type="checkbox" name="kvkk_acknowledged" value="1" {{ old('kvkk_acknowledged') ? 'checked' : '' }} class="accent-rg-purple mt-0.5 flex-shrink-0">
                        <span class="text-rg-grayText leading-relaxed">
                            <a href="{{ route('page.show', ['slug' => 'kvkk-aydinlatma']) }}" class="text-rg-purple hover:underline" target="_blank" rel="noopener">{{ __('KVKK Aydınlatma Metni') }}</a>{{ __("'ni okudum ve kabul ediyorum.") }}
                        </span>
                    </label>
                    <label class="flex items-start gap-2 text-sm cursor-pointer">
                        <input type="checkbox" name="marketing_consent" value="1" {{ old('marketing_consent') ? 'checked' : '' }} class="accent-rg-purple mt-0.5 flex-shrink-0">
                        <span class="text-rg-grayText">{{ __('Kampanya ve fırsatlardan haberdar olmak istiyorum.') }}</span>
                    </label>
                </div>
                <button type="submit"
                        class="w-full bg-rg-purple hover:bg-rg-darkPlum text-white font-semibold py-3 rounded-btn transition-colors duration-200">
                    {{ __('Hesap Oluştur') }}
                </button>
            </form>

            <div class="relative my-4">
                <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-rg-lightLavender"></div></div>
                <div class="relative flex justify-center"><span class="bg-white px-3 text-xs text-rg-grayText">{{ __('veya') }}</span></div>
            </div>

            <a href="{{ route('auth.google') }}"
               class="w-full flex items-center justify-center gap-3 border border-rg-lightLavender hover:border-rg-midPurple hover:bg-rg-lightLavender/30 text-rg-darkText text-sm font-medium py-2.5 rounded-btn transition-all duration-200">
                <svg class="w-4 h-4" viewBox="0 0 24 24">
                    <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                    <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                    <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                    <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                </svg>
                {{ __('Google ile Kayıt Ol') }}
            </a>
        </div>
    </div>
</div>
@endsection
