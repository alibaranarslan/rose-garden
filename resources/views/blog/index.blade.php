@extends('layouts.app')

@section('content')
    @php
        $featuredPost = $posts->first();
        $secondaryPosts = $posts->slice(1)->values();
    @endphp

    <div class="rg-content-shell space-y-8 md:space-y-10">
        <x-page-hero
            class="rg-page-hero--compact"
            :eyebrow="__('Atölyeden Notlar')"
            :title="__('Çiçek bakımı, hediye dili ve sezon seçimleri için editoryal rehber')"
            :description="__('Rose Garden blogu; ürün seçimini destekleyen bakım notları, özel gün önerileri ve daha rafine hediye kararları için hazırlanmış bir rehber alanı olarak çalışır.')"
            compact
        >
            <x-slot:actions>
                <a href="{{ \App\Support\StorefrontLocale::route('products.index') }}" class="inline-flex items-center justify-center rounded-full bg-rg-deepPurple px-5 py-3 text-sm font-semibold text-white transition-colors duration-200 hover:bg-rg-purple">
                    {{ __('Koleksiyonu Keşfet') }}
                </a>
                <a href="{{ \App\Support\StorefrontLocale::route('contact') }}" class="rg-button-secondary">
                    {{ __('Butik öneri iste') }}
                </a>
            </x-slot:actions>

            <x-slot:stats>
                <div class="rg-page-stat">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-rg-midPurple dark:text-rg-lavender/80">{{ __('Yayınlanan yazı') }}</p>
                    <p class="mt-2 text-2xl font-semibold text-rg-deepPurple dark:text-white">{{ number_format($posts->total(), 0, ',', '.') }}</p>
                </div>
                <div class="rg-page-stat">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-rg-midPurple dark:text-rg-lavender/80">{{ __('Editoryal amaç') }}</p>
                    <p class="mt-2 text-sm leading-relaxed text-rg-grayText dark:text-white/84">{{ __('Yazılar, yalnızca içerik değil; ürün seçimini kolaylaştıran sakin bir servis yüzeyi olarak kurgulanır.') }}</p>
                </div>
            </x-slot:stats>
        </x-page-hero>

        @if ($featuredPost)
            @php
                $featuredCover = \App\Support\StorefrontImage::publicImgSrc(
                    \App\Support\StorefrontImage::resolveBlog($featuredPost->featured_image, $featuredPost->slug, $featuredPost->title, $featuredPost->category?->name)
                );
                $featuredCoverPath = parse_url($featuredCover, PHP_URL_PATH) ?: $featuredCover;
                $featuredCoverIsIllustration = \Illuminate\Support\Str::endsWith($featuredCoverPath, '.svg');
            @endphp

            <section class="grid gap-5 xl:grid-cols-[minmax(0,1.18fr)_minmax(0,0.82fr)]">
                <article class="rg-surface overflow-hidden">
                    <a href="{{ \App\Support\StorefrontLocale::route('blog.show', ['slug' => $featuredPost->slug]) }}" class="grid h-full md:grid-cols-[minmax(0,1.02fr)_minmax(0,0.98fr)]">
                        <div class="overflow-hidden {{ $featuredCoverIsIllustration ? 'bg-[linear-gradient(135deg,#f8ede7,#f4e8f1)] dark:bg-[linear-gradient(135deg,#2d2133,#23182c)]' : 'bg-rg-cream dark:bg-[#2a2633]' }}">
                            <img
                                src="{{ $featuredCover }}"
                                alt="{{ $featuredPost->title }}"
                                loading="lazy"
                                class="h-full w-full transition duration-500 ease-out {{ $featuredCoverIsIllustration ? 'object-contain p-10 hover:scale-[1.02]' : 'object-cover object-center hover:scale-[1.03]' }}"
                            >
                        </div>
                        <div class="flex min-h-0 flex-col p-5 md:p-6">
                            <p class="text-xs font-medium text-rg-midPurple dark:text-rg-lavender">
                                {{ $featuredPost->category?->name }}
                                <span class="text-rg-lightLavender dark:text-white/25">·</span>
                                <time datetime="{{ optional($featuredPost->published_at)->toIso8601String() }}">{{ optional($featuredPost->published_at)->format('d.m.Y') }}</time>
                                @if($featuredPost->author)
                                    <span class="text-rg-lightLavender dark:text-white/25">·</span>
                                    <span>{{ $featuredPost->author->name }}</span>
                                @endif
                            </p>
                            <h2 class="mt-3 font-display text-[2.1rem] font-semibold leading-tight text-rg-darkText transition group-hover:text-rg-purple dark:text-white md:text-[2.3rem]">
                                {{ $featuredPost->title }}
                            </h2>
                            @if($featuredPost->excerpt)
                                <p class="rg-copy-muted mt-4 flex-1 text-sm leading-[1.85] md:text-[15px]">{{ $featuredPost->excerpt }}</p>
                            @endif
                            <span class="mt-5 inline-flex items-center gap-2 text-sm font-semibold text-rg-purple dark:text-rg-lavender">
                                {{ __('Yazıyı Oku') }}
                                <span aria-hidden="true">→</span>
                            </span>
                        </div>
                    </a>
                </article>

                <aside class="rg-surface px-5 py-6 md:px-6 md:py-7">
                    <span class="rg-kicker">{{ __('Editoryal Not') }}</span>
                    <h2 class="mt-4 text-balance font-display text-3xl leading-[1.08] text-rg-deepPurple dark:text-white">
                        {{ __('Blog artık mağazanın doğal uzantısı gibi davranıyor.') }}
                    </h2>
                    <p class="mt-4 text-sm leading-[1.85] text-rg-grayText dark:text-white/84 md:text-[15px]">
                        {{ __('Daha sakin tipografi, daha net kart hiyerarşisi ve daha büyük görsel alanlarla blog; bilgi veren ama alışveriş akışını bölmeyen bir editoryal katman haline getirildi.') }}
                    </p>

                    <div class="mt-6 space-y-3">
                        <div class="rounded-[1.25rem] border border-black/6 bg-rg-cream/72 px-4 py-4 dark:border-white/10 dark:bg-[#241f2c]">
                            <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-rg-midPurple dark:text-rg-lavender/80">{{ __('Okuma biçimi') }}</p>
                            <p class="mt-2 text-sm leading-relaxed text-rg-grayText dark:text-white/82">{{ __('Kısa rehberler, müşterinin ürün sayfasına dönmeden kararını olgunlaştırmasına yardımcı olur.') }}</p>
                        </div>
                        <div class="rounded-[1.25rem] border border-black/6 bg-rg-cream/72 px-4 py-4 dark:border-white/10 dark:bg-[#241f2c]">
                            <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-rg-midPurple dark:text-rg-lavender/80">{{ __('Marka tonu') }}</p>
                            <p class="mt-2 text-sm leading-relaxed text-rg-grayText dark:text-white/82">{{ __('Editoryal yüzey, premium hissi korurken utility tarafıyla daha uyumlu bir ritme çekildi.') }}</p>
                        </div>
                    </div>
                </aside>
            </section>
        @endif

        @if ($secondaryPosts->isNotEmpty())
            <section class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-3">
                @foreach ($secondaryPosts as $post)
                    @php
                        $coverImage = \App\Support\StorefrontImage::publicImgSrc(
                            \App\Support\StorefrontImage::resolveBlog($post->featured_image, $post->slug, $post->title, $post->category?->name)
                        );
                        $coverPath = parse_url($coverImage, PHP_URL_PATH) ?: $coverImage;
                        $coverIsIllustration = \Illuminate\Support\Str::endsWith($coverPath, '.svg');
                    @endphp
                    <article class="group flex h-full flex-col overflow-hidden rounded-[1.75rem] border border-rg-lightLavender bg-white shadow-[0_16px_40px_rgba(34,24,40,0.07)] transition duration-300 ease-out hover:-translate-y-1 hover:shadow-[0_22px_48px_rgba(34,24,40,0.11)] dark:border-white/10 dark:bg-rg-deepPurple/35 dark:hover:border-rg-lavender/25">
                        <a href="{{ \App\Support\StorefrontLocale::route('blog.show', ['slug' => $post->slug]) }}" class="flex h-full flex-col focus:outline-none focus-visible:ring-2 focus-visible:ring-rg-purple focus-visible:ring-offset-2 dark:focus-visible:ring-offset-rg-deepPurple">
                            <div class="relative aspect-[4/3] w-full shrink-0 overflow-hidden {{ $coverIsIllustration ? 'bg-[linear-gradient(135deg,#f8ede7,#f4e8f1)] dark:bg-[linear-gradient(135deg,#2d2133,#23182c)]' : 'bg-rg-cream dark:bg-[#2a2633]' }}">
                                <img
                                    src="{{ $coverImage }}"
                                    alt="{{ $post->title }}"
                                    loading="lazy"
                                    class="h-full w-full transition duration-500 ease-out {{ $coverIsIllustration ? 'object-contain p-8 group-hover:scale-[1.02]' : 'object-cover object-center group-hover:scale-[1.03]' }}"
                                >
                            </div>
                            <div class="flex flex-1 flex-col p-4 md:p-5">
                                <p class="text-xs font-medium text-rg-midPurple dark:text-rg-lavender">
                                    {{ $post->category?->name }}
                                    <span class="text-rg-lightLavender dark:text-white/25">·</span>
                                    <time datetime="{{ optional($post->published_at)->toIso8601String() }}">{{ optional($post->published_at)->format('d.m.Y') }}</time>
                                    @if($post->author)
                                        <span class="text-rg-lightLavender dark:text-white/25">·</span>
                                        <span>{{ $post->author->name }}</span>
                                    @endif
                                </p>
                                <h2 class="mt-2 font-display text-xl font-semibold leading-snug text-rg-darkText transition group-hover:text-rg-purple dark:text-white dark:group-hover:text-rg-lavender">
                                    {{ $post->title }}
                                </h2>
                                @if($post->excerpt)
                                    <p class="rg-copy-muted mt-3 line-clamp-3 flex-1 text-sm leading-relaxed">{{ $post->excerpt }}</p>
                                @endif
                                <span class="mt-4 inline-flex items-center gap-1 text-sm font-semibold text-rg-purple dark:text-rg-lavender">
                                    {{ __('Devamını Oku') }}
                                    <span aria-hidden="true" class="transition group-hover:translate-x-0.5">→</span>
                                </span>
                            </div>
                        </a>
                    </article>
                @endforeach
            </section>
        @endif

        <div class="mt-10 md:mt-12">{{ $posts->links('vendor.pagination.rg-account') }}</div>
    </div>
@endsection
