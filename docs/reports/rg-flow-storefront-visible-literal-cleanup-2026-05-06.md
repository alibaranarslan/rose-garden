# Storefront Visible Literal Cleanup

## Amaç
Müşteriye görünen storefront shell metinlerinde kalan bozuk Türkçe literal’leri temizlemek; büyük redesign veya localization refactor yapmadan header, nav, footer ve language switcher copy’sini düzeltmek.

## Düzeltilen Görünür Alanlar
- Header aksiyonları ve shell copy’si
- Navigation rail ve special occasion copy’si
- Footer keşfet/yasal/iletişim copy’si
- Language switcher açıklama copy’si

## Değiştirilen Dosyalar
- `C:\nwp0203\rose-garden\resources\views\layouts\partials\header.blade.php`
- `C:\nwp0203\rose-garden\resources\views\layouts\partials\nav.blade.php`
- `C:\nwp0203\rose-garden\resources\views\layouts\partials\footer.blade.php`
- `C:\nwp0203\rose-garden\resources\views\components\language-switcher.blade.php`

## Yapılan Doğrulamalar
- `php artisan test --filter=PublicSurfaceSmokeTest`
- Görünür text-node HTML taraması:
  - `hesabim = 0`
  - `Ã = 0`
  - `Ä = 0`
  - `Å = 0`

## Canlı HTML Önce/Sonra
- Önce: visible shell literal’lerinde mojibake izleri vardı.
- Sonra: visible text taramasında mojibake sinyali kalmadı.
- Not: locale route href’lerinde `hesabim` gibi segmentler route mimarisinin parçası olarak kalabilir; bu cleanup yalnız görünür copy’yi hedefler.

## Kalan Riskler
- Route href segmentleri tam olarak bu turun kapsamı dışında kaldı.
- Locale/route mimarisi değişmeden, aynı pattern URL içinde görülebilir.

