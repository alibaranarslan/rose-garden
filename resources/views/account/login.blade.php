@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="grid grid-cols-1 md:grid-cols-2 rounded-card overflow-hidden shadow-lg border border-rg-lightLavender min-h-[520px]">
        {{-- Left: Brand panel --}}
        <div class="relative hidden md:flex flex-col justify-center items-center p-10 bg-gradient-to-b from-rg-deepPurple to-rg-darkPlum text-white overflow-hidden">
            {{-- Background image --}}
            <img src="{{ asset('images/hero/hero-main.jpg') }}"
                 alt=""
                 class="absolute inset-0 w-full h-full object-cover opacity-20"
                 aria-hidden="true">
            <div class="relative z-10 text-center">
                <img src="{{ asset('images/branding/rg-logo-dark.svg') }}" alt="Rose Garden" class="h-14 w-auto mx-auto mb-6">
                <p class="font-script text-4xl text-rg-rosePink mb-3">{{ __('Hoş Geldiniz') }}</p>
                <p class="text-white/70 text-sm leading-relaxed max-w-xs">
                    {{ __('Hesabınıza giriş yaparak siparişlerinizi takip edin, favorilerinizi yönetin.') }}
                </p>
            </div>
        </div>
        {{-- Right: Form --}}
        <div class="bg-white p-8 md:p-10 flex flex-col justify-center">
            <div class="md:hidden mb-6 text-center">
                <img src="{{ asset('images/branding/rg-logo-light.svg') }}" alt="Rose Garden" class="h-12 w-auto mx-auto">
            </div>
            <h1 class="font-display text-2xl md:text-3xl font-semibold text-rg-darkText mb-1">{{ __('Giriş Yap') }}</h1>
            <p class="text-sm text-rg-grayText mb-6">{{ __('Hesabınız yok mu?') }} <a href="{{ route('register') }}" class="text-rg-purple hover:underline font-medium">{{ __('Kayıt Ol') }}</a></p>

            <form method="POST" action="{{ route('login.submit') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-rg-darkText mb-1.5">{{ __('E-posta') }}</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus
                           placeholder="ornek@email.com"
                           class="w-full border border-rg-lightLavender focus:border-rg-purple focus:ring-2 focus:ring-rg-purple/20 rounded-btn px-4 py-2.5 text-sm outline-none transition-all duration-200 @error('email') border-red-300 @enderror">
                    @error('email') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-rg-darkText mb-1.5">{{ __('Şifre') }}</label>
                    <input type="password" name="password" required
                           placeholder="••••••••"
                           class="w-full border border-rg-lightLavender focus:border-rg-purple focus:ring-2 focus:ring-rg-purple/20 rounded-btn px-4 py-2.5 text-sm outline-none transition-all duration-200">
                </div>
                @if (session('status'))
                    <div class="bg-green-50 border border-green-200 text-green-700 px-3 py-2 rounded-btn text-sm">{{ session('status') }}</div>
                @endif
                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 text-sm cursor-pointer">
                        <input type="checkbox" name="remember" value="1" class="accent-rg-purple">
                        <span class="text-rg-grayText">{{ __('Beni Hatırla') }}</span>
                    </label>
                    <a href="{{ route('password.request') }}" class="text-sm text-rg-purple hover:underline">{{ __('Şifremi Unuttum') }}</a>
                </div>
                <button type="submit"
                        class="w-full bg-rg-purple hover:bg-rg-darkPlum text-white font-semibold py-3 rounded-btn transition-colors duration-200">
                    {{ __('Giriş Yap') }}
                </button>
            </form>

            @if(config('services.google.client_id'))
                <div class="relative my-5">
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
                    {{ __('Google ile Giriş Yap') }}
                </a>
            @endif
        </div>
    </div>
</div>
@endsection
