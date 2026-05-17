@props([
    'allCategories' => collect(),
    'filterTags' => collect(),
    'category' => null,
    'availableSizes' => collect(),
])

@php
    $queryExceptPage = request()->except('page');
    $selectedTags = array_values(array_filter((array) request('tags', [])));
@endphp

<input type="hidden" name="sort" value="{{ request('sort', 'recommended') }}">

{{-- Kategori --}}
<details class="group mb-5 border-b border-rg-lightLavender/70 pb-5 first:pt-0 dark:border-white/10" open>
    <summary class="flex cursor-pointer list-none items-center justify-between gap-2 py-2 font-semibold text-rg-darkText dark:text-white text-sm select-none [&::-webkit-details-marker]:hidden">
        <span>{{ __('Kategori') }}</span>
        <svg class="w-4 h-4 shrink-0 text-rg-purple/70 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
        </svg>
    </summary>
    <div class="mt-3 space-y-1.5 pl-0.5">
        @php
            $allProductsUrl = \App\Support\StorefrontLocale::route('products.index');
            $allQ = http_build_query($queryExceptPage);
            $allHref = $allQ !== '' ? $allProductsUrl.'?'.$allQ : $allProductsUrl;
        @endphp
        <a href="{{ $allHref }}"
           class="flex items-center gap-3 rounded-lg px-2 py-2 text-sm transition-colors {{ request()->routeIs('products.index') ? 'bg-rg-lightLavender/90 font-bold text-rg-purple dark:bg-white/10 dark:text-rg-lavender' : 'text-rg-grayText dark:text-white/90 hover:bg-rg-lightLavender/50 dark:hover:bg-white/5' }}">
            <span class="flex h-4 w-4 shrink-0 items-center justify-center rounded-full border-2 {{ request()->routeIs('products.index') ? 'border-rg-purple bg-rg-purple' : 'border-rg-midPurple/40 dark:border-white/30' }}">
                @if(request()->routeIs('products.index'))
                    <span class="h-1.5 w-1.5 rounded-full bg-white"></span>
                @endif
            </span>
            {{ __('Tüm Ürünler') }}
        </a>
        @foreach ($allCategories as $cat)
            @php
                $catUrl = \App\Support\StorefrontLocale::route('products.category', ['slug' => $cat->slug]);
                $catHref = $allQ !== '' ? $catUrl.'?'.$allQ : $catUrl;
                $active = request()->routeIs('products.category') && request()->route('slug') === $cat->slug;
            @endphp
            <a href="{{ $catHref }}"
               class="flex items-center gap-3 rounded-lg px-2 py-2 text-sm transition-colors {{ $active ? 'bg-rg-lightLavender/90 font-bold text-rg-purple dark:bg-white/10 dark:text-rg-lavender' : 'text-rg-grayText dark:text-white/90 hover:bg-rg-lightLavender/50 dark:hover:bg-white/5' }}">
                <span class="flex h-4 w-4 shrink-0 items-center justify-center rounded-full border-2 {{ $active ? 'border-rg-purple bg-rg-purple' : 'border-rg-midPurple/40 dark:border-white/30' }}">
                    @if($active)
                        <span class="h-1.5 w-1.5 rounded-full bg-white"></span>
                    @endif
                </span>
                <span class="flex-1 truncate">{{ $cat->name }}</span>
                @if(isset($cat->products_count))
                    <span class="text-xs tabular-nums text-rg-grayText/80 dark:text-white/45">{{ $cat->products_count }}</span>
                @endif
            </a>
        @endforeach
    </div>
</details>

{{-- Fiyat --}}
<details class="group mb-5 border-b border-rg-lightLavender/70 pb-5 dark:border-white/10" open>
    <summary class="flex cursor-pointer list-none items-center justify-between gap-2 py-2 font-semibold text-rg-darkText dark:text-white text-sm select-none [&::-webkit-details-marker]:hidden">
        <span>{{ __('Fiyat Aralığı') }}</span>
        <svg class="w-4 h-4 shrink-0 text-rg-purple/70 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
        </svg>
    </summary>
    <div class="mt-3 rounded-xl border border-rg-lightLavender/80 bg-purple-50/40 p-3 dark:border-white/10 dark:bg-white/10">
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-rg-midPurple dark:text-rg-lavender">{{ __('Min') }} (₺)</label>
                <input type="number" name="min_price" min="0" step="1" value="{{ request('min_price') }}"
                       class="w-full rounded-lg border border-rg-lightLavender bg-white px-3 py-2.5 text-sm text-rg-darkText shadow-sm outline-none transition-shadow focus:border-rg-purple focus:ring-2 focus:ring-rg-purple/25 dark:border-white/15 dark:bg-rg-deepPurple/50 dark:text-white"
                       inputmode="numeric" placeholder="0">
            </div>
            <div>
                <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-rg-midPurple dark:text-rg-lavender">{{ __('Max') }} (₺)</label>
                <input type="number" name="max_price" min="0" step="1" value="{{ request('max_price') }}"
                       class="w-full rounded-lg border border-rg-lightLavender bg-white px-3 py-2.5 text-sm text-rg-darkText shadow-sm outline-none transition-shadow focus:border-rg-purple focus:ring-2 focus:ring-rg-purple/25 dark:border-white/15 dark:bg-rg-deepPurple/50 dark:text-white"
                       inputmode="numeric" placeholder="₺">
            </div>
        </div>
        <p class="mt-2 text-[11px] leading-relaxed text-rg-grayText dark:text-white/72">{{ __('Aralığı uygulamak için alttaki Uygula düğmesine basın.') }}</p>
    </div>
