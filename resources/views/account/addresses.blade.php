@extends('layouts.account')

@php
    $inp = 'w-full rounded-xl border border-rg-lightLavender bg-white px-4 py-3 text-sm text-rg-darkText shadow-sm outline-none transition focus:border-rg-purple focus:ring-2 focus:ring-rg-purple/45 dark:border-white/15 dark:bg-rg-deepPurple/40 dark:text-white dark:focus:border-rg-lavender dark:focus:ring-rg-lavender/35';
    $lbl = 'mb-1.5 block text-xs font-semibold uppercase tracking-wide text-rg-midPurple dark:text-rg-lavender';
@endphp

@section('account')
    <header class="mb-8">
        <h1 class="font-display text-2xl font-semibold tracking-tight text-rg-deepPurple dark:text-white md:text-3xl">{{ __('Adreslerim') }}</h1>
        <p class="mt-2 text-sm text-rg-grayText dark:text-white/78">{{ __('Teslimat için kayıtlı adresleriniz.') }}</p>
    </header>

    @if (session('status'))
        <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-500/30 dark:bg-emerald-950/40 dark:text-emerald-200">
            {{ session('status') }}
        </div>
    @endif

    <section class="mb-10 rounded-2xl border border-rg-lightLavender bg-white p-6 shadow-sm dark:border-white/10 dark:bg-rg-deepPurple/40 md:p-8">
        <h2 class="font-display text-lg font-semibold text-rg-deepPurple dark:text-white">{{ __('Yeni adres ekle') }}</h2>
        <form method="POST" action="{{ \App\Support\StorefrontLocale::route('account.addresses.store') }}" class="mt-6 grid gap-4 md:grid-cols-2">
            @csrf
            <div>
                <label for="new-label" class="{{ $lbl }}">{{ __('Adres başlığı') }}</label>
                <input id="new-label" type="text" name="label" value="{{ old('label') }}" placeholder="{{ __('Ev, iş…') }}" class="{{ $inp }}">
            </div>
            <div class="hidden md:block"></div>
            <div>
                <label for="new-recipient_name" class="{{ $lbl }}">{{ __('Alıcı ad soyad') }}</label>
                <input id="new-recipient_name" type="text" name="recipient_name" value="{{ old('recipient_name') }}" required class="{{ $inp }}">
            </div>
            <div>
                <label for="new-recipient_phone" class="{{ $lbl }}">{{ __('Alıcı telefon') }}</label>
                <input id="new-recipient_phone" type="text" name="recipient_phone" value="{{ old('recipient_phone') }}" required class="{{ $inp }}">
            </div>
            <div>
                <label for="new-district" class="{{ $lbl }}">{{ __('İlçe') }}</label>
                <input id="new-district" type="text" name="district" value="{{ old('district') }}" required class="{{ $inp }}">
            </div>
            <div>
                <label for="new-city" class="{{ $lbl }}">{{ __('İl') }}</label>
                <input id="new-city" type="text" name="city" value="{{ old('city') }}" required class="{{ $inp }}" placeholder="{{ __('İl') }}">
            </div>
            <div>
                <label for="new-postal_code" class="{{ $lbl }}">{{ __('Posta kodu') }}</label>
                <input id="new-postal_code" type="text" name="postal_code" value="{{ old('postal_code') }}" class="{{ $inp }}">
            </div>
            <div class="md:col-span-2">
                <label for="new-address_line" class="{{ $lbl }}">{{ __('Açık adres') }}</label>
                <textarea id="new-address_line" name="address_line" rows="3" required class="{{ $inp }} resize-y">{{ old('address_line') }}</textarea>
            </div>
            <div class="md:col-span-2">
                <label class="flex cursor-pointer items-center gap-2 text-sm text-rg-grayText dark:text-white/75">
                    <input type="checkbox" name="is_default" value="1" class="h-4 w-4 rounded border-rg-lightLavender text-rg-purple focus:ring-rg-purple dark:border-white/25">
                    {{ __('Varsayılan adres yap') }}
                </label>
            </div>
            <div class="md:col-span-2">
                <button type="submit" class="rounded-xl bg-rg-purple px-6 py-3 text-sm font-semibold text-white shadow-md transition hover:bg-rg-darkPlum hover:shadow-lg">
                    {{ __('Adres ekle') }}
                </button>
            </div>
        </form>
    </section>

    <div class="grid gap-6 lg:grid-cols-2">
        @forelse ($addresses as $address)
            <article class="rounded-2xl border border-rg-lightLavender bg-white p-5 shadow-sm dark:border-white/10 dark:bg-rg-deepPurple/40 md:p-6">
                <div class="flex flex-wrap items-start justify-between gap-2 border-b border-rg-lightLavender/80 pb-3 dark:border-white/10">
                    <h3 class="font-display text-lg font-semibold text-rg-deepPurple dark:text-white">
                        {{ $address->label ?: __('Adres') }}
                    </h3>
                    @if ($address->is_default)
                        <span class="rounded-full bg-rg-lightLavender px-2.5 py-0.5 text-xs font-bold text-rg-purple dark:bg-white/15 dark:text-rg-lavender">{{ __('Varsayılan') }}</span>
                    @endif
                </div>
                <p class="mt-3 text-sm text-rg-darkText dark:text-white/90">{{ $address->address_line }}</p>
                <p class="mt-1 text-sm text-rg-grayText dark:text-white/78">{{ $address->district }} / {{ $address->city }}</p>

                <form method="POST" action="{{ \App\Support\StorefrontLocale::route('account.addresses.update', ['address' => $address->id]) }}" class="mt-5 grid gap-3 md:grid-cols-2">
                    @csrf
                    @method('PUT')
                    <div class="md:col-span-2">
                        <label class="{{ $lbl }}">{{ __('Başlık') }}</label>
                        <input type="text" name="label" value="{{ $address->label }}" class="{{ $inp }}">
                    </div>
                    <div>
                        <label class="{{ $lbl }}">{{ __('Alıcı') }}</label>
                        <input type="text" name="recipient_name" value="{{ $address->recipient_name }}" required class="{{ $inp }}">
                    </div>
                    <div>
                        <label class="{{ $lbl }}">{{ __('Telefon') }}</label>
                        <input type="text" name="recipient_phone" value="{{ $address->recipient_phone }}" required class="{{ $inp }}">
                    </div>
                    <div>
                        <label class="{{ $lbl }}">{{ __('İlçe') }}</label>
                        <input type="text" name="district" value="{{ $address->district }}" required class="{{ $inp }}">
                    </div>
                    <div>
                        <label class="{{ $lbl }}">{{ __('İl') }}</label>
                        <input type="text" name="city" value="{{ $address->city }}" required class="{{ $inp }}">
                    </div>
                    <div class="md:col-span-2">
                        <label class="{{ $lbl }}">{{ __('Açık adres') }}</label>
                        <textarea name="address_line" rows="2" required class="{{ $inp }} resize-y">{{ $address->address_line }}</textarea>
                    </div>
                    <div class="md:col-span-2">
                        <label class="flex cursor-pointer items-center gap-2 text-sm text-rg-grayText dark:text-white/75">
                            <input type="checkbox" name="is_default" value="1" @checked($address->is_default) class="h-4 w-4 rounded border-rg-lightLavender text-rg-purple focus:ring-rg-purple dark:border-white/25">
                            {{ __('Varsayılan adres') }}
                        </label>
                    </div>
                    <div class="flex flex-wrap gap-2 md:col-span-2">
                        <button type="submit" class="rounded-xl border-2 border-rg-purple bg-white px-4 py-2.5 text-sm font-semibold text-rg-purple transition hover:bg-rg-lightLavender/50 dark:bg-rg-deepPurple/60 dark:text-rg-lavender">
                            {{ __('Güncelle') }}
                        </button>
                    </div>
                </form>

                <div class="mt-4 flex flex-wrap gap-4 border-t border-rg-lightLavender/60 pt-4 dark:border-white/10">
                    <form method="POST" action="{{ \App\Support\StorefrontLocale::route('account.addresses.default', ['address' => $address->id]) }}">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="text-sm font-semibold text-rg-purple hover:underline dark:text-rg-lavender">{{ __('Varsayılan yap') }}</button>
                    </form>
                    <form
                        method="POST"
                        action="{{ \App\Support\StorefrontLocale::route('account.addresses.delete', ['address' => $address->id]) }}"
                        data-confirm-message="{{ __('Bu adresi silmek istediğinize emin misiniz?') }}"
                        onsubmit="return confirm(this.dataset.confirmMessage);"
                    >
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-sm font-semibold text-red-600 hover:underline dark:text-red-400">{{ __('Sil') }}</button>
                    </form>
                </div>
            </article>
        @empty
            <p class="text-sm text-rg-grayText dark:text-white/82 lg:col-span-2">{{ __('Kayıtlı adres bulunamadı.') }}</p>
        @endforelse
    </div>
@endsection
