<x-filament-panels::page class="admin-page-frame">
    <div class="admin-note">
        Bu ekran vitrin ve operasyon ayarlarını birlikte yönetir. Müşteriye görünen alanlar ile yalnız iç operasyonu etkileyen alanlar açıklama düzeyinde ayrışmalıdır.
    </div>

    <div class="admin-section-panel">
        <form wire:submit="save" class="admin-page-frame" data-tour-anchor="settings.form">
            {{ $this->form }}

            <div class="admin-action-bar" data-tour-anchor="settings.save">
                <x-filament::button type="submit" color="primary">
                    Kaydet
                </x-filament::button>
            </div>
        </form>
    </div>
</x-filament-panels::page>
