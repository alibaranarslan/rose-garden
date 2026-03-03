@extends('layouts.app')

@section('content')
    <h1 class="font-display text-3xl mb-6">Siparislerim</h1>
    <div class="bg-white border border-rg-lightLavender rounded-card overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-rg-lightLavender/30">
                <tr>
                    <th class="text-left px-4 py-3">Siparis No</th>
                    <th class="text-left px-4 py-3">Tarih</th>
                    <th class="text-left px-4 py-3">Durum</th>
                    <th class="text-left px-4 py-3">Toplam</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($orders as $order)
                    <tr class="border-t">
                        <td class="px-4 py-3"><a href="{{ route('account.order.show', ['orderNumber' => $order->order_number]) }}" class="hover:text-rg-purple">{{ $order->order_number }}</a></td>
                        <td class="px-4 py-3">{{ $order->created_at->format('d.m.Y') }}</td>
                        <td class="px-4 py-3">{{ $order->status }}</td>
                        <td class="px-4 py-3">₺ {{ number_format($order->total, 2, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-6">{{ $orders->links() }}</div>
@endsection
