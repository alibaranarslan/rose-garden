@props(['title'])

<section
    class="relative mb-6 overflow-hidden rounded-2xl border border-purple-100/80 bg-gradient-to-br from-purple-50 via-fuchsia-50/35 to-pink-50/60 px-6 py-10 text-center shadow-sm dark:border-white/10 dark:from-rg-deepPurple/90 dark:via-rg-darkPlum/75 dark:to-rg-deepPurple/85 md:mb-8 md:px-10 md:py-12"
    aria-labelledby="category-catalog-heading"
>
    {{-- Hafif çiçek / organik desen (SVG tekrar) --}}
    <div
        class="pointer-events-none absolute inset-0 opacity-[0.14] dark:opacity-[0.09]"
        style="background-image: url('data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22120%22 height=%22120%22 viewBox=%220 0 120 120%22%3E%3Cpath fill=%22%239333ea%22 fill-opacity=%22.35%22 d=%22M60 8c4 8 4 16 0 24-8-4-16-4-24 0 4-8 4-16 0-24 8 4 16 4 24 0zm28 28c6 6 6 14 0 20-6-6-14-6-20 0 6-6 14-6 20 0zm-56 0c6 6 6 14 0 20 6-6 14-6 20 0-6-6-6-14 0-20zm28 36c5 10 5 20 0 30-10-5-20-5-30 0 5-10 5-20 0-30 10 5 20 5 30 0z%22/%3E%3C/svg%3E'); background-size: 120px 120px;"
        aria-hidden="true"
    ></div>
    <div
        class="pointer-events-none absolute -right-16 -top-16 h-48 w-48 rounded-full bg-fuchsia-200/25 blur-3xl dark:bg-rg-purple/20"
        aria-hidden="true"
    ></div>
    <div
        class="pointer-events-none absolute -bottom-20 -left-12 h-56 w-56 rounded-full bg-purple-200/30 blur-3xl dark:bg-rg-midPurple/15"
        aria-hidden="true"
    ></div>

    <h1
        id="category-catalog-heading"
        class="relative font-display text-3xl font-bold tracking-tight text-rg-deepPurple dark:text-white md:text-4xl lg:text-5xl"
    >
        {{ $title }}
    </h1>
</section>
