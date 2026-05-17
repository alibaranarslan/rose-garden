# RG Pulse Homepage Polish - 2026-04-17

## Amac
Canli storefront homepage'ini daha dolu, daha ticari ve daha profesyonel hissettiren bir merchandising ritmine yaklastirmak; bunu yaparken canonical homepage ownership hattini korumak ve katalog/query mantigina geri donmemek.

## Homepage polish hedefleri
- Hero sonrasi kesif ve satin alma yonunu daha net hissettirmek
- Buyuk moduller arasindaki anlamsiz whitespace'i azaltmak
- Secili vitrin alanini tek kart hissinden cikarip daha verimli urun proof tasiyan bir kompozisyona yaklastirmak
- Ozel gun blogunu zayif ara katman olmaktan cikarip anlamli bir yonlendirme alani haline getirmek
- Header ve nav okunurlugunu iyilestirmek
- Dark/light dengesi ile `tr/en/ku` toleransini bozmamak

## Yapilan gorsel ve merchandising kararlari
- Homepage shell icinde section rhythm lokal olarak sikilastirildi; global sistem degistirilmedi.
- Erken homepage akisinda `best_sellers`, `new_arrivals` oncesinde daha ticari agirlikla konumlandi.
- Hizli kesif modulu, kategori girisini korurken satin alma niyeti tasiyan hizli linkler ve gercek urun proof ile guclendirildi.
- Secili vitrin, tek spot urun anlatimindan cikartilip ana urun + iki yakin urun proof tasiyan bir merchandising blok olarak yeniden dengelendi.
- Product rail basliklari sayfa akisinda daha az boslukla ve urun adedi sinyaliyle desteklendi.
- Ozel gun blogu, gorsel + tarih/durum/deger + urun onerisi birlesimiyle daha guclu bir yonlendirme alanina donusturuldu.

## Hangi moduller sikilasti
- `resources/views/home/layout-studio.blade.php`
  Homepage shell icinde section spacing lokal olarak daraltildi.
- `resources/views/home/sections/category-showcase.blade.php`
  Baslik bolgesi ve yan panel copy/CTA ritmi toparlandi.
- `resources/views/home/sections/product-rail.blade.php`
  Baslik-alan boslugu kucultuldu.

## Hangi moduller guclendi
- `resources/views/home/sections/featured-showcase.blade.php`
  Bir ana urun ve iki destek urunle daha verimli bir secili vitrin kompozisyonu kuruldu.
- `resources/views/home/sections/occasion-spotlight.blade.php`
  Gorsel, baglamsal tarih/durum, urun adedi ve urun onerisi ayni blokta toplandi.
- `resources/views/home/sections/category-showcase.blade.php`
  Hizli kesif, alisverise basla sinyali ve gercek urun proof ile daha netlestirildi.

## Header/nav tarafinda ne duzeldi
- `resources/views/layouts/partials/header.blade.php`
  Logo altinda cizgi gibi gorunen dekoratif problem kaldirildi; tagline daha kontrollu bir pill icine alindi.
- `resources/views/layouts/partials/header.blade.php`
  Search shell kontrasti ve kontrol hizasi guclendirildi.
- `resources/views/layouts/partials/nav.blade.php`
  Desktop nav pill'leri daha buyuk, daha okunur ve daha profesyonel bir casing/tracking ile guncellendi.
- `resources/css/app.css`
  Header nav pill icin hedefli stil sinifi eklendi.

## Degistirilen dosyalar
- `C:\nwp0203\rose-garden\resources\views\home\layout-studio.blade.php`
- `C:\nwp0203\rose-garden\resources\views\home\sections\category-showcase.blade.php`
- `C:\nwp0203\rose-garden\resources\views\home\sections\featured-showcase.blade.php`
- `C:\nwp0203\rose-garden\resources\views\home\sections\product-rail.blade.php`
- `C:\nwp0203\rose-garden\resources\views\home\sections\occasion-spotlight.blade.php`
- `C:\nwp0203\rose-garden\resources\views\layouts\partials\header.blade.php`
- `C:\nwp0203\rose-garden\resources\views\layouts\partials\nav.blade.php`
- `C:\nwp0203\rose-garden\resources\css\app.css`

## Yapilan dogrulamalar
- `php artisan test tests/Feature/Storefront/PublicSurfaceSmokeTest.php`
- `php artisan test tests/Feature/Storefront/LayoutPublishingToStorefrontTest.php`
- `php artisan test tests/Feature/Storefront/StorefrontVisibilityTest.php`
- `php artisan test tests/Feature/Storefront/HeaderThemeTest.php tests/Feature/Storefront/BrandingSettingsTest.php`
- `php artisan view:cache`
- `npm run build`

## Kalan riskler
- Bu tur gorsel ritim ve merchandising kompozisyonuna odaklandigi icin gercek cihazlarda desktop viewport gorusel kontrolu halen degerli.
- Header/nav copy'lerinde mevcut locale kaynaklari ASCII fallback ile calisiyor; bu tur locale mimarisine geri donulmedi.
- Ozel gun blogunun gercek gucu, o tarihe bagli urun cesitliligiyle sinirli kalmaya devam ediyor; fake density eklenmedi.

## Sonraki guvenli adim
Homepage sonrasinda utility polish turunda, storefront genel shell okunurlugu ve ikincil yuzeylerin daha kompakt ama tutarli bir gorsel ritme alinmasi.
