@php $data = $this->getViewData(); @endphp

<x-filament-panels::page class="admin-page-frame">
    <div class="admin-note">Sadakat kuralları, müşteri bakiyeleri ve raporlar aynı çalışma alanında toplanır. Manuel puan işlemleri finansal etki doğurur.</div>

    <div data-tour-anchor="loyalty.tabs" class="flex flex-wrap gap-2">
        @foreach(['rules' => 'Puan kuralları', 'customers' => 'Müşteri puanları', 'reports' => 'Raporlar'] as $tab => $label)
            <button wire:click="$set('activeTab', '{{ $tab }}')" class="rounded-full px-4 py-2 text-sm font-medium transition {{ $activeTab === $tab ? 'bg-primary-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700' }}">{{ $label }}</button>
        @endforeach
    </div>

    <div data-tour-anchor="loyalty.content">
        @if($activeTab === 'rules')
            <section class="admin-section-panel">
                <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Kural ve manuel işlem paneli</h2>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Kazanım oranı, minimum kullanım tutarı ve manuel puan hareketleri buradan yönetilir.</p>
                <form wire:submit="saveRules" class="mt-4 space-y-6">
                    {{ $this->form }}
                    <div class="admin-action-bar">
                        <x-filament::button type="button" color="gray" wire:click="processManualPoints">
                            Manuel puan işlemini uygula
                        </x-filament::button>
                        <x-filament::button type="submit" color="primary">Kuralları kaydet</x-filament::button>
                    </div>
                </form>
            </section>
        @elseif($activeTab === 'customers')
            <section class="admin-section-panel">
                <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Müşteri puan bakiyeleri</h2>
                <div class="mt-4 overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="border-b border-slate-200 dark:border-slate-800"><tr><th class="px-4 py-3 text-left font-medium text-slate-500 dark:text-slate-400">Müşteri</th><th class="px-4 py-3 text-right font-medium text-slate-500 dark:text-slate-400">Bakiye</th><th class="px-4 py-3 text-right font-medium text-slate-500 dark:text-slate-400">Kazanılan</th><th class="px-4 py-3 text-right font-medium text-slate-500 dark:text-slate-400">Kullanılan</th></tr></thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                            @forelse($data['topUsers'] as $lp)
                                <tr><td class="px-4 py-3 text-slate-900 dark:text-white">{{ $lp->user->name }}</td><td class="px-4 py-3 text-right font-medium text-slate-700 dark:text-slate-200">₺{{ number_format($lp->balance, 2) }}</td><td class="px-4 py-3 text-right text-emerald-600 dark:text-emerald-300">₺{{ number_format($lp->total_earned, 2) }}</td><td class="px-4 py-3 text-right text-amber-600 dark:text-amber-300">₺{{ number_format($lp->total_spent, 2) }}</td></tr>
                            @empty
                                <tr><td colspan="4" class="px-4 py-5"><div class="admin-empty-state">Bakiye taşıyan müşteri bulunmuyor.</div></td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        @else
            <div class="admin-page-grid admin-page-grid--three">
                <div class="admin-section-panel"><p class="text-sm text-slate-500 dark:text-slate-400">Toplam dağıtılan</p><p class="mt-3 text-3xl font-bold text-slate-900 dark:text-white">₺{{ number_format($data['totalDistributed'], 2) }}</p></div>
                <div class="admin-section-panel"><p class="text-sm text-slate-500 dark:text-slate-400">Toplam kullanılan</p><p class="mt-3 text-3xl font-bold text-slate-900 dark:text-white">₺{{ number_format($data['totalUsed'], 2) }}</p></div>
                <div class="admin-section-panel"><p class="text-sm text-slate-500 dark:text-slate-400">Bekleyen bakiye</p><p class="mt-3 text-3xl font-bold text-slate-900 dark:text-white">₺{{ number_format($data['pendingBalance'], 2) }}</p><p class="mt-2 text-xs text-slate-500 dark:text-slate-400">Kullanım oranı: %{{ $data['usageRate'] }}</p></div>
            </div>
        @endif
    </div>
</x-filament-panels::page>
