@if (($blogCards ?? collect())->isNotEmpty())
    @php
        $locale = app()->getLocale();
        $title = data_get($settings, "title_override.$locale") ?: __('Blog Seçkisi');
        $subtitle = data_get($settings, "subtitle_override.$locale") ?: __('Çiçek seçimi, teslim deneyimi ve marka tonu üzerine notlar.');
    @endphp

    <section class="rg-section">
        <div class="mb-6 flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
            <div class="max-w-3xl">
                <span class="rg-kicker">{{ __('Blog') }}</span>
                <h2 class="mt-3 font-display text-3xl text-rg-deepPurple dark:text-white md:text-4xl">{{ $title }}</h2>
            </div>
            <p class="max-w-xl text-pretty text-sm leading-relaxed text-rg-grayText dark:text-white/82">{{ $subtitle }}</p>
        </div>

        <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
            @foreach ($blogCards as $card)
                <article class="rg-surface flex h-full flex-col overflow-hidden">
                    <a href="{{ \App\Support\StorefrontLocale::route('blog.show', ['slug' => $card['slug']]) }}" class="block aspect-[16/10] overflow-hidden {{ $card['cover_illustration'] ? 'bg-[linear-gradient(135deg,#f8ede7,#f4e8f1)] dark:bg-[linear-gradient(135deg,#2d2133,#23182c)]' : 'bg-rg-lightLavender/45 dark:bg-[#2a2633]' }}">
                        <img
                            src="{{ $card['cover_image'] }}"
                            alt="{{ $card['title'] }}"
                            loading="lazy"
                            class="h-full w-full transition-transform duration-500 {{ $card['cover_illustration'] ? 'object-contain p-8 hover:scale-[1.02]' : 'object-cover object-center hover:scale-[1.04]' }}"
                        >
                    </a>
                    <div class="flex flex-1 flex-col p-5">
                        <time datetime="{{ $card['published_at'] }}" class="text-xs font-semibold uppercase tracking-[0.24em] text-rg-midPurple dark:text-rg-lavender">{{ $card['published_label'] }}</time>
                        <h3 class="mt-3 text-balance font-display text-2xl leading-snug text-rg-deepPurple dark:text-white">{{ $card['title'] }}</h3>
                        <p class="rg-copy-muted mt-3 flex-1 text-pretty text-sm leading-relaxed">{{ $card['excerpt'] }}</p>
                        <a href="{{ \App\Support\StorefrontLocale::route('blog.show', ['slug' => $card['slug']]) }}" class="mt-5 inline-flex items-center gap-2 text-sm font-semibold text-rg-deepPurple transition-colors duration-200 hover:text-rg-purple dark:text-white dark:hover:text-rg-lavender">
                            {{ __('Yazıyı aç') }}
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    </div>
                </article>
            @endforeach
        </div>
    </section>
@endif
