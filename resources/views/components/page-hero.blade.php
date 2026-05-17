@props([
    'eyebrow' => null,
    'title' => '',
    'description' => null,
    'compact' => false,
])

@php
    $hasActions = isset($actions) && trim((string) $actions) !== '';
    $hasStats = isset($stats) && trim((string) $stats) !== '';
    $hasAside = isset($aside) && trim((string) $aside) !== '';
@endphp

<section {{ $attributes->class(['rg-page-hero', 'rg-page-hero--compact' => $compact]) }}>
    <div @class([
        'rg-page-hero__grid',
        'rg-page-hero__grid--with-aside' => $hasAside,
    ])>
        <div class="space-y-5">
            @if (filled($eyebrow))
                <span class="rg-kicker">{{ $eyebrow }}</span>
            @endif

            <div class="space-y-3">
                <h1 class="max-w-4xl text-balance font-display text-4xl leading-[1.08] text-rg-deepPurple dark:text-white md:text-5xl">
                    {{ $title }}
                </h1>

                @if (filled($description))
                    <p class="max-w-3xl text-pretty text-sm leading-7 text-rg-copy-muted dark:text-white/86 md:text-[15px]">
                        {{ $description }}
                    </p>
                @endif
            </div>

            @if ($hasActions)
                <div class="flex flex-wrap gap-3">
                    {{ $actions }}
                </div>
            @endif

            @if ($hasStats)
                <div class="rg-page-hero__stats">
                    {{ $stats }}
                </div>
            @endif
        </div>

        @if ($hasAside)
            <div class="rg-page-hero__aside">
                {{ $aside }}
            </div>
        @endif
    </div>
</section>
