<div
    x-data="{
        openSettings: false,
        consent: { required: true, analytics: false, marketing: false },
        visible: false,
        init() {
            const raw = localStorage.getItem('cookie_consent');
            if (!raw) {
                this.visible = true;
                return;
            }
            try {
                const parsed = JSON.parse(raw);
                const isExpired = !parsed.expires_at || Date.now() > parsed.expires_at;
                if (isExpired) {
                    localStorage.removeItem('cookie_consent');
                    this.visible = true;
                    return;
                }
                this.consent.analytics = !!parsed.analytics;
                this.consent.marketing = !!parsed.marketing;
                window.dispatchEvent(new CustomEvent('cookie-consent-updated', { detail: parsed }));
            } catch (_) {
                this.visible = true;
            }
        },
        persist(values) {
            const payload = {
                required: true,
                analytics: !!values.analytics,
                marketing: !!values.marketing,
                updated_at: Date.now(),
                expires_at: Date.now() + 365 * 24 * 60 * 60 * 1000,
            };
            localStorage.setItem('cookie_consent', JSON.stringify(payload));
            window.dispatchEvent(new CustomEvent('cookie-consent-updated', { detail: payload }));
            this.visible = false;
            this.openSettings = false;
        },
        acceptAll() {
            this.persist({ analytics: true, marketing: true });
        },
        rejectOptional() {
            this.persist({ analytics: false, marketing: false });
        },
        saveCustom() {
            this.persist(this.consent);
        }
    }"
    x-show="visible"
    x-transition
    class="fixed bottom-0 inset-x-0 z-50 bg-rg-deepPurple text-white p-4"
    style="display: none;"
>
    <div class="max-w-7xl mx-auto space-y-3">
        <p class="text-sm">
            {{ __('Bu site, deneyiminizi iyileştirmek için çerez kullanmaktadır.') }}
            {{ __('Çerez politikamız hakkında detaylı bilgi için') }}
            <a href="{{ route('page.show', ['slug' => 'cerez-politikasi']) }}" class="underline">{{ __('tıklayınız') }}</a>.
        </p>
        <div class="flex flex-wrap items-center gap-2">
            <button type="button" class="bg-rg-purple text-white px-3 py-2 rounded-btn text-sm" @click="acceptAll">{{ __('Kabul Et') }}</button>
            <button type="button" class="bg-white/10 text-white px-3 py-2 rounded-btn text-sm" @click="rejectOptional">{{ __('Reddet') }}</button>
            <button type="button" class="bg-white/10 text-white px-3 py-2 rounded-btn text-sm" @click="openSettings = !openSettings">{{ __('Detaylı Ayarlar') }}</button>
        </div>

        <div x-show="openSettings" class="space-y-2 border border-white/20 rounded p-3 text-sm" style="display: none;">
            <label class="flex items-center justify-between gap-3">
                <span>{{ __('Zorunlu Çerezler') }} ({{ __('Her zaman aktif') }})</span>
                <input type="checkbox" checked disabled>
            </label>
            <label class="flex items-center justify-between gap-3">
                <span>{{ __('Analitik Çerezler') }}</span>
                <input type="checkbox" x-model="consent.analytics">
            </label>
            <label class="flex items-center justify-between gap-3">
                <span>{{ __('Pazarlama Çerezleri') }}</span>
                <input type="checkbox" x-model="consent.marketing">
            </label>
            <button type="button" class="bg-rg-purple text-white px-3 py-2 rounded-btn text-sm" @click="saveCustom">{{ __('Tercihleri Kaydet') }}</button>
        </div>
    </div>
</div>