</details>

@if($filterTags->isNotEmpty())
    <details class="group mb-5 border-b border-rg-lightLavender/70 pb-5 dark:border-white/10" open>
        <summary class="flex cursor-pointer list-none items-center justify-between gap-2 py-2 font-semibold text-rg-darkText dark:text-white text-sm select-none [&::-webkit-details-marker]:hidden">
            <span>{{ __('Çiçek Türü / Etiket') }}</span>
            <svg class="w-4 h-4 shrink-0 text-rg-purple/70 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
            </svg>
        </summary>
        <div class="mt-3 max-h-52 space-y-2 overflow-y-auto pr-1">
            @foreach ($filterTags as $tag)
                <label class="flex cursor-pointer items-start gap-3 rounded-lg px-1 py-1.5 hover:bg-rg-lightLavender/40 dark:hover:bg-white/5 transition-colors">
                    <input type="checkbox" name="tags[]" value="{{ $tag->slug }}" class="peer sr-only" @checked(in_array($tag->slug, $selectedTags, true))>
                    <span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-md border-2 border-rg-midPurple/45 bg-white transition-all peer-checked:border-rg-purple peer-checked:bg-rg-purple peer-checked:[&_svg]:opacity-100 dark:border-white/25 dark:bg-rg-deepPurple/50 dark:peer-checked:bg-rg-lavender dark:peer-checked:border-rg-lavender">
                        <svg class="h-3 w-3 text-white opacity-0 transition-opacity" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                    </span>
                    <span class="text-sm text-rg-darkText dark:text-white/90 leading-snug">{{ $tag->name }}</span>
                </label>
            @endforeach
        </div>
    </details>
@endif

@if($availableSizes->isNotEmpty())
    <details class="group mb-5 border-b border-rg-lightLavender/70 pb-5 dark:border-white/10" open>
        <summary class="flex cursor-pointer list-none items-center justify-between gap-2 py-2 font-semibold text-rg-darkText dark:text-white text-sm select-none [&::-webkit-details-marker]:hidden">
            <span>{{ __('Boyut') }}</span>
            <svg class="w-4 h-4 shrink-0 text-rg-purple/70 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
            </svg>
        </summary>
        <div class="mt-3 space-y-2">
            <label class="flex cursor-pointer items-center gap-3 rounded-lg px-1 py-2 hover:bg-rg-lightLavender/40 dark:hover:bg-white/5">
                <input type="radio" name="size" value="" @checked(! request()->filled('size'))
                       class="h-4 w-4 shrink-0 border-rg-lightLavender text-rg-purple focus:ring-2 focus:ring-rg-purple/40 dark:border-white/25 dark:bg-rg-deepPurple dark:text-rg-lavender">
                <span class="text-sm text-rg-darkText dark:text-white/90">{{ __('Tüm Boyutlar') }}</span>
            </label>
            @foreach ($availableSizes as $size)
                <label class="flex cursor-pointer items-center gap-3 rounded-lg px-1 py-2 hover:bg-rg-lightLavender/40 dark:hover:bg-white/5">
                    <input type="radio" name="size" value="{{ $size }}" @checked(request('size') === $size)
                           class="h-4 w-4 shrink-0 border-rg-lightLavender text-rg-purple focus:ring-2 focus:ring-rg-purple/40 dark:border-white/25 dark:bg-rg-deepPurple dark:text-rg-lavender">
                    <span class="text-sm text-rg-darkText dark:text-white/90">{{ $size }}</span>
                </label>
            @endforeach
        </div>
    </details>
@endif

<details class="group mb-4 pb-1" open>
    <summary class="flex cursor-pointer list-none items-center justify-between gap-2 py-2 font-semibold text-rg-darkText dark:text-white text-sm select-none [&::-webkit-details-marker]:hidden">
        <span>{{ __('Stok Durumu') }}</span>
        <svg class="w-4 h-4 shrink-0 text-rg-purple/70 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
        </svg>
    </summary>
    <div class="mt-3">
        <label class="flex cursor-pointer items-center gap-3 rounded-lg px-1 py-2 hover:bg-rg-lightLavender/40 dark:hover:bg-white/5">
            <input type="checkbox" name="stock" value="1" class="peer sr-only" @checked(request()->boolean('stock'))>
            <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-md border-2 border-rg-midPurple/45 bg-white peer-checked:border-rg-purple peer-checked:bg-rg-purple peer-checked:[&_svg]:opacity-100 dark:border-white/25 dark:bg-rg-deepPurple/50 dark:peer-checked:bg-rg-lavender">
                <svg class="h-3 w-3 text-white opacity-0 transition-opacity" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>
            </span>
            <span class="text-sm text-rg-darkText dark:text-white/90">{{ __('Yalnızca stokta olanlar') }}</span>
        </label>
    </div>
</details>

<button type="submit"
        class="mt-4 w-full rounded-xl bg-gradient-to-r from-rg-purple to-rg-midPurple px-4 py-3.5 text-sm font-semibold text-white shadow-md transition-all hover:from-rg-darkPlum hover:to-rg-purple hover:shadow-lg focus:outline-none focus-visible:ring-2 focus-visible:ring-rg-lavender focus-visible:ring-offset-2 dark:focus-visible:ring-offset-rg-deepPurple">
    {{ __('Uygula') }}
</button>

