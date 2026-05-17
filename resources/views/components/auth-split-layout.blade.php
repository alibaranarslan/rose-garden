@props([
    'title' => '',
    'heroImage' => null,
])

@php
    $heroStrip = \App\Support\StorefrontImage::productVisualStrip(1);
    $heroPick = $heroStrip[0] ?? \App\Support\StorefrontImage::decorativeCategoryStrip()[0];
    $heroSrc = \App\Support\StorefrontImage::publicImgSrc($heroImage ?? $heroPick);
@endphp

<div {{ $attributes->merge(['class' => 'mx-auto flex w-full max-w-5xl items-center justify-center py-6 md:py-10']) }}>
    <div class="grid w-full overflow-hidden rounded-[1.5rem] border border-rg-lightLavender shadow-[0_18px_50px_rgba(34,24,40,0.08)] dark:border-white/10 dark:shadow-black/25 md:grid-cols-2 md:min-h-[min(540px,calc(100vh-10rem))]">
        <div class="relative hidden flex-col justify-center overflow-hidden bg-gradient-to-b from-rg-deepPurple via-[#38263e] to-rg-darkPlum p-8 text-white md:flex lg:p-10">
            <img
                src="{{ $heroSrc }}"
                alt=""
                class="pointer-events-none absolute inset-0 h-full w-full object-cover opacity-20"
                aria-hidden="true"
            >
            <div class="relative z-10">
                @isset($hero)
                    {{ $hero }}
                @endisset
            </div>
        </div>

        <div class="flex flex-col justify-center bg-white p-7 md:p-9 lg:p-10 dark:bg-rg-deepPurple/95">
            <div class="mb-6 text-center md:hidden">
                <x-site-logo variant="light" type="wordmark" placement="auth_light" />
            </div>
            @if ($title !== '')
                <h1 class="font-display text-2xl font-semibold tracking-tight text-rg-deepPurple dark:text-white md:text-3xl">{{ $title }}</h1>
            @endif
            {{ $slot }}
        </div>
    </div>
</div>
