<button
    type="button"
    class="inline-flex h-11 w-11 items-center justify-center rounded-full border border-rg-lightLavender/80 bg-white/92 p-1.5 leading-none text-rg-darkText shadow-sm transition-colors hover:bg-rg-lightLavender/40 hover:text-rg-purple dark:border-white/12 dark:bg-white/10 dark:text-white/92 dark:hover:bg-white/14 dark:hover:text-rg-lavender"
    aria-pressed="false"
    data-theme-toggle
    title="{{ __('Tema değiştir') }}"
>
    <span class="sr-only" data-theme-toggle-label>{{ __('Koyu temaya geç') }}</span>
    <svg class="h-5 w-5 dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 0 1 8.646 3.646 9 9 0 1 0 20.354 15.354z"/>
    </svg>
    <svg class="hidden h-5 w-5 dark:block" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364-.707-.707M6.343 6.343l-.707-.707m12.728 0-.707.707M6.343 17.657l-.707.707"/>
        <circle cx="12" cy="12" r="4" stroke-width="2"/>
    </svg>
</button>

@once
    @push('scripts')
        <script>
            (function () {
                var k = 'rg-theme';
                function syncBtn(btn) {
                    var dark = document.documentElement.classList.contains('dark');
                    btn.setAttribute('aria-pressed', dark ? 'true' : 'false');
                    var label = btn.querySelector('[data-theme-toggle-label]');
                    if (label) {
                        label.textContent = dark ? @json(__('Açık temaya geç')) : @json(__('Koyu temaya geç'));
                    }
                }
                document.querySelectorAll('[data-theme-toggle]').forEach(function (btn) {
                    syncBtn(btn);
                    btn.addEventListener('click', function () {
                        var next = !document.documentElement.classList.contains('dark');
                        document.documentElement.classList.toggle('dark', next);
                        localStorage.setItem(k, next ? 'dark' : 'light');
                        document.querySelectorAll('[data-theme-toggle]').forEach(syncBtn);
                    });
                });
            })();
        </script>
    @endpush
@endonce
