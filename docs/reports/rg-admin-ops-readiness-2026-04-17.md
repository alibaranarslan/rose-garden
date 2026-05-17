# RG Admin / Ops Readiness

**Tarih:** 2026-04-17  
**Kapsam:** storefront’u besleyen admin panel, ayarlar, operasyon akışları ve canlıya alım hazırlığı  
**Not:** Bu turda storefront redesign yapılmadı; yalnızca admin/ops ve live-readiness yüzeyleri sıkılaştırıldı.

## Amaç

Storefront’u etkileyen admin yüzeylerini doğrulamak, yanlış yapılandırma riskini azaltmak, içerik operasyonlarını görünür hale getirmek ve canlıya çıkış öncesi kritik blocker’ları netleştirmek.

## Storefront’u Etkileyen Admin Touchpoint’ler

- `App\Filament\Pages\LayoutStudio`
- `App\Filament\Pages\GeneralSettings`
- `App\Filament\Pages\PaymentSettings`
- `App\Filament\Pages\EmailSettings`
- `App\Filament\Pages\SmsSettings`
- `App\Filament\Pages\SeoSettings`
- `App\Filament\Pages\CacheManagement`
- `App\Filament\Pages\ReportsAnalytics`
- `App\Filament\Resources\ProductResource`
- `App\Filament\Resources\CategoryResource`
- `App\Filament\Resources\SpecialOccasionResource`
- `App\Filament\Resources\PageResource`
- `App\Filament\Resources\BlogPostResource`
- `App\Filament\Resources\HeaderThemeResource`
- `App\Filament\Resources\DeliveryZoneResource`
- `App\Filament\Resources\DeliveryTimeSlotResource`
- `App\Filament\Resources\CouponResource`
- `App\Filament\Resources\OrderResource`
- `App\Filament\Resources\PaymentResource`
- `App\Filament\Resources\NotificationTemplateResource`
- `App\Filament\Resources\NotificationLogResource`
- `App\Filament\Resources\AbandonedCartResource`
- `App\Filament\Resources\UserResource`
- `App\Filament\Pages\LoyaltyManagement`

## Kritik Settings Ownership

- Branding ve genel vitrin metinleri `GeneralSettings` üzerinden yönetiliyor.
- Vitrin publish akışı `LayoutStudio` + `LayoutConfigService` ile ilerliyor.
- Ödeme bilgileri `PaymentSettings` üzerinden tutuluyor.
- SMTP ayarları `EmailSettings` ile, SMS ayarları `SmsSettings` ile yönetiliyor.
- SEO canonical / meta / robots yönetimi `SeoSettings` ile yönetiliyor.
- Canonical URL ve robots çıktısı artık aynı origin mantığına bağlı.
- `robots.txt` artık `seo.robots_txt_extra` alanını da public çıktıya katıyor.

## Content Ops Readiness

- Homepage içerik akışı admin’den yönetilebilir durumda.
- `GeneralSettings` içindeki hero spotlight seçimi artık yalnızca storefront-ready ürünlerle sınırlandı.
- Geçersiz manuel hero ürünü seçilirse güvenli otomatik vitrine düşüyor.
- `ProductResource` ve `CategoryResource` görsel yükleri yalnızca gerçek image tiplerini kabul ediyor.
- `BlogPostResource` öne çıkan görsel alanı da image-only hale getirildi.
- `/sayfa/{slug}` CMS tarafından yönetiliyor; `/iletisim` settings-fed utility page olarak kalıyor.
- `/sss` ve `/teslimat-bilgileri` hâlâ Blade içinde hardcoded operasyon sayfaları; bu iki yüzey admin’den tam yönetilebilir değil.

## Live-Readiness Blocker Listesi

### Kapandı

- Manuel hero ürününün storefront-ready olmayan kayda işaret etmesi
- Canonical domain’in path ile birlikte saklanması
- `robots.txt` ile sitemap URL’sinin farklı root kullanması
- Admin image alanlarında PDF kabulü
- PayTR / e-posta / SMS / SEO ayarlarının yanlış yapılandırılmasına açık alanlar

### Operasyonel veri bekliyor

