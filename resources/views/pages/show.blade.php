@extends('layouts.app')

@section('content')
    <article class="bg-white border border-rg-lightLavender rounded-card p-6 prose max-w-none">
        <h1 class="font-display text-4xl">{{ $page->title }}</h1>
        {!! $page->content !!}
    </article>
@endsection
