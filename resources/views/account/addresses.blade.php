@extends('layouts.app')

@section('content')
    <h1 class="font-display text-3xl mb-6">Adreslerim</h1>

    <form method="POST" action="{{ route('account.addresses.store') }}" class="bg-white border border-rg-lightLavender rounded-card p-4 mb-6 grid grid-cols-1 md:grid-cols-2 gap-3">
        @csrf
        <input type="text" name="label" placeholder="Adres basligi (Ev, Is vb.)" class="w-full border rounded-btn px-3 py-2">
        <input type="text" name="recipient_name" placeholder="Alici Ad Soyad" class="w-full border rounded-btn px-3 py-2" required>
        <input type="text" name="recipient_phone" placeholder="Alici Telefon" class="w-full border rounded-btn px-3 py-2" required>
        <input type="text" name="district" placeholder="Ilce" class="w-full border rounded-btn px-3 py-2" required>
        <input type="text" name="city" value="Adiyaman" placeholder="Sehir" class="w-full border rounded-btn px-3 py-2" required>
        <input type="text" name="postal_code" placeholder="Posta Kodu" class="w-full border rounded-btn px-3 py-2">
        <textarea name="address_line" rows="2" placeholder="Acik adres" class="md:col-span-2 w-full border rounded-btn px-3 py-2" required></textarea>
        <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" name="is_default" value="1">
            Varsayilan adres yap
        </label>
        <div class="md:col-span-2">
            <button type="submit" class="bg-rg-purple text-white px-4 py-2 rounded-btn">Yeni Adres Ekle</button>
        </div>
    </form>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @forelse ($addresses as $address)
            <div class="bg-white border border-rg-lightLavender rounded-card p-4">
                <h3 class="font-semibold mb-2">
                    {{ $address->label ?: 'Adres' }}
                    @if ($address->is_default)
                        <span class="text-xs text-rg-purple">(Varsayilan)</span>
                    @endif
                </h3>
                <p class="text-sm text-rg-grayText">{{ $address->address_line }}</p>
                <p class="text-sm text-rg-grayText">{{ $address->district }} / {{ $address->city }}</p>

                <form method="POST" action="{{ route('account.addresses.update', ['address' => $address->id]) }}" class="mt-3 space-y-2">
                    @csrf
                    @method('PUT')
                    <input type="text" name="label" value="{{ $address->label }}" class="w-full border rounded-btn px-3 py-2 text-sm">
                    <input type="text" name="recipient_name" value="{{ $address->recipient_name }}" class="w-full border rounded-btn px-3 py-2 text-sm" required>
                    <input type="text" name="recipient_phone" value="{{ $address->recipient_phone }}" class="w-full border rounded-btn px-3 py-2 text-sm" required>
                    <input type="text" name="district" value="{{ $address->district }}" class="w-full border rounded-btn px-3 py-2 text-sm" required>
                    <input type="text" name="city" value="{{ $address->city }}" class="w-full border rounded-btn px-3 py-2 text-sm" required>
                    <textarea name="address_line" rows="2" class="w-full border rounded-btn px-3 py-2 text-sm" required>{{ $address->address_line }}</textarea>
                    <label class="flex items-center gap-2 text-xs">
                        <input type="checkbox" name="is_default" value="1" @checked($address->is_default)>
                        Varsayilan adres
                    </label>
                    <button type="submit" class="text-sm border border-rg-purple text-rg-purple px-3 py-1 rounded-btn">Duzenle</button>
                </form>

                <div class="mt-2 flex gap-2">
                    <form method="POST" action="{{ route('account.addresses.default', ['address' => $address->id]) }}">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="text-xs text-rg-purple underline">Varsayilan yap</button>
                    </form>
                    <form method="POST" action="{{ route('account.addresses.delete', ['address' => $address->id]) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-xs text-red-600 underline">Sil</button>
                    </form>
                </div>
            </div>
        @empty
            <p class="text-sm text-rg-grayText">Kayitli adres bulunamadi.</p>
        @endforelse
    </div>
@endsection
