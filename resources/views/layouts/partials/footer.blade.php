<footer class="bg-rg-deepPurple text-white mt-16">
    {{-- Main footer --}}
    <div class="max-w-7xl mx-auto px-4 py-12 grid grid-cols-1 md:grid-cols-4 gap-8">
        {{-- Brand column --}}
        <div class="md:col-span-1">
            <img src="{{ asset('images/branding/rg-logo-dark.svg') }}"
                 alt="Rose Garden Çiçek Çikolata"
                 class="h-12 w-auto mb-3">
            <p class="text-sm text-white/75 leading-relaxed mb-4">
                {{ __('Adıyaman\'ın en özel çiçek ve çikolata mağazası. Taze çiçekler, el yapımı çikolatalar ve aynı gün teslimat.') }}
            </p>
            {{-- Social media --}}
            @php
                $igLink = $siteSettings->get('social', collect())->get('instagram', '');
                $fbLink = $siteSettings->get('social', collect())->get('facebook', '');
                $waPhone = $siteSettings->get('contact', collect())->get('whatsapp_phone', '905420000000');
            @endphp
            <div class="flex items-center gap-3">
                <a href="{{ $igLink ?: '#' }}" {{ $igLink ? 'target="_blank" rel="noopener"' : '' }} aria-label="Instagram"
                   class="w-8 h-8 rounded-full bg-white/10 hover:bg-rg-purple flex items-center justify-center transition-colors duration-200">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                    </svg>
                </a>
                <a href="{{ $fbLink ?: '#' }}" {{ $fbLink ? 'target="_blank" rel="noopener"' : '' }} aria-label="Facebook"
                   class="w-8 h-8 rounded-full bg-white/10 hover:bg-rg-purple flex items-center justify-center transition-colors duration-200">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                    </svg>
                </a>
                <a href="https://api.whatsapp.com/send?phone={{ $waPhone }}" target="_blank" rel="noopener" aria-label="WhatsApp"
                   class="w-8 h-8 rounded-full bg-white/10 hover:bg-green-600 flex items-center justify-center transition-colors duration-200">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                    </svg>
                </a>
            </div>
        </div>

        {{-- Quick links --}}
        <div>
            <h5 class="font-semibold text-rg-lavender mb-4 text-sm uppercase tracking-wider">{{ __('Hızlı Linkler') }}</h5>
            <ul class="space-y-2.5 text-sm">
                <li><a href="{{ route('products.index') }}" class="text-white/75 hover:text-white hover:translate-x-1 inline-block transition-all duration-200">{{ __('Tüm Ürünler') }}</a></li>
                <li><a href="{{ route('blog.index') }}" class="text-white/75 hover:text-white hover:translate-x-1 inline-block transition-all duration-200">{{ __('Blog') }}</a></li>
                <li><a href="{{ route('faq') }}" class="text-white/75 hover:text-white hover:translate-x-1 inline-block transition-all duration-200">{{ __('Sık Sorulan Sorular') }}</a></li>
                <li><a href="{{ route('delivery.info') }}" class="text-white/75 hover:text-white hover:translate-x-1 inline-block transition-all duration-200">{{ __('Teslimat Bilgileri') }}</a></li>
                <li><a href="{{ route('contact') }}" class="text-white/75 hover:text-white hover:translate-x-1 inline-block transition-all duration-200">{{ __('İletişim') }}</a></li>
                <li><a href="{{ route('page.show', ['slug' => 'hakkimizda']) }}" class="text-white/75 hover:text-white hover:translate-x-1 inline-block transition-all duration-200">{{ __('Hakkımızda') }}</a></li>
            </ul>
        </div>

        {{-- Legal --}}
        <div>
            <h5 class="font-semibold text-rg-lavender mb-4 text-sm uppercase tracking-wider">{{ __('Yasal') }}</h5>
            <ul class="space-y-2.5 text-sm">
                <li><a href="{{ route('page.show', ['slug' => 'gizlilik-politikasi']) }}" class="text-white/75 hover:text-white hover:translate-x-1 inline-block transition-all duration-200">{{ __('Gizlilik Politikası') }}</a></li>
                <li><a href="{{ route('page.show', ['slug' => 'iade-iptal']) }}" class="text-white/75 hover:text-white hover:translate-x-1 inline-block transition-all duration-200">{{ __('İade ve İptal Koşulları') }}</a></li>
                <li><a href="{{ route('page.show', ['slug' => 'kvkk-aydinlatma']) }}" class="text-white/75 hover:text-white hover:translate-x-1 inline-block transition-all duration-200">{{ __('KVKK Aydınlatma') }}</a></li>
                <li><a href="{{ route('page.show', ['slug' => 'mesafeli-satis-sozlesmesi']) }}" class="text-white/75 hover:text-white hover:translate-x-1 inline-block transition-all duration-200">{{ __('Mesafeli Satış Sözleşmesi') }}</a></li>
            </ul>
        </div>

        {{-- Contact + Payment --}}
        <div>
            <h5 class="font-semibold text-rg-lavender mb-4 text-sm uppercase tracking-wider">{{ __('İletişim') }}</h5>
            <ul class="space-y-3 text-sm text-white/75 mb-6">
                <li class="flex items-start gap-2">
                    <svg class="w-4 h-4 mt-0.5 flex-shrink-0 text-rg-lavender" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span>Adıyaman Merkez, Türkiye</span>
                </li>
                @php
                    $contactPhone = $siteSettings->get('contact', collect())->get('contact_phone', '+90 542 000 00 00');
                    $contactEmail = $siteSettings->get('contact', collect())->get('contact_email', 'info@rosegarden.com');
                    $phoneRaw = preg_replace('/\D/', '', $contactPhone);
                @endphp
                <li class="flex items-center gap-2">
                    <svg class="w-4 h-4 flex-shrink-0 text-rg-lavender" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                    </svg>
                    <a href="tel:+{{ $phoneRaw }}" class="hover:text-white transition-colors">{{ $contactPhone }}</a>
                </li>
                <li class="flex items-center gap-2">
                    <svg class="w-4 h-4 flex-shrink-0 text-rg-lavender" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    <a href="mailto:{{ $contactEmail }}" class="hover:text-white transition-colors">{{ $contactEmail }}</a>
                </li>
            </ul>
            {{-- Payment icons --}}
            <div>
                <p class="text-xs text-white/50 mb-2 uppercase tracking-wider">{{ __('Ödeme Yöntemleri') }}</p>
                <div class="flex items-center gap-2 flex-wrap">
                    {{-- Visa --}}
                    <div class="bg-white rounded px-2 py-1 flex items-center justify-center h-7">
                        <svg class="h-4" viewBox="0 0 50 16" fill="none">
                            <text x="0" y="13" font-family="Arial,sans-serif" font-size="14" font-weight="700" fill="#1A1F71">VISA</text>
                        </svg>
                    </div>
                    {{-- MasterCard --}}
                    <div class="bg-white rounded px-2 py-1 flex items-center justify-center h-7 gap-0.5">
                        <div class="w-4 h-4 rounded-full bg-red-500 opacity-90"></div>
                        <div class="w-4 h-4 rounded-full bg-yellow-400 -ml-2 opacity-90"></div>
                    </div>
                    {{-- Troy --}}
                    <div class="bg-white rounded px-2 py-1 flex items-center justify-center h-7">
                        <svg class="h-4" viewBox="0 0 48 16" fill="none">
                            <text x="0" y="13" font-family="Arial,sans-serif" font-size="13" font-weight="700" fill="#0066A1">troy</text>
                        </svg>
                    </div>
                    {{-- PayTR --}}
                    <div class="bg-white rounded px-2 py-1 flex items-center justify-center h-7">
                        <span class="text-xs font-bold text-gray-700">PayTR</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Bottom bar --}}
    <div class="border-t border-white/10">
        <div class="max-w-7xl mx-auto px-4 py-4 flex flex-col sm:flex-row items-center justify-between gap-2 text-xs text-white/50">
            <p>© {{ date('Y') }} Rose Garden Çiçek Çikolata. {{ __('Tüm hakları saklıdır.') }}</p>
            <p>{{ __('Adıyaman\'da tasarlandı & geliştirildi') }}</p>
        </div>
    </div>
</footer>
