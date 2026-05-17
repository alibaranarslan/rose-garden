@php $items = $this->getMediaItems(); @endphp

<x-filament-panels::page class="admin-page-frame">
    <div class="admin-note">Medya arşivini arayın, kullanılmayan dosyaları filtreleyin ve görünüm modunu işinize göre değiştirin. Silme işlemleri kalıcıdır.</div>

    <section class="admin-section-panel" data-tour-anchor="media.hero">
        <div class="flex flex-wrap items-center gap-3">
            <div class="min-w-[220px] flex-1"><input type="text" wire:model.live.debounce.300ms="search" placeholder="Dosya adına göre ara..." class="fi-input block w-full" /></div>
            <button wire:click="toggleOrphaned" class="inline-flex items-center gap-2 rounded-2xl border px-4 py-2 text-sm font-medium transition {{ $showOrphaned ? 'border-amber-300 bg-amber-50 text-amber-800 dark:border-amber-400/30 dark:bg-amber-400/10 dark:text-amber-200' : 'border-slate-200 bg-white text-slate-700 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200' }}"><x-heroicon-o-exclamation-triangle class="h-4 w-4" />Kullanılmayanları göster</button>
            <div class="flex overflow-hidden rounded-2xl border border-slate-200 dark:border-slate-700"><button wire:click="setViewMode('grid')" class="px-3 py-2 {{ $viewMode === 'grid' ? 'bg-primary-600 text-white' : 'bg-white text-slate-600 dark:bg-slate-900 dark:text-slate-300' }}" title="Izgara görünümü"><x-heroicon-o-squares-2x2 class="h-4 w-4" /></button><button wire:click="setViewMode('list')" class="px-3 py-2 {{ $viewMode === 'list' ? 'bg-primary-600 text-white' : 'bg-white text-slate-600 dark:bg-slate-900 dark:text-slate-300' }}" title="Liste görünümü"><x-heroicon-o-list-bullet class="h-4 w-4" /></button></div>
            <span class="rounded-full bg-slate-100 px-3 py-2 text-xs font-semibold text-slate-600 dark:bg-slate-800 dark:text-slate-300">{{ $items->count() }} dosya</span>
        </div>
    </section>

    @if($items->isEmpty())
        <div class="admin-empty-state"><x-heroicon-o-photo class="mx-auto mb-3 h-12 w-12 opacity-40" />Medya bulunamadı.</div>
    @elseif($viewMode === 'grid')
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 xl:grid-cols-5" data-tour-anchor="media.browser">
            @foreach($items as $item)
                <article class="admin-section-panel overflow-hidden p-0">
                    <div class="aspect-square bg-slate-100 dark:bg-slate-800">@if($item['thumb_url'])<img src="{{ $item['thumb_url'] }}" alt="{{ $item['file_name'] }}" class="h-full w-full object-cover" loading="lazy" />@else<div class="flex h-full items-center justify-center"><x-heroicon-o-document class="h-10 w-10 text-slate-400" /></div>@endif</div>
                    <div class="space-y-1 px-3 py-3"><p class="truncate text-sm font-medium text-slate-900 dark:text-white" title="{{ $item['file_name'] }}">{{ $item['file_name'] }}</p><p class="text-xs text-slate-500 dark:text-slate-400">{{ $item['size'] }}</p><p class="truncate text-xs text-slate-500 dark:text-slate-400" title="{{ $item['collection'] }}">{{ $item['collection'] }} · {{ $item['model_type'] }}</p></div>
                    <div class="admin-action-bar border-0 px-3 pb-3 pt-0 justify-start">@if($item['is_orphaned'])<x-filament::button wire:click="deleteMedia({{ $item['id'] }})" wire:confirm="Bu medyayı silmek istediğinize emin misiniz?" color="danger" size="sm">Sil</x-filament::button>@else<span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-400/10 dark:text-emerald-200">Aktif kayda bağlı</span>@endif</div>
                </article>
            @endforeach
        </div>
    @else
        <section class="admin-section-panel p-0" data-tour-anchor="media.browser"><div class="overflow-x-auto"><table class="min-w-full text-sm"><thead class="bg-slate-50 dark:bg-slate-900/70"><tr><th class="px-4 py-3 text-left font-medium text-slate-500 dark:text-slate-400">Önizleme</th><th class="px-4 py-3 text-left font-medium text-slate-500 dark:text-slate-400">Dosya adı</th><th class="px-4 py-3 text-left font-medium text-slate-500 dark:text-slate-400">Boyut</th><th class="px-4 py-3 text-left font-medium text-slate-500 dark:text-slate-400">Koleksiyon</th><th class="px-4 py-3 text-left font-medium text-slate-500 dark:text-slate-400">Model</th><th class="px-4 py-3 text-left font-medium text-slate-500 dark:text-slate-400">Yüklenme</th><th class="px-4 py-3 text-center font-medium text-slate-500 dark:text-slate-400">İşlem</th></tr></thead><tbody class="divide-y divide-slate-200 dark:divide-slate-800">@foreach($items as $item)<tr><td class="px-4 py-3">@if($item['thumb_url'])<img src="{{ $item['thumb_url'] }}" alt="" class="h-10 w-10 rounded-xl object-cover" />@else<div class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100 dark:bg-slate-800"><x-heroicon-o-document class="h-5 w-5 text-slate-400" /></div>@endif</td><td class="px-4 py-3 font-medium text-slate-900 dark:text-white">{{ $item['file_name'] }}</td><td class="px-4 py-3 text-slate-600 dark:text-slate-300">{{ $item['size'] }}</td><td class="px-4 py-3 text-slate-600 dark:text-slate-300">{{ $item['collection'] }}</td><td class="px-4 py-3 text-slate-600 dark:text-slate-300">{{ $item['model_type'] }}{{ $item['model_id'] ? ' #' . $item['model_id'] : '' }}</td><td class="px-4 py-3 text-slate-600 dark:text-slate-300 whitespace-nowrap">{{ $item['created_at'] }}</td><td class="px-4 py-3 text-center">@if($item['is_orphaned'])<x-filament::button wire:click="deleteMedia({{ $item['id'] }})" wire:confirm="Bu medyayı silmek istediğinize emin misiniz?" color="danger" size="sm">Sil</x-filament::button>@else<span class="whitespace-nowrap rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-400/10 dark:text-emerald-200">Bağlı</span>@endif</td></tr>@endforeach</tbody></table></div></section>
    @endif
</x-filament-panels::page>
