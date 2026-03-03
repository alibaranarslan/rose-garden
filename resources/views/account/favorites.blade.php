@extends('layouts.app')

@section('content')
    <h1 class="font-display text-3xl mb-6">Favorilerim</h1>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        @forelse ($favorites as $favorite)
            <x-product-card :product="$favorite->product" />
        @empty
            <p class="text-sm text-rg-grayText">Heniz favori urununuz yok.</p>
        @endforelse
    </div>
    <div class="mt-6">{{ $favorites->links() }}</div>
@endsection
