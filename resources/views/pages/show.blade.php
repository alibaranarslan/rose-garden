@extends('layouts.app')

@section('content')
    @php
        $pageContent = preg_replace_callback(
            '/href=(["\'])\/(?!\/)([^"\']*)\1/i',
            function (array $matches): string {
                $path = '/'.ltrim($matches[2], '/');
                $localizablePrefixes = [
                    '/blog',
                    '/hesabim',
                    '/iletisim',
                    '/odeme',
                    '/ozel-gunler',
                    '/sayfa/',
                    '/sepet',
                    '/siparis-takip',
                    '/sss',
                    '/teslimat-bilgileri',
                    '/urunler',
                ];

                $isLocalStorefrontLink = collect($localizablePrefixes)
                    ->contains(fn (string $prefix): bool => str_starts_with($path, $prefix));

                if (! $isLocalStorefrontLink) {
                    return $matches[0];
                }

                $localizedPath = \App\Support\StorefrontLocale::path(
                    $path,
                    null,
                    request()->route('locale') !== null
                );

                return 'href='.$matches[1].e($localizedPath).$matches[1];
            },
            $page->content
        ) ?? $page->content;
    @endphp

    <div class="rg-content-shell mx-auto max-w-5xl space-y-8 md:space-y-10">
        <x-page-hero
            compact
            :eyebrow="__('Rose Garden Bilgi Sayfası')"
            :title="$page->title"
            :description="$page->meta_description ?: __('Bu sayfa, Rose Garden marka deneyiminin metin ve içerik tarafını daha okunabilir, daha profesyonel bir yüzeyde sunmak için düzenlenmiştir.')"
        >
            <x-slot:stats>
                <div class="rg-page-stat">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-rg-midPurple dark:text-rg-lavender/80">{{ __('İçerik tipi') }}</p>
                    <p class="mt-2 text-sm font-semibold text-rg-deepPurple dark:text-white">{{ __('Kurumsal içerik') }}</p>
                </div>
                <div class="rg-page-stat">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-rg-midPurple dark:text-rg-lavender/80">{{ __('Yayın durumu') }}</p>
                    <p class="mt-2 text-sm font-semibold text-rg-deepPurple dark:text-white">{{ __('Canlı') }}</p>
                </div>
            </x-slot:stats>
        </x-page-hero>

        <article class="rg-surface px-5 py-8 sm:px-8 md:px-10 md:py-12">
            <div
                class="cms-page-body max-w-none
                prose prose-neutral dark:prose-invert lg:prose-lg
                prose-p:mb-6 prose-p:text-[1.0625rem] prose-p:leading-[1.8] prose-p:text-rg-darkText/95 dark:prose-p:text-white/88
                prose-headings:font-display prose-headings:font-bold
                prose-h1:hidden
                prose-h2:mt-10 prose-h2:mb-4 prose-h2:text-2xl prose-h2:text-rg-purple dark:prose-h2:text-rg-lavender
                prose-h3:mt-8 prose-h3:mb-3 prose-h3:text-xl prose-h3:text-rg-darkPlum dark:prose-h3:text-white
                prose-h4:mt-6 prose-h4:font-semibold prose-h4:text-rg-deepPurple dark:prose-h4:text-white/95
                prose-a:text-rg-purple prose-a:font-medium hover:prose-a:underline dark:prose-a:text-rg-lavender
                prose-strong:text-rg-darkText prose-strong:font-bold dark:prose-strong:text-white
                prose-ul:my-6 prose-ol:my-6 prose-li:my-2
                prose-blockquote:border-l-4 prose-blockquote:border-rg-purple prose-blockquote:pl-4 prose-blockquote:italic dark:prose-blockquote:border-rg-lavender
                prose-img:rounded-xl"
            >
                {!! $pageContent !!}
            </div>
        </article>
    </div>
@endsection
