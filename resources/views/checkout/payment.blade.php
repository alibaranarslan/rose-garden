@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto py-12">
    <h1 class="text-2xl font-display text-rg-darkPlum mb-6 text-center">{{ __('Ödeme') }}</h1>

    <div class="bg-white rounded-card border border-rg-lightLavender p-4 mb-6">
        <div class="flex justify-between items-center mb-2">
            <span class="text-rg-grayText">{{ __('Sipariş No') }}:</span>
            <strong class="text-rg-darkText">{{ $order->order_number }}</strong>
        </div>
        <div class="flex justify-between items-center">
            <span class="text-rg-grayText">{{ __('Toplam') }}:</span>
            <strong class="text-rg-darkPlum text-xl">₺{{ number_format($order->total, 2, ',', '.') }}</strong>
        </div>
    </div>

    @if (!empty($iframeUrl))
        <div class="bg-white rounded-card border border-rg-lightLavender overflow-hidden">
            <iframe src="{{ $iframeUrl }}" id="paytriframe" frameborder="0"
                    style="width: 100%; min-height: 600px;"
                    scrolling="no"></iframe>
        </div>
    @else
        <div class="bg-amber-50 border border-amber-200 rounded-card p-6 text-center">
            <p class="text-amber-800">{{ __('Ödeme sistemi şu an yapılandırılıyor. Lütfen daha sonra tekrar deneyin.') }}</p>
        </div>
    @endif
</div>

@push('scripts')
<script>
    iFrameResize({}, '#paytriframe');
</script>
@endpush
@endsection
