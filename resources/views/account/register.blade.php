@php
    $metaTitle = __('Kayıt ol');
@endphp
@extends('layouts.app')

@section('content')
    <x-auth-split-layout :title="__('Kayıt ol')">
        <x-slot name="hero">
            <div class="text-center">
                <x-site-logo variant="dark" type="lockup" placement="auth_dark" class="mb-6" />
                <p class="font-script text-3xl text-rg-rosePink">{{ __('Aramıza katılın') }}</p>
                <ul class="mx-auto mt-5 max-w-xs space-y-2 text-left text-sm text-white/80">
                    @foreach ([
                        __('Siparişlerinizi kolayca takip edin'),
                        __('Favori ürünlerinizi kaydedin'),
                        __('Hızlı ödeme için adres kaydedin'),
                    ] as $benefit)
                        <li class="flex items-start gap-2">
                            <svg class="mt-0.5 h-4 w-4 shrink-0 text-rg-rosePink" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>{{ $benefit }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </x-slot>

        <p class="mb-6 text-sm text-rg-grayText dark:text-white/82">
            {{ __('Zaten hesabınız var mı?') }}
            <a href="{{ \App\Support\StorefrontLocale::route('login') }}" class="font-semibold text-rg-purple hover:underline dark:text-rg-lavender">{{ __('Giriş yap') }}</a>
        </p>

        <form method="POST" action="{{ \App\Support\StorefrontLocale::route('register.submit') }}" class="space-y-5">
            @csrf
            <x-auth-input :label="__('Ad Soyad')" name="name" type="text" :required="true" :value="old('name')" :placeholder="__('Adınız Soyadınız')" autocomplete="name" />

            <div class="grid gap-5 sm:grid-cols-2">
                <x-auth-input :label="__('E-posta')" name="email" type="email" :required="true" placeholder="ornek@email.com" autocomplete="email" />
                <x-auth-input :label="__('Telefon')" name="phone" type="text" :value="old('phone')" placeholder="05XX XXX XX XX" autocomplete="tel" />
            </div>

            <div class="grid gap-5 sm:grid-cols-2">
                <x-auth-input :label="__('Şifre')" name="password" type="password" :required="true" placeholder="••••••••" autocomplete="new-password" />
                <x-auth-input :label="__('Şifre tekrar')" name="password_confirmation" type="password" :required="true" placeholder="••••••••" autocomplete="new-password" />
            </div>

            <div class="space-y-3 rounded-xl border {{ $errors->has('kvkk_acknowledged') ? 'border-red-300 bg-red-50/80 dark:border-red-400/60 dark:bg-red-950/25' : 'border-rg-lightLavender/80 bg-rg-cream/50 dark:border-white/10 dark:bg-white/10' }} p-4">
                <label class="flex cursor-pointer items-start gap-3 text-sm text-rg-grayText dark:text-white/75">
                    <input
                        type="checkbox"
                        name="kvkk_acknowledged"
                        value="1"
                        {{ old('kvkk_acknowledged') ? 'checked' : '' }}
                        class="mt-0.5 h-4 w-4 rounded border-rg-lightLavender text-rg-purple focus:ring-rg-purple dark:border-white/25"
                        required
                        @error('kvkk_acknowledged') aria-invalid="true" aria-describedby="kvkk-acknowledged-error" @enderror
                    >
                    <span class="leading-relaxed">
                        <a href="{{ \App\Support\StorefrontLocale::route('page.show', ['slug' => 'kvkk-aydinlatma']) }}" class="font-medium text-rg-purple hover:underline dark:text-rg-lavender" target="_blank" rel="noopener">{{ __('KVKK Aydınlatma Metni') }}</a>{{ __("'ni okudum ve kabul ediyorum.") }}
                    </span>
                </label>
                @error('kvkk_acknowledged')
                    <p id="kvkk-acknowledged-error" class="text-xs font-medium text-red-600 dark:text-red-300">{{ $message }}</p>
                @enderror
                <label class="flex cursor-pointer items-start gap-3 text-sm text-rg-grayText dark:text-white/75">
                    <input type="checkbox" name="marketing_consent" value="1" {{ old('marketing_consent') ? 'checked' : '' }} class="mt-0.5 h-4 w-4 rounded border-rg-lightLavender text-rg-purple focus:ring-rg-purple dark:border-white/25">
                    <span>{{ __('Kampanya ve fırsatlardan haberdar olmak istiyorum.') }}</span>
                </label>
            </div>

            <button type="submit" class="w-full rounded-xl bg-rg-purple py-3.5 text-sm font-semibold text-white shadow-md transition hover:bg-rg-darkPlum hover:shadow-lg focus:outline-none focus-visible:ring-2 focus-visible:ring-rg-purple focus-visible:ring-offset-2 dark:focus-visible:ring-offset-rg-deepPurple">
                {{ __('Hesap oluştur') }}
            </button>
        </form>

        @if(config('services.google.client_id'))
            <div class="relative my-6">
                <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-rg-lightLavender dark:border-white/10"></div></div>
                <div class="relative flex justify-center"><span class="bg-white px-3 text-xs font-medium text-rg-grayText dark:bg-rg-deepPurple dark:text-white/72">{{ __('veya') }}</span></div>
            </div>

            <a href="{{ \App\Support\StorefrontLocale::route('auth.google') }}"
               class="inline-flex w-full items-center justify-center gap-3 rounded-xl border border-gray-200 bg-white px-4 py-3.5 text-sm font-semibold text-gray-800 shadow-sm transition hover:bg-gray-50 hover:shadow-md dark:border-white/20 dark:bg-white dark:text-gray-900 dark:hover:bg-gray-100">
                <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                    <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                    <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                    <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                </svg>
                {{ __('Google ile kayıt ol') }}
            </a>
        @endif
    </x-auth-split-layout>
@endsection
