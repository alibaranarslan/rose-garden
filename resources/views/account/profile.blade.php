@extends('layouts.app')

@section('content')
    <h1 class="font-display text-3xl mb-6">Profilim</h1>
    <form method="POST" action="{{ route('account.profile.update') }}" class="max-w-xl bg-white border border-rg-lightLavender rounded-card p-6 space-y-4">
        @csrf
        @method('PUT')
        <input type="text" name="name" value="{{ old('name', $user->name) }}" placeholder="Ad Soyad" class="w-full border rounded-btn px-3 py-2">
        <input type="email" name="email" value="{{ old('email', $user->email) }}" placeholder="Email" class="w-full border rounded-btn px-3 py-2">
        <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="Telefon" class="w-full border rounded-btn px-3 py-2">
        <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="marketing_consent" value="1" @checked($user->marketing_consent)> Pazarlama izni veriyorum</label>
        <button type="submit" class="bg-rg-purple text-white px-4 py-2 rounded-btn">Guncelle</button>
    </form>
@endsection
