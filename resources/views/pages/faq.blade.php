@extends('layouts.app')

@section('content')
    <div class="rg-content-shell space-y-8 md:space-y-10">
        <x-page-hero
            class="rg-page-hero--compact"
            :eyebrow="__('Sık Sorulan Sorular')"
            :title="__('Teslimat, ödeme ve ürün süreci hakkında en çok sorulan başlıklar')"
            :description="__('Bu alan, sipariş öncesinde oluşabilecek belirsizlikleri azaltmak ve müşterinin destek ekibine ihtiyaç duymadan temel kararlara ulaşmasını sağlamak için düzenlendi.')"
            compact
        >
            <x-slot:stats>
                <div class="rg-page-stat">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-rg-midPurple dark:text-rg-lavender/80">{{ __('Öne çıkan konu') }}</p>
                    <p class="mt-2 text-sm font-semibold text-rg-deepPurple dark:text-white">{{ __('Teslimat ve hazırlık akışı') }}</p>
                </div>
                <div class="rg-page-stat">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-rg-midPurple dark:text-rg-lavender/80">{{ __('Destek yaklaşımı') }}</p>
                    <p class="mt-2 text-sm leading-relaxed text-rg-grayText dark:text-white/84">{{ __('Kısa, net ve satış sürecini destekleyen cevaplar; mağaza deneyimini bölmeden bilgi verir.') }}</p>
                </div>
            </x-slot:stats>
        </x-page-hero>

        @php
            $faqs = [
                [__('Siparişimi nasıl takip edebilirim?'),
                 __('Siparişiniz onaylandıktan sonra kayıtlı e-posta adresinize takip bilgileri gönderilir. Ayrıca "Hesabım → Siparişlerim" bölümünden siparişlerinizin durumunu anlık olarak görebilirsiniz. Herhangi bir sorunuz için WhatsApp hattımızdan da bize ulaşabilirsiniz.')],
                [__('Aynı gün teslimat hangi saatlere kadar geçerli?'),
                 __("Saat 14:00'e kadar verilen siparişlerde aynı gün teslimat imkânı sunulmaktadır. 14:00'ten sonra verilen siparişler ertesi gün en erken saat 10:00-12:00 arasında teslim edilir. Hafta sonu ve resmî tatillerde teslimat saatleri değişkenlik gösterebilir.")],
                [__('Hangi ödeme yöntemlerini kabul ediyorsunuz?'),
                 __('Visa, MasterCard ve Troy markalı tüm kredi/banka kartları ile ödeme yapabilirsiniz. Ödemeler PayTR altyapısı üzerinden 3D Secure ile güvenli şekilde işlenir. Kapıda ödeme seçeneği şu an aktif değildir.')],
                [__('Çiçekler taze mi gönderiliyor?'),
                 __('Tüm çiçeklerimiz her sabah taze olarak temin edilmekte ve sipariş alındıktan sonra hazırlanmaktadır. Teslimat anında ürünlerinizin tazeliğinden emin olmak için özel koruyucu ambalaj kullanıyoruz. Bir şikâyetiniz olması hâlinde 24 saat içinde bize bildirmenizi rica ederiz.')],
                [__('İade ve iptal koşulları nelerdir?'),
                 __("Bozulabilir ürünler (taze çiçek, çikolata) niteliği gereği iade kapsamı dışındadır. Ancak teslimat öncesinde verilen siparişler saat 10:00'a kadar ücretsiz iptal edilebilir. Ürünün hasarlı ya da yanlış teslim edilmesi durumunda fotoğraflı bildirim gönderdikten sonra değişim veya iade işlemi gerçekleştirilir.")],
                [__('Teslimat bölgeniz neresi?'),
                 __('Şu an yalnızca Adıyaman merkez ve yakın ilçelerine (Kahta, Besni, Gölbaşı) teslimat yapılmaktadır. Bölge dışı siparişler için lütfen önce WhatsApp üzerinden bilgi alın; bazı durumlarda kargo ile gönderim mümkün olabilir.')],
                [__('Özel gün siparişleri ne kadar önceden verilmeli?'),
                 __('Sevgililer Günü, Anneler Günü ve Yılbaşı gibi özel dönemlerde yoğunluk yaşanabilmektedir. Bu dönemler için siparişlerinizi en az 3-5 gün önceden vermenizi öneririz. Standart dönemlerde aynı gün siparişleri kabul edilmektedir.')],
                [__('Hediye notu ekleyebilir miyim?'),
                 __('Evet. Sepet veya ödeme adımında "Hediye Notu" alanına mesajınızı yazabilirsiniz. Notunuz özel zarfta, butik kartla birlikte ürününüzün içine eklenir. Kartın üzerine yazılmasını istediğiniz mesaj en fazla 150 karakter olabilir.')],
            ];
        @endphp

        <div class="grid gap-6 lg:grid-cols-[minmax(0,1.12fr)_minmax(0,0.88fr)]">
            <div class="space-y-3">
                @foreach ($faqs as [$question, $answer])
                    <x-accordion-item :question="$question">
                        <p>{{ $answer }}</p>
                    </x-accordion-item>
                @endforeach
            </div>

            <aside class="space-y-5">
                <div class="rg-surface p-5 md:p-6">
                    <span class="rg-kicker">{{ __('Hızlı Rehber') }}</span>
                    <h2 class="mt-4 text-balance font-display text-3xl leading-[1.08] text-rg-deepPurple dark:text-white">
                        {{ __('Kararı hızlandıran kısa başlıklar') }}
                    </h2>
                    <p class="mt-4 text-sm leading-[1.85] text-rg-grayText dark:text-white/84 md:text-[15px]">
                        {{ __('SSS yüzeyi artık sadece cevap listesi değil; sipariş öncesi güven sorularını hızlıca kapatan yardımcı bir bölüm gibi davranır.') }}
                    </p>

                    <div class="mt-6 space-y-3">
                        <div class="rounded-[1.25rem] border border-black/6 bg-rg-cream/72 px-4 py-4 dark:border-white/10 dark:bg-[#241f2c]">
                            <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-rg-midPurple dark:text-rg-lavender/80">{{ __('Sipariş öncesi') }}</p>
                            <p class="mt-2 text-sm leading-relaxed text-rg-grayText dark:text-white/82">{{ __('Ödeme, teslimat ve iptal koşulları tek akışta okunur; böylece güven soruları erken çözülür.') }}</p>
                        </div>
                        <div class="rounded-[1.25rem] border border-black/6 bg-rg-cream/72 px-4 py-4 dark:border-white/10 dark:bg-[#241f2c]">
                            <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-rg-midPurple dark:text-rg-lavender/80">{{ __('Gerektiğinde destek') }}</p>
                            <p class="mt-2 text-sm leading-relaxed text-rg-grayText dark:text-white/82">{{ __('SSS’nin çözemediği durumlar için iletişim yönlendirmesi görünür biçimde korunur.') }}</p>
                        </div>
                    </div>
                </div>

                <div class="rounded-[1.8rem] border border-rg-lightLavender bg-rg-lightLavender/35 p-6 text-center shadow-[0_18px_42px_rgba(34,24,40,0.06)] dark:border-white/10 dark:bg-white/10 md:p-8">
                    <p class="text-sm text-rg-darkText dark:text-white/85">{{ __('Aradığınız cevabı bulamadınız mı?') }}</p>
                    <a href="{{ \App\Support\StorefrontLocale::route('contact') }}" class="mt-4 inline-flex items-center gap-2 rounded-xl bg-rg-purple px-6 py-3 text-sm font-semibold text-white shadow-md transition hover:bg-rg-darkPlum hover:shadow-lg">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                        {{ __('Bize yazın') }}
                    </a>
                </div>
            </aside>
        </div>
    </div>
@endsection