- `PaymentSettings` için gerçek PayTR / banka bilgileri
- `EmailSettings` için SMTP host / kullanıcı / şifre / gönderici bilgileri
- `SmsSettings` için sağlayıcı bilgileri ve gönderim durumu
- `DeliveryZoneResource` için aktif bölge ve ücret verileri
- `DeliveryTimeSlotResource` için aktif slot verileri
- `HeaderThemeResource` için takvim bazlı tema verileri
- `PageResource`, `BlogPostResource`, `SpecialOccasionResource` için canlı içerik doluluğu

### Sonraya kalan

- `/sss` ve `/teslimat-bilgileri` sayfalarının admin-fed kaynağa taşınması
- Operasyonel README / runbook’ların tek bir canonical akışta toparlanması
- Locale-aware link helper temizliğinin repo genelinde tamamlanması

## Yapılan Değişiklikler

- `GeneralSettings` içinde manuel hero ürün seçimi güvenli fallback ile sınırlandı.
- `GeneralSettings`, `CategoryResource` ve `BlogPostResource` image upload alanlarından PDF kabulü kaldırıldı.
- `PaymentSettings` içinde IBAN / timeout normalizasyonu ve daha net guardrail’ler eklendi.
- `EmailSettings` ve `SmsSettings` alanları için daha sıkı input limitleri eklendi.
- `SeoSettings` canonical domain normalize edecek şekilde güncellendi.
- `SeoDefaults` middleware canonical domain’i public canonical URL’ye taşıyacak şekilde hizalandı.
- `robots.txt` route’u `robots_txt_extra` ile birleştirildi ve sitemap URL’si canonical domain’e bağlandı.
- `GenerateSitemapCommand` canonical domain root’u ile çalışacak şekilde güncellendi.
- Public canonical meta helper’ı path içermeyen origin mantığına hizalandı.
- Yeni guardrail testleri eklendi.

## Yapılan Doğrulamalar

- `php -l`:
  - `app/Filament/Pages/GeneralSettings.php`
  - `app/Filament/Pages/PaymentSettings.php`
  - `app/Filament/Pages/EmailSettings.php`
  - `app/Filament/Pages/SmsSettings.php`
  - `app/Filament/Pages/SeoSettings.php`
  - `app/Console/Commands/GenerateSitemapCommand.php`
  - `app/Http/Middleware/SeoDefaults.php`
  - `routes/web.php`
  - `app/Filament/Resources/CategoryResource.php`
  - `app/Filament/Resources/BlogPostResource.php`
  - `tests/Feature/Admin/SettingsGovernanceTest.php`
  - `tests/Feature/Storefront/BrandingSettingsTest.php`
  - `tests/Feature/Storefront/PublicSurfaceSmokeTest.php`
- Feature / unit test:
  - `SettingsGovernanceTest`
  - `BrandingSettingsTest`
  - `PublicSurfaceSmokeTest`
  - `ReportsAndOperationsTest`
  - `ComplianceResourcesTest`
  - `DynamicMailConfigTest`
  - `SmsServiceTest`
- Ops smoke:
  - `php artisan sitemap:generate` `DB_CONNECTION=sqlite`, `DB_DATABASE=database/database.sqlite`, `CACHE_STORE=file`, `QUEUE_CONNECTION=sync`
  - `php artisan deploy:verify --base-url=http://localhost:8001` aynı override ile

## Kalan Riskler

- Workspace’in default `.env` değeri MySQL’e gidiyor; bu ortamda MySQL bağlantısı yoksa default deploy smoke başarısız olur.
- Default queue/cache ayarları production benzeri ortamda tekrar doğrulanmalı.
- Blade içindeki bazı utility sayfalar hâlâ settings-fed değil.
- `public/sitemap.xml` smoke sırasında yeniden üretildi; build artifact olarak takip edilmeli.

## Önerilen Son Güvenli Adım Sırası

1. Production benzeri DB / cache / queue değerlerini netleştir.
2. `PaymentSettings`, `EmailSettings`, `SmsSettings` alanlarını gerçek operasyon verisiyle doldur.
3. `DeliveryZoneResource` ve `DeliveryTimeSlotResource` kayıtlarını aktive et.
4. `SeoSettings` içindeki canonical domain ve robots ek kurallarını son kez gözden geçir.
5. `php artisan deploy:verify` ve `php artisan sitemap:generate` komutlarını prod-benzeri ortamda tekrar çalıştır.
6. Sonra kısa manuel frontend smoke ile yalnızca admin/ops kaynaklı public yüzeyleri kontrol et.
