@extends('layouts.app')

@section('content')
    <section class="max-w-2xl mx-auto bg-white border border-rg-lightLavender rounded-card p-6">
        <h1 class="font-display text-3xl mb-4">Siparis Takip</h1>
        <form method="POST" action="{{ route('order.track.submit') }}" class="flex gap-2 mb-5">
            @csrf
            <input type="text" name="order_number" placeholder="Siparis numarasi" class="flex-1 border rounded-btn px-3 py-2">
            <button type="submit" class="bg-rg-purple text-white px-4 py-2 rounded-btn">Sorgula</button>
        </form>
        @if (isset($order) && $order)
            <p class="text-sm">Siparis No: {{ $order->order_number }}</p>
            <p class="text-sm">Durum: {{ $order->status }}</p>
        @else
            <p class="text-sm text-rg-grayText">Durum ciktisi burada gorunecek.</p>
        @endif
    </section>
@endsection
