@extends('layouts.app')

@section('content')
    <section class="max-w-2xl mx-auto bg-white border border-rg-lightLavender rounded-card p-6">
        <h1 class="font-display text-3xl mb-4">{{ __('Sipariş Takip') }}</h1>
        <form method="POST" action="{{ route('order.track.submit') }}" class="flex gap-2 mb-5">
            @csrf
            <input type="text" name="order_number" placeholder="{{ __('Sipariş numaranızı girin') }}" value="{{ old('order_number', request('order_number')) }}" class="flex-1 border rounded-btn px-3 py-2">
            <button type="submit" class="bg-rg-purple text-white px-4 py-2 rounded-btn">{{ __('Sorgula') }}</button>
        </form>

        @if (isset($order) && $order)
            <div class="border border-rg-lightLavender rounded-card p-4">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-sm text-rg-grayText">{{ __('Sipariş No') }}</p>
                        <p class="font-semibold">{{ $order->order_number }}</p>
                    </div>
                    <span class="px-3 py-1 text-xs font-semibold rounded-full
                        @switch($order->status)
                            @case('pending') bg-yellow-100 text-yellow-800 @break
                            @case('processing') bg-blue-100 text-blue-800 @break
                            @case('shipped') bg-indigo-100 text-indigo-800 @break
                            @case('delivered') bg-green-100 text-green-800 @break
                            @case('cancelled') bg-red-100 text-red-800 @break
                            @default bg-gray-100 text-gray-800
                        @endswitch
                    ">{{ ucfirst($order->status) }}</span>
                </div>

                @if($order->statusHistory && $order->statusHistory->isNotEmpty())
                    <div class="mt-4">
                        <h3 class="text-sm font-semibold mb-3">{{ __('Sipariş Durumu Geçmişi') }}</h3>
                        <div class="relative pl-6 space-y-4">
                            @foreach($order->statusHistory->sortByDesc('created_at') as $index => $history)
                                <div class="relative">
                                    <div class="absolute -left-6 top-1 w-3 h-3 rounded-full {{ $index === 0 ? 'bg-rg-purple' : 'bg-rg-lightLavender' }}"></div>
                                    @if(!$loop->last)
                                        <div class="absolute -left-[18px] top-4 w-0.5 h-full bg-rg-lightLavender"></div>
                                    @endif
                                    <div>
                                        <span class="text-sm font-medium">{{ ucfirst($history->status) }}</span>
                                        <span class="text-xs text-rg-grayText ml-2">{{ $history->created_at->format('d.m.Y H:i') }}</span>
                                        @if($history->note)
                                            <p class="text-xs text-rg-grayText mt-1">{{ $history->note }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        @elseif(request()->isMethod('POST'))
            <p class="text-sm text-red-600">{{ __('Bu sipariş numarasıyla eşleşen bir kayıt bulunamadı.') }}</p>
        @else
            <p class="text-sm text-rg-grayText">{{ __('Sipariş durumunuz burada görünecek.') }}</p>
        @endif
    </section>
@endsection
