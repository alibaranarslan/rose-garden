@props([
    'compact' => false,
])

@php
    $settings = $__data['siteSettings'] ?? collect();
    $contact = $settings instanceof \Illuminate\Support\Collection
        ? $settings->get('contact', collect())
        : data_get($settings, 'contact', collect());
    $whatsAppPhone = \App\Support\ContactLinks::phoneForWhatsApp($contact);
    $whatsAppHref = $whatsAppPhone ? 'https://api.whatsapp.com/send?phone='.$whatsAppPhone : null;
@endphp

<div {{ $attributes->class([
    'rg-order-paused-notice',
    'rg-order-paused-notice--compact' => $compact,
]) }}>
    <div class="min-w-0">
        <p class="rg-order-paused-kicker">{{ __('Yakında alışverişe açık') }}</p>
        <h2 class="rg-order-paused-title">{{ __('Katalog yayında, online sipariş çok yakında açılıyor.') }}</h2>
        <p class="rg-order-paused-copy">
            {{ __('Ürünleri şimdiden inceleyebilirsiniz. Sipariş ve ödeme adımı, ürün/fiyat bilgileri netleştiğinde aktif edilecek.') }}
        </p>
    </div>

    @if($whatsAppHref)
        <a href="{{ $whatsAppHref }}" target="_blank" rel="noopener" class="rg-order-paused-action">
            <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
            <span>{{ __('WhatsApp ile bilgi al') }}</span>
        </a>
    @endif
</div>
