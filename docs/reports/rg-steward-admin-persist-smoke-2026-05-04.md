# RG Steward Admin Persist Smoke 2026-05-04

**Tarih:** 2026-05-04  
**Kapsam:** admin persist blocker için browser-level canlı smoke, product save ve general settings save doğrulaması  
**Not:** Yeni kod yazılmadı. Bu tur yalnızca teknik olarak kapatıldığı raporlanan persist blocker'ı canlı admin senaryosuyla doğruladı.

## Amaç

Admin panelde daha önce blocker olarak görülen iki save zincirinin gerçekten browser-level kalıcı yazdığını doğrulamak:

- Product `short_description`
- General settings `site_name.tr`

Ek hedef, bu değişikliklerin storefront tarafında görünür şekilde yansıyabildiğini kanıtlamak ve sonra güvenli biçimde eski hale döndürmekti.

## Denenen Canlı Senaryolar

### 1. Product persist smoke

- Admin login yapıldı.
- `Products` listesinden `Rustik Kırmızı Gül Buketi — Pamuk ve Hediyeli` kaydı açıldı.
- `short_description` içine izlenebilir bir `persist-smoke` marker eklendi.
- Save akışı çalıştırıldı.
- DB satırı ve public PDP kontrol edildi.
- Ardından kayıt eski haline geri döndürüldü.

### 2. General settings persist smoke

- `General Settings` açıldı.
- `site_name.tr` içine `LIVE` marker eklendi.
- Save akışı çalıştırıldı.
- `settings` tablosu ve homepage shell kontrol edildi.
- Ardından kayıt eski haline geri döndürüldü.

## Product Save Sonucu

- `products.short_description` browser-level değişiklik sonrası kalıcı olarak yazıldı.
- Raw DB değeri marker ile güncellendi.
- Public PDP üzerinde marker görünür oldu.
- Sonra değer eski haline geri döndürüldü ve public PDP marker'sız hale geldi.

## General Settings Save Sonucu

- `settings(group=general,key=site_name)` browser-level değişiklik sonrası kalıcı olarak yazıldı.
- Raw DB değeri marker ile güncellendi.
- Homepage title / shell marker'lı değeri gösterdi.
- Sonra değer eski haline geri döndürüldü ve homepage title tekrar önceki haline döndü.

## Storefront Yansıması

- Product marker public PDP'de görüldü.
- General settings marker homepage title'da görüldü.
- Bu iki yansıma, admin save -> DB write -> storefront render zincirinin browser-level çalıştığını gösterdi.

## Geri Alınan / Alınmayan Veri Değişiklikleri

- Product `short_description` değişikliği geri alındı.
- General settings `site_name.tr` değişikliği geri alındı.
- Kalıcı bırakılan veri değişikliği yok.

## Blocker Kapanıp Kapanmadı?

- Evet, persist blocker kapandı.
- Önceki live scenario turunda görülen browser-level save/persist sorunu bu smoke'ta tekrar üretilemedi.
- Product ve general settings save akışları artık browser-level kalıcı yazıyor ve storefront'a yansıyor.

## Yapılan Doğrulamalar

- Admin login başarılıydı.
- Product edit sayfası açıldı.
- General settings sayfası açıldı.
- DB doğrudan tinker ile doğrulandı.
- Public PDP ve homepage title ile storefront yansıması doğrulandı.
- Geri dönüş sonrası değerlerin eski haline döndüğü tekrar doğrulandı.

## Değiştirilen Dosyalar

- `kod degisikligi yok`

## Sonraki Güvenli Adım

1. Admin persist blocker kapanmış kabul edilerek mobile QA scope'a geç.
2. Persist zinciri başka admin resource'larda da kritikse kısa bir çapraz smoke ile tekrar bak.
3. Public smoke sonrası deploy akışında aynı iki save yüzeyi için tekrar hızlı kontrol yap.
