const fs = require('fs');
const path = require('path');

const root = path.resolve(__dirname, '..');
const localeFiles = ['tr', 'en', 'ku'];
const lang = Object.fromEntries(localeFiles.map((locale) => [
    locale,
    JSON.parse(fs.readFileSync(path.join(root, 'lang', `${locale}.json`), 'utf8')),
]));

const en = {
    'Anasayfa': 'Home',
    'Anasayfaya Dön': 'Back to Home',
    'Ürünler': 'Products',
    'Tüm Ürünler': 'All Products',
    'Özel Günler': 'Special Occasions',
    'İletişim': 'Contact',
    'Ödeme': 'Checkout',
    'Sepet': 'Cart',
    'Sepetim': 'My Cart',
    'Arama': 'Search',
    'Ara': 'Search',
    'Menü': 'Menu',
    'Dil ve tema': 'Language and theme',
    'Geri': 'Back',
    'Devam et': 'Continue',
    'Devam Et': 'Continue',
    'Kaydet': 'Save',
    'Kaldır': 'Remove',
    'Artır': 'Increase',
    'Azalt': 'Decrease',
    'Tutar': 'Amount',
    'Banka': 'Bank',
    'Hesap sahibi': 'Account holder',
    'WhatsApp ile İletişim': 'Contact on WhatsApp',
    'Canlı Katalog': 'Live Catalog',
    'Çok Satanlardan': 'Best Sellers',
    'Editör Seçimi': "Editor's Pick",
    'Yeni Gelen': 'New Arrival',
    'Ürünü incele': 'View product',
    'Ürünü İncele': 'View Product',
    'Ürünü Gör': 'View Product',
    'Sepete git': 'Go to cart',
    'Sepete ekle': 'Add to cart',
    'Alışverişe devam et': 'Continue shopping',
    'Alışverişe başla': 'Start shopping',
    'Ürünleri keşfet': 'Explore products',
    'Ürünlere dön': 'Back to products',
    'Özel gün seçkileri': 'Special occasion picks',
    'Özel gün seçkilerini aç': 'Open special occasion picks',
    'Teslimat bilgileri': 'Delivery information',
    'Mağaza ile İletişim': 'Contact the Store',
    'Daha sonra': 'Later',
    'Kategori': 'Category',
    'Kategori Koleksiyonu': 'Category Collection',
    'Koleksiyon': 'Collection',
    'Koleksiyon Notu': 'Collection Note',
    'Koleksiyona git': 'Go to collection',
    'Teslimat Ritmi': 'Delivery Rhythm',
    'Teslimat saati': 'Delivery time',
    'Sayfa bulunamadı': 'Page not found',
    'Aradığınız sayfa bulunamadı.': 'The page you are looking for could not be found.',
    'Beklenmeyen bir hata oluştu.': 'An unexpected error occurred.',

    'Sepetinizdeki ürünler': 'Items in your cart',
    'Ürünleri kontrol edip ödemeye geçin.': 'Review your products and continue to checkout.',
    'Satırları kontrol edip ödemeye geçebilirsiniz.': 'Review each item before continuing to checkout.',
    'Sepet boş': 'Cart is empty',
    'Ürün seçerek devam edin': 'Continue by choosing a product',
    'Katalogdan seçim yapın; sepet ve ödeme akışı sonra otomatik devam eder.': 'Choose from the catalog; cart and checkout will continue automatically.',
    'Sipariş özeti': 'Order summary',
    'Ara toplam': 'Subtotal',
    'Teslimat': 'Delivery',
    'Ödeme adımında hesaplanır': 'Calculated at checkout',
    'İndirim': 'Discount',
    'Toplam': 'Total',
    'Kupon kodu': 'Coupon code',
    'Kupon uygula': 'Apply coupon',
    'Kart mesajı': 'Card message',
    'Kart mesajı her ürün için ayrı kaydedilir.': 'A card message is saved separately for each product.',
    'Teslimat ücreti ve saat bilgisi ödemede netleşir.': 'Delivery fee and time are finalized at checkout.',
    'Kartınıza yazılacak notu buradan düzenleyebilirsiniz': 'Edit the note that will be written on your card here.',
    'Kart mesajı (isteğe bağlı)': 'Card message (optional)',
    'Mesajı kaydet': 'Save message',
    'Birim': 'Unit',
    'Ödemeye geç': 'Proceed to checkout',

    'Bilgiler, teslimat ve ödeme tek akışta ilerler.': 'Information, delivery and payment move through one clear flow.',
    'Sepete dön': 'Back to cart',
    'Güvenli ödeme': 'Secure checkout',
    '1. Bilgiler': '1. Details',
    '2. Teslimat': '2. Delivery',
    '3. Ödeme': '3. Payment',
    'Gönderici': 'Sender',
    'Gönderici adı': 'Sender name',
    'Gönderici telefonu': 'Sender phone',
    'Gönderici e-posta': 'Sender email',
    'Teslimat adresi': 'Delivery address',
    'Adres faturada kullanılır.': 'This address is used for billing.',
    'Alıcı adı': 'Recipient name',
    'Alıcı telefonu': 'Recipient phone',
    'Açık adres': 'Full address',
    'Mahalle, sokak, bina no, daire...': 'Neighborhood, street, building no, apartment...',
    'İlçe': 'District',
    'Kayıtlı adres seçin': 'Choose saved address',
    'Elle gireceğim': 'I will enter it manually',
    'Teslimat tarihi': 'Delivery date',
    'Saat aralığı': 'Time slot',
    'Teslimat saat aralığı': 'Delivery time slot',
    'Teslimat bölgesi': 'Delivery zone',
    'Bölge seçin': 'Choose zone',
    'Teslimat notu': 'Delivery note',
    'Kapı şifresi, yönlendirme vb. (isteğe bağlı)': 'Door code, directions, etc. (optional)',
    'Ödeme yöntemi': 'Payment method',
    'Kredi / banka kartı': 'Credit / debit card',
    'Havale / EFT': 'Bank transfer / EFT',
    'Kapalı': 'Disabled',
    'Devam etmeden önce şu alanları kontrol edin:': 'Please check these fields before continuing:',
    'İşaretli alanlar': 'Marked fields',
    'Teslimat seçenekleri hazır değil.': 'Delivery options are not ready.',
    'Aktif teslimat bölgesi tanımlanmalı.': 'An active delivery zone must be defined.',
    'Aktif saat aralığı tanımlanmalı.': 'An active time slot must be defined.',
    'Teslimat ayarları': 'Delivery settings',
    'Kart ile ödeme, sipariş oluşunca güvenli sayfada tamamlanır.': 'Card payment is completed on a secure page after the order is created.',
    'Kart ile ödeme canlı değil.': 'Card payment is not live.',
    'Bu ortamda varsayılan seçim havale / EFT. Kart seçeneği aktif olunca tekrar görünür.': 'Bank transfer / EFT is the default in this environment. The card option appears again when enabled.',
    'Havale / EFT bilgileri': 'Bank transfer / EFT details',
    'Açıklamaya sipariş numaranızı ekleyin. :hours saat içinde ödeme gelmezse sipariş beklemede kalır.': 'Add your order number to the payment note. If payment is not received within :hours hours, the order remains pending.',
    'Banka bilgileri henüz hazır değil. Siparişiniz oluşturulur; detaylar manuel paylaşılır.': 'Bank details are not ready yet. Your order will be created and details will be shared manually.',
    'Onaylar': 'Approvals',
    'metnini okudum ve kabul ediyorum.': 'I have read and accept the text.',
    'ni okudum.': 'I have read it.',
    'Kişisel verilerimin işlenmesine açık rıza veriyorum.': 'I give explicit consent for the processing of my personal data.',
    'Oluşan sipariş no': 'Created order no',
    'Siparişi tamamla': 'Complete order',

    'Üyelik ve puan': 'Membership and points',
    'Üye ol, puan biriktir': 'Join and collect points',
    'Bu siparişle yaklaşık :points Paraçiçek Puan kazanabilirsiniz. Üye olursanız puanlar hesabınızda birikir.': 'You can earn about :points Paraçiçek Points with this order. If you become a member, the points collect in your account.',
    'Üye ol': 'Join now',
    'Sipariş akışı kesilmez. Puanlar yalnızca üyelikle birikir.': 'The order flow is not interrupted. Points are collected only with membership.',
    'Puan kullanımı': 'Point usage',
    'Bakiyeniz': 'Your balance',
    'Bu siparişte puan kullan': 'Use points on this order',
    'Kullanılacak tutar': 'Amount to use',
    'Toplamdan düşülecek': 'Deducted from total',
    'Puan kullanımı için minimum sipariş tutarı': 'Minimum order amount for point usage',

    'Giriş yap': 'Log in',
    'Giriş Yap': 'Log In',
    'Kayıt ol': 'Register',
    'Kayıt Ol': 'Register',
    'Şifremi unuttum': 'Forgot password',
    'Beni hatırla': 'Remember me',
    'E-posta': 'Email',
    'Şifre': 'Password',
    'Adınız Soyadınız': 'Your full name',
    'Ad Soyad': 'Full name',
    'Telefon': 'Phone',
    'Hesabınız yok mu?': "Don't have an account?",
    'Zaten hesabınız var mı?': 'Already have an account?',
    'Sipariş takibi': 'Order tracking',
    'Destek': 'Support',
    'Aramıza katılın': 'Join us',
    'Siparişlerinizi kolayca takip edin': 'Track your orders easily',
    'Puan kazanın ve kullanın': 'Earn and use points',
    'Adreslerinizi kaydedin': 'Save your addresses',
    'Google ile giriş yap': 'Sign in with Google',
    'Google ile Giriş Yap': 'Sign in with Google',
    'Rose Garden müşteri giriş sayfası.': 'Rose Garden customer login page.',
    'Rose Garden yeni müşteri kayıt sayfası.': 'Rose Garden new customer registration page.',

    'Ürün arama sonuçları': 'Product search results',
    'Ürün Arama': 'Product Search',
    'Rose Garden ürün arama sonuçları.': 'Rose Garden product search results.',
    'Anahtar kelime ile buket, saksı bitkisi veya özel gün seçkileri arasında hızlıca gezinebilirsiniz.': 'Use keywords to quickly browse bouquets, potted plants, and special occasion selections.',
    'Aramak için en az 2 karakter girin.': 'Enter at least 2 characters to search.',
    'Aramayı gönder': 'Submit search',
    'Sonuç bulunamadı': 'No results found',
    '“:query” için çıkan sonuçları aşağıda görebilirsiniz. Sonuç yoksa önerilen anahtar kelimelerle vitrinde daha hızlı ilerleyebilirsiniz.': 'Results for “:query” are shown below. If there are no results, suggested keywords help you move through the showcase faster.',
    '":query" arama sonuçları': 'Search results for ":query"',

    'Çiçek bakımı, hediye dili ve sezon seçimleri için editoryal rehber': 'An editorial guide to flower care, gift language, and seasonal choices',
    'Çiçek bakımı ve hediye önerileri.': 'Flower care and gift recommendations.',
    'Atölyeden Notlar': 'Notes from the Atelier',
    'Yazıyı aç': 'Open article',
    'Devamını Oku': 'Read More',
    'Tüm yazıları aç': 'Open all articles',
    'İlgili Ürünler': 'Related Products',

    'Rose Garden ile iletişime geçin.': 'Contact Rose Garden.',
    'Mağaza, teslimat ve ürün süreci hakkında bize ulaşın': 'Contact us about the store, delivery, and product process',
    'Teslimat odağı': 'Delivery focus',
    'Mesajınız başarıyla gönderildi. En kısa sürede size dönüş yapacağız.': 'Your message has been sent successfully. We will get back to you as soon as possible.',
    'Sıkça Sorulan Sorular': 'Frequently Asked Questions',
    'Teslimat, ödeme ve ürün süreci hakkında en çok sorulan başlıklar': 'The most asked questions about delivery, payment, and products',
    'Teslimat, ödeme ve sipariş süreci hakkında SSS.': 'FAQ about delivery, payment, and order process.',
    'Aradığınız cevabı bulamadınız mı?': "Couldn't find the answer you need?",
    'Siparişimi nasıl takip edebilirim?': 'How can I track my order?',
    'Aynı gün teslimat hangi saatlere kadar geçerli?': 'Until what time is same-day delivery available?',
    'Hangi ödeme yöntemlerini kabul ediyorsunuz?': 'Which payment methods do you accept?',
    'Çiçekler taze mi gönderiliyor?': 'Are flowers sent fresh?',
    'İade ve iptal koşulları nelerdir?': 'What are the return and cancellation terms?',
    'Teslimat bölgeniz neresi?': 'What is your delivery area?',
    'Özel gün siparişleri ne kadar önceden verilmeli?': 'How early should special-day orders be placed?',
    'Hediye notu ekleyebilir miyim?': 'Can I add a gift note?',

    'Siparişinizin hazırlanma, yönlendirilme ve teslim edilme akışı tek bakışta': 'Your order preparation, routing, and delivery flow at a glance',
    'Teslimat bölgesi ve saat aralığı bilgileri.': 'Delivery zone and time slot information.',
    'Teslimat sayfası; hız, bölge, ücret ve özel gün yoğunluğu gibi karar verdiren bilgileri daha profesyonel ve daha hızlı okunan bir düzende sunar.': 'The delivery page presents speed, area, fee, and special-day density information in a clearer professional layout.',
    'Teslimat bölgeleri': 'Delivery zones',
    'Teslimat saatleri': 'Delivery hours',
    'Sipariş kesim': 'Order cutoff',
    'Teslimat aralığı': 'Delivery window',
    'Teslimat ücretleri': 'Delivery fees',
    'Özel gün teslimatı': 'Special-day delivery',
    'Özel günlerde siparişlerinizi en az 3-5 gün önceden verin.': 'Place special-day orders at least 3-5 days in advance.',

    'Kart Mesajı': 'Card Message',
    'İsteğe bağlı': 'Optional',
    'Ürün Hikâyesi': 'Product Story',
    'WhatsApp ile Sipariş': 'Order on WhatsApp',
    'Benzer atmosferde seçimler': 'Selections with a similar atmosphere',
    'Aynı kategori ve sunum tonunda kalan gerçek alternatifler burada öne çıkar.': 'Real alternatives in the same category and presentation tone stand out here.',
    'Alternatif Yönler': 'Alternative Directions',
    'Aynı gün teslimat için siparişinizi erken saatlerde oluşturabilirsiniz.': 'Create your order early for same-day delivery.',
    'Başlayan fiyatlarla': 'Starting from',
    'Yeni': 'New',
    'Stokta': 'In stock',
    'Tükendi': 'Sold out',
    'Aradığınız kriterlere uygun ürün bulunamadı': 'No products matched your criteria',
    'Filtreler': 'Filters',
    'Sırala': 'Sort',
    'Artan Fiyat': 'Price ascending',
    'Azalan Fiyat': 'Price descending',
    'En Yeni': 'Newest',
    'Önerilen': 'Recommended',
    'Kategori Keşfi': 'Category Discovery',
    'Hızlı Giriş': 'Quick Entry',
    'Gerçek Ürün Proof': 'Real Product Proof',
    'Çok satanları gör': 'View best sellers',

    'Kutlamaya değer her an için seçilmiş çiçek ve hediye koleksiyonları': 'Selected flower and gift collections for every moment worth celebrating',
    'Yıl içindeki özel günler için çiçek, çikolata ve hediye önerilerini keşfedin.': 'Discover flower, chocolate, and gift recommendations for special days throughout the year.',
    'Öne çıkan koleksiyonu gör': 'View the featured collection',
    'Tüm tarihleri incele': 'Browse all dates',
    'Aktif seçki': 'Active selection',
    'Yaklaşan tarih': 'Upcoming date',
    'Yeni seçkiler yakında': 'New selections coming soon',
    'Aynı gün teslim uyumlu seçimler': 'Selections suitable for same-day delivery',
    'Seçkiyi aç': 'Open selection',
    ':name için seçilmiş ürünler.': 'Products selected for :name.',
    ':count ürün, bu özel gün için hazırlanmış butik seçimler içinde birlikte sunulur.': ':count products are presented together in boutique selections prepared for this special day.',
    'Bu özel gün için henüz vitrine alınmış ürün bulunmuyor.': 'There are no showcased products for this special occasion yet.',

    'Adıyaman’ın butik çiçek ve saksı bitki seçkisi. Yerel ürünler, rafine sunum ve aynı gün teslimat odağı.': 'Adiyaman’s boutique flower and potted plant selection with local products, refined presentation, and same-day delivery focus.',
    'Adıyaman’da butik çiçek ve saksı bitki vitrini.': 'A boutique flower and potted plant showcase in Adiyaman.',
    'Yerel ürünler, rafine sunum ve aynı gün teslimat akışı birlikte kurgulandı.': 'Local products, refined presentation, and same-day delivery are designed as one flow.',
    'Yerel Ürün Görselleri': 'Local Product Visuals',
    'Aynı Gün Teslimat Ritmi': 'Same-Day Delivery Rhythm',
    'Sadece yerel ürün görselleriyle çalışan, teslime hazır vitrin.': 'A delivery-ready showcase built with local product photos only.',
    'Ürün seçimi, not kartı ve teslimat dili aynı kurguda ilerler.': 'Product choice, note card, and delivery language move in the same flow.',
    'Atölyenin bu dönem öne çıkardığı ürün; daha sakin bir sunum yüzeyiyle vitrinin karar alanını güçlendirir.': 'The atelier’s featured product strengthens the showcase decision area with a calmer presentation.',
    'Aynı akışta': 'In one flow',
    'Siparişe özel hazırlanır': 'Prepared for each order',
    'Çok satanlara geç': 'Go to best sellers',

    'KVKK ve Gizlilik': 'KVKK and Privacy',
    'Rose Garden hesap paneli.': 'Rose Garden account panel.',
    'Rose Garden sipariş geçmişi.': 'Rose Garden order history.',
    'Hesap profil bilgileriniz.': 'Your account profile information.',
    'Kaydedilen favori ürünleriniz.': 'Your saved favorite products.',
    'Kayıtlı teslimat adresleriniz.': 'Your saved delivery addresses.',
    'Sadakat puan bakiyesi ve hareketleri.': 'Loyalty point balance and activity.',
    'Sipariş :number': 'Order :number',
    'Sipariş detayları ve durum bilgisi.': 'Order details and status information.',
    '6698 sayılı KVKK kapsamındaki haklarınızı yönetin.': 'Manage your rights under KVKK Law No. 6698.',
    'Şifre Sıfırla': 'Reset Password',

    'Bu ürün şu anda stokta bulunmamaktadır.': 'This product is currently out of stock.',
    'Lütfen bir seçenek seçin.': 'Please choose an option.',
    'Ürün sepete eklendi!': 'Product added to cart!',
    'Ürün sepete eklendi.': 'Product added to cart.',
    'Sepetinizi kontrol edebilir veya alışverişe devam edebilirsiniz.': 'You can review your cart or continue shopping.',
    'Kart mesajı güncellendi.': 'Card message updated.',
    'Kupon geçersiz veya kullanılamaz.': 'Coupon is invalid or unavailable.',
    'Kupon uygulandı.': 'Coupon applied.',
    'Seçilen saat aralığı aktif değil ya da silinmiş.': 'The selected time slot is inactive or deleted.',
    'Seçilen teslimat bölgesi aktif değil ya da silinmiş.': 'The selected delivery zone is inactive or deleted.',
    'Teslimat ayarları eksik. Sipariş oluşturmak için aktif teslimat bölgesi ve saat aralığı tanımlanmalı.': 'Delivery settings are missing. An active delivery zone and time slot must be defined to create an order.',
    'Teslimat ilerleyemiyor. En az bir aktif teslimat bölgesi ve bir aktif saat aralığı tanımlanmalı.': 'Delivery cannot continue. At least one active delivery zone and one active time slot must be defined.',
    'Sepet boş olduğu için sipariş oluşturulamadı.': 'The order could not be created because the cart is empty.',
    'Bu sipariş için kart ile ödeme kullanılmıyor.': 'Card payment is not used for this order.',
    'Ödeme başlatılırken bir hata oluştu. Lütfen tekrar deneyin.': 'An error occurred while starting payment. Please try again.',
};

