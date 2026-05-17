# Rose Garden Canonical Surface Map

Date: 2026-04-17
Workspace: `C:\nwp0203\rose-garden`
Method: salt-okuma keşif (`routes/web.php`, controller/view/service zinciri, `app/Filament/**`, `php artisan route:list --json`, hedefli `php artisan tinker` URL doğrulamaları)

## Executive summary

- Canlı homepage hattı `routes/web.php` içindeki named `home` route üzerinden `App\Http\Controllers\StorefrontHomeController@index` ile akıyor; render hedefi `resources/views/home/layout-studio.blade.php`.
- `App\Http\Controllers\HomeController@index` ve `resources/views/home/index.blade.php` kod tabanında duruyor ama hiçbir route tarafından çağrılmıyor; bu hat aktif storefront homepage sahibi değil.
- Storefront için locale'siz named route seti ile `/{locale}` prefixed ikinci bir route seti birlikte yaşıyor. Aynı yüzeyleri iki kez tanımlıyorlar; locale'li grup çoğunlukla namesiz ve locale'siz grubun cache middleware'lerini de tam taşımıyor.
- Homepage için Layout Studio gerçekten üretim kaynağı: `StorefrontHomeController` -> `LayoutConfigService::resolveState()` -> `HomeModuleDataService::collect()/buildSections()` -> `home.layout-studio`; publish zinciri `App\Filament\Pages\LayoutStudio` üzerinden `layout_revisions` ve `settings` kayıtlarına yazıyor.
- Cart ve checkout tek katman değil. Cart: route closure -> `resources/views/cart/index.blade.php` -> `App\Livewire\CartPage`. Checkout entry: route closure -> `resources/views/checkout/index.blade.php` -> `App\Livewire\CheckoutWizard`. Payment continuation ve sonuç ekranları ayrıca `CheckoutController` ve `PaytrService` ile sürüyor.
- Locale sahipliği route isimleriyle değil, middleware ve manuel URL inşasıyla taşınıyor. `SetLocale` middleware locale'i route/query/session'dan seçiyor; `language-switcher` ve `seo-meta` prefixed URL'leri elle kuruyor.
- `php artisan tinker` ile doğrulandı: `route('home', ['locale' => 'en'])` sonucu `/en` değil `http://localhost:8001?locale=en`. Aynı davranış `products.index`, `products.show` ve `search` için de geçerli. Bu, locale duplicate route setinin named-route sahibi olmadığını somut olarak gösteriyor.
- Trust, hero ve fallback copy birden fazla dosyada çoğalıyor. Özellikle `home/index.blade.php`, `home/sections/hero.blade.php`, `components/store-hero.blade.php`, `components/trust-badges.blade.php`, `home/sections/trust-badges.blade.php`, `layouts/partials/announcement-bar*.blade.php` paralel truth source üretiyor.
- Static informational pages aynı seviyede yönetilmiyor: `/sayfa/{slug}` CMS tabanlı ve `PageResource` ile besleniyor; `/iletisim` kısmen settings tabanlı; `/sss` ve `/teslimat-bilgileri` ise Blade içine gömülü operasyonel copy taşıyor.

## Canonical storefront surface map

