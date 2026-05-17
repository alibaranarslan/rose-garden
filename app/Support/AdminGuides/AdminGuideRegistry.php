<?php

namespace App\Support\AdminGuides;

use App\Filament\Pages\CacheManagement;
use App\Filament\Pages\Dashboard;
use App\Filament\Pages\EmailSettings;
use App\Filament\Pages\GeneralSettings;
use App\Filament\Pages\LayoutStudio;
use App\Filament\Pages\LoyaltyManagement;
use App\Filament\Pages\MediaLibrary;
use App\Filament\Pages\PaymentSettings;
use App\Filament\Pages\ReportsAnalytics;
use App\Filament\Pages\SeoSettings;
use App\Filament\Pages\SmsSettings;
use App\Filament\Resources\AbandonedCartResource;
use App\Filament\Resources\BlogCategoryResource;
use App\Filament\Resources\BlogPostResource;
use App\Filament\Resources\CategoryResource;
use App\Filament\Resources\CouponResource;
use App\Filament\Resources\CustomerEventResource;
use App\Filament\Resources\DataRequestResource;
use App\Filament\Resources\DeliveryTimeSlotResource;
use App\Filament\Resources\DeliveryZoneResource;
use App\Filament\Resources\HeaderThemeResource;
use App\Filament\Resources\KeywordDictionaryResource;
use App\Filament\Resources\NotificationLogResource;
use App\Filament\Resources\NotificationTemplateResource;
use App\Filament\Resources\OrderResource;
use App\Filament\Resources\PageResource;
use App\Filament\Resources\PaymentResource;
use App\Filament\Resources\ProductResource;
use App\Filament\Resources\SpecialOccasionResource;
use App\Filament\Resources\UserResource;
use App\Models\User;
use App\Support\AdminPrivileges;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminGuideRegistry
{
    private const ROLE_RANKS = [
        'admin' => 1,
        'super_admin' => 2,
    ];

    public function catalogForUser(?User $user): array
    {
        return array_values(array_filter(
            array_map(fn (array $guide): array => $this->normalizeGuide($guide, $user), $this->definitions()),
            fn (array $guide): bool => $this->isVisibleToUser($guide, $user)
        ));
    }

    public function forRequest(Request $request, ?User $user): ?array
    {
        $matches = array_values(array_filter(
            $this->catalogForUser($user),
            fn (array $guide): bool => $this->matchesRequest($request, $guide)
        ));

        if ($matches === []) {
            return null;
        }

        usort($matches, fn (array $a, array $b): int => ($b['minimum_tier_rank'] ?? 0) <=> ($a['minimum_tier_rank'] ?? 0));

        return $matches[0];
    }

    public function forPage(?string $pageClass, ?User $user): ?array
    {
        if (blank($pageClass)) {
            return null;
        }

        $matches = array_values(array_filter(
            $this->catalogForUser($user),
            fn (array $guide): bool => ($guide['page_match'] ?? null) === $pageClass
        ));

        if ($matches === []) {
            return null;
        }

        usort($matches, fn (array $a, array $b): int => ($b['minimum_tier_rank'] ?? 0) <=> ($a['minimum_tier_rank'] ?? 0));

        return $matches[0];
    }

    public function findVisibleByKey(string $guideKey, ?User $user): ?array
    {
        foreach ($this->catalogForUser($user) as $guide) {
            if (($guide['guide_key'] ?? null) === $guideKey) {
                return $guide;
            }
        }

        return null;
    }

    public function currentTier(?User $user): string
    {
        if (! AdminPrivileges::canAccessAdminPanel($user)) {
            return 'guest';
        }

        if (AdminPrivileges::canPublishConfiguration($user)) {
            return 'super_admin';
        }

        return 'admin';
    }

    private function normalizeGuide(array $guide, ?User $user): array
    {
        $minimumTier = $guide['minimum_tier'] ?? 'admin';

        return $this->normalizeTurkishText([
            'guide_key' => $guide['guide_key'],
            'title' => $guide['title'],
            'summary' => $guide['summary'],
            'why_it_matters' => $guide['why_it_matters'],
            'impact' => $guide['impact'],
            'minimum_tier' => $minimumTier,
            'minimum_tier_rank' => $this->tierRank($minimumTier),
            'page_match' => $guide['page_match'] ?? null,
            'route_names' => $guide['route_names'] ?? [],
            'path_patterns' => $guide['path_patterns'] ?? [],
            'quick_actions' => array_values(array_filter(
                $guide['quick_actions'] ?? [],
                fn (array $action): bool => $this->tierRank($this->currentTier($user)) >= $this->tierRank($action['minimum_tier'] ?? 'admin')
            )),
            'steps' => $guide['steps'] ?? [],
            'coachmarks' => $guide['coachmarks'] ?? [],
            'related_guides' => $guide['related_guides'] ?? [],
        ]);
    }

    private function normalizeTurkishText(mixed $value): mixed
    {
        if (is_array($value)) {
            return array_map(fn (mixed $item): mixed => $this->normalizeTurkishText($item), $value);
        }

        if (! is_string($value)) {
            return $value;
        }

        return strtr($value, [
            'Siparis' => 'Sipariş',
            'siparis' => 'sipariş',
            'Odeme' => 'Ödeme',
            'odeme' => 'ödeme',
            'Urun' => 'Ürün',
            'urun' => 'ürün',
            'Ozel Gun' => 'Özel Gün',
            'Ozel gun' => 'Özel gün',
            'ozel gun' => 'özel gün',
            'Yonetim' => 'Yönetim',
            'yonetim' => 'yönetim',
            'Kayit' => 'Kayıt',
            'kayit' => 'kayıt',
            'Gonderim' => 'Gönderim',
            'Gonder' => 'Gönder',
            'gonder' => 'gönder',
            'Hatirlatma' => 'Hatırlatma',
            'hatirlatma' => 'hatırlatma',
            'Sablon' => 'Şablon',
            'sablon' => 'şablon',
            'Degisiklik' => 'Değişiklik',
            'degisiklik' => 'değişiklik',
            'Kutuphanesi' => 'Kütüphanesi',
            'kutuphaneyi' => 'kütüphaneyi',
            'gorsel' => 'görsel',
            'Gorsel' => 'Görsel',
            'gorunum' => 'görünüm',
            'gorunur' => 'görünür',
            'goster' => 'göster',
            'bagli' => 'bağlı',
            'baglidir' => 'bağlıdır',
            'baglam' => 'bağlam',
            'baglami' => 'bağlamı',
            'Calisma' => 'Çalışma',
            'calisma' => 'çalışma',
            'Icerik' => 'İçerik',
            'icerik' => 'içerik',
            'Iletisim' => 'İletişim',
            'iletisim' => 'iletişim',
            'Musteri' => 'Müşteri',
            'musteri' => 'müşteri',
            'Guven' => 'Güven',
            'guven' => 'güven',
            'Tum' => 'Tüm',
            'tum' => 'tüm',
            'onbellek' => 'önbellek',
            'temizligi' => 'temizliği',
            'genis' => 'geniş',
            'yanlis' => 'yanlış',
            'Yanlis' => 'Yanlış',
            'dogru' => 'doğru',
            'Dogru' => 'Doğru',
            'onemli' => 'önemli',
            'onizleme' => 'önizleme',
            'onizlemeleri' => 'önizlemeleri',
            'duzen' => 'düzen',
            'duzenleyin' => 'düzenleyin',
            'duzenler' => 'düzenler',
            'Yuklenen' => 'Yüklenen',
            'Buyuk' => 'Büyük',
            'hizli' => 'hızlı',
            'Hizli' => 'Hızlı',
            'icin' => 'için',
            'acik' => 'açık',
            'acin' => 'açın',
            'alanini' => 'alanını',
            'akisi' => 'akışı',
            'akisina' => 'akışına',
            'akisinda' => 'akışında',
            'akisini' => 'akışını',
            'amacli' => 'amaçlı',
            'arsiv' => 'arşiv',
            'ayiklama' => 'ayıklama',
            'ayiklayin' => 'ayıklayın',
            'ayni' => 'aynı',
            'basarili' => 'başarılı',
            'baslangic' => 'başlangıç',
            'bakimi' => 'bakımı',
            'bilinclı' => 'bilinçli',
            'bilinclidir' => 'bilinçlidir',
            'bolge' => 'bölge',
            'Bolgeleri' => 'Bölgeleri',
            'cozum' => 'çözüm',
            'dagilimi' => 'dağılımı',
            'dagilimini' => 'dağılımını',
            'degisimini' => 'değişimini',
            'degerlendirir' => 'değerlendirir',
            'donusum' => 'dönüşüm',
            'dusunun' => 'düşünün',
            'dusulen' => 'düşülen',
            'etkiledigini' => 'etkilediğini',
            'farkli' => 'farklı',
            'gore' => 'göre',
            'gorevi' => 'görevi',
            'gozle' => 'gözle',
            'hatirlatir' => 'hatırlatır',
            'hiyerarsisi' => 'hiyerarşisi',
            'islemler' => 'işlemler',
            'islemi' => 'işlemi',
            'islem' => 'işlem',
            'isler' => 'işler',
            'Kisisel' => 'Kişisel',
            'kullanici' => 'kullanıcı',
            'kullanicilari' => 'kullanıcıları',
            'kullanin' => 'kullanın',
            'kullanim' => 'kullanım',
            'kullanilan' => 'kullanılan',
            'müdahale' => 'müdahale',
            'netlestirin' => 'netleştirin',
            'olculur' => 'ölçülür',
            'ozetler' => 'özetler',
            'sozluk' => 'sözlük',
            'sozlerini' => 'sözlerini',
            'tarayicisi' => 'tarayıcısı',
            'temizleyin' => 'temizleyin',
            'uygunlugu' => 'uygunluğu',
            'varliklari' => 'varlıkları',
            'yayin' => 'yayın',
            'yayinlama' => 'yayınlama',
            'yardimci' => 'yardımcı',
            'yalniz' => 'yalnız',
            'zayiflatir' => 'zayıflatır',
        ]);
    }

    private function matchesRequest(Request $request, array $guide): bool
    {
        $routeName = $request->route()?->getName();

        foreach ($guide['route_names'] ?? [] as $routePattern) {
            if ($routeName !== null && Str::is($routePattern, $routeName)) {
                return true;
            }
        }

        foreach ($guide['path_patterns'] ?? [] as $pathPattern) {
            if ($request->is($pathPattern)) {
                return true;
            }
        }

        return false;
    }

    private function isVisibleToUser(array $guide, ?User $user): bool
    {
        return $this->tierRank($this->currentTier($user)) >= ($guide['minimum_tier_rank'] ?? 0);
    }

    private function tierRank(string $tier): int
    {
        return self::ROLE_RANKS[$tier] ?? 0;
    }

    private function pathPatternsFromUrl(string $url): array
    {
        $path = trim((string) parse_url($url, PHP_URL_PATH), '/');

        if ($path === '') {
            return [''];
        }

        if ($path === 'admin') {
            return ['admin'];
        }

        return [$path, $path.'*'];
    }

    private function guide(
        string $guideKey,
        string $title,
        string $summary,
        string $whyItMatters,
        string $impact,
        string $minimumTier,
        string $url,
        ?string $pageMatch,
        array $steps,
        array $coachmarks,
        array $relatedGuides = [],
        array $quickActions = [],
        array $routeNames = []
    ): array {
        return [
            'guide_key' => $guideKey,
            'title' => $title,
            'summary' => $summary,
            'why_it_matters' => $whyItMatters,
            'impact' => $impact,
            'minimum_tier' => $minimumTier,
            'page_match' => $pageMatch,
            'route_names' => $routeNames,
            'path_patterns' => $this->pathPatternsFromUrl($url),
            'quick_actions' => $quickActions,
            'steps' => $steps,
            'coachmarks' => $coachmarks,
            'related_guides' => $relatedGuides,
        ];
    }

    private function resourceGuide(
        string $guideKey,
        string $title,
        string $summary,
        string $whyItMatters,
        string $impact,
        string $minimumTier,
        string $url,
        string $routePattern,
        array $relatedGuides = []
    ): array {
        return $this->guide(
            $guideKey,
            $title,
            $summary,
            $whyItMatters,
            $impact,
            $minimumTier,
            $url,
            null,
            [
                ['title' => 'Amaci netlestirin', 'description' => 'Bu ekranin public vitrin mi, siparis akisi mi yoksa operasyonel takip mi etkiledigini once net okuyun.'],
                ['title' => 'Filtre ve tabloyu kullanin', 'description' => 'Liste ekranlari hizli ayiklama ve toplu operasyon icin ana calisma alanidir.'],
                ['title' => 'Formu public etkisiyle dusunun', 'description' => 'Kaydettiginiz alanlar vitrine, siparise ya da iletisime yansiyorsa son kez gozle kontrol edin.'],
            ],
            [
                ['selector' => '.fi-header', 'title' => 'Sayfa baglami', 'body' => 'Bu ust alan listedeki veya formdaki mevcut gorevi hatirlatir.'],
                ['selector' => '.fi-ta', 'title' => 'Liste tablosu', 'body' => 'Filtre, arama ve toplu aksiyonlar burada toplanir.'],
                ['selector' => '.fi-fo', 'title' => 'Form alani', 'body' => 'Create ve edit ekranlarinda asil veri girisi burada yapilir.'],
                ['selector' => '.fi-page-actions', 'title' => 'Sayfa aksiyonlari', 'body' => 'Kaydetme, export ya da yardimci islemler bu alanda yer alir.'],
            ],
            $relatedGuides,
            [
                [
                    'label' => $title,
                    'description' => 'Bu yonetim alanini acin.',
                    'url' => $url,
                ],
            ],
            [$routePattern]
        );
    }

    private function definitions(): array
    {
        return [
            $this->guide(
                'dashboard-overview',
                'Operasyon Masası',
                'Sipariş, ödeme, vitrin ve geri kazanım sinyallerini tek ekranda toplar.',
                'Günlük operasyonun en hızlı başlangıç noktası budur; ekip burada neye müdahale edeceğini hızla görür.',
                'Bu ekran yalnız operasyonel yönlendirme içindir.',
                'admin',
                Dashboard::getUrl(panel: 'admin'),
                Dashboard::class,
                [
                    ['title' => 'Önce acil siparişlere bakın', 'description' => 'Bekleyen ödeme, bugün teslim edilecekler ve başarısız bildirimler burada önde durur.'],
                    ['title' => 'Sonra fulfillment ve vitrin dengesini okuyun', 'description' => 'Stok, kampanya ve anasayfa durumu aynı akış içinde görünür.'],
                    ['title' => 'Geri kazanimi ve ticari ozeti en sonda yorumlayin', 'description' => 'Terk edilen sepet, event reminder ve gunluk ciro bu bloklarla tamamlanir.'],
                ],
                [
                    ['anchor' => 'dashboard.attention', 'title' => 'Bugün müdahale gerekenler', 'body' => 'Sipariş, ödeme ve bildirim istisnaları burada öncelik sırası ile görünür.'],
                    ['anchor' => 'dashboard.guide-entry', 'title' => 'Öğretici girişi', 'body' => 'Panel akışını hızlıca öğrenmek için bu tetikleyiciyi kullanın.'],
                    ['anchor' => 'dashboard.queue', 'title' => 'Acil sipariş tablosu', 'body' => 'Operasyonun ilk aksiyon alanı bu tablo üzerinden ilerler.'],
                    ['anchor' => 'dashboard.fulfillment', 'title' => 'Teslimat ve fulfillment riski', 'body' => 'Stok ve bugün teslim edilecek siparişleri birlikte okursunuz.'],
                    ['anchor' => 'dashboard.storefront', 'title' => 'Vitrin durumu', 'body' => 'Taslak farkı, kampanya yoğunluğu ve aktif kuponlar burada görünür.'],
                ],
                ['orders', 'products', 'layout-studio', 'reports-analytics'],
                [
                    ['label' => 'Siparisler', 'description' => 'Bekleyen siparisleri acin.', 'url' => OrderResource::getUrl(panel: 'admin')],
                    ['label' => 'Yerlesim Studyosu', 'description' => 'Storefront bloklarini duzenleyin.', 'url' => LayoutStudio::getUrl(panel: 'admin')],
                    ['label' => 'Raporlar', 'description' => 'Ciro ve donusum sinyallerini izleyin.', 'url' => ReportsAnalytics::getUrl(panel: 'admin')],
                ]
            ),
            $this->resourceGuide('orders', 'Siparis Yonetimi', 'Siparisleri, kart mesajlarini, adresleri ve operasyonel durum guncellemelerini yonetir.', 'Hazirlama, teslimat ve musteri bilgilendirmesi bu ekranla hizalanir.', 'Bu degisiklik siparis akisina etki eder.', 'admin', OrderResource::getUrl(panel: 'admin'), 'filament.admin.resources.orders.*', ['payments', 'notification-logs']),
            $this->resourceGuide('payments', 'Odeme Kayitlari', 'Kartli ve havale odemeleri ile callback sonucunu ayni kayit tabaninda toplar.', 'Odeme akisinin sagligi ve siparis teyidi bu ekranda okunur.', 'Bu ekran yalniz operasyonel izleme icindir.', 'admin', PaymentResource::getUrl(panel: 'admin'), 'filament.admin.resources.payments.*', ['orders', 'payment-settings']),
            $this->resourceGuide('products', 'Urun ve Varyantlar', 'Urun detaylari, varyantlar, fiyat ve gorsel yonetimini birlikte toplar.', 'Storefront vitrin, sepet ve checkout deneyimi bu verilerle beslenir.', 'Bu degisiklik vitrinde gorunur.', 'admin', ProductResource::getUrl(panel: 'admin'), 'filament.admin.resources.products.*', ['categories', 'layout-studio', 'special-occasions']),
            $this->resourceGuide('categories', 'Kategori Yonetimi', 'Kategori yapisi vitrin dagilimini ve katalog akisini belirler.', 'Yanlis kategori hiyerarsisi urun bulunabilirligini zayiflatir.', 'Bu degisiklik vitrinde gorunur.', 'admin', CategoryResource::getUrl(panel: 'admin'), 'filament.admin.resources.categories.*', ['products', 'layout-studio']),
            $this->resourceGuide('special-occasions', 'Ozel Gunler', 'Merchandising ve kampanya amacli ozel gun kurgularini yonetir.', 'Koleksiyon ve sezon odakli vitrinleri destekleyen ticari bir katmandir.', 'Bu degisiklik vitrinde gorunur.', 'admin', SpecialOccasionResource::getUrl(panel: 'admin'), 'filament.admin.resources.special-occasions.*', ['products', 'header-themes']),
            $this->resourceGuide('blog-posts', 'Blog Yazilari', 'Blog iceriklerini listeleme, yazma ve yayinlama akisinda yonetir.', 'SEO ve yumusak donusum alani olarak blog katmanini canli tutar.', 'Bu degisiklik vitrinde gorunur.', 'admin', BlogPostResource::getUrl(panel: 'admin'), 'filament.admin.resources.blog-posts.*', ['blog-categories', 'seo-settings']),
            $this->resourceGuide('blog-categories', 'Blog Kategorileri', 'Blog iceriginin kesif ve arsiv duzenini belirler.', 'Tutarsiz kategori yapisi blogun gezinilebilirligini zayiflatir.', 'Bu degisiklik vitrinde gorunur.', 'admin', BlogCategoryResource::getUrl(panel: 'admin'), 'filament.admin.resources.blog-categories.*', ['blog-posts']),
            $this->resourceGuide('static-pages', 'Statik Sayfalar', 'Kurumsal, teslimat ve yasal icerikleri yonetir.', 'Guven, policy ve bilgilendirme alanlari bu ekrandan beslenir.', 'Bu degisiklik vitrinde gorunur.', 'admin', PageResource::getUrl(panel: 'admin'), 'filament.admin.resources.pages.*', ['general-settings', 'seo-settings']),
            $this->guide(
                'media-library',
                'Medya Kutuphanesi',
                'Yuklenen gorsel ve dosyalari arama, filtreleme ve temizlik akisinda yonetir.',
                'Katalog, blog ve kampanya yuzeylerinde kullanilan medya kalitesi bu ekranin saglikli kullanilmasina baglidir.',
                'Bu degisiklik vitrinde gorunur olabilir.',
                'admin',
                MediaLibrary::getUrl(panel: 'admin'),
                MediaLibrary::class,
                [
                    ['title' => 'Arama ile ayiklayin', 'description' => 'Buyuk medya havuzunda dosya adina gore hizli filtreleme yapin.'],
                    ['title' => 'Yetim dosyalari dikkatle temizleyin', 'description' => 'Kullanilmayan dosya filtresi yardimci olur; ama publicte bagli medya kalmamasina dikkat edin.'],
                    ['title' => 'Grid ve listeyi ihtiyaca gore degistirin', 'description' => 'Gorsel onizleme icin grid, denetim icin liste gorunumu daha uygundur.'],
                ],
                [
                    ['anchor' => 'media.hero', 'title' => 'Kutuphaneyi yonetin', 'body' => 'Arama, gorunum modu ve orphaned filtresi burada bulunur.'],
                    ['anchor' => 'media.browser', 'title' => 'Dosya tarayicisi', 'body' => 'Liste veya grid halinde gercek medya varliklari bu alanda gorulur.'],
                ],
                ['products', 'blog-posts']
            ),
            $this->resourceGuide('delivery-zones', 'Teslimat Bolgeleri', 'Teslimat uygunlugu ve alan bazli servis mantigini yonetir.', 'Yanlis bolge kurgusu checkout ve teslimat sozlerini bozar.', 'Bu degisiklik siparis akisina etki eder.', 'admin', DeliveryZoneResource::getUrl(panel: 'admin'), 'filament.admin.resources.delivery-zones.*', ['delivery-time-slots', 'orders']),
            $this->resourceGuide('delivery-time-slots', 'Teslimat Saatleri', 'Teslimat zaman araliklarini operasyonel takvime gore duzenler.', 'Slot mantigi checkoutta kullaniciya verilen teslimat vaadini belirler.', 'Bu degisiklik siparis akisina etki eder.', 'admin', DeliveryTimeSlotResource::getUrl(panel: 'admin'), 'filament.admin.resources.delivery-time-slots.*', ['delivery-zones', 'orders']),
            $this->resourceGuide('coupons', 'Kuponlar', 'Yuzde, sabit tutar veya teslimat indirimi kampanyalarini yonetir.', 'Ticari kampanyalarin siparis toplaminda dogru davranmasi bu ekrana baglidir.', 'Bu degisiklik siparis akisina etki eder.', 'admin', CouponResource::getUrl(panel: 'admin'), 'filament.admin.resources.coupons.*', ['loyalty-management', 'reports-analytics']),
            $this->guide(
                'loyalty-management',
                'Sadakat ve Puanlar',
                'Puan kurallari, manuel puan islemleri ve bakiye gorunumunu yonetir.',
                'Paracicek puanlari tekrar siparis ve sepet davranisini dogrudan etkiler.',
                'Bu degisiklik siparis akisina etki eder.',
                'admin',
                LoyaltyManagement::getUrl(panel: 'admin'),
                LoyaltyManagement::class,
                [
                    ['title' => 'Kurallari once netlestirin', 'description' => 'Kazanma orani, minimum kullanim ve son kullanma mantigi once bu ekranda belirlenir.'],
                    ['title' => 'Manuel puan islemini dikkatle kullanin', 'description' => 'Elle eklenen veya dusulen puanlar musteri deneyimini ve raporu etkiler.'],
                    ['title' => 'Rapor sekmesini ihmal etmeyin', 'description' => 'Toplam dagitim ve kullanim orani kampanya verimini okumak icin onemlidir.'],
                ],
                [
                    ['anchor' => 'loyalty.tabs', 'title' => 'Calisma sekmeleri', 'body' => 'Kural, musteri bakiyesi ve rapor alanlari bu sekmelerden ayrilir.'],
                    ['anchor' => 'loyalty.content', 'title' => 'Aktif icerik alani', 'body' => 'Secilen sekmeye ait form ya da rapor bloklari burada render edilir.'],
                ],
                ['coupons', 'users']
            ),
            $this->resourceGuide('users', 'Musteriler ve Kullanicilar', 'Musteri kayitlarini, favorileri, siparisleri ve yetkili kullanicilari listeler.', 'Hassas kullanici verisi ve rol davranislari burada gorundugu icin guvenlik kritik bir ekrandir.', 'Bu ekran islem akisina etki eder.', 'super_admin', UserResource::getUrl(panel: 'admin'), 'filament.admin.resources.users.*', ['customer-events', 'data-requests']),
            $this->resourceGuide('customer-events', 'Musteri Davranis Kayitlari', 'Musteri hareketlerini ve ilgi sinyallerini operasyonel kayit olarak toplar.', 'Tekrarlayan davranislari ve pazarlama sinyallerini okumaya yardim eder.', 'Bu ekran yalniz operasyonel izleme icindir.', 'admin', CustomerEventResource::getUrl(panel: 'admin'), 'filament.admin.resources.customer-events.*', ['users', 'abandoned-carts']),
            $this->resourceGuide('keyword-dictionary', 'Kart Mesaji Analizi', 'Kart mesajlari ve duygu kaliplari icin sozluk bakimini yonetir.', 'Analitik ve icerik kalitesi tarafinda tekrar eden kaliplari okumaya yardim eder.', 'Bu ekran yalniz operasyonel izleme icindir.', 'admin', KeywordDictionaryResource::getUrl(panel: 'admin'), 'filament.admin.resources.keyword-dictionaries.*', ['customer-events']),
            $this->resourceGuide('data-requests', 'KVKK Veri Talepleri', 'Kisisel veri taleplerini listeleme, isleme ve kapatma akisinda yonetir.', 'Uyumluluk ve operasyonel takip acisindan atlanmamasi gereken bir ekrandir.', 'Bu ekran islem akisina etki eder.', 'admin', DataRequestResource::getUrl(panel: 'admin'), 'filament.admin.resources.data-requests.*', ['users', 'static-pages']),
            $this->resourceGuide('abandoned-carts', 'Terk Edilen Sepetler', 'Hatirlatma uygunlugu ve yeniden kazanma aksiyonlarini takip eder.', 'Reminder kurali ve duplicate riski bu ekrandan okunur.', 'Bu ekran islem akisina etki eder.', 'admin', AbandonedCartResource::getUrl(panel: 'admin'), 'filament.admin.resources.abandoned-carts.*', ['notification-logs', 'reports-analytics']),
            $this->resourceGuide('notification-templates', 'Bildirim Sablonlari', 'Email ve SMS icerik sablonlarini kanal bazinda duzenler.', 'Siparis, hatirlatma ve banka transfer bildirimleri bu katmandan beslenir.', 'Bu degisiklik siparis akisina etki eder.', 'admin', NotificationTemplateResource::getUrl(panel: 'admin'), 'filament.admin.resources.notification-templates.*', ['notification-logs', 'email-settings', 'sms-settings']),
            $this->resourceGuide('notification-logs', 'Bildirim Gecmisi', 'Gonderim loglarini, hata mesajlarini ve kanal bazli durumlari listeler.', 'Failed, queued ve basarili akis operasyonun en kritik gozlem katmanlarindandir.', 'Bu ekran yalniz operasyonel izleme icindir.', 'admin', NotificationLogResource::getUrl(panel: 'admin'), 'filament.admin.resources.notification-logs.*', ['notification-templates', 'abandoned-carts']),
            $this->guide(
                'layout-studio',
                'Yerlesim Studyosu',
                'Storefront blok sirasini, modullerin yogunlugunu ve genel vitrin kararlarini taslakta yonetir.',
                'Anasayfanin duygusu, CTA dengesi ve merchandising agirligi burada kurulur.',
                'Bu degisiklik vitrinde gorunur.',
                'admin',
                LayoutStudio::getUrl(panel: 'admin'),
                LayoutStudio::class,
                [
                    ['title' => 'Modul sirasini taslakta kurun', 'description' => 'Hero, vitrin ve guven bloklarini sezgisel bir akisla yerlestirin.'],
                    ['title' => 'Secili modul ayarlarini baglamla okuyun', 'description' => 'CTA, limit ve yogunluk ayarlari tek blokta degil tum vitrinde dengeli olmalidir.'],
                    ['title' => 'Kaydedip onizleyin', 'description' => 'Canliya almadan once TR, EN ve KU onizlemeleri mutlaka kontrol edilmelidir.'],
                ],
                [
                    ['anchor' => 'layout.hero', 'title' => 'Studyo ozet alani', 'body' => 'Taslak, canli surum ve onizleme sinyalleri burada toplanir.'],
                    ['anchor' => 'layout.modules', 'title' => 'Modul listesi', 'body' => 'Anasayfa bloklarini secme, acma-kapatma ve siralama bu alanda yapilir.'],
                    ['anchor' => 'layout.settings', 'title' => 'Secili modul ayarlari', 'body' => 'Baslik, CTA, yogunluk ve cihaz davranislari burada duzenlenir.'],
                    ['anchor' => 'layout.appearance', 'title' => 'Genel gorunum', 'body' => 'Renk, tipografi ve kart hissi bu bolumden sekillenir.'],
                ],
                ['layout-publishing', 'header-themes', 'products']
            ),
            $this->guide(
                'layout-publishing',
                'Vitrin Yayinlama',
                'Taslak, preview, publish ve rollback akisinin super admin tarafini anlatir.',
                'Canli vitrini etkileyen en kritik admin akislarindan biridir.',
                'Bu degisiklik vitrinde gorunur.',
                'super_admin',
                LayoutStudio::getUrl(panel: 'admin'),
                LayoutStudio::class,
                [
                    ['title' => 'Taslagi kaydetmeden yayinlamayin', 'description' => 'Preview baglantilari son kayitli taslagi gosterir; once taslagi sabitleyin.'],
                    ['title' => 'Yayin ve geri alma zincirini bilin', 'description' => 'Rollback karari da publish kadar guclu bir operasyon aksiyonudur.'],
                    ['title' => 'Publish sonrasi storefrontu kontrol edin', 'description' => 'Anasayfa bloklari ve CTA copyleri canli vitrinde tekrar gozden gecirilmelidir.'],
                ],
                [
                    ['anchor' => 'layout.publish', 'title' => 'Yayin kontrolu', 'body' => 'Taslagi kaydetme, preview ve canliya alma aksiyonlari burada bulunur.'],
                    ['anchor' => 'layout.rollback', 'title' => 'Rollback alani', 'body' => 'Onceki revizyonu tekrar taslaga yukleyip kontrollu geri alma yapabilirsiniz.'],
                ],
                ['layout-studio', 'header-themes']
            ),
            $this->resourceGuide('header-themes', 'Header Temalari', 'Takvim bazli veya manuel acilan header temasini yonetir.', 'Donemsel kampanya ve ozel gun hissi storefront ust alaninda dogrudan gorunur.', 'Bu degisiklik vitrinde gorunur.', 'admin', HeaderThemeResource::getUrl(panel: 'admin'), 'filament.admin.resources.header-themes.*', ['layout-studio', 'special-occasions']),
            $this->guide(
                'general-settings',
                'Genel Ayarlar',
                'Marka, iletisim ve genel storefront kimligini yonetir.',
                'Header, footer ve guven alanlarinin ana kaynagi bu ekrandir.',
                'Bu degisiklik vitrinde gorunur.',
                'super_admin',
                GeneralSettings::getUrl(panel: 'admin'),
                GeneralSettings::class,
                [
                    ['title' => 'Marka alanlarini bir arada dusunun', 'description' => 'Logo, slogan ve iletisim metni birlikte storefront algisini kurar.'],
                    ['title' => 'Public etkili helper notlarini okuyun', 'description' => 'Bu ekran dogrudan storefront shellini besler.'],
                ],
                [
                    ['anchor' => 'settings.form', 'title' => 'Ayar formu', 'body' => 'Publice yansiyan marka ve iletisim alanlari bu formda toplanir.'],
                    ['anchor' => 'settings.save', 'title' => 'Kaydet aksiyonu', 'body' => 'Degisiklikleri kaydettikten sonra header ve footeru tekrar kontrol edin.'],
                ],
                ['seo-settings', 'header-themes']
            ),
            $this->guide(
                'payment-settings',
                'Odeme Ayarlari',
                'Kartli odeme, havale ve ticari odeme metinlerinin ana konfigurasyonunu yonetir.',
                'Checkout ve odeme teyidi akisi bu ekrandaki kararlarla calisir.',
                'Bu degisiklik siparis akisina etki eder.',
                'super_admin',
                PaymentSettings::getUrl(panel: 'admin'),
                PaymentSettings::class,
                [
                    ['title' => 'Canli odeme kararlarini dikkatle girin', 'description' => 'Havale, banka bilgisi ve PayTR davranisi checkout deneyimini dogrudan etkiler.'],
                    ['title' => 'Bildirime yansiyan alanlari unutmayin', 'description' => 'Banka transfer icerikleri ve hatirlatma akislari bu ayari baz alir.'],
                ],
                [
                    ['anchor' => 'settings.form', 'title' => 'Odeme formu', 'body' => 'Odeme saglayicisi, havale bilgisi ve helper notlari burada bulunur.'],
                    ['anchor' => 'settings.save', 'title' => 'Kaydet ve dogrula', 'body' => 'Kayittan sonra checkout ve banka transfer mesajlarini test edin.'],
                ],
                ['orders', 'payments', 'notification-templates']
            ),
            $this->guide(
                'sms-settings',
                'SMS Ayarlari',
                'SMS gonderim konfigurasyonunu ve kanal davranisini yonetir.',
                'Siparis ve hatirlatma iletisimi icin kritik bir operasyon ekranidir.',
                'Bu degisiklik islem akisina etki eder.',
                'super_admin',
                SmsSettings::getUrl(panel: 'admin'),
                SmsSettings::class,
                [
                    ['title' => 'Saglayici ve gonderici alanlarini birlikte dusunun', 'description' => 'Eksik veya yanlis bilgi log ekraninda failed kayitlara yol acar.'],
                    ['title' => 'Bildirim logu ile capraz kontrol yapin', 'description' => 'Kayit sonrasi Notification Log ekraninda kanal davranisini izleyin.'],
                ],
                [
                    ['anchor' => 'settings.form', 'title' => 'SMS formu', 'body' => 'Kanalin calisma zamani ayarlari burada yer alir.'],
                    ['anchor' => 'settings.save', 'title' => 'Kaydet', 'body' => 'Degisiklikten sonra ornek bir test akisi ile loglari kontrol edin.'],
                ],
                ['notification-templates', 'notification-logs']
            ),
            $this->guide(
                'email-settings',
                'E-posta Ayarlari',
                'SMTP ve gonderici alanlarini panelden yonetir.',
                'Siparis, reminder ve test e-postalari bu runtime konfigi kullanir.',
                'Bu degisiklik islem akisina etki eder.',
                'super_admin',
                EmailSettings::getUrl(panel: 'admin'),
                EmailSettings::class,
                [
                    ['title' => 'SMTP ve gondericiyi birlikte dogrulayin', 'description' => 'Host ve kimlik bilgisi kadar from adi ve from adresi de kritiktir.'],
                    ['title' => 'Kaydettikten sonra test gonderin', 'description' => 'Panel testleri gercek runtime ayarini kullanir; success logu ile dogrulayin.'],
                ],
                [
                    ['anchor' => 'settings.form', 'title' => 'SMTP ayarlari', 'body' => 'Host, port, guvenlik ve gonderici bilgileri bu formda toplanir.'],
                    ['anchor' => 'settings.save', 'title' => 'Kaydet ve test et', 'body' => 'Ayarlari kaydettikten sonra test gonderimi ile gercekten calistigini teyit edin.'],
                ],
                ['notification-templates', 'notification-logs']
            ),
            $this->guide(
                'seo-settings',
                'SEO Ayarlari',
                'Global storefront title, description ve indeksleme tercihlerini yonetir.',
                'Search ve paylasim yuzeylerindeki site seviyesi sinyaller bu ekrandan beslenir.',
                'Bu degisiklik vitrinde gorunur.',
                'super_admin',
                SeoSettings::getUrl(panel: 'admin'),
                SeoSettings::class,
                [
                    ['title' => 'Global ve sayfa bazli SEOyu ayirin', 'description' => 'Bu ekran site seviyesi kararlar icindir; urun ve blog bazli alanlarla karistirmayin.'],
                    ['title' => 'Robots kararlarini kontrollu verin', 'description' => 'Yanlis global karar gereksiz noindex ya da fazla indeksleme sorununa yol acar.'],
                ],
                [
                    ['anchor' => 'settings.form', 'title' => 'SEO formu', 'body' => 'Global title, description ve indeksleme tercihleri burada bulunur.'],
                    ['anchor' => 'settings.save', 'title' => 'Kaydet ve representative kontrol yapin', 'body' => 'Representative product ve blog sayfalarinda ciktiyi yeniden kontrol edin.'],
                ],
                ['static-pages', 'blog-posts']
            ),
            $this->guide(
                'reports-analytics',
                'Raporlar ve Analitik',
                'Ciro, siparis, tekrar musteri ve trafik dagilimini birlikte gosterir.',
                'Ticari kararlarin sezgiyle degil veriyle alinmasi icin bu ekran temel referanstir.',
                'Bu ekran yalniz operasyonel izleme icindir.',
                'admin',
                ReportsAnalytics::getUrl(panel: 'admin'),
                ReportsAnalytics::class,
                [
                    ['title' => 'Donem secimini net yapin', 'description' => 'Bugun, son 7 gun ve son 30 gun karsilastirmalari farkli kararlar uretir.'],
                    ['title' => 'Delta kartlarini tek basina yorumlamayin', 'description' => 'Siparis durumu, cihaz ve referer dagilimi ile birlikte okuyun.'],
                    ['title' => 'Exportu karar notlari icin kullanin', 'description' => 'CSV disa aktarim ekip ici degerlendirme ve teslim notlari icin uygundur.'],
                ],
                [
                    ['anchor' => 'reports.hero', 'title' => 'Rapor baglami', 'body' => 'Donem secimi, aciklama ve export aksiyonu bu ust alanda toplanir.'],
                    ['anchor' => 'reports.comparison', 'title' => 'Delta kartlari', 'body' => 'Ciro, siparis ve ortalama sepet degisimini burada okursunuz.'],
                    ['anchor' => 'reports.status', 'title' => 'Siparis durumu', 'body' => 'Operasyonun hangi durumlarda yogunlastigini bu blok ozetler.'],
                    ['anchor' => 'reports.devices', 'title' => 'Cihaz dagilimi', 'body' => 'Mobil ve desktop agirligini kampanya ve vitrin kararlarinda kullanin.'],
                    ['anchor' => 'reports.export', 'title' => 'CSV disa aktarim', 'body' => 'Bu aksiyon o anki rapor kesitini dosya olarak alir.'],
                ],
                ['orders', 'coupons', 'layout-studio']
            ),
            $this->guide(
                'cache-management',
                'Cache ve Sistem Bakimi',
                'Config, route, view ve tam cache temizleme operasyonunu yonetir.',
                'Yanlis zamanda yapilan temizlik performans ve yayin akisina etki edebilir; bilincli kullanilmalidir.',
                'Bu ekran yalniz operasyonel izleme icindir.',
                'super_admin',
                CacheManagement::getUrl(panel: 'admin'),
                CacheManagement::class,
                [
                    ['title' => 'Neyi temizlediginizi bilin', 'description' => 'Config, view ve route temizligi farkli sorunlara cozum olur; refleks olarak full reset yapmayin.'],
                    ['title' => 'Tam temizligi istisna olarak kullanin', 'description' => 'Tum cache temizligi daha genis etkilidir; ancak gerekli oldugunda kullanilmalidir.'],
                ],
                [
                    ['anchor' => 'cache.actions', 'title' => 'Hedefli temizlik', 'body' => 'Config, view, route ve optimize aksiyonlari burada toplanir.'],
                    ['anchor' => 'cache.full-reset', 'title' => 'Tam cache temizligi', 'body' => 'En genis etkiye sahip operasyon aksiyonu bu bloktadir.'],
                ],
                ['reports-analytics', 'layout-publishing']
            ),
        ];
    }
}

