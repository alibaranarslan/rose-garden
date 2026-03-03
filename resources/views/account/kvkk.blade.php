@extends('layouts.app')

@section('content')
    <h1 class="font-display text-3xl mb-6">KVKK ve Gizlilik</h1>

    @if (session('status'))
        <p class="mb-4 rounded bg-green-50 text-green-700 px-3 py-2 text-sm">{{ session('status') }}</p>
    @endif

    @if ($errors->any())
        <div class="mb-4 rounded bg-red-50 text-red-700 px-3 py-2 text-sm">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <p class="text-sm text-rg-grayText mb-6">
        6698 sayili KVKK kapsaminda kisisel verilerinizle ilgili haklarinizi bu sayfadan kullanabilirsiniz.
    </p>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
        <div class="lg:col-span-2 bg-white border border-rg-lightLavender rounded-card p-4">
            <h2 class="font-semibold text-lg mb-3">Kisisel Veri Ozeti</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm">
                <p><strong>Ad Soyad:</strong> {{ $summary['name'] }}</p>
                <p><strong>E-posta:</strong> {{ $summary['email'] }}</p>
                <p><strong>Telefon:</strong> {{ $summary['phone'] ?: '-' }}</p>
                <p><strong>Kayit Tarihi:</strong> {{ $summary['registered_at']?->format('d.m.Y H:i') }}</p>
                <p><strong>KVKK Onayi:</strong> {{ $summary['kvkk_accepted_at']?->format('d.m.Y H:i') ?: 'Kayit bulunamadi' }}</p>
                <p><strong>Pazarlama Izni:</strong> {{ $summary['marketing_consent'] ? 'Onayli' : 'Reddedilmis' }}</p>
                <p><strong>Adres Sayisi:</strong> {{ $summary['address_count'] }}</p>
                <p><strong>Siparis Sayisi:</strong> {{ $summary['order_count'] }}</p>
                <p><strong>Puan Bakiyesi:</strong> {{ number_format($summary['loyalty_balance'], 0, ',', '.') }}</p>
            </div>
        </div>

        <div class="bg-white border border-rg-lightLavender rounded-card p-4">
            <h2 class="font-semibold text-lg mb-3">Pazarlama Izni</h2>
            <p class="text-sm mb-4">
                Mevcut durum:
                <strong>{{ $summary['marketing_consent'] ? 'Onayli' : 'Reddedilmis' }}</strong>
            </p>
            <form method="POST" action="{{ route('account.kvkk.withdraw-marketing') }}" onsubmit="return confirm('Pazarlama iznini geri cekmek istiyor musunuz?');">
                @csrf
                <button type="submit" class="w-full bg-rg-purple text-white px-4 py-2 rounded-btn text-sm">
                    Pazarlama Iznini Geri Cek
                </button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
        <div class="lg:col-span-2 bg-white border border-rg-lightLavender rounded-card p-4">
            <h2 class="font-semibold text-lg mb-3">Veri Talebi Olustur</h2>
            <form method="POST" action="{{ route('account.kvkk.request') }}" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-sm mb-1">Talep Turu</label>
                    <select name="type" class="w-full border border-rg-lightLavender rounded-btn px-3 py-2 text-sm">
                        <option value="view">Verilerimi Goruntule</option>
                        <option value="export">Verilerimi Disa Aktar</option>
                        <option value="delete">Hesabimi ve Verilerimi Sil</option>
                        <option value="consent_withdraw">Izin Geri Cekme</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm mb-1">Aciklama (opsiyonel)</label>
                    <textarea name="reason" rows="3" maxlength="500" class="w-full border border-rg-lightLavender rounded-btn px-3 py-2 text-sm">{{ old('reason') }}</textarea>
                </div>
                <button type="submit" class="bg-rg-purple text-white px-4 py-2 rounded-btn text-sm">Talep Gonder</button>
            </form>
        </div>

        <div class="bg-white border border-rg-lightLavender rounded-card p-4">
            <h2 class="font-semibold text-lg mb-3">Veri Disa Aktarma</h2>
            <p class="text-sm text-rg-grayText mb-4">Kisisel verileriniz JSON dosyasi olarak indirilecektir.</p>
            <a href="{{ route('account.kvkk.export') }}" class="inline-block w-full text-center bg-rg-purple text-white px-4 py-2 rounded-btn text-sm">
                Verilerimi Indir
            </a>
        </div>
    </div>

    <div class="bg-white border border-rg-lightLavender rounded-card p-4">
        <h2 class="font-semibold text-lg mb-3">Onceki Taleplerim</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left border-b border-rg-lightLavender">
                        <th class="py-2 pr-3">Tarih</th>
                        <th class="py-2 pr-3">Tur</th>
                        <th class="py-2 pr-3">Durum</th>
                        <th class="py-2 pr-3">Admin Notu</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($requests as $item)
                        <tr class="border-b border-rg-lightLavender/60">
                            <td class="py-2 pr-3">{{ $item->created_at?->format('d.m.Y H:i') }}</td>
                            <td class="py-2 pr-3">{{ $item->type }}</td>
                            <td class="py-2 pr-3">{{ $item->status }}</td>
                            <td class="py-2 pr-3">{{ $item->admin_notes ?: '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-3 text-rg-grayText">Henuz talep kaydiniz bulunmuyor.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