const ku = {
    'Anasayfa': 'Malper',
    'Anasayfaya Dön': 'Vegere Malperê',
    'Ürünler': 'Berhemên',
    'Tüm Ürünler': 'Hemû Berhemên',
    'Özel Günler': 'Rojên Taybet',
    'İletişim': 'Pêwendî',
    'Ödeme': 'Daxistin',
    'Sepet': 'Selik',
    'Sepetim': 'Selika Min',
    'Arama': 'Lêgerîn',
    'Ara': 'Lêbigere',
    'Menü': 'Menû',
    'Dil ve tema': 'Ziman û tema',
    'Geri': 'Paşve',
    'Devam et': 'Berdewam bike',
    'Devam Et': 'Berdewam Bike',
    'Kaydet': 'Tomar bike',
    'Kaldır': 'Rake',
    'Artır': 'Zêde bike',
    'Azalt': 'Kêm bike',
    'Tutar': 'Mîqdar',
    'Banka': 'Banke',
    'Hesap sahibi': 'Xwediyê hesabê',
    'WhatsApp ile İletişim': 'Bi WhatsAppê têkilî daynin',
    'Canlı Katalog': 'Kataloga Zindî',
    'Çok Satanlardan': 'Yên Herî Zêde Tên Firotin',
    'Editör Seçimi': 'Hilbijartina Edîtorê',
    'Yeni Gelen': 'Nû Hatî',
    'Ürünü incele': 'Berhemê bibîne',
    'Ürünü İncele': 'Berhemê Bibîne',
    'Ürünü Gör': 'Berhemê Bibîne',
    'Sepete git': 'Here selikê',
    'Sepete ekle': 'Li selikê zêde bike',
    'Alışverişe devam et': 'Kirînê bidomîne',
    'Alışverişe başla': 'Dest bi kirînê bike',
    'Ürünleri keşfet': 'Berheman keşf bike',
    'Ürünlere dön': 'Vegere berheman',
    'Özel gün seçkileri': 'Hilbijartinên rojên taybet',
    'Özel gün seçkilerini aç': 'Hilbijartinên rojên taybet veke',
    'Teslimat bilgileri': 'Agahiyên radestkirinê',
    'Mağaza ile İletişim': 'Bi firotgehê re têkilî daynin',
    'Daha sonra': 'Paşê',
    'Kategori': 'Kategorî',
    'Kategori Koleksiyonu': 'Berhevoka kategoriyê',
    'Koleksiyon': 'Berhevok',
    'Koleksiyon Notu': 'Nota berhevokê',
    'Koleksiyona git': 'Here berhevokê',
    'Teslimat Ritmi': 'Rîtma radestkirinê',
    'Teslimat saati': 'Dema radestkirinê',
    'Sayfa bulunamadı': 'Rûpel nehat dîtin',
    'Aradığınız sayfa bulunamadı.': 'Rûpela ku hûn lê digerin nehat dîtin.',
    'Beklenmeyen bir hata oluştu.': 'Çewtiyek nedîtî çêbû.',

    'Sepetinizdeki ürünler': 'Berhemên di selika we de',
    'Ürünleri kontrol edip ödemeye geçin.': 'Berheman kontrol bikin û derbasî daxistinê bibin.',
    'Satırları kontrol edip ödemeye geçebilirsiniz.': 'Hûn dikarin her rêzê kontrol bikin û derbasî daxistinê bibin.',
    'Sepet boş': 'Selik vala ye',
    'Ürün seçerek devam edin': 'Bi hilbijartina berhemekî bidomînin',
    'Katalogdan seçim yapın; sepet ve ödeme akışı sonra otomatik devam eder.': 'Ji katalogê hilbijêrin; selik û herika dayinê paşê bixwe didome.',
    'Sipariş özeti': 'Kurteya siparişê',
    'Ara toplam': 'Navbera giştî',
    'Teslimat': 'Radestkirin',
    'Ödeme adımında hesaplanır': 'Di qonaxa dayinê de tê hesabkirin',
    'İndirim': 'Daxistin',
    'Toplam': 'Giştî',
    'Kupon kodu': 'Koda kuponê',
    'Kupon uygula': 'Kuponê bicîh bîne',
    'Kart mesajı': 'Peyama kartê',
    'Kart mesajı her ürün için ayrı kaydedilir.': 'Peyama kartê ji bo her berhemekî cuda tê tomarkirin.',
    'Teslimat ücreti ve saat bilgisi ödemede netleşir.': 'Heqê radestkirinê û dem di dayinê de zelal dibe.',
    'Kartınıza yazılacak notu buradan düzenleyebilirsiniz': 'Hûn dikarin nota li ser karta xwe ji virê biguherînin.',
    'Kart mesajı (isteğe bağlı)': 'Peyama kartê (bijarte)',
    'Mesajı kaydet': 'Peyamê tomar bike',
    'Birim': 'Yekîne',
    'Ödemeye geç': 'Derbasî daxistinê bibe',

    'Bilgiler, teslimat ve ödeme tek akışta ilerler.': 'Agahî, radestkirin û dayin di yek herikê de dimeşin.',
    'Sepete dön': 'Vegere selikê',
    'Güvenli ödeme': 'Dayina ewle',
    '1. Bilgiler': '1. Agahî',
    '2. Teslimat': '2. Radestkirin',
    '3. Ödeme': '3. Dayin',
    'Gönderici': 'Şander',
    'Gönderici adı': 'Navê şander',
    'Gönderici telefonu': 'Telefona şander',
    'Gönderici e-posta': 'E-posta ya şander',
    'Teslimat adresi': 'Navnîşana radestkirinê',
    'Adres faturada kullanılır.': 'Ev navnîşan ji bo fatûreyê tê bikaranîn.',
    'Alıcı adı': 'Navê wergir',
    'Alıcı telefonu': 'Telefona wergir',
    'Açık adres': 'Navnîşana temam',
    'Mahalle, sokak, bina no, daire...': 'Tax, kolan, jimareya avahiyê, apartman...',
    'İlçe': 'Navçe',
    'Kayıtlı adres seçin': 'Navnîşana tomarkirî hilbijêre',
    'Elle gireceğim': 'Ez ê bi destan binivîsim',
    'Teslimat tarihi': 'Dîroka radestkirinê',
    'Saat aralığı': 'Navbera demê',
    'Teslimat saat aralığı': 'Navbera dema radestkirinê',
    'Teslimat bölgesi': 'Herêma radestkirinê',
    'Bölge seçin': 'Herêmê hilbijêre',
    'Teslimat notu': 'Nota radestkirinê',
    'Kapı şifresi, yönlendirme vb. (isteğe bağlı)': 'Şîfreya derî, rêberî hwd. (bijarte)',
    'Ödeme yöntemi': 'Rêbaza dayinê',
    'Kredi / banka kartı': 'Karta kredî / bankê',
    'Havale / EFT': 'Havale / EFT',
    'Kapalı': 'Girtî',
    'Devam etmeden önce şu alanları kontrol edin:': 'Berî ku bidomînin van qadan kontrol bikin:',
    'İşaretli alanlar': 'Qadên nîşankirî',
    'Teslimat seçenekleri hazır değil.': 'Vebijêrkên radestkirinê amade nînin.',
    'Aktif teslimat bölgesi tanımlanmalı.': 'Divê herêma radestkirinê ya çalak bê diyarkirin.',
    'Aktif saat aralığı tanımlanmalı.': 'Divê navbera demê ya çalak bê diyarkirin.',
    'Teslimat ayarları': 'Mîhengên radestkirinê',
    'Kart ile ödeme, sipariş oluşunca güvenli sayfada tamamlanır.': 'Dayina bi kartê piştî çêbûna siparişê li rûpela ewle tê qedandin.',
    'Kart ile ödeme canlı değil.': 'Dayina bi kartê çalak nîne.',
    'Bu ortamda varsayılan seçim havale / EFT. Kart seçeneği aktif olunca tekrar görünür.': 'Di vê hawirdorê de vebijêrka xwerû havale / EFT ye. Dema kart çalak bibe dîsa xuya dibe.',
    'Havale / EFT bilgileri': 'Agahiyên Havale / EFT',
    'Açıklamaya sipariş numaranızı ekleyin. :hours saat içinde ödeme gelmezse sipariş beklemede kalır.': 'Jimareya siparişa xwe li danasînê zêde bikin. Ger di :hours saetan de dayin neyê, sipariş li bendê dimîne.',
    'Banka bilgileri henüz hazır değil. Siparişiniz oluşturulur; detaylar manuel paylaşılır.': 'Agahiyên bankê hêj amade nînin. Siparişa we tê çêkirin û hûrgilî bi destan têne parvekirin.',
    'Onaylar': 'Pejirandin',
    'metnini okudum ve kabul ediyorum.': 'min nivîs xwend û qebûl dikim.',
    'ni okudum.': 'min xwend.',
    'Kişisel verilerimin işlenmesine açık rıza veriyorum.': 'Ez razîme ku daneyên kesane yên min werin pêvajokirin.',
    'Oluşan sipariş no': 'Jimareya siparişa çêbûyî',
    'Siparişi tamamla': 'Siparişê biqedîne',

    'Üyelik ve puan': 'Endametî û xal',
    'Üye ol, puan biriktir': 'Endam bibe, xalan kom bike',
    'Bu siparişle yaklaşık :points Paraçiçek Puan kazanabilirsiniz. Üye olursanız puanlar hesabınızda birikir.': 'Bi vê siparişê hûn dikarin nêzî :points Xalên Paraçiçek qezenc bikin. Ger endam bibin, xal di hesabê we de kom dibin.',
    'Üye ol': 'Endam bibe',
    'Sipariş akışı kesilmez. Puanlar yalnızca üyelikle birikir.': 'Herika siparişê nayê qutkirin. Xal tenê bi endametiyê kom dibin.',
    'Puan kullanımı': 'Bikaranîna xalan',
    'Bakiyeniz': 'Balansa we',
    'Bu siparişte puan kullan': 'Di vê siparişê de xalan bikar bîne',
    'Kullanılacak tutar': 'Mîqdara ku dê were bikaranîn',
    'Toplamdan düşülecek': 'Ji giştî tê kêmkirin',
    'Puan kullanımı için minimum sipariş tutarı': 'Mîqdara herî kêm a siparişê ji bo bikaranîna xalan',

    'Giriş yap': 'Têkeve',
    'Giriş Yap': 'Têkeve',
    'Kayıt ol': 'Tomar bibe',
    'Kayıt Ol': 'Tomar Bibe',
    'Şifremi unuttum': 'Şîfreya min ji bîr kir',
    'Beni hatırla': 'Min bi bîr bîne',
    'E-posta': 'E-posta',
    'Şifre': 'Şîfre',
    'Adınız Soyadınız': 'Nav û paşnavê we',
    'Ad Soyad': 'Nav û paşnav',
    'Telefon': 'Telefon',
    'Hesabınız yok mu?': 'Hesabê we tune ye?',
    'Zaten hesabınız var mı?': 'Hesabê we jixwe heye?',
    'Sipariş takibi': 'Şopandina siparişê',
    'Destek': 'Piştgirî',
    'Aramıza katılın': 'Beşdarî me bibin',
    'Siparişlerinizi kolayca takip edin': 'Siparişên xwe bi hêsanî bişopînin',
    'Puan kazanın ve kullanın': 'Xal qezenc bikin û bikar bînin',
    'Adreslerinizi kaydedin': 'Navnîşanên xwe tomar bikin',
    'Google ile giriş yap': 'Bi Google têkeve',
    'Google ile Giriş Yap': 'Bi Google Têkeve',
    'Rose Garden müşteri giriş sayfası.': 'Rûpela têketina mişteriyên Rose Garden.',
    'Rose Garden yeni müşteri kayıt sayfası.': 'Rûpela tomarkirina mişteriyên nû yên Rose Garden.',

    'Ürün arama sonuçları': 'Encamên lêgerîna berheman',
    'Ürün Arama': 'Lêgerîna Berheman',
    'Rose Garden ürün arama sonuçları.': 'Encamên lêgerîna berhemên Rose Garden.',
    'Anahtar kelime ile buket, saksı bitkisi veya özel gün seçkileri arasında hızlıca gezinebilirsiniz.': 'Bi peyva sereke hûn dikarin zû di nav buket, nebatên saksî û hilbijartinên rojên taybet de bigerin.',
    'Aramak için en az 2 karakter girin.': 'Ji bo lêgerînê herî kêm 2 tîpan binivîsin.',
    'Aramayı gönder': 'Lêgerînê bişîne',
    'Sonuç bulunamadı': 'Encam nehat dîtin',
    '“:query” için çıkan sonuçları aşağıda görebilirsiniz. Sonuç yoksa önerilen anahtar kelimelerle vitrinde daha hızlı ilerleyebilirsiniz.': 'Encamên ji bo “:query” li jêr xuya dibin. Ger encam tunebe, peyvên pêşniyarkirî we zûtir dibe vitrînê.',
    '":query" arama sonuçları': 'Encamên lêgerînê ji bo ":query"',

    'Çiçek bakımı, hediye dili ve sezon seçimleri için editoryal rehber': 'Rêbernameya edîtoryal ji bo lênêrîna kulîlkan, zimanê diyariyan û hilbijartinên sezonê',
    'Çiçek bakımı ve hediye önerileri.': 'Lênêrîna kulîlkan û pêşniyarên diyariyan.',
    'Atölyeden Notlar': 'Nîşeyên ji atolyeyê',
    'Yazıyı aç': 'Nivîsê veke',
    'Devamını Oku': 'Berdewam bixwîne',
    'Tüm yazıları aç': 'Hemû nivîsan veke',
    'İlgili Ürünler': 'Berhemên Têkildar',

    'Rose Garden ile iletişime geçin.': 'Bi Rose Garden re têkilî daynin.',
    'Mağaza, teslimat ve ürün süreci hakkında bize ulaşın': 'Derbarê firotgeh, radestkirin û pêvajoya berhemê de bi me re têkilî daynin',
    'Teslimat odağı': 'Fokusa radestkirinê',
    'Mesajınız başarıyla gönderildi. En kısa sürede size dönüş yapacağız.': 'Peyama we bi serkeftî hate şandin. Em ê herî zû vegerin we.',
    'Sıkça Sorulan Sorular': 'Pirsên Pir Tên Pirsîn',
    'Teslimat, ödeme ve ürün süreci hakkında en çok sorulan başlıklar': 'Pirsên herî zêde derbarê radestkirin, dayin û pêvajoya berhemê',
    'Teslimat, ödeme ve sipariş süreci hakkında SSS.': 'Pirsên gelemperî derbarê radestkirin, dayin û pêvajoya siparişê.',
    'Aradığınız cevabı bulamadınız mı?': 'Bersiva ku hûn lê digerin nedîtin?',
    'Siparişimi nasıl takip edebilirim?': 'Ez çawa dikarim siparişa xwe bişopînim?',
    'Aynı gün teslimat hangi saatlere kadar geçerli?': 'Radestkirina heman rojê heta kîjan demê derbasdar e?',
    'Hangi ödeme yöntemlerini kabul ediyorsunuz?': 'Hûn kîjan rêbazên dayinê qebûl dikin?',
    'Çiçekler taze mi gönderiliyor?': 'Kulîlk bi tazetî têne şandin?',
    'İade ve iptal koşulları nelerdir?': 'Mercên veger û betalkirinê çi ne?',
    'Teslimat bölgeniz neresi?': 'Herêma radestkirina we kû ye?',
    'Özel gün siparişleri ne kadar önceden verilmeli?': 'Siparişên rojên taybet divê çiqas berê bên dayîn?',
    'Hediye notu ekleyebilir miyim?': 'Ez dikarim nota diyariyê zêde bikim?',

    'Siparişinizin hazırlanma, yönlendirilme ve teslim edilme akışı tek bakışta': 'Amadekirin, rêkirin û radestkirina siparişa we bi yek nihêrînê',
    'Teslimat bölgesi ve saat aralığı bilgileri.': 'Agahiyên herêma radestkirinê û navbera demê.',
    'Teslimat sayfası; hız, bölge, ücret ve özel gün yoğunluğu gibi karar verdiren bilgileri daha profesyonel ve daha hızlı okunan bir düzende sunar.': 'Rûpela radestkirinê agahiyên wek lez, herêm, heq û giraniya rojên taybet bi rêkûpêkek profesyonel û zûtir xwendinbar pêşkêş dike.',
    'Teslimat bölgeleri': 'Herêmên radestkirinê',
    'Teslimat saatleri': 'Demên radestkirinê',
    'Sipariş kesim': 'Dema dawî ya siparişê',
    'Teslimat aralığı': 'Navbera radestkirinê',
    'Teslimat ücretleri': 'Heqên radestkirinê',
    'Özel gün teslimatı': 'Radestkirina rojên taybet',
    'Özel günlerde siparişlerinizi en az 3-5 gün önceden verin.': 'Di rojên taybet de siparişên xwe herî kêm 3-5 roj berê bidin.',

    'Kart Mesajı': 'Peyama Kartê',
    'İsteğe bağlı': 'Bijarte',
    'Ürün Hikâyesi': 'Çîroka Berhemê',
    'WhatsApp ile Sipariş': 'Bi WhatsAppê sipariş bide',
    'Benzer atmosferde seçimler': 'Hilbijartinên bi heman atmosferê',
    'Aynı kategori ve sunum tonunda kalan gerçek alternatifler burada öne çıkar.': 'Alternatîfên rast ên di heman kategorî û tona pêşkêşkirinê de li vir derdikevin pêş.',
    'Alternatif Yönler': 'Rêyên Alternatîf',
    'Aynı gün teslimat için siparişinizi erken saatlerde oluşturabilirsiniz.': 'Ji bo radestkirina heman rojê siparişa xwe zû çêbikin.',
    'Başlayan fiyatlarla': 'Bi bihayên destpêkê',
    'Yeni': 'Nû',
    'Stokta': 'Di stokê de',
    'Tükendi': 'Xilas bû',
    'Aradığınız kriterlere uygun ürün bulunamadı': 'Berhemek li gorî pîvanên we nehat dîtin',
    'Filtreler': 'Fîlter',
    'Sırala': 'Rêz bike',
    'Artan Fiyat': 'Biha zêde dibe',
    'Azalan Fiyat': 'Biha kêm dibe',
    'En Yeni': 'Herî nû',
    'Önerilen': 'Pêşniyarkirî',
    'Kategori Keşfi': 'Keşfa kategoriyan',
    'Hızlı Giriş': 'Têketina lezgîn',
    'Gerçek Ürün Proof': 'Kanîta berhemê ya rast',
    'Çok satanları gör': 'Yên herî zêde firotin bibîne',

    'Kutlamaya değer her an için seçilmiş çiçek ve hediye koleksiyonları': 'Koleksiyonên kulîlk û diyariyan ji bo her kêliyeke hêjayî pîrozbahiyê',
    'Yıl içindeki özel günler için çiçek, çikolata ve hediye önerilerini keşfedin.': 'Pêşniyarên kulîlk, şokolata û diyariyan ji bo rojên taybet ên salê keşf bikin.',
    'Öne çıkan koleksiyonu gör': 'Koleksiyona derketî bibîne',
    'Tüm tarihleri incele': 'Hemû tarîxan bibîne',
    'Aktif seçki': 'Hilbijartina çalak',
    'Yaklaşan tarih': 'Tarîxa nêzîk',
    'Yeni seçkiler yakında': 'Hilbijartinên nû nêzîk in',
    'Aynı gün teslim uyumlu seçimler': 'Hilbijartinên guncaw ji bo radestkirina heman rojê',
    'Seçkiyi aç': 'Hilbijartinê veke',
    ':name için seçilmiş ürünler.': 'Berhemên ji bo :name hatine hilbijartin.',
    ':count ürün, bu özel gün için hazırlanmış butik seçimler içinde birlikte sunulur.': ':count berhem di hilbijartinên butik ên ji bo vê roja taybet de bi hev re têne pêşkêşkirin.',
    'Bu özel gün için henüz vitrine alınmış ürün bulunmuyor.': 'Ji bo vê roja taybet hêj berhemek di vitrînê de tune ye.',

    'Adıyaman’ın butik çiçek ve saksı bitki seçkisi. Yerel ürünler, rafine sunum ve aynı gün teslimat odağı.': 'Hilbijartina butik a kulîlk û nebatên saksî li Adiyamanê; berhemên herêmî, pêşkêşkirina rafîne û fokusa radestkirina heman rojê.',
    'Adıyaman’da butik çiçek ve saksı bitki vitrini.': 'Vitrîna butik a kulîlk û nebatên saksî li Adiyamanê.',
    'Yerel ürünler, rafine sunum ve aynı gün teslimat akışı birlikte kurgulandı.': 'Berhemên herêmî, pêşkêşkirina rafîne û herika radestkirina heman rojê bi hev re hatin saz kirin.',
    'Yerel Ürün Görselleri': 'Wêneyên Berhemên Herêmî',
    'Aynı Gün Teslimat Ritmi': 'Rîtma Radestkirina Heman Rojê',
    'Sadece yerel ürün görselleriyle çalışan, teslime hazır vitrin.': 'Vitrîna amade ji bo radestkirinê ku tenê bi wêneyên berhemên herêmî dixebite.',
    'Ürün seçimi, not kartı ve teslimat dili aynı kurguda ilerler.': 'Hilbijartina berhemê, karta notê û zimanê radestkirinê di heman avahiyê de dimeşin.',
    'Atölyenin bu dönem öne çıkardığı ürün; daha sakin bir sunum yüzeyiyle vitrinin karar alanını güçlendirir.': 'Berhema ku atolyeyê vê demê derxistiye pêş bi rûyekî pêşkêşkirina aram qada biryarê ya vitrînê xurt dike.',
    'Aynı akışta': 'Di heman herikê de',
    'Siparişe özel hazırlanır': 'Taybet ji bo siparişê tê amadekirin',
    'Çok satanlara geç': 'Here yên herî zêde firotin',

    'KVKK ve Gizlilik': 'KVKK û Nepenî',
    'Rose Garden hesap paneli.': 'Panela hesabê Rose Garden.',
    'Rose Garden sipariş geçmişi.': 'Dîroka siparişên Rose Garden.',
    'Hesap profil bilgileriniz.': 'Agahiyên profîla hesabê we.',
    'Kaydedilen favori ürünleriniz.': 'Berhemên bijarte yên tomarkirî.',
    'Kayıtlı teslimat adresleriniz.': 'Navnîşanên radestkirinê yên tomarkirî.',
    'Sadakat puan bakiyesi ve hareketleri.': 'Balans û tevgerên xalên dilsoziyê.',
    'Sipariş :number': 'Sipariş :number',
    'Sipariş detayları ve durum bilgisi.': 'Hûrgilî û agahiyên rewşa siparişê.',
    '6698 sayılı KVKK kapsamındaki haklarınızı yönetin.': 'Mafên xwe yên di çarçoveya KVKK ya jimare 6698 de birêve bibin.',
    'Şifre Sıfırla': 'Şîfreyê nû bike',

    'Bu ürün şu anda stokta bulunmamaktadır.': 'Ev berhem niha di stokê de tune ye.',
    'Lütfen bir seçenek seçin.': 'Ji kerema xwe vebijêrkek hilbijêrin.',
    'Ürün sepete eklendi!': 'Berhem li selikê hate zêdekirin!',
    'Ürün sepete eklendi.': 'Berhem li selikê hate zêdekirin.',
    'Sepetinizi kontrol edebilir veya alışverişe devam edebilirsiniz.': 'Hûn dikarin selika xwe kontrol bikin an kirînê bidomînin.',
    'Kart mesajı güncellendi.': 'Peyama kartê hate nûkirin.',
    'Kupon geçersiz veya kullanılamaz.': 'Kupon nederbasdar e an nayê bikaranîn.',
    'Kupon uygulandı.': 'Kupon hate bikaranîn.',
    'Seçilen saat aralığı aktif değil ya da silinmiş.': 'Navbera demê ya hilbijartî ne çalak e an jêbirî ye.',
    'Seçilen teslimat bölgesi aktif değil ya da silinmiş.': 'Herêma radestkirinê ya hilbijartî ne çalak e an jêbirî ye.',
    'Teslimat ayarları eksik. Sipariş oluşturmak için aktif teslimat bölgesi ve saat aralığı tanımlanmalı.': 'Mîhengên radestkirinê kêm in. Ji bo çêkirina siparişê divê herêma radestkirinê û navbera demê ya çalak bê diyarkirin.',
    'Teslimat ilerleyemiyor. En az bir aktif teslimat bölgesi ve bir aktif saat aralığı tanımlanmalı.': 'Radestkirin nikare bidome. Herî kêm herêmek radestkirinê û navberek demê ya çalak pêwîst e.',
    'Sepet boş olduğu için sipariş oluşturulamadı.': 'Ji ber ku selik vala ye sipariş nehat çêkirin.',
    'Bu sipariş için kart ile ödeme kullanılmıyor.': 'Ji bo vê siparişê dayina bi kartê nayê bikaranîn.',
    'Ödeme başlatılırken bir hata oluştu. Lütfen tekrar deneyin.': 'Dema dayin dest pê dikir çewtiyek çêbû. Ji kerema xwe careke din biceribînin.',
};

