<div class="bg-gradient-to-r from-rg-deepPurple via-rg-darkPlum to-rg-deepPurple text-white text-xs md:text-sm">
    <div class="max-w-7xl mx-auto px-4 py-2 flex items-center justify-center gap-3 flex-wrap">
        <span class="flex items-center gap-1.5">
            <svg class="w-3.5 h-3.5 text-rg-rosePink flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8l1.5 10a2 2 0 002 1.7h7a2 2 0 002-1.7L19 8"/>
            </svg>
            {{ __('Aynı Gün Teslimat') }}
        </span>
        <span class="text-rg-lavender hidden sm:inline">•</span>
        <span class="hidden sm:flex items-center gap-1.5">
            <svg class="w-3.5 h-3.5 text-rg-rosePink flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
            {{ __('Taze Çiçek Garantisi') }}
        </span>
        <span class="text-rg-lavender hidden md:inline">•</span>
        <span class="hidden md:flex items-center gap-1.5">
            <svg class="w-3.5 h-3.5 text-rg-rosePink flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
            </svg>
            {{ $siteSettings->get('contact', collect())->get('contact_phone', '+90 542 000 00 00') }}
        </span>
    </div>
</div>
