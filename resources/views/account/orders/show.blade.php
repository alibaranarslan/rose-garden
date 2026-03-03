@extends('layouts.app')

@section('content')
    <h1 class="font-display text-3xl mb-4">Siparis Detayi</h1>
    <div class="bg-white border border-rg-lightLavender rounded-card p-5">
        <p class="mb-2">Siparis No: {{ $order->order_number }}</p>
        <p class="mb-2">Durum: {{ $order->status }}</p>
        <p class="mb-4">Toplam: ₺ {{ number_format($order->total, 2, ',', '.') }}</p>
        <div class="space-y-2">
            @foreach ($order->items as $item)
                <p class="text-sm">{{ $item->product_name }} x{{ $item->quantity }} — ₺ {{ number_format($item->total_price, 2, ',', '.') }}</p>
            @endforeach
        </div>
        <form method="POST" action="{{ route('account.order.reorder', ['orderNumber' => $order->order_number]) }}" class="mt-4">
            @csrf
            <button type="submit" class="bg-rg-purple text-white px-4 py-2 rounded-btn">Tekrar Siparis Ver</button>
        </form>
    </div>
@endsection
