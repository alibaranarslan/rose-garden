@php
    $selectedModule = $this->getSelectedModuleState();
    $selectedModuleIndex = $this->getSelectedModuleIndex();
    $publishedRevision = $this->getPublishedRevision();
    $draftRevision = $this->getDraftRevision();
    $previewUrls = $this->getPreviewUrls();
    $revisionOptions = $this->getRevisionOptions();
    $moduleCount = count($modules);
    $activeModuleCount = collect($modules)->where('is_active', true)->count();
    $selectedSettings = $selectedModule['settings'] ?? [];
    $variantLabels = [
        'default' => 'Varsayılan',
        'editorial' => 'Editoryal',
        'showcase' => 'Vitrin',
        'grid' => 'Izgara',
        'spotlight' => 'Odak alanı',
        'stack' => 'Yığın',
    ];
    $backgroundToneLabels = [
        'surface' => 'Yüzey',
        'muted' => 'Sade',
        'contrast' => 'Kontrast',
    ];
@endphp

@once
    <style>
        .rg-layout-shell {
            --studio-bg: #fff7f8;
            --studio-surface: #fffafb;
            --studio-line: rgba(190, 24, 93, 0.12);
            --studio-accent: #be185d;
            --studio-accent-soft: rgba(244, 114, 182, 0.12);
        }

        .rg-layout-shell .studio-hero {
            position: relative;
            overflow: hidden;
            border-radius: 1.75rem;
            border: 1px solid rgba(190, 24, 93, 0.12);
            background:
                radial-gradient(circle at top right, rgba(244, 114, 182, 0.18), transparent 30%),
                linear-gradient(135deg, #fff7f8, #fff1f2);
            box-shadow: 0 24px 60px rgba(190, 24, 93, 0.08);
        }

        .dark .rg-layout-shell .studio-hero {
            background:
                radial-gradient(circle at top right, rgba(244, 114, 182, 0.18), transparent 30%),
                linear-gradient(135deg, rgba(76, 5, 25, 0.92), rgba(63, 10, 28, 0.9));
            border-color: rgba(244, 114, 182, 0.18);
            box-shadow: none;
        }

        .rg-layout-shell .studio-stat,
        .rg-layout-shell .studio-panel,
        .rg-layout-shell .studio-module {
            border-radius: 1.5rem;
        }

        .rg-layout-shell .studio-stat {
            border: 1px solid rgba(190, 24, 93, 0.12);
            background: rgba(255, 255, 255, 0.82);
            backdrop-filter: blur(12px);
        }

        .dark .rg-layout-shell .studio-stat {
            border-color: rgba(244, 114, 182, 0.15);
            background: rgba(76, 5, 25, 0.36);
        }

        .rg-layout-shell .studio-chip {
            border-radius: 9999px;
            border: 1px solid rgba(190, 24, 93, 0.14);
            background: rgba(255, 255, 255, 0.72);
            color: #9d174d;
        }

        .dark .rg-layout-shell .studio-chip {
            background: rgba(244, 114, 182, 0.08);
            color: #f9a8d4;
        }

        .rg-layout-shell .studio-panel {
            border: 1px solid rgba(190, 24, 93, 0.08);
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(255, 249, 250, 0.96));
            box-shadow: 0 18px 40px rgba(190, 24, 93, 0.04);
        }

        .dark .rg-layout-shell .studio-panel {
            border-color: rgba(244, 114, 182, 0.14);
            background: linear-gradient(180deg, rgba(63, 10, 28, 0.82), rgba(76, 5, 25, 0.7));
            box-shadow: none;
        }

        .rg-layout-shell .studio-module {
            position: relative;
            overflow: hidden;
        }

        .rg-layout-shell .studio-module[draggable="true"] {
            cursor: grab;
        }

        .rg-layout-shell .studio-module.is-dragging {
            opacity: 0.6;
            transform: scale(0.99);
        }

        .rg-layout-shell .studio-module::before {
            content: "";
            position: absolute;
            inset: 0 auto 0 0;
            width: 0.35rem;
            background: transparent;
            transition: background-color 160ms ease;
        }

        .rg-layout-shell .studio-module.is-selected::before {
            background: linear-gradient(180deg, #ec4899, #be185d);
        }

        .rg-layout-shell .studio-section-note {
            border-radius: 1rem;
            border: 1px dashed rgba(190, 24, 93, 0.2);
            background: rgba(255, 241, 242, 0.8);
            color: #9d174d;
        }

        .dark .rg-layout-shell .studio-section-note {
            background: rgba(190, 24, 93, 0.08);
            color: #f9a8d4;
        }
    </style>
@endonce

@once
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            let draggedItem = null;

            document.addEventListener('dragstart', (event) => {
                const item = event.target.closest('[data-sort-item]');

                if (!item) {
                    return;
                }

                draggedItem = item;
                item.classList.add('is-dragging');

                if (event.dataTransfer) {
                    event.dataTransfer.effectAllowed = 'move';
                    event.dataTransfer.setData('text/plain', item.dataset.moduleId || '');
                }
            });

            document.addEventListener('dragover', (event) => {
                const container = event.target.closest('[data-layout-sortable]');

                if (!container || !draggedItem) {
                    return;
                }

                event.preventDefault();

                const siblings = [...container.querySelectorAll('[data-sort-item]:not(.is-dragging)')];
                const nextItem = siblings.find((element) => {
                    const rect = element.getBoundingClientRect();

                    return event.clientY <= rect.top + (rect.height / 2);
                });

                if (nextItem) {
                    container.insertBefore(draggedItem, nextItem);
                } else {
                    container.appendChild(draggedItem);
                }
            });

            document.addEventListener('drop', (event) => {
                const container = event.target.closest('[data-layout-sortable]');

                if (!container || !draggedItem) {
                    return;
                }

                event.preventDefault();

                const root = container.closest('[wire\\:id]');
                const componentId = root?.getAttribute('wire:id');
                const order = [...container.querySelectorAll('[data-sort-item]')].map((element) => element.dataset.moduleId);

                if (componentId && window.Livewire) {
                    window.Livewire.find(componentId)?.call('updateModuleOrder', order);
                }
            });

            document.addEventListener('dragend', (event) => {
                const item = event.target.closest('[data-sort-item]');

                if (item) {
                    item.classList.remove('is-dragging');
                }

                draggedItem = null;
            });
        });
    </script>
