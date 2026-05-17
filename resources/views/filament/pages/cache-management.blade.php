<x-filament-panels::page>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <x-filament::section heading="Cache Temizleme İşlemleri" data-tour-anchor="cache.actions">
            <div class="space-y-3">
                <div class="flex items-center justify-between p-4 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg">
                    <div>
                        <p class="font-medium text-gray-900 dark:text-white">Config Cache</p>
                        <p class="text-xs text-gray-500">Konfigürasyon dosyaları cache'ini temizler.</p>
                    </div>
                    <x-filament::button wire:click="clearConfig" color="warning" size="sm">
                        Temizle
                    </x-filament::button>
                </div>

                <div class="flex items-center justify-between p-4 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg">
                    <div>
                        <p class="font-medium text-gray-900 dark:text-white">View Cache</p>
                        <p class="text-xs text-gray-500">Derlenmiş Blade şablonlarını temizler.</p>
                    </div>
                    <x-filament::button wire:click="clearView" color="warning" size="sm">
                        Temizle
                    </x-filament::button>
                </div>

                <div class="flex items-center justify-between p-4 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg">
                    <div>
                        <p class="font-medium text-gray-900 dark:text-white">Route Cache</p>
                        <p class="text-xs text-gray-500">Route tanımları cache'ini temizler.</p>
                    </div>
                    <x-filament::button wire:click="clearRoute" color="warning" size="sm">
                        Temizle
                    </x-filament::button>
                </div>

                <div class="flex items-center justify-between p-4 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg">
                    <div>
                        <p class="font-medium text-gray-900 dark:text-white">Uygulama Optimizasyonu</p>
                        <p class="text-xs text-gray-500">Autoloader ve route/config derlemelerini yeniler.</p>
                    </div>
                    <x-filament::button wire:click="optimizeApp" color="success" size="sm">
                        Optimize Et
                    </x-filament::button>
                </div>
            </div>
        </x-filament::section>

        <x-filament::section heading="Tam Temizlik" data-tour-anchor="cache.full-reset">
            <div class="p-6 flex flex-col items-center text-center gap-4">
                <x-heroicon-o-trash class="w-12 h-12 text-danger-400" />
                <div>
                    <p class="font-semibold text-gray-900 dark:text-white">Tüm Cache'i Temizle</p>
                    <p class="text-sm text-gray-500 mt-1">
                        Application cache, config, view ve route cache dahil tüm önbellek temizlenir.
                    </p>
                </div>
                <x-filament::button
                    wire:click="clearAll"
                    color="danger"
                    wire:confirm="Tüm cache temizlenecek. Devam etmek istiyor musunuz?"
                >
                    Tüm Cache'i Temizle
                </x-filament::button>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
