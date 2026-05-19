# Rose Garden SEO, Sitemap ve EN/KU İlk Üç Düzeltme Raporu

Tarih: 2026-05-19

## Kapsam

- Sitemap çıktısında eski/test domain görünmesi ve fiziksel `public/sitemap.xml` dosyasının route'u gölgeleme riski.
- SEO title alanlarında marka suffix tekrarları.
- EN/KU yüzeylerde yüksek etkili müşteri alanlarında kalan Türkçe veya generic placeholder çeviriler.

## Yapılanlar

- `/sitemap.xml` Laravel route'u artık dinamik `App\Support\SitemapXml` çıktısı döndürüyor.
- Public domainlerde sitemap URL şeması `https://` olarak normalize ediliyor; localhost geliştirme adresleri korunuyor.
- Tracked `public/sitemap.xml` kaldırıldı ve tekrar commit edilmemesi için `.gitignore` kapsamına alındı.
- `sitemap:generate` komutu aynı `SitemapXml` kaynağını kullanacak şekilde sadeleştirildi.
- SEO meta bileşeni, başlık sağlanmadığında site adını tek başına kullanıyor; başlık zaten marka suffix'i içeriyorsa tekrar eklemiyor.
- Ürün listeleme meta title/description metinleri TR/EN/KU locale'e göre üretiliyor; EN başlıklarda `Ürünleri` eki kalmıyor.
- EN/KU JSON dosyalarında iletişim, özel gün, ürün CTA/label ve görünür generic filler üreten yüksek etkili anahtarlar doğal metinlerle güncellendi.

## Doğrulama

- `php -l app\Support\SitemapXml.php`
- `php -l app\Console\Commands\GenerateSitemapCommand.php`
- `php -l app\Http\Controllers\ProductController.php`
- `php artisan test tests\Feature\Storefront\PublicSurfaceSmokeTest.php tests\Feature\Admin\AdminContentSeoReflectionTest.php`
- `npm run build`

Sonuç: hedef testler 20/20 geçti, Vite production build başarılı.

## Not

Bu tur genel i18n overhaul değildir. Yüksek etkili EN/KU müşteri yüzeylerindeki görünür sorunlar düzeltildi; tüm CMS/admin içerik çeviri kapsamı ayrı scope olarak kalır.