| Surface | Route | Controller/Closure | View | Livewire | Data source/service | Admin source | Status |
| --- | --- | --- | --- | --- | --- | --- | --- |
| Homepage | `/` named `home`<br>`/{locale}` (`locale = tr\|en\|ku`) | `StorefrontHomeController@index` | `resources/views/home/layout-studio.blade.php` | Yok | `LayoutConfigService`, `HomeModuleDataService`, `HeaderThemeResolver`, `LocalizedSettings`, `StorefrontImage` | `App\Filament\Pages\LayoutStudio`, `App\Filament\Pages\GeneralSettings`, `App\Filament\Resources\HeaderThemeResource`, `App\Filament\Pages\SeoSettings` | `canonical` - `php artisan route:list --json` named `home` route'u doğrudan bu controller'a bağlıyor; render zinciri Layout Studio publish state'ini okuyor. |
| Listing / all products | `/urunler` named `products.index`<br>`/{locale}/urunler` namesiz alias | `ProductController@index` | `resources/views/products/index.blade.php` | Kart seviyesinde `favorite-toggle`, `add-to-cart` | `Product`, `Category`, `Tag`, `ProductVariant` | `App\Filament\Resources\ProductResource`, `App\Filament\Resources\CategoryResource`, `App\Filament\Pages\SeoSettings` | `canonical` - named route ve route:list action aynı controller'a işaret ediyor; PLP bu Blade üzerinden kurulmuş. |
| Listing / category | `/kategori/{slug}` named `products.category`<br>`/{locale}/kategori/{slug}` namesiz alias | `ProductController@index` | `resources/views/products/index.blade.php` | Kart seviyesinde `favorite-toggle`, `add-to-cart` | `Category` subtree çözümü, `Product`, `Tag`, `ProductVariant` | `App\Filament\Resources\CategoryResource`, `App\Filament\Resources\ProductResource` | `canonical` - kategori ve genel listing aynı kanonik PLP view'ini paylaşıyor; route param sadece filtre bağlamı ekliyor. |
| Product detail | `/urun/{slug}` named `products.show`<br>`/{locale}/urun/{slug}` closure proxy | `ProductController@show` | `resources/views/products/show.blade.php` | `add-to-cart`, `favorite-toggle` | `Product`, related product query, `StorefrontImage` | `App\Filament\Resources\ProductResource`, `App\Filament\Resources\CategoryResource`, `App\Filament\Pages\SeoSettings` | `canonical` - PDP named route locale'siz grupta; locale'li route aynı controller'a closure ile delege ediyor. |
| Search | `/arama` named `search`<br>`/{locale}/arama` namesiz alias | `SearchController@index` | `resources/views/search/results.blade.php` | Yok | `Product` full-page arama sorgusu | `App\Filament\Resources\ProductResource`, `App\Filament\Pages\SeoSettings` | `canonical` - public search yüzeyi controller tabanlı full page; `App\Livewire\ProductSearch` bu yüzeyin sahibi değil. |
| Cart | `/sepet` named `cart`<br>`/{locale}/sepet` namesiz alias | Route closure -> `view('cart.index')` | `resources/views/cart/index.blade.php` | `App\Livewire\CartPage` | `CartItem`, `Coupon`, session/user cart ownership | `App\Filament\Resources\CouponResource`, dolaylı olarak `App\Filament\Resources\ProductResource` | `canonical` - public entry route closure olsa da gerçek davranış `CartPage` içinde. |
| Checkout entry | `/odeme` named `checkout`<br>`/{locale}/odeme` namesiz alias | Route closure -> `view('checkout.index')` | `resources/views/checkout/index.blade.php` | `App\Livewire\CheckoutWizard` | `CartItem`, `Order`, `OrderItem`, `DeliveryZone`, `DeliveryTimeSlot`, `Coupon`, `LoyaltyPoint`, `LoyaltyService`, `PaymentSettings` | `App\Filament\Pages\PaymentSettings`, `App\Filament\Resources\DeliveryZoneResource`, `App\Filament\Resources\DeliveryTimeSlotResource`, `App\Filament\Resources\CouponResource`, `App\Filament\Pages\LoyaltyManagement` | `canonical` - checkout yaratımı controller'da değil Livewire wizard içinde. |
| Checkout payment | `/odeme/{order}` named `checkout.payment`<br>`/{locale}/odeme/{order}` closure proxy | `CheckoutController@processPayment` | `resources/views/checkout/payment.blade.php` | Yok | `Order`, `PaytrService`, `PaymentSettings` | `App\Filament\Pages\PaymentSettings`, `App\Filament\Resources\OrderResource` | `canonical` - ödeme iframe aşaması wizard sonrası controller katmanına geçiyor. |
| Checkout result | `/odeme/basarili`, `/odeme/basarisiz` named `checkout.success` / `checkout.fail`<br>`/{locale}/...` namesiz alias | `CheckoutController@success`, `CheckoutController@fail` | `resources/views/checkout/success.blade.php`, `resources/views/checkout/fail.blade.php` | Yok | `Order`, `PaymentSettings::bankTransferDetails()` | `App\Filament\Pages\PaymentSettings`, `App\Filament\Resources\OrderResource` | `canonical` - checkout sonrası yüzeyler ayrı controller view'leri olarak sahiplenilmiş. |
| Login | `/giris` named `login`<br>`/{locale}/giris` namesiz alias | `Auth\LoginController@showLogin` | `resources/views/account/login.blade.php` | Yok | `User`, guest cart merge akışı login submit'te `CartItem` kullanıyor | `App\Filament\Resources\UserResource` | `canonical` - auth giriş ekranı tek view üzerinden. |
| Register | `/kayit` named `register`<br>`/{locale}/kayit` namesiz alias | `Auth\LoginController@showRegister` | `resources/views/account/register.blade.php` | Yok | `User`, `CartItem`, `preferred_language` seti | `App\Filament\Resources\UserResource` | `canonical` - kayıt sonrası kullanıcı dili `app()->getLocale()` ile kaydediliyor. |
| Password reset | `/sifremi-unuttum`, `/sifre-sifirla/{token}` named `password.request` / `password.reset`<br>`/{locale}/...` namesiz alias | `Auth\PasswordResetController` | `resources/views/account/forgot-password.blade.php`, `resources/views/account/reset-password.blade.php` | Yok | Laravel password broker, `User` | `App\Filament\Resources\UserResource` | `canonical` - reset flow controller tabanlı ve ayrı auth view'lerine bağlı. |
| Account surfaces | `/hesabim`, `/hesabim/siparislerim`, `/hesabim/siparis/{orderNumber}`, `/hesabim/favorilerim`, `/hesabim/puanlarim`, `/hesabim/adreslerim`, `/hesabim/profilim`, `/hesabim/kvkk`<br>`/{locale}/...` alias grubu | `AccountController` | `resources/views/account/*.blade.php`, `resources/views/account/orders/*.blade.php`, `resources/views/layouts/account.blade.php` | Yok | `Order`, `Address`, `Favorite`, `LoyaltyPoint`, `LoyaltyTransaction`, `DataRequest`, `CartItem` reorder | `App\Filament\Resources\OrderResource`, `App\Filament\Resources\UserResource`, `App\Filament\Pages\LoyaltyManagement`, `App\Filament\Resources\DataRequestResource` | `canonical` - hesap alanı tek controller ailesi ve tek account layout altında toplanmış. |
| Blog index | `/blog` named `blog.index`<br>`/{locale}/blog` namesiz alias | `BlogController@index` | `resources/views/blog/index.blade.php` | Yok | `BlogPost`, `BlogCategory`, `StorefrontImage` | `App\Filament\Resources\BlogPostResource`, `App\Filament\Resources\BlogCategoryResource`, `App\Filament\Pages\SeoSettings` | `canonical` - named route locale'siz grupta, public blog listesi bu controller üzerinden. |
| Blog detail | `/blog/{slug}` named `blog.show`<br>`/{locale}/blog/{slug}` closure proxy | `BlogController@show` | `resources/views/blog/show.blade.php` | Yok | `BlogPost`, related `Product`, `StorefrontImage` | `App\Filament\Resources\BlogPostResource`, `App\Filament\Resources\ProductResource` | `canonical` - detail view aynı controller mantığını locale'li alias ile de kullanıyor. |
| Static page (CMS) | `/sayfa/{slug}` named `page.show`<br>`/{locale}/sayfa/{slug}` closure proxy | `PageController@show` | `resources/views/pages/show.blade.php` | Yok | `Page` model | `App\Filament\Resources\PageResource`, `App\Filament\Pages\SeoSettings` | `canonical` - gerçek CMS static page yüzeyi bu route; içerik verisi `pages` tablosundan geliyor. |
| Contact page | `/iletisim` named `contact`<br>`/{locale}/iletisim` namesiz alias | `PageController@contact` | `resources/views/pages/contact.blade.php` | Yok | `siteSettings`/`SiteBranding`, contact form POST | `App\Filament\Pages\GeneralSettings` | `canonical` - view settings tabanlı iletişim verisi kullanıyor; `PageResource` ile beslenmiyor. |
| FAQ | `/sss` named `faq`<br>`/{locale}/sss` namesiz alias | `PageController@faq` | `resources/views/pages/faq.blade.php` | Yok | Blade içine gömülü FAQ array'i | Doğrudan admin kaynağı yok | `canonical` - route aktif, ancak içerik CMS ya da settings'ten değil doğrudan Blade'den geliyor. |
| Delivery info | `/teslimat-bilgileri` named `delivery.info`<br>`/{locale}/teslimat-bilgileri` namesiz alias | `PageController@deliveryInfo` | `resources/views/pages/delivery-info.blade.php` | Yok | Blade içine gömülü bölgeler, saatler ve ücret örnekleri | Doğrudan admin kaynağı yok; operasyonel komşu kaynaklar `DeliveryZoneResource` / `DeliveryTimeSlotResource` | `canonical` - route aktif ama sayfa içeriği admin delivery tablolarından okunmuyor. |
| Special occasions index | `/ozel-gunler` named `special-occasions.index`<br>`/{locale}/ozel-gunler` namesiz alias | `SpecialOccasionController@index` | `resources/views/special-occasions/index.blade.php` | Yok | `SpecialOccasion`, `Product`, `StorefrontImage` | `App\Filament\Resources\SpecialOccasionResource`, `App\Filament\Resources\ProductResource`, `App\Filament\Resources\CategoryResource` | `canonical` - storefront takvim yüzeyi bu controller ve view ile sahiplenilmiş. |
| Special occasion detail | `/ozel-gunler/{slug}` named `special-occasions.show`<br>`/{locale}/ozel-gunler/{slug}` closure proxy | `SpecialOccasionController@show` | `resources/views/special-occasions/show.blade.php` | Yok | `SpecialOccasion`, category/product bağları, `StorefrontImage` | `App\Filament\Resources\SpecialOccasionResource`, `App\Filament\Resources\ProductResource` | `canonical` - detail yüzeyi controller bazlı ve locale alias ile çoğaltılmış. |
| Order tracking | `/siparis-takip` named `order.track`<br>`POST /siparis-takip` named `order.track.submit`<br>`/{locale}/...` alias grubu | `OrderTrackingController@index`, `OrderTrackingController@track` | `resources/views/order-tracking/index.blade.php` | Yok | `Order`, `OrderStatus` support sınıfı, `statusHistory` | `App\Filament\Resources\OrderResource` | `canonical` - anonim takip yüzeyi tek form ve tek result view üzerinden çalışıyor. |

