@php
    $metaTitle = __('Şifremi unuttum');
@endphp
@extends('layouts.app')

@section('content')
    <x-auth-split-layout :title="__('Şifremi unuttum')">
        <x-slot name="hero">
            <div class="text-center">
                <x-site-logo variant="dark" type="lockup" placement="auth_dark" class="mb-6" />
                <p class="font-script text-3xl text-rg-rosePink md:text-[2.1rem]">{{ __('Güvenli sıfırlama') }}</p>
                <p class="mx-auto mt-4 max-w-xs text-sm text-white/75">{{ __('E-postanıza tek kullanımlık sıfırlama bağlantısı göndeririz.') }}</p>
            </div>
        </x-slot>

        @if (session('status'))
            <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-500/30 dark:bg-emerald-950/40 dark:text-emerald-200">
                {{ session('status') }}
            </div>
        @endif

        <p class="mb-6 text-sm leading-relaxed text-rg-grayText dark:text-white/82">
            {{ __('E-posta adresinizi girin; sıfırlama bağlantısını gönderelim.') }}
        </p>

        <form method="POST" action="{{ \App\Support\StorefrontLocale::route('password.email') }}" class="space-y-5">
            @csrf
            <x-auth-input :label="__('E-posta')" name="email" type="email" :required="true" :autofocus="true" autocomplete="email" />

            <button type="submit" class="w-full rounded-xl bg-rg-purple py-3.5 text-sm font-semibold text-white shadow-md transition hover:bg-rg-darkPlum hover:shadow-lg focus:outline-none focus-visible:ring-2 focus-visible:ring-rg-purple focus-visible:ring-offset-2 dark:focus-visible:ring-offset-rg-deepPurple">
                {{ __('Sıfırlama bağlantısı gönder') }}
            </button>
        </form>

        <p class="mt-6 text-center text-sm">
            <a href="{{ \App\Support\StorefrontLocale::route('login') }}" class="font-semibold text-rg-purple hover:underline dark:text-rg-lavender">{{ __('Giriş sayfasına dön') }}</a>
        </p>
    </x-auth-split-layout>
@endsection
