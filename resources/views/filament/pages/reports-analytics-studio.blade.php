@php $data = $this->getViewData(); @endphp

<x-filament-panels::page class="admin-page-frame">
    <section class="admin-section-panel" data-tour-anchor="reports.hero">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-primary-600">Ticari analiz</p>
                <h2 class="mt-2 text-2xl font-semibold tracking-tight text-slate-900 dark:text-white">Gelir, sipariş ve kalite görünümü</h2>
                <p class="mt-3 max-w-3xl text-sm leading-6 text-slate-500 dark:text-slate-400">Ciroyu tek başına göstermek yerine sipariş durumu, tekrar müşteri oranı, kupon kullanımı ve trafik kaynağıyla birlikte değerlendirir.</p>
            </div>
            <div class="flex flex-wrap gap-2" data-tour-anchor="reports.export">
                @foreach(['today' => 'Bugün', '7days' => 'Son 7 gün', '30days' => 'Son 30 gün'] as $key => $label)
                    <x-filament::button wire:click="setPeriod('{{ $key }}')" color="{{ $period === $key ? 'primary' : 'gray' }}" size="sm">{{ $label }}</x-filament::button>
                @endforeach
                <x-filament::button tag="a" href="#" wire:click.prevent="exportCsv" color="success" size="sm">CSV dışa aktar</x-filament::button>
            </div>
        </div>
        <div class="mt-4 flex flex-wrap gap-3 text-xs text-slate-500 dark:text-slate-400"><span class="rounded-full bg-slate-100 px-3 py-1 dark:bg-slate-800">{{ $data['periodLabel'] }}</span><span class="rounded-full bg-slate-100 px-3 py-1 dark:bg-slate-800">Karşılaştırma: {{ $data['previousPeriodLabel'] }}</span></div>
    </section>

    <div class="admin-page-grid admin-page-grid--three" data-tour-anchor="reports.comparison">
        @foreach ([
            ['label' => 'Toplam ciro', 'value' => '₺'.number_format($data['totalRevenue'], 2), 'delta' => $data['comparison']['revenue']],
            ['label' => 'Toplam sipariş', 'value' => number_format($data['totalOrders']), 'delta' => $data['comparison']['orders']],
            ['label' => 'Ortalama sepet', 'value' => '₺'.number_format($data['avgOrderValue'], 2), 'delta' => $data['comparison']['aov']],
        ] as $card)
            <div class="admin-section-panel"><p class="text-sm text-slate-500 dark:text-slate-400">{{ $card['label'] }}</p><p class="mt-3 text-3xl font-bold text-slate-900 dark:text-white">{{ $card['value'] }}</p><p class="mt-2 text-xs {{ $card['delta']['direction'] === 'up' ? 'text-emerald-600 dark:text-emerald-300' : 'text-rose-600 dark:text-rose-300' }}">{{ $card['delta']['direction'] === 'up' ? '+' : '' }}{{ number_format($card['delta']['difference'], 2) }} @if(! is_null($card['delta']['percentage'])) ({{ $card['delta']['direction'] === 'up' ? '+' : '' }}{{ $card['delta']['percentage'] }}%) @endif</p></div>
        @endforeach
    </div>

    <div class="admin-page-grid admin-page-grid--two">
        <section class="admin-section-panel" data-tour-anchor="reports.status">
            <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Sipariş durum kırılımı</h3>
            <div class="mt-4 space-y-3">
                @foreach (['pending','awaiting_payment','paid','preparing','on_the_way','delivered','cancelled','refunded'] as $status)
                    @php $count = $data['statusBreakdown'][$status] ?? 0; $maxStatus = max((int) ($data['statusBreakdown']->max() ?? 1), 1); $width = min(100, (int) round(($count / $maxStatus) * 100)); @endphp
                    <div><div class="mb-1 flex items-center justify-between text-sm"><span class="font-medium text-slate-800 dark:text-slate-200">{{ $status }}</span><span class="text-slate-500 dark:text-slate-400">{{ number_format($count) }}</span></div><div class="h-2 rounded-full bg-slate-100 dark:bg-slate-800"><div class="h-2 rounded-full bg-primary-500" style="width: {{ $width }}%"></div></div></div>
                @endforeach
            </div>
        </section>

        <section class="admin-section-panel">
            <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Müşteri kalitesi</h3>
            <div class="mt-4 grid gap-4 md:grid-cols-2">
                <div class="rounded-2xl border border-slate-200/80 p-4 dark:border-slate-800"><p class="text-sm text-slate-500 dark:text-slate-400">Tekrar müşteri oranı</p><p class="mt-3 text-3xl font-bold text-slate-900 dark:text-white">%{{ number_format($data['repeatCustomerRate'], 1) }}</p></div>
                <div class="rounded-2xl border border-slate-200/80 p-4 dark:border-slate-800"><p class="text-sm text-slate-500 dark:text-slate-400">Kupon kullanımı</p><p class="mt-3 text-3xl font-bold text-slate-900 dark:text-white">%{{ number_format($data['couponUsageRate'], 1) }}</p></div>
            </div>
        </section>
    </div>

    <div class="admin-page-grid admin-page-grid--two">
        <section class="admin-section-panel">
            <h3 class="text-lg font-semibold text-slate-900 dark:text-white">En çok gelir getiren ürünler</h3>
            <div class="mt-4 overflow-x-auto"><table class="min-w-full text-sm"><thead class="border-b border-slate-200 dark:border-slate-800"><tr><th class="px-4 py-3 text-left font-medium text-slate-500 dark:text-slate-400">Ürün</th><th class="px-4 py-3 text-right font-medium text-slate-500 dark:text-slate-400">Gelir</th></tr></thead><tbody class="divide-y divide-slate-200 dark:divide-slate-800">@forelse ($data['topProducts'] as $product)<tr><td class="px-4 py-3 text-slate-900 dark:text-white">{{ $product->getTranslation('name', 'tr') }}</td><td class="px-4 py-3 text-right font-medium text-slate-700 dark:text-slate-200">₺{{ number_format($product->revenue ?? 0, 2) }}</td></tr>@empty<tr><td colspan="2" class="px-4 py-5"><div class="admin-empty-state">Ürün bazlı gelir verisi bulunamadı.</div></td></tr>@endforelse</tbody></table></div>
        </section>

        <section class="admin-section-panel">
            <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Günlük ciro</h3>
            <div class="mt-4 space-y-3">@forelse($data['dailyRevenue'] as $row)@php $maxRevenue = max((float) ($data['dailyRevenue']->max('revenue') ?? 1), 1); $width = min(100, (int) round(($row->revenue / $maxRevenue) * 100)); @endphp<div><div class="mb-1 flex items-center justify-between text-sm"><span class="text-slate-700 dark:text-slate-300">{{ \Carbon\Carbon::parse($row->date)->format('d.m.Y') }}</span><span class="font-medium text-slate-900 dark:text-white">₺{{ number_format($row->revenue, 2) }}</span></div><div class="h-2 rounded-full bg-slate-100 dark:bg-slate-800"><div class="h-2 rounded-full bg-emerald-500" style="width: {{ $width }}%"></div></div></div>@empty<div class="admin-empty-state">Günlük gelir verisi bulunamadı.</div>@endforelse</div>
        </section>
    </div>

    <div class="admin-page-grid admin-page-grid--two">
        <section class="admin-section-panel" data-tour-anchor="reports.devices">
            <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Cihaz dağılımı</h3>
            <div class="mt-4 space-y-3">@forelse ($data['deviceDistribution'] as $device => $count)@php $maxDevice = max((int) ($data['deviceDistribution']->max() ?? 1), 1); $width = min(100, (int) round(($count / $maxDevice) * 100)); @endphp<div><div class="mb-1 flex items-center justify-between text-sm"><span class="font-medium capitalize text-slate-800 dark:text-slate-200">{{ $device }}</span><span class="text-slate-500 dark:text-slate-400">{{ number_format($count) }}</span></div><div class="h-2 rounded-full bg-slate-100 dark:bg-slate-800"><div class="h-2 rounded-full bg-amber-500" style="width: {{ $width }}%"></div></div></div>@empty<div class="admin-empty-state">Cihaz verisi bulunamadı.</div>@endforelse</div>
        </section>
        <section class="admin-section-panel">
            <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Referer kaynakları</h3>
            <div class="mt-4 space-y-3">@forelse ($data['refererDistribution'] as $source => $count)@php $maxSource = max((int) ($data['refererDistribution']->max() ?? 1), 1); $width = min(100, (int) round(($count / $maxSource) * 100)); @endphp<div><div class="mb-1 flex items-center justify-between text-sm"><span class="font-medium capitalize text-slate-800 dark:text-slate-200">{{ $source }}</span><span class="text-slate-500 dark:text-slate-400">{{ number_format($count) }}</span></div><div class="h-2 rounded-full bg-slate-100 dark:bg-slate-800"><div class="h-2 rounded-full bg-primary-500" style="width: {{ $width }}%"></div></div></div>@empty<div class="admin-empty-state">Referer verisi bulunamadı.</div>@endforelse</div>
        </section>
    </div>
</x-filament-panels::page>