## Duplicate or deprecated paths

### Kesin duplicate / deprecated

- `app/Http/Controllers/HomeController.php` ve `resources/views/home/index.blade.php`
  - Durum: `deprecated`
  - Neden: `routes/web.php` içinde hiçbir route bu controller'a bağlı değil.
  - Ne gölgeliyor: `StorefrontHomeController@index` -> `resources/views/home/layout-studio.blade.php`

- `app/Http/Controllers/CheckoutController::index()`
  - Durum: `duplicate`
  - Neden: public `/odeme` ve `/{locale}/odeme` rotaları controller'a değil doğrudan `view('checkout.index')` closure'ına bağlı.
  - Ne gölgeliyor: `routes/web.php` içindeki iki checkout entry closure'ı

- `routes/web.php` içindeki locale'li storefront grubu (`prefix('{locale}')`) ile locale'siz storefront grubu
  - Durum: `duplicate` route group
  - Neden: homepage, listing, product, search, cart, checkout, auth, account, blog, static pages, special occasions ve order tracking yüzeyleri iki ayrı grupta tekrar tanımlı.
  - Gölgeleme biçimi: URL-space çakışması değil; aynı yüzeylerin iki paralel tanımı.
  - Kritik fark: named routes yalnızca locale'siz grupta; cache middleware de yalnızca locale'siz grupta daha tutarlı.

