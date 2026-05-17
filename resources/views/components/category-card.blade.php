@props([
    'category',
    'featured' => false,
])

@php
    $coverPath = data_get($category, 'resolved_cover_path') ?: data_get($category, 'image');
    $image = \App\Support\StorefrontImage::resolveCategory(
        $coverPath,
        data_get($category, 'slug'),
        data_get($category, 'name'),
    );
    $imageSrc = \App\Support\StorefrontImage::publicImgSrc($image);
    $imageIsSvg = \Illuminate\Support\Str::endsWith(parse_url($image, PHP_URL_PATH) ?? $image, '.svg');
@endphp

<a href="{{ \App\Support\StorefrontLocale::route('products.category', ['slug' => data_get($category, 'slug')]) }}"
   class="group relative block overflow-hidden rounded-[1.65rem] border border-black/[0.06] bg-white/85 shadow-card-soft ring-1 ring-black/[0.03] transition-all duration-300 ease-rg-out hover:-translate-y-1 hover:shadow-card-soft-hover dark:border-white/11 dark:bg-white/12 dark:ring-white/[0.04] {{ $featured ? 'col-span-2 min-h-[15rem] sm:min-h-[20rem]' : 'min-h-[10rem] sm:min-h-[13rem]' }}">
    <img src="{{ $imageSrc }}"
         alt="{{ data_get($category, 'name') }}"
         loading="lazy"
         class="absolute inset-0 h-full w-full transition-transform duration-700 group-hover:scale-[1.04] {{ $imageIsSvg ? 'object-contain object-center p-8 sm:p-10' : 'object-cover' }}">
    @if ($imageIsSvg)
        <div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-black/68 via-black/22 to-black/5" aria-hidden="true"></div>
    @else
        <div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-black/90 via-black/48 to-black/18" aria-hidden="true"></div>
        <div class="pointer-events-none absolute inset-0 bg-gradient-to-b from-black/35 via-black/10 to-transparent" aria-hidden="true"></div>
        <div class="pointer-events-none absolute inset-x-0 bottom-0 h-[55%] bg-gradient-to-t from-black/80 via-black/35 to-transparent" aria-hidden="true"></div>
    @endif
    <div class="absolute inset-x-0 bottom-0 p-3 md:p-4">
        <div class="rounded-[1.25rem] border border-white/28 bg-black/48 px-4 py-3.5 shadow-[0_16px_48px_rgba(0,0,0,0.45)] backdrop-blur-lg md:px-5 md:py-4 dark:border-white/18 dark:bg-black/58">
            <span class="text-[11px] font-semibold uppercase tracking-[0.24em] text-white drop-shadow-[0_1px_3px_rgba(0,0,0,0.85)]">{{ __('Koleksiyon') }}</span>
            <span class="mt-1.5 block font-display text-[1.75rem] leading-[1.15] text-white drop-shadow-[0_2px_14px_rgba(0,0,0,0.65)] md:text-[1.9rem]">{{ data_get($category, 'name') }}</span>
            <span class="mt-2.5 inline-flex items-center gap-2 text-sm font-semibold text-white drop-shadow-[0_1px_3px_rgba(0,0,0,0.85)]">
                {{ __('İncele') }}
                <svg class="h-4 w-4 transition-transform duration-200 group-hover:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </span>
        </div>
    </div>
</a>