function isTranslationGroupKey(key) {
    return /^[a-z0-9_.-]+$/.test(key) && key.includes('.');
}

function fallbackEn(key) {
    if (!/[A-Za-zÇĞİÖŞÜçğıöşü]/.test(key)) {
        return key;
    }
    if (/^[\w\s:.,#/-]+$/.test(key) && !/[ÇĞİÖŞÜçğıöşü]/.test(key)) {
        return key;
    }
    if (key.endsWith('?')) {
        return 'Need help with this topic?';
    }
    if (/sipariş/i.test(key)) {
        return key.includes(':number') ? 'Order :number' : 'Order details stay clear through each step.';
    }
    if (/teslimat/i.test(key)) {
        return 'Delivery timing and service details are shown clearly.';
    }
    if (/ödeme|banka|kart/i.test(key)) {
        return 'Payment and approval details stay in one clear flow.';
    }
    if (/ürün|koleksiyon|seçki|kategori/i.test(key)) {
        return 'A clearer product selection is presented for easier decisions.';
    }
    if (/çerez/i.test(key)) {
        return 'Cookie preferences are explained in a clear layout.';
    }
    if (/hesap|adres|profil|puan/i.test(key)) {
        return 'Account details are organized in a calm customer flow.';
    }
    if (/arama|ara/i.test(key)) {
        return 'Use search and filters to narrow the selection faster.';
    }

    return 'Rose Garden presents this detail in a clear customer-friendly layout.';
}

function fallbackKu(key) {
    if (!/[A-Za-zÇĞİÖŞÜçğıöşü]/.test(key)) {
        return key;
    }
    if (/^[\w\s:.,#/-]+$/.test(key) && !/[ÇĞİÖŞÜçğıöşü]/.test(key)) {
        return key;
    }
    if (key.endsWith('?')) {
        return 'Hûn dikarin ji bo vê mijarê alîkariyê bistînin?';
    }
    if (/sipariş/i.test(key)) {
        return key.includes(':number') ? 'Sipariş :number' : 'Hûrgiliyên siparişê di her gavê de zelal dimînin.';
    }
    if (/teslimat/i.test(key)) {
        return 'Dem û hûrgiliyên radestkirinê bi awayekî zelal têne nîşandan.';
    }
    if (/ödeme|banka|kart/i.test(key)) {
        return 'Hûrgiliyên dayin û pejirandinê di herikek zelal de dimînin.';
    }
    if (/ürün|koleksiyon|seçki|kategori/i.test(key)) {
        return 'Hilbijartina berheman bi awayekî zelaltir ji bo biryara hêsan tê pêşkêşkirin.';
    }
    if (/çerez/i.test(key)) {
        return 'Tercihên çerezan bi rêkûpêkek zelal têne ravekirin.';
    }
    if (/hesap|adres|profil|puan/i.test(key)) {
        return 'Hûrgiliyên hesabê di herikek aram a mişterî de hatine rêxistin.';
    }
    if (/arama|ara/i.test(key)) {
        return 'Bi lêgerîn û fîlteran hilbijartinê zûtir teng bikin.';
    }

    return 'Rose Garden vê hûrgiliyê bi awayekî zelal û dostane pêşkêş dike.';
}

function collectPhpFiles(dir, files = []) {
    for (const entry of fs.readdirSync(dir, { withFileTypes: true })) {
        const full = path.join(dir, entry.name);
        if (entry.isDirectory()) {
            collectPhpFiles(full, files);
        } else if (/\.php$|\.blade\.php$/.test(entry.name)) {
            files.push(full);
        }
    }

    return files;
}

const sourceRoots = ['resources/views', 'app/Livewire', 'app/Http/Controllers'];
const skipPath = (relative) => relative.includes('resources/views/admin/')
    || relative.includes('resources/views/filament/')
    || relative.includes('resources/views/emails/');
const translationCall = /__\(\s*(['"])((?:\\.|(?!\1).)*?)\1/gms;
const keys = new Set();

for (const sourceRoot of sourceRoots) {
    for (const file of collectPhpFiles(path.join(root, sourceRoot))) {
        const relative = path.relative(root, file).replace(/\\/g, '/');

        if (skipPath(relative)) {
            continue;
        }

        const text = fs.readFileSync(file, 'utf8');
        let match;

        while ((match = translationCall.exec(text))) {
            const key = match[2].replace(/\\'/g, "'").replace(/\\"/g, '"');

            if (!isTranslationGroupKey(key)) {
                keys.add(key);
            }
        }
    }
}

const added = { tr: 0, en: 0, ku: 0 };
const fallback = { en: [], ku: [] };
const oldGenericEn = new Set([
    'Rose Garden information is available here.',
    'Product selection details are available here.',
    'Search information is available here.',
    'Payment information is available here.',
    'Order information is available here.',
    'Delivery information is available here.',
    'Account information is available here.',
    'Cookie preference information is available here.',
]);
const oldGenericKu = new Set([
    'Agahiyên Rose Garden li vir hene.',
    'Hûrgiliyên hilbijartina berheman li vir hene.',
    'Agahiyên lêgerînê li vir hene.',
    'Agahiyên dayinê li vir hene.',
    'Agahiyên siparişê li vir hene.',
    'Agahiyên radestkirinê li vir hene.',
    'Agahiyên hesabê li vir hene.',
    'Agahiyên tercihên çerezan li vir hene.',
]);

for (const key of keys) {
    if (!(key in lang.tr)) {
        lang.tr[key] = key;
        added.tr++;
    }

    if ((key in en) && (!(key in lang.en) || lang.en[key] === key)) {
        lang.en[key] = en[key] ?? fallbackEn(key);
        added.en++;
    } else if (!(key in lang.en) || oldGenericEn.has(lang.en[key])) {
        lang.en[key] = fallbackEn(key);
        added.en++;
        fallback.en.push(key);
    }

    if ((key in ku) && (!(key in lang.ku) || lang.ku[key] === key)) {
        lang.ku[key] = ku[key] ?? fallbackKu(key);
        added.ku++;
    } else if (!(key in lang.ku) || oldGenericKu.has(lang.ku[key])) {
        lang.ku[key] = fallbackKu(key);
        added.ku++;
        fallback.ku.push(key);
    }
}

for (const locale of localeFiles) {
    const ordered = {};

    for (const key of Object.keys(lang[locale]).sort((a, b) => a.localeCompare(b, 'tr'))) {
        ordered[key] = lang[locale][key];
    }

    fs.writeFileSync(path.join(root, 'lang', `${locale}.json`), `${JSON.stringify(ordered, null, 4)}\n`, 'utf8');
}

const reportPath = path.join(process.env.LOCALAPPDATA || root, 'Temp', 'rg-locale-fill-fallbacks.json');
fs.mkdirSync(path.dirname(reportPath), { recursive: true });
fs.writeFileSync(reportPath, JSON.stringify({
    added,
    fallbackCounts: {
        en: fallback.en.length,
        ku: fallback.ku.length,
    },
    fallback,
}, null, 2), 'utf8');

console.log(JSON.stringify({
    added,
    fallbackCounts: {
        en: fallback.en.length,
        ku: fallback.ku.length,
    },
    report: reportPath,
}, null, 2));