- `resources/views/layouts/partials/announcement-bar.blade.php`
  - Durum: `deprecated`
  - Neden: `resources/views/layouts/app.blade.php` yalnızca `announcement-bar-dynamic.blade.php` include ediyor.
  - Ne gölgeliyor: `resources/views/layouts/partials/announcement-bar-dynamic.blade.php`

### Unreferenced legacy candidates

- `app/Livewire/CartIcon.php` + `resources/views/livewire/cart-icon.blade.php`
  - Durum: `unclear`
  - Bulgular: view veya component referansı bulunmadı; header `resources/views/components/cart-link.blade.php` ile doğrudan `CartItem` count çekiyor.

- `app/Livewire/ProductSearch.php` + `resources/views/livewire/product-search.blade.php`
  - Durum: `unclear`
  - Bulgular: view/component referansı bulunmadı; aktif search yüzeyi `SearchController@index` -> `resources/views/search/results.blade.php`.

## Homepage ownership

### Aktif homepage hattı

- Route sahibi:
  - `routes/web.php`
  - Named route: `home`
  - Route list sonucu: `GET / -> App\Http\Controllers\StorefrontHomeController@index`

- Render zinciri:
  - `StorefrontHomeController@index`
  - `StorefrontHomeController::renderHome()`
  - `LayoutConfigService::resolveState()`
  - `HomeModuleDataService::collect()`
  - `HomeModuleDataService::buildSections()`
  - `view('home.layout-studio')`