@endonce

<x-filament-panels::page>
    <div class="rg-layout-shell space-y-6">
        <x-filament::section data-tour-anchor="layout.hero">
            <div class="studio-hero p-6 lg:p-8">
                <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
                    <div class="max-w-3xl">
                        <div class="flex flex-wrap gap-2">
                            <span class="studio-chip inline-flex items-center px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.24em]">Vitrin Yayın Akışı</span>
                            <span class="studio-chip inline-flex items-center px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.24em]">{{ $activeModuleCount }}/{{ $moduleCount }} aktif modül</span>
                        </div>

                        <h2 class="mt-4 text-3xl font-semibold tracking-tight text-rose-950 dark:text-white sm:text-[2rem]">Rose Garden Yerleşim Stüdyosu</h2>
                        <p class="mt-3 max-w-2xl text-sm leading-6 text-rose-900/80 dark:text-rose-100/85">
                            Vitrin blok akışını, vitrin yoğunluğunu ve marka hissini tek bir yerden yönetin. Taslağı kaydedin, önizleme ile son
                            dokunuşları kontrol edin ve ardından canlıya alın.
                        </p>

                        <div class="mt-5 flex flex-wrap gap-2 text-xs">
                            <span class="studio-chip inline-flex items-center px-3 py-1">Butik vitrin hissi korunur</span>
                            <span class="studio-chip inline-flex items-center px-3 py-1">TR / EN / KU önizleme hazır</span>
                            <span class="studio-chip inline-flex items-center px-3 py-1">Yayın ve geri alma aynı akışta</span>
                        </div>
                    </div>

                    <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4 lg:min-w-[39rem]">
                        <div class="studio-stat p-4">
                            <p class="text-[11px] uppercase tracking-[0.22em] text-rose-700/70 dark:text-rose-100/70">Taslak</p>
                            <p class="mt-3 text-sm font-semibold text-rose-950 dark:text-white">{{ $draftRevision->updated_at?->format('d.m.Y H:i') ?? 'Hazır' }}</p>
                            <p class="mt-2 text-xs text-rose-900/60 dark:text-rose-100/70">{{ $this->hasUnsavedChanges ? 'Kaydedilmemiş değişiklik var' : 'Senkron durumda' }}</p>
                        </div>
                        <div class="studio-stat p-4">
                            <p class="text-[11px] uppercase tracking-[0.22em] text-rose-700/70 dark:text-rose-100/70">Canlı Sürüm</p>
                            <p class="mt-3 text-sm font-semibold text-rose-950 dark:text-white">{{ $publishedRevision?->published_at?->format('d.m.Y H:i') ?? 'Henüz yok' }}</p>
                            <p class="mt-2 text-xs text-rose-900/60 dark:text-rose-100/70">{{ $publishedRevision?->name ?? 'İlk yayın bekleniyor' }}</p>
                        </div>
                        <div class="studio-stat p-4">
                            <p class="text-[11px] uppercase tracking-[0.22em] text-rose-700/70 dark:text-rose-100/70">Vitrin Akışı</p>
                            <p class="mt-3 text-sm font-semibold text-rose-950 dark:text-white">{{ $moduleCount }} blok / {{ $activeModuleCount }} aktif</p>
                            <p class="mt-2 text-xs text-rose-900/60 dark:text-rose-100/70">Öne çıkan vitrin, koleksiyon ve güven alanları aynı taslakta birlikte şekillenir.</p>
                        </div>
                        <div class="studio-stat p-4">
                            <p class="text-[11px] uppercase tracking-[0.22em] text-rose-700/70 dark:text-rose-100/70">Önizleme</p>
                            <div class="mt-3 flex flex-wrap gap-2">
                                @foreach ($previewUrls as $locale => $url)
                                    <a href="{{ $url }}" target="_blank" rel="noreferrer" class="inline-flex items-center rounded-full border border-rose-200 bg-white/80 px-3 py-1 text-[11px] font-semibold text-rose-700 transition hover:border-rose-400 hover:bg-rose-50 dark:border-rose-400/20 dark:bg-rose-400/10 dark:text-rose-200 dark:hover:bg-rose-400/15">
                                        {{ strtoupper($locale) }} önizleme
                                    </a>
                                @endforeach
                            </div>
                            <p class="mt-2 text-xs text-rose-900/60 dark:text-rose-100/70">Kayıtlı taslak önizleme ile açılır.</p>
                        </div>
                    </div>
                </div>
            </div>
        </x-filament::section>

        <div class="grid gap-6 xl:grid-cols-[minmax(0,0.95fr)_minmax(0,1.35fr)]">
            <x-filament::section data-tour-anchor="layout.modules" heading="Vitrin Modülleri" description="Modülleri seçin, yerel sıra kontrolleriyle taslakta yukarı-aşağı taşıyın ve kaydettikten sonra önizleme alın.">
                <div class="studio-section-note mb-4 px-4 py-3 text-sm leading-6">
                    Her blok vitrin deneyiminin bir parçası. Sırayı, yoğunluğu ve buton davranışını taslakta sakince kurun; ardından önizleme ile
                    bütün anasayfa dengesini kontrol edin.
                </div>

                <div class="space-y-3" data-layout-sortable>
                    @foreach ($modules as $module)
                        @php
                            $moduleSettings = $module['settings'] ?? [];
                        @endphp
                        <div
                            data-module-id="{{ $module['id'] }}"
                            data-sort-item
                            draggable="true"
                            class="studio-module {{ $selectedModule && $selectedModule['key'] === $module['key'] ? 'is-selected border-rose-300 bg-rose-50/80 shadow-sm dark:border-rose-400/40 dark:bg-rose-400/10' : 'border-gray-200 bg-white hover:border-rose-200 hover:bg-rose-50/40 dark:border-gray-800 dark:bg-gray-900 dark:hover:border-rose-400/30 dark:hover:bg-rose-950/30' }} flex w-full items-start justify-between gap-3 border px-4 py-4 text-left transition"
                        >
                            <button
                                type="button"
                                wire:click="selectModule('{{ $module['key'] }}')"
                                class="flex min-w-0 flex-1 items-start gap-3 text-left"
                            >
                                <span class="mt-1 inline-flex h-10 w-10 items-center justify-center rounded-2xl border border-rose-200 bg-rose-50 text-rose-700 dark:border-rose-400/20 dark:bg-rose-400/10 dark:text-rose-200">
                                    {{ str_pad((string) ($loop->iteration), 2, '0', STR_PAD_LEFT) }}
                                </span>
                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <p class="font-semibold text-gray-950 dark:text-white">{{ $module['name'] }}</p>
                                        <span class="rounded-full bg-gray-100 px-2 py-0.5 text-[11px] font-medium text-gray-500 dark:bg-gray-800 dark:text-gray-300">{{ $module['key'] }}</span>
                                        <span class="rounded-full bg-rose-100 px-2 py-0.5 text-[11px] font-medium text-rose-700 dark:bg-rose-400/10 dark:text-rose-200">{{ $variantLabels[data_get($moduleSettings, 'variant', 'default')] ?? 'Varsayılan' }}</span>
                                    </div>
                                    <p class="mt-1 text-sm leading-5 text-gray-500 dark:text-gray-400">{{ $module['description'] }}</p>
                                    <div class="mt-3 flex flex-wrap gap-2 text-[11px] text-gray-500 dark:text-gray-400">
                                        <span class="rounded-full border border-gray-200 px-2.5 py-1 dark:border-gray-700">Masaüstü {{ data_get($moduleSettings, 'columns_desktop', 1) }} kolon</span>
                                        <span class="rounded-full border border-gray-200 px-2.5 py-1 dark:border-gray-700">İçerik {{ data_get($moduleSettings, 'content_limit', 6) }}</span>
                                        <span class="rounded-full border border-gray-200 px-2.5 py-1 dark:border-gray-700">{{ $backgroundToneLabels[data_get($moduleSettings, 'background_tone', 'surface')] ?? 'Yüzey' }}</span>
                                    </div>
                                </div>
                            </button>

                            <div class="flex shrink-0 flex-wrap items-center justify-end gap-2">
                                <x-filament::button type="button" size="sm" color="gray" wire:click="moveModuleUp({{ $module['id'] }})">
                                    Yukarı
                                </x-filament::button>
                                <x-filament::button type="button" size="sm" color="gray" wire:click="moveModuleDown({{ $module['id'] }})">
                                    Aşağı
                                </x-filament::button>
                                <span class="rounded-full px-2.5 py-1 text-[11px] font-semibold {{ $module['is_active'] ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300' : 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400' }}">
                                    {{ $module['is_active'] ? 'Aktif' : 'Pasif' }}
                                </span>
                                <x-filament::button type="button" size="sm" color="{{ $module['is_active'] ? 'gray' : 'success' }}" wire:click.stop="toggleModule({{ $module['id'] }})">
                                    {{ $module['is_active'] ? 'Kapat' : 'Aç' }}
                                </x-filament::button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </x-filament::section>

            <div class="space-y-6">
                <x-filament::section data-tour-anchor="layout.settings" heading="Seçili Modül Ayarları" description="Başlık özelleştirmesi, buton ve yoğunluk kararlarını modül bazında yönetin.">
                    @if ($selectedModule && $selectedModuleIndex !== null)
                        @php
                            $fieldBase = 'modules.'.$selectedModuleIndex.'.settings';
                        @endphp

                        <div class="space-y-6">
                            <div class="studio-panel grid gap-4 px-5 py-4 md:grid-cols-[minmax(0,1fr)_auto]">
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-rose-600 dark:text-rose-200">Seçili blok</p>
                                    <h3 class="mt-2 text-lg font-semibold text-gray-950 dark:text-white">{{ $selectedModule['name'] }}</h3>
                                    <p class="mt-2 text-sm leading-6 text-gray-600 dark:text-gray-300">
                                        Vitrin başlıkları, buton metinleri ve ızgara yoğunluğu bu panelde birlikte ele alınır.
                                    </p>
                                </div>

                                <div class="flex flex-wrap gap-2 self-start text-[11px]">
                                    <span class="rounded-full bg-gray-100 px-3 py-1 font-medium text-gray-600 dark:bg-gray-800 dark:text-gray-300">{{ $variantLabels[data_get($selectedSettings, 'variant', 'default')] ?? 'Varsayılan' }}</span>
                                    <span class="rounded-full bg-emerald-100 px-3 py-1 font-medium text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300">{{ $selectedModule['is_active'] ? 'Aktif' : 'Pasif' }}</span>
                                    <span class="rounded-full bg-rose-100 px-3 py-1 font-medium text-rose-700 dark:bg-rose-400/10 dark:text-rose-200">Masaüstü {{ data_get($selectedSettings, 'columns_desktop', 1) }}</span>
                                </div>
                            </div>

                            @php
                                $selectedWarnings = $this->getSelectedModuleWarnings();
                            @endphp

                            <div class="studio-panel space-y-4 px-5 py-4">
                                <div class="flex flex-wrap items-center justify-between gap-3">
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900 dark:text-white">Hızlı Ön Ayarlar</p>
                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Modülü tek tek alanlarla uğraşmadan dengeli, vitrin odaklı veya kompakt bir kurguya taşıyın.</p>
                                    </div>

                                    <div class="flex flex-wrap gap-2">
                                        <x-filament::button type="button" size="sm" color="gray" wire:click="applyModulePreset('balanced')">Dengeli</x-filament::button>
                                        <x-filament::button type="button" size="sm" color="warning" wire:click="applyModulePreset('showcase')">Vitrin</x-filament::button>
                                        <x-filament::button type="button" size="sm" color="gray" wire:click="applyModulePreset('compact')">Kompakt</x-filament::button>
                                        <x-filament::button type="button" size="sm" color="danger" wire:click="resetSelectedModuleSettings">Varsayılana Dön</x-filament::button>
                                    </div>
                                </div>

                                @if ($selectedWarnings !== [])
                                    <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-900 dark:border-rose-400/20 dark:bg-rose-400/10 dark:text-rose-100">
                                        <p class="font-semibold">Dikkat edilmesi gereken noktalar</p>
                                        <ul class="mt-2 space-y-1">
                                            @foreach ($selectedWarnings as $warning)
                                                <li>{{ $warning }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <p class="text-xs leading-5 text-gray-500 dark:text-gray-400">
                                    Canlıda etkin olan temel kararlar: sıra, görünürlük, başlık, alt başlık, buton, içerik limiti, kapsayıcı genişliği, arka plan tonu ve iç boşluk.
                                    Kolon ve görsel oranı gibi alanlar ise ızgara veya görsel kullanan vitrin bloklarında etkisini gösterir.
                                </p>
                            </div>

                            <details open class="studio-panel px-5 py-4">
                                <summary class="cursor-pointer text-sm font-semibold text-gray-900 dark:text-white">Temel yerleşim ayarları</summary>
                                <div class="mt-4 space-y-4">
                            <div class="grid gap-4 md:grid-cols-2">
                                <label class="space-y-2">
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Yerleşim türü</span>
                                    <select wire:model.defer="{{ $fieldBase }}.variant" class="w-full rounded-xl border-gray-300 bg-white text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                                        <option value="default">Varsayılan</option>
                                        <option value="editorial">Editoryal</option>
                                        <option value="showcase">Vitrin</option>
                                        <option value="grid">Izgara</option>
                                        <option value="spotlight">Odak alanı</option>
                                        <option value="stack">Yığın</option>
                                    </select>
                                </label>
                                <label class="space-y-2">
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-200">İçerik limiti</span>
                                    <input type="number" min="1" max="24" wire:model.defer="{{ $fieldBase }}.content_limit" class="w-full rounded-xl border-gray-300 bg-white text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                                </label>
                                <label class="space-y-2">
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Arka plan tonu</span>
                                    <select wire:model.defer="{{ $fieldBase }}.background_tone" class="w-full rounded-xl border-gray-300 bg-white text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                                        <option value="surface">Yüzey</option>
                                        <option value="muted">Sade</option>
                                        <option value="contrast">Kontrast</option>
                                    </select>
                                </label>
                                <label class="space-y-2">
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Kart yoğunluğu</span>
                                    <select wire:model.defer="{{ $fieldBase }}.card_density" class="w-full rounded-xl border-gray-300 bg-white text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                                        <option value="compact">Sıkı</option>
                                        <option value="comfortable">Rahat</option>
                                        <option value="airy">Havadar</option>
                                    </select>
                                </label>
                                <label class="space-y-2">
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Vurgu modu</span>
                                    <select wire:model.defer="{{ $fieldBase }}.accent_mode" class="w-full rounded-xl border-gray-300 bg-white text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                                        <option value="brand">Marka</option>
                                        <option value="neutral">Nötr</option>
                                        <option value="soft">Yumuşak</option>
                                    </select>
                                </label>
                                <label class="space-y-2">
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-200">İç boşluk ölçeği</span>
                                    <select wire:model.defer="{{ $fieldBase }}.padding_scale" class="w-full rounded-xl border-gray-300 bg-white text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                                        <option value="compact">Sıkı</option>
                                        <option value="regular">Standart</option>
                                        <option value="relaxed">Ferah</option>
                                    </select>
                                </label>
                            </div>

                            <div class="grid gap-4 md:grid-cols-3">
                                <label class="space-y-2">
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Mobil kolon</span>
                                    <input type="number" min="1" max="4" wire:model.defer="{{ $fieldBase }}.columns_mobile" class="w-full rounded-xl border-gray-300 bg-white text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                                </label>
                                <label class="space-y-2">
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Tablet kolon</span>
                                    <input type="number" min="1" max="6" wire:model.defer="{{ $fieldBase }}.columns_tablet" class="w-full rounded-xl border-gray-300 bg-white text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                                </label>
                                <label class="space-y-2">
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Masaüstü kolonu</span>
                                    <input type="number" min="1" max="12" wire:model.defer="{{ $fieldBase }}.columns_desktop" class="w-full rounded-xl border-gray-300 bg-white text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                                </label>
                            </div>

                            <div class="grid gap-4 md:grid-cols-3">
                                <label class="flex items-center gap-3 rounded-2xl border border-gray-200 px-4 py-3 dark:border-gray-800">
                                    <input type="checkbox" wire:model.defer="{{ $fieldBase }}.show_on_mobile" class="rounded border-gray-300 text-primary-600 shadow-sm focus:ring-primary-500">
                                    <span class="text-sm text-gray-700 dark:text-gray-200">Mobilde göster</span>
                                </label>
                                <label class="flex items-center gap-3 rounded-2xl border border-gray-200 px-4 py-3 dark:border-gray-800">
                                    <input type="checkbox" wire:model.defer="{{ $fieldBase }}.show_on_tablet" class="rounded border-gray-300 text-primary-600 shadow-sm focus:ring-primary-500">
                                    <span class="text-sm text-gray-700 dark:text-gray-200">Tablette göster</span>
                                </label>
                                <label class="flex items-center gap-3 rounded-2xl border border-gray-200 px-4 py-3 dark:border-gray-800">
                                    <input type="checkbox" wire:model.defer="{{ $fieldBase }}.show_on_desktop" class="rounded border-gray-300 text-primary-600 shadow-sm focus:ring-primary-500">
                                    <span class="text-sm text-gray-700 dark:text-gray-200">Masaüstünde göster</span>
                                </label>
                            </div>
                                </div>
                            </details>

                            <details class="studio-panel px-5 py-4">
                                <summary class="cursor-pointer text-sm font-semibold text-gray-900 dark:text-white">Metinler ve butonlar</summary>
                                <div class="mt-4 space-y-4">
                            <div class="grid gap-4 lg:grid-cols-3">
                                @foreach (['tr' => 'TR', 'en' => 'EN', 'ku' => 'KU'] as $locale => $label)
                                    <div class="studio-panel rounded-2xl p-4">
                                        <p class="text-xs font-semibold uppercase tracking-[0.22em] text-gray-500 dark:text-gray-400">{{ $label }} metinleri</p>
                                        <div class="mt-4 space-y-3">
                                            <label class="space-y-2">
                                                <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Başlık</span>
                                                <input type="text" wire:model.defer="{{ $fieldBase }}.title_override.{{ $locale }}" class="w-full rounded-xl border-gray-300 bg-white text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                                            </label>
                                            <label class="space-y-2">
                                                <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Alt başlık</span>
                                                <textarea rows="3" wire:model.defer="{{ $fieldBase }}.subtitle_override.{{ $locale }}" class="w-full rounded-xl border-gray-300 bg-white text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white"></textarea>
                                            </label>
                                            <label class="space-y-2">
                                                <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Buton etiketi</span>
                                                <input type="text" wire:model.defer="{{ $fieldBase }}.cta_label.{{ $locale }}" class="w-full rounded-xl border-gray-300 bg-white text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="grid gap-4 md:grid-cols-[auto_minmax(0,1fr)]">
                                <label class="flex items-center gap-3 rounded-2xl border border-gray-200 px-4 py-3 dark:border-gray-800">
                                    <input type="checkbox" wire:model.defer="{{ $fieldBase }}.cta_enabled" class="rounded border-gray-300 text-primary-600 shadow-sm focus:ring-primary-500">
                                    <span class="text-sm text-gray-700 dark:text-gray-200">Butonu göster</span>
                                </label>
                                <label class="space-y-2">
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Buton bağlantısı</span>
                                    <input type="text" wire:model.defer="{{ $fieldBase }}.cta_url" class="w-full rounded-xl border-gray-300 bg-white text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                                </label>
                            </div>
                                </div>
                            </details>
                        </div>
                    @else
                        <p class="text-sm text-gray-500 dark:text-gray-400">Düzenlemek için soldan bir modül seçin.</p>
                    @endif
                </x-filament::section>

                <x-filament::section data-tour-anchor="layout.appearance" heading="Genel Görünüm">
                    <div class="studio-section-note mb-4 px-4 py-3 text-sm leading-6">
                        Marka hissini belirteçlerle yönetin. Renk, yazı tipi ve kenar yumuşaklığı kararlarını değiştirirken vitrinin zarif ve tutarlı kalması hedeflenir.
                    </div>

                    <div class="studio-panel mb-4 space-y-4 px-5 py-4">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <div>
                                <p class="text-sm font-semibold text-gray-900 dark:text-white">Görünüm Ön Ayarları</p>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Marka hissini daha romantik, modern veya minimal bir vitrin çizgisine tek tıkla çekin.</p>
                            </div>

                            <div class="flex flex-wrap gap-2">
                                <x-filament::button type="button" size="sm" color="warning" wire:click="applyAppearancePreset('romantik')">Romantik</x-filament::button>
                                <x-filament::button type="button" size="sm" color="gray" wire:click="applyAppearancePreset('modern')">Modern</x-filament::button>
                                <x-filament::button type="button" size="sm" color="gray" wire:click="applyAppearancePreset('minimal')">Minimal</x-filament::button>
                            </div>
                        </div>
                    </div>

                    <div class="grid gap-4 md:grid-cols-2">
                        <label class="space-y-2">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Ana renk</span>
                            <input type="color" wire:model.defer="appearance.primary_color" class="h-11 w-full rounded-xl border border-gray-300 bg-white p-1 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                        </label>
                        <label class="space-y-2">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Vurgu rengi</span>
                            <input type="color" wire:model.defer="appearance.accent_color" class="h-11 w-full rounded-xl border border-gray-300 bg-white p-1 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                        </label>
                        <label class="space-y-2">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Arka plan rengi</span>
                            <input type="color" wire:model.defer="appearance.background_color" class="h-11 w-full rounded-xl border border-gray-300 bg-white p-1 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                        </label>
                        <label class="space-y-2">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Yazı tipi</span>
                            <select wire:model.defer="appearance.font_family" class="w-full rounded-xl border-gray-300 bg-white text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                                <option value="inter">Inter</option>
                                <option value="playfair">Playfair Display</option>
                                <option value="poppins">Poppins</option>
                            </select>
                        </label>
                        <label class="space-y-2">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Kenar yumuşaklığı</span>
                            <select wire:model.defer="appearance.radius_preset" class="w-full rounded-xl border-gray-300 bg-white text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                                <option value="soft">Yumuşak</option>
                                <option value="rounded">Yuvarlak</option>
                                <option value="sharp">Keskin</option>
                            </select>
                        </label>
                        <label class="space-y-2">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Gölgelendirme</span>
                            <select wire:model.defer="appearance.shadow_preset" class="w-full rounded-xl border-gray-300 bg-white text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                                <option value="none">Yok</option>
                                <option value="soft">Yumuşak</option>
                                <option value="elevated">Belirgin</option>
                            </select>
                        </label>
                    </div>
                </x-filament::section>
            </div>
        </div>

        <x-filament::section data-tour-anchor="layout.publish" heading="Yayın ve Geri Alma">
            <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_minmax(0,0.9fr)]">
                <div class="studio-panel space-y-4 px-5 py-5">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-rose-600 dark:text-rose-200">Yayın kontrolü</p>
                            <h3 class="mt-1 text-lg font-semibold text-gray-950 dark:text-white">Vitrin değişikliklerini yayına alın</h3>
                        </div>

                        <div class="flex flex-wrap gap-3">
                            @foreach ($previewUrls as $locale => $url)
                                <a href="{{ $url }}" target="_blank" rel="noreferrer" class="inline-flex items-center rounded-full border border-rose-200 px-3 py-1 text-xs font-semibold text-rose-700 transition hover:border-rose-400 hover:bg-rose-50 dark:border-rose-400/20 dark:text-rose-200 dark:hover:bg-rose-400/10">
                                    {{ strtoupper($locale) }} önizleme
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-3">
                        <x-filament::button wire:click="saveDraft" color="gray" icon="heroicon-o-pencil-square">
                            Taslağı Kaydet
                        </x-filament::button>
                        <x-filament::button wire:click="publishDraft" color="primary" icon="heroicon-o-rocket-launch">
                            Canlıya Al
                        </x-filament::button>
                    </div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        @if ($this->hasUnsavedChanges)
                            Önizleme bağlantıları son kaydedilen taslağı gösterir; yeni değişiklikleri önizlemek için önce taslağı kaydedin.
                        @else
                            Taslak kayıtlı ve önizleme akışı güncel.
                        @endif
                    </p>
                </div>

                <div data-tour-anchor="layout.rollback" class="studio-panel rounded-2xl p-4">
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">Geri Alma</p>
                    <div class="mt-4 space-y-3">
                        <select wire:model.defer="restoreRevisionId" class="w-full rounded-xl border-gray-300 bg-white text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-700 dark:bg-gray-950 dark:text-white">
                            <option value="">Revizyon seçin</option>
                            @foreach ($revisionOptions as $id => $label)
                                <option value="{{ $id }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        <x-filament::button wire:click="restoreRevision" color="warning" icon="heroicon-o-arrow-uturn-left" class="w-full">
                            Revizyonu Taslağa Yükle
                        </x-filament::button>
                    </div>
                </div>
            </div>
        </x-filament::section>

        <div class="sticky bottom-4 z-20">
            <div class="mx-auto flex max-w-6xl flex-col gap-3 rounded-3xl border border-rose-200/80 bg-white/95 px-4 py-4 shadow-[0_20px_45px_-30px_rgba(190,24,93,0.35)] backdrop-blur dark:border-rose-400/10 dark:bg-slate-950/90 lg:flex-row lg:items-center lg:justify-between">
                <div class="min-w-0">
                    <p class="text-sm font-semibold text-slate-900 dark:text-white">Vitrin özeti</p>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                        {{ $selectedModule['name'] ?? 'Bir blok seçin' }}
                        @if ($selectedModule)
                            • {{ $selectedModule['is_active'] ? 'aktif' : 'pasif' }}
                            • masaüstü {{ data_get($selectedSettings, 'columns_desktop', 1) }} kolon
                        @endif
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $this->hasUnsavedChanges ? 'bg-amber-100 text-amber-700 dark:bg-amber-400/15 dark:text-amber-300' : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300' }}">
                        {{ $this->hasUnsavedChanges ? 'Kaydedilmemiş değişiklik var' : 'Taslak güncel' }}
                    </span>

                    <a href="{{ $previewUrls['tr'] ?? '#' }}" target="_blank" rel="noreferrer" class="inline-flex items-center rounded-full border border-rose-200 px-3 py-2 text-xs font-semibold text-rose-700 transition hover:border-rose-400 hover:bg-rose-50 dark:border-rose-400/20 dark:text-rose-200 dark:hover:bg-rose-400/10">
                        Önizleme
                    </a>

                    <x-filament::button wire:click="saveDraft" color="gray" size="sm" icon="heroicon-o-pencil-square">
                        Taslağı Kaydet
                    </x-filament::button>

                    <x-filament::button wire:click="publishDraft" color="primary" size="sm" icon="heroicon-o-rocket-launch" :disabled="$this->isPublishRestricted()">
                        Canlıya Al
                    </x-filament::button>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
