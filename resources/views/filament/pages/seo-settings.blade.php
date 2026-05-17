<x-filament-panels::page class="admin-page-frame">
    <form wire:submit="save" class="space-y-6">
        <div class="admin-note">SEO ayarları vitrinde doğrudan görünür. Canonical domain, meta alanları ve robots.txt ek kuralları arama görünürlüğünü etkiler.</div>
        <section class="admin-section-panel">
            {{ $this->form }}
            <div class="admin-action-bar mt-6"><x-filament::button type="submit">SEO ayarlarını kaydet</x-filament::button></div>
        </section>
    </form>
</x-filament-panels::page>
