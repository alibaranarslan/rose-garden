@extends('layouts.app')

@section('content')
    <div class="rg-account-shell mx-auto max-w-6xl">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:gap-8 xl:gap-10">
            @include('account.partials.sidebar')

            <div class="min-w-0 flex-1">
                @yield('account')
            </div>
        </div>
    </div>
@endsection
