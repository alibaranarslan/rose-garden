# RG Pulse Header Alignment Micro Fix - 2026-04-18

## Amaç
Customer-ready fast track sonunda header satırında kalan son iki görünür kusuru kapatmak:
- search shell içindeki `Ara` butonunun taşmış/kırpılmış görünmesi
- search bar ile yanındaki utility pill'lerin aynı ritimde hizalanmaması

## Kapatılan son header kusurları
- Search submit button artık outer shell yüksekliği ile yarışmıyor; iç padding alanında temiz biçimde oturuyor.
- Search form ve utility cluster aynı 44px ritme çekildi, satır içinde görsel basamak farkı azaltıldı.

## Yapılan hizalama düzeltmeleri
- Header row `md:items-stretch` yapısına alındı, böylece search ve utility tarafı aynı satır geometrisini paylaşıyor.
- Utility cluster için `align-self: stretch` ve ortak minimum yükseklik tanımlandı.
- Search shell de aynı şekilde `align-self: stretch` ile bu ritme bağlandı.

## Search button clipping fix özeti
- Search shell içinde küçük iç padding tanımlandı.
- `Ara` butonu dış kabuk yüksekliği yerine iç kabukta `h-9` olarak konumlandı.
- Sağ marj kaldırıldı, minimum genişlik korundu ve line-height sıkılaştırıldı.
- Input alanı da dikey padding yerine tam yükseklik akışına alındı; böylece form tek parça, temiz bir control gibi duruyor.

## Değiştirilen dosyalar
- `resources/views/layouts/partials/header.blade.php`
- `resources/css/app.css`

## Yapılan doğrulamalar
- `php artisan test tests/Feature/Storefront/PublicSurfaceSmokeTest.php tests/Feature/Storefront/HeaderThemeTest.php tests/Feature/Storefront/BrandingSettingsTest.php`
- `npm run build`

## Kalan riskler
- Bu tur görsel mikro-alignment turuydu; son piksel seviyesinde tarayıcı bazlı görsel kontrol yine faydalı olur.
- Mobile-specific ayar yapılmadı; mevcut davranış korunmaya çalışıldı.

## Hızlı scope sonrası karar
Header üzerinde müşteri-facing son iki ana kusur için yüksek ROI mikro-fix uygulanmış durumda. Son güvenli adım kısa bir desktop visual smoke; yeni bir sorun çıkmazsa fast-track kapanışına hazır kabul edilebilir.
