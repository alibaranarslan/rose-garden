# Rose Garden — Frontend/Backend sertleştirme günlüğü

Tarih: 5 Nisan 2026  
Kapsam: Plan “RG FE/BE canlı sertleştirme” yürütmesi — uyumluluk taraması, bulgular, kod düzeltmeleri, test genişletmesi, kalite komutları, skor notları.

## Özet

- `php artisan test`: **24 test, 148 assertion**, tamamı geçti (önceki baseline: 20 test / 123 assertion).
- Kritik yeni bulgu: **`/{locale}/urunler`** rotasında Laravel’in rota parametresi enjeksiyonu, `ProductController::index` içinde `{locale}` değerini yanlışlıkla **`$slug`** olarak bağlıyordu; sonuç **404** (var olmayan kategori `slug=en`). İmza düzeltildi.
- Dil değiştirici: dil değiştirirken **sorgu dizesi** (`?page=`, filtreler vb.) korunuyor.
- Vitrin kahraman görseli: boş `alt` yerine **H1 tabanlı anlamlı `alt`** (erişilebilirlik).
- `composer audit`: güvenlik uyarısı yok; **1 terk edilmiş paket** notu.
- `npm audit fix`: picomatch yüksek riskleri giderildi; **esbuild/vite** ile ilgili orta seviye uyarılar **geliştirme sunucusu** bağlamında kaldı (`npm audit fix --force` Vite 8’e zorlar — bu turda uygulanmadı).
- `npm run build`: başarılı.
- `php artisan deploy:verify`: tüm kontroller geçti (`APP_URL` örneği `http://localhost:8001`).
- **Lighthouse**: bu ortamda Chrome kurulu olmadığı için CLI koşturulamadı; önceki tur metrikleri için bkz. [live-readiness-2026-04-06.md](live-readiness-2026-04-06.md).

## Çalıştırılan komutlar (kayıt)

| Komut | Sonuç (özet) |
|--------|----------------|
| `php artisan test` | 24 passed, 148 assertions |
| `php artisan route:list` | 180 rota listelendi (vitrin + `{locale}` ağaçları + Filament + Livewire + `/health` + `/up`) |
| `composer audit` | Güvenlik: yok; abandoned: `filament/spatie-laravel-translatable-plugin` |
| `npm audit` → `npm audit fix` | Kısmen düzeltildi; kalan: esbuild (moderate), vite bağımlılığı |
| `npm run build` | Vite production build OK |
| `php artisan deploy:verify` | OK (health + anasayfa) |
| `npx lighthouse@12.8.2 …` | **Başarısız**: “No Chrome installations found” |

## RG-LR (önceki canlı hazırlık raporu) çapraz durumu

Kaynak: [live-readiness-2026-04-06.md](live-readiness-2026-04-06.md) (RG-LR-001 … RG-LR-011).

Bu turda otomatik regresyon olarak:

- Storefront smoke + uyumluluk testleri yeşil; önceki raporda kapatıldığı belirtilen **Livewire/Alpine, cache.page güvenliği, meta favicon koşulları** vb. için **yeniden kırılma sinyali yok** (tam sayfa Lighthouse tekrarlanmadı).

## Yeni / bu turda tespit edilen uyumsuzluklar ve giderim

| ID | Yüzey | Semptom | Kök neden | Çözüm | Doğrulama |
|----|--------|---------|-----------|--------|-----------|
| RG-HB-001 | `/{locale}/urunler` | 404 (boş veya normal DB) | `ProductController::index(Request, ?slug)` imzasında `{locale}` route parametresi isim eşleşmediği için sırayla `$slug`’a bağlanıyordu (`en` → kategori sorgusu `firstOrFail`) | `index(Request $request, ?string $locale = null, ?string $slug = null)` | `PublicSurfaceSmokeTest` + `test_locale_prefixed_core_paths_return_success` + `test_locale_prefixed_product_listing_does_not_confuse_locale_with_category_slug` |
| RG-HB-002 | Dil değiştirici | Dil değişince `?` sorgu parametreleri kayboluyordu | `language-switcher` yalnızca path birleştiriyordu | `getQueryString()` ile `?…` eklendi; path normalizasyonu (`ltrim('/')`) | Manuel / kod incelemesi |
| RG-HB-003 | `store-hero` LCP görseli | `alt=""` | SEO/a11y zayıf | `alt` = kısaltılmış düz metin başlık (`$h1`) | Gözle + Lighthouse önerisi (Chrome olduğunda) |

