@extends('layouts.app')

@section('content')
    <h1 class="font-display text-3xl mb-6">{{ __('Ödeme') }}</h1>
    <livewire:checkout-wizard />
@endsection
