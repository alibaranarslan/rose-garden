<x-filament-panels::page>
    @php $data = $this->getViewData(); @endphp

    {{-- Period Filter --}}
    <div class="flex flex-wrap gap-2 mb-6">
        @foreach(['today' => 'Bugün', '7days' => 'Son 7 Gün', '30days' => 'Son 30 Gün'] as $key => $label)
            <x-filament::button
                wire:click="setPeriod('{{ $key }}')"
                color="{{ $period === $key ? 'primary' : 'gray' }}"
                size="sm"
            >
                {{ $label }}
            </x-filament::button>
        @endforeach
        <x-filament::button
            tag="a"
            href="#"
            wire:click.prevent="exportCsv"
            color="success"
            size="sm"
        >
            CSV Disa Aktar
        </x-filament::button>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <x-filament::card>
            <div class="text-center">
                <p class="text-3xl font-bold text-success-600">₺{{ number_format($data['totalRevenue'], 2) }}</p>
                <p class="text-sm text-gray-500 mt-1">Toplam Ciro</p>
            </div>
        </x-filament::card>
        <x-filament::card>
            <div class="text-center">
                <p class="text-3xl font-bold text-primary-600">{{ number_format($data['totalOrders']) }}</p>
                <p class="text-sm text-gray-500 mt-1">Toplam Sipariş</p>
            </div>
        </x-filament::card>
        <x-filament::card>
            <div class="text-center">
                <p class="text-3xl font-bold text-info-600">₺{{ number_format($data['avgOrderValue'], 2) }}</p>
                <p class="text-sm text-gray-500 mt-1">Ortalama Sipariş Değeri</p>
            </div>
        </x-filament::card>
    </div>

    {{-- Top Products --}}
    <x-filament::section heading="En Çok Satan Ürünler">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left border-b border-gray-200 dark:border-gray-700">
                    <th class="pb-2">Ürün</th>
                    <th class="pb-2 text-right">Gelir</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['topProducts'] as $product)
                    <tr class="border-b border-gray-100 dark:border-gray-800">
                        <td class="py-2">{{ $product->getTranslation('name', 'tr') }}</td>
                        <td class="py-2 text-right font-medium">₺{{ number_format($product->revenue ?? 0, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </x-filament::section>

    {{-- Daily Revenue --}}
    <x-filament::section heading="Günlük Ciro">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left border-b border-gray-200 dark:border-gray-700">
                    <th class="pb-2">Tarih</th>
                    <th class="pb-2 text-right">Sipariş</th>
                    <th class="pb-2 text-right">Ciro</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['dailyRevenue'] as $row)
                    <tr class="border-b border-gray-100 dark:border-gray-800">
                        <td class="py-2">{{ \Carbon\Carbon::parse($row->date)->format('d.m.Y') }}</td>
                        <td class="py-2 text-right">{{ $row->orders }}</td>
                        <td class="py-2 text-right font-medium text-success-600">₺{{ number_format($row->revenue, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </x-filament::section>
</x-filament-panels::page>
