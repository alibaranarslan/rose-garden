<x-filament-panels::page class="admin-page-frame">
    <div class="admin-note">Müşteri kaydı, puan bakiyesi, son siparişler ve favoriler bu ekranda bir araya gelir. Manuel puan aksiyonu operasyonel etki üretir.</div>

    <div class="admin-page-grid admin-page-grid--two">
        <section class="admin-section-panel">
            <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Müşteri bilgileri</h2>
            <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2">
                <div><p class="text-sm text-slate-500 dark:text-slate-400">Ad soyad</p><p class="font-medium text-slate-900 dark:text-white">{{ $record->name }}</p></div>
                <div><p class="text-sm text-slate-500 dark:text-slate-400">E-posta</p><p class="font-medium text-slate-900 dark:text-white">{{ $record->email }}</p></div>
                <div><p class="text-sm text-slate-500 dark:text-slate-400">Telefon</p><p class="font-medium text-slate-900 dark:text-white">{{ $record->phone ?? '—' }}</p></div>
                <div><p class="text-sm text-slate-500 dark:text-slate-400">Kayıt tarihi</p><p class="font-medium text-slate-900 dark:text-white">{{ $record->created_at->format('d.m.Y H:i') }}</p></div>
                <div><p class="text-sm text-slate-500 dark:text-slate-400">KVKK onayı</p><p class="font-medium text-slate-900 dark:text-white">{{ $record->kvkk_accepted_at?->format('d.m.Y') ?? 'Onaylanmadı' }}</p></div>
                <div><p class="text-sm text-slate-500 dark:text-slate-400">Pazarlama izni</p><p class="font-medium text-slate-900 dark:text-white">{{ $record->marketing_consent ? 'Evet' : 'Hayır' }}</p></div>
            </div>
        </section>

        <section class="admin-section-panel">
            <h2 class="text-lg font-semibold text-slate-900 dark:text-white">ParaÇiçek puanları</h2>
            @if ($record->loyaltyPoints)
                <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-3">
                    <div class="rounded-2xl border border-rose-200/70 p-4 dark:border-rose-500/20"><p class="text-sm text-slate-500 dark:text-slate-400">Mevcut bakiye</p><p class="mt-2 text-2xl font-semibold text-slate-900 dark:text-white">₺{{ number_format($record->loyaltyPoints->balance, 2) }}</p></div>
                    <div class="rounded-2xl border border-emerald-200/70 p-4 dark:border-emerald-500/20"><p class="text-sm text-slate-500 dark:text-slate-400">Toplam kazanılan</p><p class="mt-2 text-2xl font-semibold text-slate-900 dark:text-white">₺{{ number_format($record->loyaltyPoints->total_earned, 2) }}</p></div>
                    <div class="rounded-2xl border border-amber-200/70 p-4 dark:border-amber-500/20"><p class="text-sm text-slate-500 dark:text-slate-400">Toplam kullanılan</p><p class="mt-2 text-2xl font-semibold text-slate-900 dark:text-white">₺{{ number_format($record->loyaltyPoints->total_spent, 2) }}</p></div>
                </div>
            @else
                <div class="admin-empty-state mt-4">Henüz puan hareketi yok.</div>
            @endif
        </section>
    </div>

    <section class="admin-section-panel">
        <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Son siparişler</h2>
        <div class="mt-4 overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="border-b border-slate-200 dark:border-slate-800"><tr><th class="px-4 py-3 text-left font-medium text-slate-500 dark:text-slate-400">Sipariş no</th><th class="px-4 py-3 text-left font-medium text-slate-500 dark:text-slate-400">Tutar</th><th class="px-4 py-3 text-left font-medium text-slate-500 dark:text-slate-400">Durum</th><th class="px-4 py-3 text-left font-medium text-slate-500 dark:text-slate-400">Tarih</th></tr></thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                    @forelse ($record->orders()->latest()->limit(10)->get() as $order)
                        <tr><td class="px-4 py-3 text-slate-900 dark:text-white">{{ $order->order_number }}</td><td class="px-4 py-3 text-slate-600 dark:text-slate-300">₺{{ number_format($order->total, 2) }}</td><td class="px-4 py-3 text-slate-600 dark:text-slate-300">{{ $order->status }}</td><td class="px-4 py-3 text-slate-600 dark:text-slate-300">{{ $order->created_at->format('d.m.Y') }}</td></tr>
                    @empty
                        <tr><td colspan="4" class="px-4 py-5"><div class="admin-empty-state">Henüz sipariş bulunmuyor.</div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <section class="admin-section-panel">
        @php $favorites = $record->favorites()->with('product')->latest('created_at')->limit(8)->get(); @endphp
        <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Son favoriler</h2>
        @if ($favorites->isEmpty())
            <div class="admin-empty-state mt-4">Henüz favori ürün bulunmuyor.</div>
        @else
            <div class="mt-4 grid grid-cols-1 gap-3 md:grid-cols-2">
                @foreach ($favorites as $favorite)
                    <div class="rounded-2xl border border-slate-200/80 px-4 py-3 dark:border-slate-800">
                        <p class="font-medium text-slate-900 dark:text-white">{{ $favorite->product?->getTranslation('name', 'tr') ?? 'Silinmiş ürün' }}</p>
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Favorilere eklenme: {{ $favorite->created_at?->format('d.m.Y H:i') ?? '—' }}</p>
                    </div>
                @endforeach
            </div>
        @endif
    </section>
</x-filament-panels::page>