## Test genişletmesi

Dosya: [tests/Feature/Storefront/PublicSurfaceSmokeTest.php](../../tests/Feature/Storefront/PublicSurfaceSmokeTest.php)

- `en` ve `ku` için çekirdek GET yolları: `urunler`, `blog`, `sepet`, `iletisim`, `ozel-gunler`, `siparis-takip`, `sss`.
- `tr|en|ku` için `/odeme/basarili` ve `/odeme/basarisiz`.
- `POST` `cookie-consent.store` JSON (CSRF test pragmatiği: `withoutMiddleware(VerifyCsrfToken::class)` — gerçek tarayıcı `fetch` + meta token ile çalışır).
- Locale + ürün listesi regresyon testi (RG-HB-001).

## Puanlama ve tavan (follow-up)

| Alan | Bu turda ölçülen | Not |
|------|------------------|-----|
| PHPUnit | 24/24 | Sürekli CI için ana sinyal |
| Composer audit | 0 advisory | Abandoned paket izleme |
| npm audit | 2 moderate (esbuild/vite) | Üretim bundle’ı etkilemez; Vite major yükseltme ayrı plan |
| Lighthouse | Koşturulamadı | Chrome yüklü makinede veya CI’da `lighthouse-ci` önerilir |
| Güvenlik başlıkları | Değişiklik yok | Mevcut: [app/Http/Middleware/SecurityHeaders.php](../../app/Http/Middleware/SecurityHeaders.php) (CSP, Referrer-Policy, XFO, vb.) |

**Tavan için önerilen sonraki işler (öncelik sırasıyla):**

1. Lighthouse’ı **Chrome yüklü** ortamda (veya GitHub Action + `chrome-headless-shell`) anasayfa + PLP + PDP + sepet + checkout için koştur; hedefleri raporda sayısal tut.
2. Vite 6→8 ve bağımlı **esbuild** advisory’leri için ayrı yükseltme PR’ı (`npm audit fix --force` dışında kontrollü geçiş).
3. `/{locale}/sepet` ve `/{locale}/odeme` rotaları hâlâ isimsiz; `route()` her zaman öneksiz Türkçe path üretir — locale **URL ile görünür** tutarlılığı istenirse ayrı rota isimlendirme/refactor (Laravel’de çift `name` çakışmasına dikkat).

## Sağlık uçları (operasyon notu)

- Laravel framework: **`GET /up`** (bootstrap `health` tanımı).
- Uygulama: **`GET /health`** JSON (veritabanı ping). İzleme aracında tek canonical seçilmeli.

## Değişen dosyalar (bu tur)

- [app/Http/Controllers/ProductController.php](../../app/Http/Controllers/ProductController.php)
- [resources/views/components/language-switcher.blade.php](../../resources/views/components/language-switcher.blade.php)
- [resources/views/components/store-hero.blade.php](../../resources/views/components/store-hero.blade.php)
- [tests/Feature/Storefront/PublicSurfaceSmokeTest.php](../../tests/Feature/Storefront/PublicSurfaceSmokeTest.php)
- `package-lock.json` (`npm audit fix` sonrası; commit’e dahil edin)

## Canlıya çıkış — operatör kontrol listesi (kısa)

- `.env` üretim: `APP_URL`, `APP_DEBUG=false`, PayTR, mail, queue/cron, `php artisan config:cache route:cache view:cache`.
- CDN / TLS: HSTS genelde reverse proxy’de; uygulama `ForceHttps` üretimde aktif.
- Bu günlük ve önceki [live-readiness-2026-04-06.md](live-readiness-2026-04-06.md) birlikte release notu olarak arşivlenebilir.
