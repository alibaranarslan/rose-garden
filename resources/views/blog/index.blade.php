@extends('layouts.app')

@section('content')
    <h1 class="font-display text-3xl mb-6">Blog</h1>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        @foreach ($posts as $post)
            <article class="bg-white border border-rg-lightLavender rounded-card overflow-hidden hover:border-rg-midPurple hover:shadow-lg transition-all duration-200">
                <a href="{{ route('blog.show', ['slug' => $post->slug]) }}" class="block">
                    <img src="{{ $post->featured_image ?? asset('images/placeholder.svg') }}" alt="{{ $post->title }}" loading="lazy" class="w-full h-52 object-cover">
                    <div class="p-4">
                        <p class="text-xs text-rg-grayText mb-2">{{ $post->category?->name }} • {{ optional($post->published_at)->format('d.m.Y') }}</p>
                        <h3 class="font-semibold text-xl mb-2">{{ $post->title }}</h3>
                        <p class="text-sm text-rg-grayText mb-3">{{ $post->excerpt }}</p>
                        <span class="text-sm text-rg-purple font-medium hover:underline">{{ __('Devamını Oku') }} →</span>
                    </div>
                </a>
            </article>
        @endforeach
    </div>
    <div class="mt-6">{{ $posts->links() }}</div>
@endsection
