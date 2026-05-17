@php
    $metaTitle = __('Şifre sıfırla');
@endphp
@extends('layouts.app')

@section('content')
    <x-auth-split-layout :title="__('Yeni şifre belirleyin')">
        <x-slot name="hero">
            <div class="text-center">
                <x-site-logo variant="dark" type="lockup" placement="auth_dark" class="mb-6" />
                <p class="font-script text-3xl text-rg-rosePink md:text-[2.1rem]">{{ __('Yeni başlangıç') }}</p>
                <p class="mx-auto mt-4 max-w-xs text-sm text-white/75">{{ __('Hesabınız için güçlü ve yeni bir şifre belirleyin.') }}</p>
            </div>
        </x-slot>

        <form method="POST" action="{{ \App\Support\StorefrontLocale::route('password.update') }}" class="space-y-5">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <x-auth-input :label="__('E-posta')" name="email" type="email" :required="true" :value="old('email', $email)" autocomplete="email" />
            <x-auth-input :label="__('Yeni şifre')" name="password" type="password" :required="true" autocomplete="new-password" />
            <x-auth-input :label="__('Yeni şifre tekrar')" name="password_confirmation" type="password" :required="true" autocomplete="new-password" />

            <button type="submit" class="w-full rounded-xl bg-rg-purple py-3.5 text-sm font-semibold text-white shadow-md transition hover:bg-rg-darkPlum hover:shadow-lg focus:outline-none focus-visible:ring-2 focus-visible:ring-rg-purple focus-visible:ring-offset-2 dark:focus-visible:ring-offset-rg-deepPurple">
                {{ __('Şifreyi sıfırla') }}
            </button>
        </form>
    </x-auth-split-layout>
@endsection
