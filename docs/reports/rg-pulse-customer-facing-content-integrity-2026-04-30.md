## amaç

Homepage üzerinde müşterinin ilk bakışta negatif algılayacağı yarım, boş, placeholder veya baskın overlay sinyallerini dar bir hızlı scope ile temizlemek.

## kapatılan negatif müşteri-facing sinyaller

- `Özel Günler` alanındaki `0 ürün` gibi doğrudan negatif sayısal sinyal kaldırıldı.
- Özel gün support copy artık sıfır ürün durumunda boşluk ve eksiklik vurgulamak yerine nötr bir keşif yönü veriyor.
- Homepage product rail hattında adı veya slug'ı eksik kartların render edilmesi engellendi; bozuk/yarım kart hissi azaltıldı.
- Cookie banner daha küçük, daha sakin ve içerik üstünde daha az baskın hale getirildi.

## özel günler alanında ne değişti

- Ürün sayısı artık yalnız pozitif olduğunda `Ürün Seçkisi` olarak gösteriliyor.
- Ürün sayısı sıfırsa aynı kutu `Hazırlık Yönü` kartına dönüyor ve kategori/keşif yönü gösteriyor.
- Support kart içindeki `0 ürünle...` dili kaldırıldı; sıfır ürün durumunda daha dürüst ama negatif olmayan bir yönlendirme kullanıldı.

## boş/placeholder kartlar için ne yapıldı

- Homepage section ve shared rail component içinde adı veya slug'ı eksik ürünler render dışı bırakıldı.
- Bu sayede müşteriye boş başlıklı veya yarım kart görünmesi engellendi.
- Data service katmanı genişletilmedi; cleanup yalnız homepage render katmanında tutuldu.

## cookie banner tarafında ne değişti

- Desktop genişliği ve toplam kütlesi küçültüldü.
- Başlık, padding ve buton yoğunluğu azaltıldı.
- Banner gölge ve opaklık baskısı düşürüldü; işlev ve tercih akışı korunurken demo sırasında içerik daha az eziliyor.

## değiştirilen dosyalar

- `C:\nwp0203\rose-garden\resources\views\home\sections\occasion-spotlight.blade.php`
- `C:\nwp0203\rose-garden\resources\views\home\sections\product-rail.blade.php`
- `C:\nwp0203\rose-garden\resources\views\components\product-rail.blade.php`
- `C:\nwp0203\rose-garden\resources\views\cookie-consent.blade.php`
- `C:\nwp0203\rose-garden\app\Services\HomeModuleDataService.php`

## yapılan doğrulamalar

- `npm run build`
- `php artisan test tests/Feature/Storefront/PublicSurfaceSmokeTest.php tests/Feature/Storefront/LayoutPublishingToStorefrontTest.php tests/Feature/Storefront/StorefrontVisibilityTest.php`
- `http://127.0.0.1:8001` için headless Chrome screenshot ile görsel kontrol
- Homepage HTML içinde `0 ürün`/`placeholder` negatif sinyalleri için hızlı içerik taraması

## kalan riskler

- Özel günler alanının gücü hâlâ gerçek katalog derinliğine bağlı; bu tur sahte doluluk eklemedi.
- Rail Alpine/runtime kırığı bu scope dışında bırakıldı.
- Görsel placeholder temizliği homepage render hattında yapıldı; diğer yüzeylerde ayrı doğrulama gerekebilir.

## hızlı scope kapanışa hazır olup olmadığı

Evet. Müşteri gözüne batan ana negatifler olan `0 ürün` sinyali, yarım rail kart riski ve baskın cookie overlay bu dar scope içinde temizlendi. Customer-ready quick close için hazır.
