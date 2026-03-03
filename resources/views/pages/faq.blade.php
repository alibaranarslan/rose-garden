@extends('layouts.app')

@section('content')
    {{-- Hero --}}
    <div class="bg-gradient-to-r from-rg-deepPurple to-rg-darkPlum text-white rounded-card p-8 mb-8 text-center">
        <h1 class="font-display text-3xl md:text-4xl font-bold mb-2">{{ __('Sık Sorulan Sorular') }}</h1>
        <p class="text-white/75">{{ __('Aklınızdaki soruların cevapları burada.') }}</p>
    </div>

    <section class="max-w-3xl mx-auto" x-data="{ open: 1 }">
        @php
        $faqs = [
            [1,  'Siparişimi nasıl takip edebilirim?',
                 'Siparişiniz onaylandıktan sonra kayıtlı e-posta adresinize takip bilgileri gönderilir. Ayrıca "Hesabım → Siparişlerim" bölümünden siparişlerinizin durumunu anlık olarak görebilirsiniz. Herhangi bir sorunuz için WhatsApp hattımızdan da bize ulaşabilirsiniz.'],
            [2,  'Aynı gün teslimat hangi saatlere kadar geçerli?',
                 'Saat 14:00\'e kadar verilen siparişlerde aynı gün teslimat imkânı sunulmaktadır. 14:00\'ten sonra verilen siparişler ertesi gün en erken saat 10:00-12:00 arasında teslim edilir. Hafta sonu ve resmi tatillerde teslimat saatleri değişkenlik gösterebilir.'],
            [3,  'Hangi ödeme yöntemlerini kabul ediyorsunuz?',
                 'Visa, MasterCard ve Troy markalı tüm kredi/banka kartları ile ödeme yapabilirsiniz. Ödemeler PayTR altyapısı üzerinden 3D Secure ile güvenli şekilde işlenir. Kapıda ödeme seçeneği şu an aktif değildir.'],
            [4,  'Çiçekler taze mi gönderiliyor?',
                 'Tüm çiçeklerimiz her sabah taze olarak temin edilmekte ve sipariş alındıktan sonra hazırlanmaktadır. Teslimat anında ürünlerinizin tazeliğinden emin olmak için özel koruyucu ambalaj kullanıyoruz. Bir şikâyetiniz olması hâlinde 24 saat içinde bize bildirmenizi rica ederiz.'],
            [5,  'İade ve iptal koşulları nelerdir?',
                 'Bozulabilir ürünler (taze çiçek, çikolata) niteliği gereği iade kapsamı dışındadır. Ancak teslimat öncesinde verilen siparişler saat 10:00\'a kadar ücretsiz iptal edilebilir. Ürünün hasarlı ya da yanlış teslim edilmesi durumunda fotoğraflı bildirim gönderdikten sonra değişim veya iade işlemi gerçekleştirilir.'],
            [6,  'Teslimat bölgeniz neresi?',
                 'Şu an yalnızca Adıyaman merkez ve yakın ilçelerine (Kahta, Besni, Gölbaşı) teslimat yapılmaktadır. Bölge dışı siparişler için lütfen önce WhatsApp üzerinden bilgi alın; bazı durumlarda kargo ile gönderim mümkün olabilir.'],
            [7,  'Özel gün siparişleri ne kadar önceden verilmeli?',
                 'Sevgililer Günü, Anneler Günü ve Yılbaşı gibi özel dönemlerde yoğunluk yaşanabilmektedir. Bu dönemler için siparişlerinizi en az 3-5 gün önceden vermenizi öneririz. Standart dönemlerde aynı gün siparişleri kabul edilmektedir.'],
            [8,  'Hediye notu ekleyebilir miyim?',
                 'Evet! Sepet veya ödeme adımında "Hediye Notu" alanına mesajınızı yazabilirsiniz. Notunuz özel zarfta, butik kartla birlikte ürününüzün içine eklenir. Kartın üzerine yazılmasını istediğiniz mesaj en fazla 150 karakter olabilir.'],
        ];
        @endphp

        @foreach ($faqs as [$num, $question, $answer])
        <div class="bg-white border border-rg-lightLavender rounded-card mb-3 overflow-hidden">
            <button
                class="w-full flex items-center justify-between px-5 py-4 text-left"
                @click="open = open === {{ $num }} ? 0 : {{ $num }}"
                :aria-expanded="open === {{ $num }}">
                <span class="font-semibold text-rg-darkText pr-4">{{ $question }}</span>
                <svg class="w-5 h-5 text-rg-purple flex-shrink-0 transition-transform duration-200"
                     :class="open === {{ $num }} ? 'rotate-180' : ''"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div x-show="open === {{ $num }}"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-1"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="px-5 pb-4">
                <p class="text-sm text-rg-grayText leading-relaxed">{{ $answer }}</p>
            </div>
        </div>
        @endforeach

        <div class="mt-8 bg-rg-lightLavender/50 rounded-card p-5 text-center">
            <p class="text-sm text-rg-darkText mb-3">{{ __('Aradığınız cevabı bulamadınız mı?') }}</p>
            <a href="{{ route('contact') }}"
               class="inline-flex items-center gap-2 bg-rg-purple text-white text-sm font-semibold px-5 py-2.5 rounded-btn hover:bg-rg-darkPlum transition-colors duration-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                {{ __('Bize Yazın') }}
            </a>
        </div>
    </section>
@endsection
