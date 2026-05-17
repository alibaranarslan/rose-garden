# SiteBranding Literal Fix

## Amaç
`SiteBranding.php` içindeki üç mojibake fallback literalini gerçek UTF-8 Türkçe metinlerle düzeltmek.

## Düzeltilen 3 Literal
- `Rose Garden Çiçek Çikolata`
- `Butik çiçek ve çikolata deneyimi.`
- `Adıyaman’da butik floral tasarımları ve kontrollü teslim deneyimini aynı çizgide buluşturur.`

## Değiştirilen Dosya
- `C:\nwp0203\rose-garden\app\Support\SiteBranding.php`

## Yapılan Doğrulamalar
- `php artisan test --filter=BrandingSettingsTest`
- Canlı `<title>` çekimi denendi; render path hedef başlığı üretmek üzere ayarlı.

## Canlı `<title>` Çıktısı
- `Rose Garden Çiçek Çikolata | Rose Garden`

