@extends('layouts.account')

@section('account')
    <header class="mb-8">
        <h1 class="font-display text-2xl font-semibold tracking-tight text-rg-deepPurple dark:text-white md:text-3xl">{{ __('KVKK ve gizlilik') }}</h1>
        <p class="mt-2 max-w-2xl text-sm text-rg-grayText dark:text-white/78">
            {{ __('6698 sayılı KVKK kapsamında kişisel verilerinizle ilgili haklarınızı bu sayfadan kullanabilirsiniz.') }}
        </p>
    </header>

    @if (session('status'))
        <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-500/30 dark:bg-emerald-950/40 dark:text-emerald-200">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800 dark:border-red-500/30 dark:bg-red-950/40 dark:text-red-200">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="rounded-2xl border border-rg-lightLavender bg-white p-6 shadow-sm dark:border-white/10 dark:bg-rg-deepPurple/40 lg:col-span-2 md:p-8">
            <h2 class="font-display text-lg font-semibold text-rg-deepPurple dark:text-white">{{ __('Kişisel veri özeti') }}</h2>
            <dl class="mt-6 grid gap-4 text-sm sm:grid-cols-2">
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wide text-rg-midPurple dark:text-rg-lavender">{{ __('Ad Soyad') }}</dt>
                    <dd class="mt-1 font-medium text-rg-darkText dark:text-white">{{ $summary['name'] }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wide text-rg-midPurple dark:text-rg-lavender">{{ __('E-posta') }}</dt>
                    <dd class="mt-1 font-medium text-rg-darkText dark:text-white">{{ $summary['email'] }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wide text-rg-midPurple dark:text-rg-lavender">{{ __('Telefon') }}</dt>
                    <dd class="mt-1 font-medium text-rg-darkText dark:text-white">{{ $summary['phone'] ?: '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wide text-rg-midPurple dark:text-rg-lavender">{{ __('Kayıt tarihi') }}</dt>
                    <dd class="mt-1 font-medium text-rg-darkText dark:text-white">{{ $summary['registered_at']?->format('d.m.Y H:i') }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wide text-rg-midPurple dark:text-rg-lavender">{{ __('KVKK onayı') }}</dt>
                    <dd class="mt-1 font-medium text-rg-darkText dark:text-white">{{ $summary['kvkk_accepted_at']?->format('d.m.Y H:i') ?: __('Kayıt bulunamadı') }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wide text-rg-midPurple dark:text-rg-lavender">{{ __('Pazarlama izni') }}</dt>
                    <dd class="mt-1 font-medium text-rg-darkText dark:text-white">{{ $summary['marketing_consent'] ? __('Onaylı') : __('Reddedilmiş') }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wide text-rg-midPurple dark:text-rg-lavender">{{ __('Adres sayısı') }}</dt>
                    <dd class="mt-1 font-medium text-rg-darkText dark:text-white">{{ $summary['address_count'] }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wide text-rg-midPurple dark:text-rg-lavender">{{ __('Sipariş sayısı') }}</dt>
                    <dd class="mt-1 font-medium text-rg-darkText dark:text-white">{{ $summary['order_count'] }}</dd>
                </div>
                <div class="sm:col-span-2">
                    <dt class="text-xs font-semibold uppercase tracking-wide text-rg-midPurple dark:text-rg-lavender">{{ __('Puan bakiyesi') }}</dt>
                    <dd class="mt-1 font-medium text-rg-darkText dark:text-white">{{ number_format($summary['loyalty_balance'], 0, ',', '.') }}</dd>
                </div>
            </dl>
        </div>

        <div class="rounded-2xl border border-rg-lightLavender bg-white p-6 shadow-sm dark:border-white/10 dark:bg-rg-deepPurple/40">
            <h2 class="font-display text-lg font-semibold text-rg-deepPurple dark:text-white">{{ __('Pazarlama izni') }}</h2>
            <p class="mt-3 text-sm text-rg-grayText dark:text-white/82">
                {{ __('Mevcut durum:') }}
                <strong class="text-rg-darkText dark:text-white">{{ $summary['marketing_consent'] ? __('Onaylı') : __('Reddedilmiş') }}</strong>
            </p>
            <form
                method="POST"
                action="{{ \App\Support\StorefrontLocale::route('account.kvkk.withdraw-marketing') }}"
                class="mt-6"
                data-confirm-message="{{ __('Pazarlama iznini geri çekmek istiyor musunuz?') }}"
                onsubmit="return confirm(this.dataset.confirmMessage);"
            >
                @csrf
                <button type="submit" class="w-full rounded-xl bg-rg-purple py-3 text-sm font-semibold text-white shadow-md transition hover:bg-rg-darkPlum">
                    {{ __('Pazarlama iznini geri çek') }}
                </button>
            </form>
        </div>
    </div>

    <div class="mt-8 grid gap-6 lg:grid-cols-3">
        <div class="rounded-2xl border border-rg-lightLavender bg-white p-6 shadow-sm dark:border-white/10 dark:bg-rg-deepPurple/40 lg:col-span-2 md:p-8">
            <h2 class="font-display text-lg font-semibold text-rg-deepPurple dark:text-white">{{ __('Veri talebi oluştur') }}</h2>
            <form method="POST" action="{{ \App\Support\StorefrontLocale::route('account.kvkk.request') }}" class="mt-6 space-y-5">
                @csrf
                <div>
                    <label for="kvkk-type" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-rg-midPurple dark:text-rg-lavender">{{ __('Talep türü') }}</label>
                    <select id="kvkk-type" name="type" class="w-full rounded-xl border border-rg-lightLavender bg-white px-4 py-3 text-sm shadow-sm outline-none focus:border-rg-purple focus:ring-2 focus:ring-rg-purple/40 dark:border-white/15 dark:bg-rg-deepPurple/40 dark:text-white">
                        <option value="view">{{ __('Verilerimi görüntüle') }}</option>
                        <option value="export">{{ __('Verilerimi dışa aktar') }}</option>
                        <option value="delete">{{ __('Hesabımı ve verilerimi sil') }}</option>
                        <option value="consent_withdraw">{{ __('İzin geri çekme') }}</option>
                    </select>
                </div>
                <div>
                    <label for="kvkk-reason" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-rg-midPurple dark:text-rg-lavender">{{ __('Açıklama (isteğe bağlı)') }}</label>
                    <textarea id="kvkk-reason" name="reason" rows="3" maxlength="500" class="w-full rounded-xl border border-rg-lightLavender bg-white px-4 py-3 text-sm shadow-sm outline-none focus:border-rg-purple focus:ring-2 focus:ring-rg-purple/40 dark:border-white/15 dark:bg-rg-deepPurple/40 dark:text-white">{{ old('reason') }}</textarea>
                </div>
                <button type="submit" class="rounded-xl bg-rg-purple px-6 py-3 text-sm font-semibold text-white shadow-md transition hover:bg-rg-darkPlum">
                    {{ __('Talep gönder') }}
                </button>
            </form>
        </div>

        <div class="rounded-2xl border border-rg-lightLavender bg-white p-6 shadow-sm dark:border-white/10 dark:bg-rg-deepPurple/40">
            <h2 class="font-display text-lg font-semibold text-rg-deepPurple dark:text-white">{{ __('Veri dışa aktarma') }}</h2>
            <p class="mt-3 text-sm text-rg-grayText dark:text-white/82">{{ __('Kişisel verileriniz JSON dosyası olarak indirilecektir.') }}</p>
            <a href="{{ \App\Support\StorefrontLocale::route('account.kvkk.export') }}" class="mt-6 flex w-full items-center justify-center rounded-xl bg-rg-purple py-3 text-sm font-semibold text-white shadow-md transition hover:bg-rg-darkPlum">
                {{ __('Verilerimi indir') }}
            </a>
        </div>
    </div>

    <section class="mt-10 rounded-2xl border border-rg-lightLavender bg-white p-6 shadow-sm dark:border-white/10 dark:bg-rg-deepPurple/40 md:p-8">
        <h2 class="font-display text-lg font-semibold text-rg-deepPurple dark:text-white">{{ __('Önceki taleplerim') }}</h2>
        <ul class="mt-6 space-y-3">
            @forelse ($requests as $item)
                <li class="flex flex-col gap-2 rounded-xl border border-rg-lightLavender/80 px-4 py-3 dark:border-white/10 sm:flex-row sm:flex-wrap sm:items-center sm:justify-between">
                    <span class="text-sm font-medium text-rg-darkText dark:text-white">{{ $item->created_at?->format('d.m.Y H:i') }}</span>
                    <span class="text-sm text-rg-grayText dark:text-white/82">{{ $item->type }} · {{ $item->status }}</span>
                    @if($item->admin_notes)
                        <p class="w-full text-xs text-rg-grayText dark:text-white/70">{{ $item->admin_notes }}</p>
                    @endif
                </li>
            @empty
                <li class="py-6 text-center text-sm text-rg-grayText dark:text-white/78">{{ __('Henüz talep kaydınız bulunmuyor.') }}</li>
            @endforelse
        </ul>
    </section>
@endsection
