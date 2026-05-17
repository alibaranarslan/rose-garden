@php
    $user = auth()->user();
@endphp

@if ($user && \App\Support\AdminPrivileges::canAccessAdminPanel($user))
    <button
        type="button"
        x-data="{}"
        x-on:click="$dispatch('rg-admin-guide:open')"
        class="hidden md:inline-flex items-center gap-2 rounded-xl border border-rose-300/40 bg-rose-50 px-3 py-2 text-sm font-semibold text-rose-800 shadow-sm transition hover:border-rose-400 hover:bg-rose-100 dark:border-rose-400/25 dark:bg-rose-400/10 dark:text-rose-200"
        aria-label="Öğretici modu aç"
    >
        <x-filament::icon icon="heroicon-o-question-mark-circle" class="h-5 w-5" />
        <span class="hidden sm:inline">Yardım</span>
    </button>
@endif
