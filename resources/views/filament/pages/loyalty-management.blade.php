<x-filament-panels::page>
    @php $data = $this->getViewData(); @endphp

    {{-- Tabs --}}
    <div class="flex gap-2 mb-6 border-b border-gray-200 dark:border-gray-700">
        @foreach(['rules' => 'Puan Kuralları', 'customers' => 'Müşteri Puanları', 'reports' => 'Raporlar'] as $tab => $label)
            <button
                wire:click="$set('activeTab', '{{ $tab }}')"
                class="px-4 py-2 text-sm font-medium {{ $activeTab === $tab ? 'border-b-2 border-primary-600 text-primary-600' : 'text-gray-500 hover:text-gray-700' }}"
            >
                {{ $label }}
            </button>
        @endforeach
    </div>

    @if($activeTab === 'rules')
        <form wire:submit="saveRules">
            {{ $this->form }}
            <div class="mt-4">
                <x-filament::button type="submit" color="primary">Kaydet</x-filament::button>
            </div>
        </form>

    @elseif($activeTab === 'customers')
        <x-filament::section heading="Puan Bakiyeleri">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left border-b border-gray-200 dark:border-gray-700">
                        <th class="pb-2">Müşteri</th>
                        <th class="pb-2 text-right">Bakiye</th>
                        <th class="pb-2 text-right">Kazanılan</th>
                        <th class="pb-2 text-right">Kullanılan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data['topUsers'] as $lp)
                        <tr class="border-b border-gray-100 dark:border-gray-800">
                            <td class="py-2">{{ $lp->user->name }}</td>
                            <td class="py-2 text-right font-medium text-primary-600">₺{{ number_format($lp->balance, 2) }}</td>
                            <td class="py-2 text-right text-success-600">₺{{ number_format($lp->total_earned, 2) }}</td>
                            <td class="py-2 text-right text-warning-600">₺{{ number_format($lp->total_spent, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </x-filament::section>

    @elseif($activeTab === 'reports')
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <x-filament::card>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">₺{{ number_format($data['totalDistributed'], 2) }}</p>
                <p class="text-sm text-gray-500">Toplam Dağıtılan</p>
            </x-filament::card>
            <x-filament::card>
                <p class="text-2xl font-bold text-warning-600">₺{{ number_format($data['totalUsed'], 2) }}</p>
                <p class="text-sm text-gray-500">Toplam Kullanılan</p>
            </x-filament::card>
            <x-filament::card>
                <p class="text-2xl font-bold text-primary-600">₺{{ number_format($data['pendingBalance'], 2) }}</p>
                <p class="text-sm text-gray-500">Bekleyen Puan</p>
            </x-filament::card>
            <x-filament::card>
                <p class="text-2xl font-bold text-success-600">{{ $data['usageRate'] }}%</p>
                <p class="text-sm text-gray-500">Kullanım Oranı</p>
            </x-filament::card>
        </div>
    @endif
</x-filament-panels::page>