- Üretim publish kaynağı:
  - `App\Filament\Pages\LayoutStudio`
  - `LayoutConfigService::publishDraft()`
  - `layout_revisions` publish state'i
  - `settings` içindeki appearance anahtarları

- Homepage'i etkileyen ek admin kaynakları:
  - `App\Filament\Pages\GeneralSettings`
    - hero copy
    - home intro copy
    - showcase copy
    - best-seller copy
    - hero spotlight mode / manual product
  - `App\Filament\Resources\HeaderThemeResource`
    - üst banner / dönemsel header tavrı
  - `App\Filament\Pages\SeoSettings`
    - canonical domain, default meta

### Eski homepage hattı

- Dosyalar:
  - `app/Http/Controllers/HomeController.php`
  - `resources/views/home/index.blade.php`

- Durum:
  - Route bağlılığı bulunmadı.
  - Aynı domain verisini kısmen yeni hatla paylaşıyor (`Product`, `Category`, `BlogPost`, `SpecialOccasion`, `Setting`, `LocalizedSettings`, `StorefrontImage`).
  - Ancak storefront runtime sahipliği yok.

### Layout Studio gerçekten üretim kaynağı mı?

- Evet.
- Gerekçe:
  - Aktif route doğrudan `StorefrontHomeController`a gidiyor.
  - Controller yalnızca `home.layout-studio` render ediyor.
  - `LayoutStudio` admin sayfası draft/publish lifecycle'ını `LayoutConfigService` üzerinden yönetiyor.
  - `StorefrontHomeController@preview` ve `themePreview` signed preview route'ları da aynı render zincirini kullanıyor.

### Copy / trust / fallback tekrarları

- Hero fallback copy:
  - `resources/views/home/index.blade.php`
  - `resources/views/home/sections/hero.blade.php`
  - `resources/views/components/store-hero.blade.php`

- Trust / reassurance copy:
  - `resources/views/components/trust-badges.blade.php`
  - `resources/views/home/sections/trust-badges.blade.php`

- Announcement copy:
  - aktif: `resources/views/layouts/partials/announcement-bar-dynamic.blade.php`
  - eski statik kopya: `resources/views/layouts/partials/announcement-bar.blade.php`

- Kategori keşif / fallback dili:
  - `resources/views/home/sections/category-showcase.blade.php`
  - `resources/views/products/index.blade.php`
  - `resources/views/products/show.blade.php`

## Cart and checkout ownership

### Cart ownership

- Entry route:
  - `/sepet`
  - `/{locale}/sepet`
  - route closure -> `view('cart.index')`

- Render ownership:
  - `resources/views/cart/index.blade.php`
  - `App\Livewire\CartPage`

- Runtime veri sahipliği:
  - guest cart: `session('cart_session_id')`
  - auth cart: `CartItem.user_id`
  - coupon handling: `Coupon`, `session('cart_coupon_id')`

