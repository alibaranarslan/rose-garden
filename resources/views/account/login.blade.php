@php
    $metaTitle = __('Giriş yap');
@endphp
@extends('layouts.app')

@section('content')
    <x-auth-split-layout :title="__('Giriş yap')">
        <x-slot name="hero">
            <div class="text-center">
                <x-site-logo variant="dark" type="lockup" placement="auth_dark" class="mb-6" />
                <p class="font-script text-3xl text-rg-rosePink">{{ __('Hoş geldiniz') }}</p>
                <p class="mx-auto mt-4 max-w-xs text-sm leading-relaxed text-white/75">
                    {{ __('Siparişlerinizi, adreslerinizi ve takip bilgilerinizi tek yerden yönetin.') }}
                </p>
            </div>
        </x-slot>

        <p class="mb-6 text-sm text-rg-grayText dark:text-white/82">
            {{ __('Hesabınız yok mu?') }}
            <a href="{{ \App\Support\StorefrontLocale::route('register') }}" class="font-semibold text-rg-purple hover:underline dark:text-rg-lavender">{{ __('Kayıt ol') }}</a>
        </p>

        <form method="POST" action="{{ \App\Support\StorefrontLocale::route('login.submit') }}" class="space-y-5">
            @csrf
            <x-auth-input :label="__('E-posta')" name="email" type="email" :required="true" :autofocus="true" placeholder="ornek@email.com" autocomplete="email" />
            <x-auth-input :label="__('Şifre')" name="password" type="password" :required="true" placeholder="••••••••" autocomplete="current-password" />

            @if (session('status'))
                <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-500/30 dark:bg-emerald-950/40 dark:text-emerald-200">
                    {{ session('status') }}
                </div>
            @endif

            <div class="flex flex-col items-start gap-3 sm:flex-row sm:items-center sm:justify-between">
                <label class="flex cursor-pointer items-center gap-2 text-sm text-rg-grayText dark:text-white/86">
                    <input type="checkbox" name="remember" value="1" class="h-4 w-4 rounded border-rg-lightLavender text-rg-purple focus:ring-rg-purple dark:border-white/25">
                    {{ __('Beni hatırla') }}
                </label>
                <a href="{{ \App\Support\StorefrontLocale::route('password.request') }}" class="text-sm font-semibold text-rg-purple hover:underline dark:text-rg-lavender">{{ __('Şifremi unuttum') }}</a>
            </div>

            <button type="submit" class="w-full rounded-xl bg-rg-purple py-3.5 text-sm font-semibold text-white shadow-md transition hover:bg-rg-darkPlum hover:shadow-lg focus:outline-none focus-visible:ring-2 focus-visible:ring-rg-purple focus-visible:ring-offset-2 dark:focus-visible:ring-offset-rg-deepPurple">
                {{ __('Giriş yap') }}
            </button>
        </form>

        <div class="mt-5 rounded-[1.2rem] border border-rg-lightLavender/80 bg-rg-cream/45 px-4 py-3 text-sm leading-relaxed text-rg-grayText dark:border-white/10 dark:bg-white/8 dark:text-white/80">
            <p>{{ __('Sipariş takibi ve destek bağlantıları burada hazır.') }}</p>
            <div class="mt-3 flex flex-wrap gap-4 text-sm font-semibold">
                <a href="{{ \App\Support\StorefrontLocale::route('order.track') }}" class="text-rg-deepPurple hover:text-rg-purple dark:text-white dark:hover:text-rg-lavender">{{ __('Sipariş takibi') }}</a>
                <a href="{{ \App\Support\StorefrontLocale::route('contact') }}" class="text-rg-deepPurple hover:text-rg-purple dark:text-white dark:hover:text-rg-lavender">{{ __('Destek') }}</a>
            </div>
        </div>

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
                {{ __('Google ile giriş yap') }}
            </a>
        @endif
    </x-auth-split-layout>
@endsection
