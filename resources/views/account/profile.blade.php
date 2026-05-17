@extends('layouts.account')

@section('account')
    <header class="mb-8">
        <h1 class="font-display text-2xl font-semibold tracking-tight text-rg-deepPurple dark:text-white md:text-3xl">{{ __('Profilim') }}</h1>
        <p class="mt-2 text-sm text-rg-grayText dark:text-white/78">{{ __('Kişisel bilgilerinizi güncelleyin.') }}</p>
        <div class="mt-4 flex flex-wrap gap-3 text-sm font-semibold">
            <a href="{{ \App\Support\StorefrontLocale::route('password.request') }}" class="text-rg-purple hover:underline dark:text-rg-lavender">{{ __('Şifre sıfırla') }}</a>
            <a href="{{ \App\Support\StorefrontLocale::route('account.dashboard') }}" class="text-rg-deepPurple hover:underline dark:text-white">{{ __('Hesap özetine dön') }}</a>
        </div>
    </header>

    @if (session('status'))
        <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-500/30 dark:bg-emerald-950/40 dark:text-emerald-200">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ \App\Support\StorefrontLocale::route('account.profile.update') }}" class="max-w-3xl rounded-2xl border border-rg-lightLavender bg-white p-6 shadow-sm dark:border-white/10 dark:bg-rg-deepPurple/40 md:p-8">
        @csrf
        @method('PUT')

        <div class="grid gap-5 sm:grid-cols-2">
            <x-auth-input :label="__('Ad')" name="first_name" type="text" :required="true" :value="old('first_name', $firstName)" autocomplete="given-name" />
            <x-auth-input :label="__('Soyad')" name="last_name" type="text" :required="true" :value="old('last_name', $lastName)" autocomplete="family-name" />
        </div>

        <div class="mt-5 grid gap-5 sm:grid-cols-2">
            <x-auth-input :label="__('E-posta')" name="email" type="email" :required="true" :value="old('email', $user->email)" autocomplete="email" />
            <x-auth-input :label="__('Telefon')" name="phone" type="text" :value="old('phone', $user->phone)" placeholder="05XX XXX XX XX" autocomplete="tel" />
        </div>

        <div class="mt-6 rounded-xl border border-rg-lightLavender/80 bg-rg-cream/50 p-4 dark:border-white/10 dark:bg-white/10">
            <label class="flex cursor-pointer items-start gap-3 text-sm text-rg-grayText dark:text-white/80">
                <input type="hidden" name="marketing_consent" value="0">
                <input type="checkbox" name="marketing_consent" value="1" @checked(old('marketing_consent', $user->marketing_consent)) class="mt-0.5 h-4 w-4 rounded border-rg-lightLavender text-rg-purple focus:ring-rg-purple dark:border-white/25">
                <span>{{ __('Kampanya ve fırsat e-postalarına izin veriyorum.') }}</span>
            </label>
        </div>

        <button type="submit" class="mt-8 rounded-xl bg-rg-purple px-8 py-3.5 text-sm font-semibold text-white shadow-md transition hover:bg-rg-darkPlum hover:shadow-lg focus:outline-none focus-visible:ring-2 focus-visible:ring-rg-purple focus-visible:ring-offset-2 dark:focus-visible:ring-offset-rg-deepPurple">
            {{ __('Kaydet') }}
        </button>
    </form>
@endsection