- Admin touchpoints:
  - `CouponResource`
  - dolaylı ürün geçerliliği için `ProductResource`

### Checkout ownership

- Entry:
  - `/odeme`
  - `/{locale}/odeme`
  - route closure -> `resources/views/checkout/index.blade.php`
  - gerçek create-order akışı: `App\Livewire\CheckoutWizard`

- Wizard sorumlulukları:
  - sender / recipient bilgileri
  - address preload
  - delivery zone & slot seçimi
  - coupon indirimi
  - loyalty point kullanımı
  - order ve order items yaratımı
  - bank transfer / credit card branching

- Payment continuation:
  - `/odeme/{order}` -> `CheckoutController@processPayment`
  - `PaytrService` iframe token üretimi

- Result surfaces:
  - `/odeme/basarili` -> `CheckoutController@success`
  - `/odeme/basarisiz` -> `CheckoutController@fail`

- Kritik admin touchpoints:
  - `PaymentSettings`
  - `DeliveryZoneResource`
  - `DeliveryTimeSlotResource`
  - `CouponResource`
  - `LoyaltyManagement`
  - `OrderResource`

- Net katman ayrımı:
  - Cart entry controller tabanlı değil.
  - Checkout entry controller tabanlı değil.
  - Order creation Livewire'da.
  - External payment handoff controller + service katmanında.
  - Bu yüzden checkout refactor'ı tek dosyalık iş değil.

## Admin touchpoints affecting storefront

- `App\Filament\Pages\LayoutStudio`
  - homepage module order, visibility, appearance, publish lifecycle

- `App\Filament\Pages\GeneralSettings`
  - site branding
  - contact data
  - social links
  - homepage copy
  - hero spotlight source

- `App\Filament\Resources\HeaderThemeResource`
  - storefront header seasonal/manual theme

- `App\Filament\Resources\ProductResource`
  - listing, PDP, add-to-cart, related products, search, homepage product rails

- `App\Filament\Resources\CategoryResource`
  - nav category tree, category listing ownership, homepage category showcase

- `App\Filament\Resources\SpecialOccasionResource`
  - special occasion index/detail and homepage occasion spotlight

- `App\Filament\Resources\BlogPostResource`
  - blog index/detail and homepage blog preview data

- `App\Filament\Resources\BlogCategoryResource`
  - blog taxonomy

- `App\Filament\Resources\PageResource`
  - only `/sayfa/{slug}` CMS surface

- `App\Filament\Pages\PaymentSettings`
  - checkout payment method behavior, bank transfer copy, PayTR activation

- `App\Filament\Resources\DeliveryZoneResource`
  - checkout fee and cutoff behavior

- `App\Filament\Resources\DeliveryTimeSlotResource`
  - checkout slot availability

- `App\Filament\Resources\CouponResource`
  - cart and checkout discount behavior

- `App\Filament\Pages\LoyaltyManagement`
  - account loyalty view and checkout loyalty redemption rules

- `App\Filament\Resources\OrderResource`
  - order tracking and account order history/status surfaces

- `App\Filament\Resources\UserResource`
  - customer lifecycle data, preferred language visibility

- `App\Filament\Resources\DataRequestResource`
  - account KVKK request surface

- `App\Filament\Pages\SeoSettings`
  - canonical domain, default meta, robots behavior

## Locale ownership model

### Runtime ownership

- Middleware:
  - `app/Http/Middleware/SetLocale.php`
  - Locale kaynak sırası: route param -> query param -> session -> default `tr`

- Allowed locale set:
  - `tr`
  - `en`
  - `ku`

- Global URL default:
  - `App\Providers\AppServiceProvider::boot()`
  - `URL::defaults(['locale' => app()->getLocale()])`

### URL ownership gerçekliği

- Named routes yalnızca locale'siz storefront grubunda tanımlı.
- Locale'li prefixed route grubu namesiz.
- Bu yüzden named route helper locale prefix sahibi değil.

### Doğrulanmış davranış

- `php artisan tinker --execute "echo route('home', ['locale' => 'en']);"`
  - sonuç: `http://localhost:8001?locale=en`

