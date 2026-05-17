@if ($instagramUrl)
    @php
        $locale = app()->getLocale();
        $title = data_get($settings, "title_override.$locale") ?: __('Instagram Akışı');
        $subtitle = data_get($settings, "subtitle_override.$locale") ?: __('Atölyeden çıkan güncel aranjmanları ve teslim anlarını sosyal kanıt olarak takip edin.');
        $ctaLabel = data_get($settings, "cta_label.$locale") ?: __('Instagram hesabına git');
        $ctaUrl = data_get($settings, 'cta_url') ?: $instagramUrl;
    @endphp

    <section class="rg-section">
        <div class="rounded-[2rem] border border-black/[0.06] bg-[linear-gradient(135deg,rgba(255,255,255,0.94),rgba(246,236,242,0.98))] px-5 py-6 shadow-[0_18px_55px_rgba(34,24,40,0.08)] md:px-6 md:py-7 dark:border-white/10 dark:bg-[linear-gradient(135deg,rgba(38,24,42,0.92),rgba(31,20,36,0.96))]">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div class="max-w-3xl">
                    <span class="rg-kicker">{{ __('Sosyal Kanıt') }}</span>
                    <h2 class="mt-3 font-display text-3xl text-rg-deepPurple dark:text-white md:text-4xl">{{ $title }}</h2>
                    <p class="mt-3 text-pretty text-sm leading-relaxed text-rg-grayText dark:text-white/82">{{ $subtitle }}</p>
                </div>
                <a href="{{ $ctaUrl }}" target="_blank" rel="noreferrer" class="inline-flex items-center justify-center rounded-full bg-rg-deepPurple px-5 py-3 text-sm font-semibold text-white transition-colors hover:bg-rg-purple">
                    {{ $ctaLabel }}
                </a>
            </div>
        </div>
    </section>
@endif
