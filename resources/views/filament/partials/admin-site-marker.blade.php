@php
    $context = $context ?? 'topbar';
    $loginExtra = $context === 'login' ? 'justify-center' : '';
@endphp

<span
    class="inline-flex max-w-full items-center gap-1.5 rounded-lg px-2.5 py-1 text-xs font-semibold leading-tight ring-1 ring-inset bg-emerald-950/90 text-emerald-50 ring-emerald-400/30 dark:bg-emerald-900/40 dark:text-emerald-100 dark:ring-emerald-500/25 {{ $loginExtra }}"
    title="Rose Garden e-ticaret yönetim paneli"
>
    <span class="opacity-80" aria-hidden="true">RG</span>
    <span class="hidden md:inline">·</span>
    <span class="sm:hidden">Yönetim</span>
    <span class="hidden sm:inline truncate">Rose Garden — E-ticaret yönetimi</span>
</span>
