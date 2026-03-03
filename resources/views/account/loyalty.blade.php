@extends('layouts.app')

@section('content')
    <h1 class="font-display text-3xl mb-6">Paracicek Puanlarim</h1>
    <div class="bg-gradient-to-r from-rg-purple to-rg-darkPlum text-white rounded-card p-6 mb-6">
        <p class="text-sm">Toplam Bakiye</p>
        <p class="text-3xl font-bold">{{ number_format($loyaltyPoint?->balance ?? 0, 0, ',', '.') }} Puan</p>
    </div>
    <div class="bg-white border border-rg-lightLavender rounded-card p-5 space-y-2">
        @forelse ($transactions as $transaction)
            <p class="text-sm">{{ $transaction->created_at?->format('d.m.Y H:i') }} — {{ $transaction->type }}: {{ number_format($transaction->amount, 2, ',', '.') }}</p>
        @empty
            <p class="text-sm text-rg-grayText">Hareket bulunamadi.</p>
        @endforelse
    </div>
@endsection
