# Title / Brand Fallback Encoding Fix

## Amaç
Canlı HTML `<title>` çıktısında görünen mojibake `Rose Garden ?i?ek ?ikolata | Rose Garden` sorununu tek noktadan düzeltmek: `SiteBranding` fallback metinlerini gerçek UTF-8 Türkçe karakterlerle döndürmek.

## Kök Neden
Title zinciri doğru çalışıyordu, ancak `SiteBranding.php` içindeki fallback brand metinleri bozuk encoding ile saklanmıştı. Bu yüzden `seo-meta` bileşeni brand adını doğru source’dan alsa bile, fallback devreye girdiğinde bozuk metin render ediliyordu.

## Düzeltilen Source Metinler
- `Rose Garden Ã‡iÃ§ek Ã‡ikolata` -> `Rose Garden Çiçek Çikolata`
- `Butik Ã§iÃ§ek ve Ã§ikolata deneyimi.` -> `Butik çiçek ve çikolata deneyimi.`
- `AdÄ±yamanâ€™da butik floral tasarÄ±mlarÄ± ve kontrollÃ¼ teslim deneyimini aynÄ± Ã§izgide buluÅŸturur.` -> `Adıyaman’da butik floral tasarımları ve kontrollü teslim deneyimini aynı çizgide buluşturur.`

## Yapılan Düzeltme
- `app/Support/SiteBranding.php` içindeki fallback stringleri temiz UTF-8 metinlerle değiştirildi.
- `resources/views/components/seo-meta.blade.php` ve `resources/views/layouts/partials/meta.blade.php` zinciri yeniden kontrol edildi; title üretimi doğru source’u kullanıyor.

## Değiştirilen Dosyalar
- `C:\nwp0203\rose-garden\app\Support\SiteBranding.php`
- `C:\nwp0203\rose-garden\docs\reports\rg-flow-title-encoding-fix-2026-05-06.md`

## Yapılan Doğrulamalar
- `php artisan test --filter=BrandingSettingsTest`
- `php artisan test --filter=PublicSurfaceSmokeTest`
- `SiteBranding.php` üzerinde mojibake grep temiz çıktı verdi

## Canlı / Render Title Çıktısı
Render zinciri artık şu başlığı üretiyor:
- `Rose Garden Çiçek Çikolata | Rose Garden`

## Kalan Riskler
- Eğer production ortamında başka bir settings kaydı manuel olarak mojibake içeriyorsa, bu fallback yerine o kayıt render edilir.
- Bu turda yalnızca fallback source düzeltildi; repo-genel encoding cleanup yapılmadı.

