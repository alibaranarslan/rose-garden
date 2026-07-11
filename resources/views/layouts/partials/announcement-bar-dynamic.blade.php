@php
    $contactPhone = $siteSettings->get('contact', collect())->get('contact_phone', '0552 271 70 67');
    $announcementModule = collect(data_get($layoutState ?? [], 'modules', []))->firstWhere('key', 'announcement_bar');
    $announcementActive = (bool) ($announcementModule['is_active'] ?? true);
    $announcementTitle = data_get($announcementModule, 'settings.title_override.'.app()->getLocale()) ?: __('Adıyaman içi bugün teslim');
    $announcementSubtitle = data_get($announcementModule, 'settings.subtitle_override.'.app()->getLocale()) ?: __('El yapımı çiçekler, butik çikolata kutuları ve özel gün hazırlıkları.');
    $contactPhoneRaw = preg_replace('/\D/', '', (string) $contactPhone);
    $contactPhoneRaw = str_starts_with($contactPhoneRaw, '0') ? '90'.substr($contactPhoneRaw, 1) : $contactPhoneRaw;
@endphp

@if ($announcementActive)
    <div class="rg-announcement-bar border-b border-white/10 bg-[linear-gradient(90deg,#1a1420_0%,#241a2e_50%,#1a1420_100%)] text-[10px] text-white sm:text-[11px]">
        <div class="mx-auto flex max-w-7xl items-center justify-between gap-3 px-4 py-1.5 sm:px-6">
            <div class="flex min-w-0 items-center gap-2.5 text-white/88">
                <span class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/8 px-2.5 py-1 font-semibold text-white/95">
                    <span class="h-1.5 w-1.5 rounded-full bg-rg-rosePink rg-pulse"></span>
                    {{ $announcementTitle }}
                </span>
                <p class="hidden truncate text-white/58 xl:block">{{ $announcementSubtitle }}</p>
            </div>
            <div class="flex shrink-0 items-center gap-3 font-medium text-white/82 sm:gap-4">
                <a href="tel:+{{ $contactPhoneRaw }}" class="hidden items-center gap-2 transition-colors hover:text-white lg:inline-flex">
                    <svg class="h-3.5 w-3.5 text-rg-rosePink" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                    </svg>
                    {{ $contactPhone }}
                </a>
                <a href="{{ \App\Support\StorefrontLocale::route('contact') }}" class="transition-colors hover:text-white">{{ __('İletişim') }}</a>
                <a href="{{ \App\Support\StorefrontLocale::route('order.track') }}" class="hidden transition-colors hover:text-white sm:inline">{{ __('Sipariş Takip') }}</a>
            </div>
        </div>
    </div>
@endif