- `php artisan tinker --execute "echo route('products.index', ['locale' => 'en']);"`
  - sonuç: `http://localhost:8001/urunler?locale=en`

- `php artisan tinker --execute "echo route('products.show', ['locale' => 'en', 'slug' => 'demo']);"`
  - sonuç: `http://localhost:8001/urun/demo?locale=en`

- `php artisan tinker --execute "echo route('search', ['locale' => 'en']);"`
  - sonuç: `http://localhost:8001/arama?locale=en`

### Sonuç

- Locale prefix sahipliği route name katmanında değil.
- Prefix üretimi şu anda manuel katmanlarda çözülüyor:
  - `resources/views/components/language-switcher.blade.php`
  - `resources/views/components/seo-meta.blade.php`
  - preview URL üretimi `LayoutConfigService` / `HeaderThemeResolver`

### SEO ownership

- `app/Http/Middleware/SeoDefaults.php`
  - yalnızca default locale (`tr`) prefix'ini canonical URL'den strip ediyor.

- `resources/views/components/seo-meta.blade.php`
  - `hreflang` ve alternate URL'leri route helper yerine manuel path inşasıyla üretiyor.

## Top technical risks

1. Named-route locale kırılması
   - Çünkü named routes locale prefix sahibi değil.
   - Somut etki: `route('home', ['locale' => app()->getLocale()])` gibi çağrılar `/en` yerine `?locale=en` üretiyor.

2. Duplicate route group divergence
   - Çünkü locale'li ve locale'siz gruplar aynı yüzeyleri iki kez taşıyor.
   - Somut fark: locale'siz grupta `cache.page:*` middleware varken locale'li grupta çoğu yüzey bunu kaybediyor.

3. Homepage truth-source çoğalması
   - Çünkü eski ve yeni homepage aynı içerik domain'ini farklı Blade kurgularında tutuyor.
   - Etkilenen dosyalar: `home/index.blade.php`, `home/layout-studio.blade.php`, `home/sections/*`, `components/store-hero.blade.php`, `components/trust-badges.blade.php`.

4. Checkout katman sınırlarının dağınık olması
   - Çünkü entry route closure, order creation Livewire, payment continuation controller, gateway logic service katmanında.
   - Yanlış dosyaya müdahale etme riski yüksek.

5. Static informational page drift
   - Çünkü `/sss` ve `/teslimat-bilgileri` operational copy'yi Blade içine gömüyor.
   - Bu veri checkout tarafındaki admin-managed `DeliveryZone` / `DeliveryTimeSlot` / `PaymentSettings` ile senkron değil.

6. Unused legacy candidates
   - `HomeController`, `CheckoutController::index`, `announcement-bar.blade.php`, `CartIcon`, `ProductSearch`
   - Bunlar yanlış sahiplik çıkarımına neden olabilir.

## Recommended “safe next-step” task order

1. Homepage sahipliğini kilitle
   - `StorefrontHomeController` + `home.layout-studio` + `LayoutStudio` publish zincirini tek kanonik hat olarak belgeleyip legacy `HomeController` hattını ayrı klasöre alınacak aday olarak etiketle.

2. Locale route modelini netleştir
   - named routes ile locale prefix arasındaki ayrımı çözmeden storefront refactor'ına girme.
   - Önce hangi grup kanonik URL-space sahibi olacak onu seç.

3. Cart / checkout katmanlarını ayrı backlog olarak böl
   - cart entry
   - checkout wizard
   - payment handoff
   - success/fail

4. Copy ve trust truth-source temizliğini homepage sonrası yap
   - hero, trust, announcement, fallback copy'yi tek kaynaklara indir.

5. Static page sahipliğini ayır
   - `/sayfa/{slug}` CMS
   - `/iletisim` settings-fed utility page
   - `/sss` ve `/teslimat-bilgileri` hardcoded utility page

6. Admin touchpoint audit'ini storefront'a göre sırala
   - önce `LayoutStudio`, `GeneralSettings`, `HeaderThemeResource`
   - sonra `ProductResource`, `CategoryResource`, `SpecialOccasionResource`
   - sonra checkout-related admin yüzeyler

