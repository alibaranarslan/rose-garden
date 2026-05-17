@extends('layouts.app')

@section('content')
    <article class="rg-content-shell space-y-8 md:space-y-10">
        @php
            $coverImage = \App\Support\StorefrontImage::publicImgSrc(
                \App\Support\StorefrontImage::resolveBlog($post->featured_image, $post->slug, $post->title, $post->category?->name)
            );
            $coverPath = parse_url($coverImage, PHP_URL_PATH) ?: $coverImage;
            $coverIsIllustration = \Illuminate\Support\Str::endsWith($coverPath, '.svg');
        @endphp

        <x-page-hero
            compact
            :eyebrow="$post->category?->name ?: __('Rose Garden Blog')"
            :title="$post->title"
            :description="__('Bu rehber; seçim sürecini sadeleştirmek, müşterinin bakım ve hediye kararlarını daha güvenli vermesini sağlamak için hazırlanmıştır.')"
        >
            <x-slot:stats>
                <div class="rg-page-stat">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-rg-midPurple dark:text-rg-lavender/80">{{ __('Yayın tarihi') }}</p>
                    <p class="mt-2 text-sm font-semibold text-rg-deepPurple dark:text-white">{{ optional($post->published_at)->format('d.m.Y') }}</p>
                </div>
                <div class="rg-page-stat">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-rg-midPurple dark:text-rg-lavender/80">{{ __('Yazan') }}</p>
                    <p class="mt-2 text-sm font-semibold text-rg-deepPurple dark:text-white">{{ $post->author?->name ?: __('Rose Garden Editoryal') }}</p>
                </div>
            </x-slot:stats>

            <x-slot:aside>
                <div class="space-y-3">
                    <div class="rg-mini-note">
                        <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-rg-midPurple dark:text-rg-lavender/80">{{ __('Okuma notu') }}</p>
                        <p class="mt-2 text-sm leading-7 text-rg-copy-muted dark:text-white/84">{{ __('Yazı boyunca yer alan öneriler, vitrine döndüğünüzde daha net bir seçim yapmanızı kolaylaştıracak şekilde tasarlanmıştır.') }}</p>
                    </div>
                    <div class="rg-mini-note">
                        <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-rg-midPurple dark:text-rg-lavender/80">{{ __('Koleksiyon bağlantısı') }}</p>
                        <p class="mt-2 text-sm leading-7 text-rg-copy-muted dark:text-white/84">{{ __('İlgili ürünler bölümü, bu yazının temasına uygun gerçek ürünleri doğrudan vitrine bağlar.') }}</p>
                    </div>
                </div>
            </x-slot:aside>
        </x-page-hero>

        <div class="rg-service-row">
            <span class="rg-service-chip">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 7h16M4 12h12M4 17h8" /></svg>
                {{ __('Kısa ve okunabilir rehber yapısı') }}
            </span>
            <span class="rg-service-chip">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 3l7 4v5c0 4.5-2.5 7.5-7 9-4.5-1.5-7-4.5-7-9V7l7-4z" /></svg>
                {{ __('Mağaza deneyimini destekleyen içerik') }}
            </span>
        </div>

        <div class="overflow-hidden rounded-[1.9rem] border border-rg-lightLavender/90 shadow-[0_18px_52px_rgba(34,24,40,0.1)] dark:border-white/10">
            <div class="aspect-[21/9] min-h-[220px] w-full max-h-[min(58vh,560px)] {{ $coverIsIllustration ? 'bg-[linear-gradient(135deg,#f8ede7,#f4e8f1)] dark:bg-[linear-gradient(135deg,#2d2133,#23182c)]' : 'bg-rg-cream dark:bg-[#2a2633]' }} sm:aspect-[2/1] md:aspect-[21/9]">
                <img
                    src="{{ $coverImage }}"
                    alt="{{ $post->title }}"
                    loading="eager"
                    class="h-full w-full {{ $coverIsIllustration ? 'object-contain p-8 md:p-10' : 'object-cover object-center' }}"
                >
            </div>
        </div>

        <div class="mx-auto max-w-3xl">
            <div
                class="prose prose-lg max-w-none dark:prose-invert
                prose-p:text-[1.0625rem] prose-p:leading-[1.88] prose-p:mb-6 prose-p:text-rg-darkText/95 dark:prose-p:text-white/90
                prose-headings:font-display prose-headings:font-bold prose-headings:text-rg-deepPurple dark:prose-headings:text-white
                prose-h2:mt-12 prose-h2:mb-4 prose-h2:text-2xl prose-h2:text-rg-purple dark:prose-h2:text-rg-lavender
                prose-h3:mt-8 prose-h3:mb-3 prose-h3:text-xl prose-h3:text-rg-darkPlum dark:prose-h3:text-white/95
                prose-a:text-rg-purple prose-a:font-medium prose-a:no-underline hover:prose-a:underline dark:prose-a:text-rg-lavender
                prose-li:my-2 prose-ul:my-6 prose-img:rounded-xl
                prose-blockquote:border-rg-purple prose-blockquote:text-rg-grayText dark:prose-blockquote:border-rg-lavender dark:prose-blockquote:text-white/88"
            >
                {!! $post->content !!}
            </div>

            <div class="mt-10 border-t border-rg-lightLavender pt-7 dark:border-white/10">
                <x-share-buttons :url="url()->current()" :title="$post->title" />
            </div>
        </div>

        @if($relatedProducts->isNotEmpty())
            <section class="mx-auto max-w-6xl border-t border-rg-lightLavender pt-10 dark:border-white/10 md:pt-12">
                <div class="mb-5 flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
                    <div>
                        <span class="rg-kicker">{{ __('İlgili Ürünler') }}</span>
                        <h2 class="mt-3 font-display text-2xl font-semibold text-rg-deepPurple dark:text-white md:text-3xl">{{ __('Bu yazıyla uyumlu seçimler') }}</h2>
                    </div>
                    <p class="max-w-xl text-sm leading-7 text-rg-copy-muted dark:text-white/84">{{ __('Rehberin tonu ve konusu ile uyumlu ürünler, içerikten mağazaya geçişi daha doğal hale getirir.') }}</p>
                </div>
                <x-product-rail :products="$relatedProducts" :interactive="false" />
            </section>
        @endif
    </article>
@endsection
