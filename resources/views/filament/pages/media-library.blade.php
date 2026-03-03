<x-filament-panels::page>
    @php $items = $this->getMediaItems(); @endphp

    {{-- Toolbar --}}
    <div class="flex flex-wrap items-center gap-3 mb-6">
        {{-- Search --}}
        <div class="flex-1 min-w-[200px]">
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Dosya adına göre ara..."
                class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500"
            />
        </div>

        {{-- Orphaned Toggle --}}
        <button
            wire:click="toggleOrphaned"
            class="px-3 py-2 text-sm rounded-lg border {{ $showOrphaned ? 'bg-warning-500 border-warning-500 text-white' : 'border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800' }} transition-colors"
        >
            <span class="flex items-center gap-1.5">
                <x-heroicon-o-exclamation-triangle class="w-4 h-4" />
                Kullanılmayanları Göster
            </span>
        </button>

        {{-- View Mode --}}
        <div class="flex rounded-lg border border-gray-300 dark:border-gray-600 overflow-hidden">
            <button
                wire:click="setViewMode('grid')"
                class="px-3 py-2 {{ $viewMode === 'grid' ? 'bg-primary-600 text-white' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300' }} transition-colors"
                title="Grid Görünüm"
            >
                <x-heroicon-o-squares-2x2 class="w-4 h-4" />
            </button>
            <button
                wire:click="setViewMode('list')"
                class="px-3 py-2 {{ $viewMode === 'list' ? 'bg-primary-600 text-white' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300' }} transition-colors"
                title="Liste Görünüm"
            >
                <x-heroicon-o-list-bullet class="w-4 h-4" />
            </button>
        </div>

        {{-- Count badge --}}
        <span class="text-sm text-gray-500 dark:text-gray-400">
            {{ $items->count() }} dosya
        </span>
    </div>

    @if($items->isEmpty())
        <div class="text-center py-12 text-gray-400 dark:text-gray-500">
            <x-heroicon-o-photo class="w-12 h-12 mx-auto mb-3 opacity-40" />
            <p class="text-sm">Medya bulunamadı</p>
        </div>
    @elseif($viewMode === 'grid')
        {{-- Grid View --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 xl:grid-cols-5 gap-4">
            @foreach($items as $item)
                <div class="group relative bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                    {{-- Thumbnail --}}
                    <div class="aspect-square bg-gray-100 dark:bg-gray-700 flex items-center justify-center overflow-hidden">
                        @if($item['thumb_url'])
                            <img
                                src="{{ $item['thumb_url'] }}"
                                alt="{{ $item['file_name'] }}"
                                class="w-full h-full object-cover"
                                loading="lazy"
                            />
                        @else
                            <x-heroicon-o-document class="w-10 h-10 text-gray-400" />
                        @endif
                    </div>

                    {{-- Info --}}
                    <div class="p-2">
                        <p class="text-xs font-medium text-gray-800 dark:text-gray-200 truncate" title="{{ $item['file_name'] }}">
                            {{ $item['file_name'] }}
                        </p>
                        <p class="text-xs text-gray-500 mt-0.5">{{ $item['size'] }}</p>
                        <p class="text-xs text-gray-400 truncate" title="{{ $item['collection'] }}">
                            {{ $item['collection'] }} · {{ $item['model_type'] }}
                        </p>
                    </div>

                    {{-- Delete button --}}
                    <button
                        wire:click="deleteMedia({{ $item['id'] }})"
                        wire:confirm="Bu medyayı silmek istediğinize emin misiniz?"
                        class="absolute top-1 right-1 p-1 bg-danger-500 text-white rounded opacity-0 group-hover:opacity-100 transition-opacity hover:bg-danger-600"
                        title="Sil"
                    >
                        <x-heroicon-o-trash class="w-3.5 h-3.5" />
                    </button>
                </div>
            @endforeach
        </div>
    @else
        {{-- List View --}}
        <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-400 w-12">Önizleme</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-400">Dosya Adı</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-400">Boyut</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-400">Koleksiyon</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-400">Model</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-400">Yükleme</th>
                        <th class="px-4 py-3 text-center font-medium text-gray-600 dark:text-gray-400 w-16">İşlem</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @foreach($items as $item)
                        <tr class="bg-white dark:bg-gray-900 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                            <td class="px-4 py-3">
                                @if($item['thumb_url'])
                                    <img src="{{ $item['thumb_url'] }}" alt="" class="w-10 h-10 object-cover rounded" />
                                @else
                                    <div class="w-10 h-10 bg-gray-100 dark:bg-gray-700 rounded flex items-center justify-center">
                                        <x-heroicon-o-document class="w-5 h-5 text-gray-400" />
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-3 font-medium text-gray-900 dark:text-white max-w-xs truncate" title="{{ $item['file_name'] }}">
                                {{ $item['file_name'] }}
                            </td>
                            <td class="px-4 py-3 text-gray-500">{{ $item['size'] }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ $item['collection'] }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ $item['model_type'] }}{{ $item['model_id'] ? ' #' . $item['model_id'] : '' }}</td>
                            <td class="px-4 py-3 text-gray-500 whitespace-nowrap">{{ $item['created_at'] }}</td>
                            <td class="px-4 py-3 text-center">
                                <button
                                    wire:click="deleteMedia({{ $item['id'] }})"
                                    wire:confirm="Bu medyayı silmek istediğinize emin misiniz?"
                                    class="p-1.5 text-danger-500 hover:bg-danger-50 dark:hover:bg-danger-900/20 rounded transition-colors"
                                    title="Sil"
                                >
                                    <x-heroicon-o-trash class="w-4 h-4" />
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</x-filament-panels::page>
