<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Sipariş #{{ $order->order_number }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 13px; margin: 20px; }
        h1 { font-size: 18px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ccc; padding: 6px 10px; text-align: left; }
        th { background: #f0f0f0; }
        .section { margin-top: 20px; }
        @media print { button { display: none; } }
    </style>
</head>
<body>
    <h1>Sipariş #{{ $order->order_number }}</h1>
    <p>Tarih: {{ $order->created_at->format('d.m.Y H:i') }}</p>

    <div class="section">
        <h2>Müşteri Bilgileri</h2>
        <p>Ad Soyad: {{ $order->sender_name }}</p>
        <p>Telefon: {{ $order->sender_phone }}</p>
        <p>E-posta: {{ $order->sender_email }}</p>
    </div>

    <div class="section">
        <h2>Teslimat Bilgileri</h2>
        <p>Alıcı: {{ $order->recipient_name }}</p>
        <p>Telefon: {{ $order->recipient_phone }}</p>
        <p>Adres: {{ $order->recipient_address }}</p>
        <p>Teslimat Tarihi: {{ $order->delivery_date ? \Carbon\Carbon::parse($order->delivery_date)->format('d.m.Y') : '-' }}</p>
        @if($order->delivery_note)
        <p>Not: {{ $order->delivery_note }}</p>
        @endif
    </div>

    <div class="section">
        <h2>Sipariş Kalemleri</h2>
        <table>
            <tr>
                <th>Ürün</th>
                <th>Adet</th>
                <th>Fiyat</th>
                <th>Toplam</th>
            </tr>
            @foreach($order->items ?? [] as $item)
            <tr>
                <td>{{ $item->product_name ?? $item->name }}</td>
                <td>{{ $item->quantity }}</td>
                <td>₺{{ number_format($item->unit_price, 2) }}</td>
                <td>₺{{ number_format($item->quantity * $item->unit_price, 2) }}</td>
            </tr>
            @endforeach
        </table>
    </div>

    <div class="section">
        <table style="width: 300px; margin-left: auto;">
            <tr><td>Ara Toplam</td><td>₺{{ number_format($order->subtotal, 2) }}</td></tr>
            <tr><td>Teslimat</td><td>₺{{ number_format($order->delivery_fee, 2) }}</td></tr>
            @if($order->discount_amount > 0)
            <tr><td>İndirim</td><td>-₺{{ number_format($order->discount_amount, 2) }}</td></tr>
            @endif
            <tr><th>Genel Toplam</th><th>₺{{ number_format($order->total, 2) }}</th></tr>
        </table>
    </div>

    <button onclick="window.print()" style="margin-top:20px; padding: 8px 20px;">Yazdır</button>
</body>
</html>
