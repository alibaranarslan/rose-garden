@extends('layouts.app')

@section('content')
    <h1 class="font-display text-3xl mb-6">Hesabim</h1>
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <a href="{{ route('account.orders') }}" class="bg-white border border-rg-lightLavender rounded-card p-4">Siparislerim ({{ $latestOrders->count() }})</a>
        <a href="{{ route('account.addresses') }}" class="bg-white border border-rg-lightLavender rounded-card p-4">Adreslerim</a>
        <a href="{{ route('account.favorites') }}" class="bg-white border border-rg-lightLavender rounded-card p-4">Favorilerim</a>
        <a href="{{ route('account.loyalty') }}" class="bg-white border border-rg-lightLavender rounded-card p-4">Puanlarim ({{ number_format($loyaltyPoint?->balance ?? 0, 0, ',', '.') }})</a>
        <a href="{{ route('account.kvkk') }}" class="bg-white border border-rg-lightLavender rounded-card p-4">KVKK ve Gizlilik</a>
    </div>
@endsection
