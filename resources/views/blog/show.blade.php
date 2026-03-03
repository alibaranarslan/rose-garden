@extends('layouts.app')

@section('content')
    <article class="bg-white border border-rg-lightLavender rounded-card p-6">
        <h1 class="font-display text-4xl mb-2">{{ $post->title }}</h1>
        <p class="text-sm text-rg-grayText mb-4">{{ optional($post->published_at)->format('d.m.Y') }} • {{ $post->category?->name }}</p>
        <img src="{{ $post->featured_image ?? asset('images/placeholder.svg') }}" alt="{{ $post->title }}" loading="lazy" class="w-full h-72 object-cover rounded-card mb-5">
        <div class="prose max-w-none">{!! $post->content !!}</div>
    </article>

    <section class="mt-8">
        <h2 class="font-display text-2xl mb-4">Ilgili Urunler</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach ($relatedProducts as $product)
                <x-product-card :product="$product" />
            @endforeach
        </div>
    </section>
@endsection
