<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class CustomerContentSeeder extends Seeder
{
    public function run(): void
    {
        $pages = [
            [
                'slug' => 'hakkimizda',
                'title' => ['tr' => 'Hakkımızda', 'en' => 'About Us', 'ku' => 'Derbarê Me'],
                'content' => [
                    'tr' => <<<'HTML'
<h2>HAKKIMIZDA</h2>
<p>Adıyaman Rose Garden Çiçek &amp; Çikolata, özel anlarınızı daha anlamlı ve unutulmaz kılmak amacıyla kurulmuş, çiçek ve hediye tasarımı alanında hizmet veren bir markadır.</p>
<p>Kurulduğumuz günden bu yana; kalite, güven ve müşteri memnuniyetini ön planda tutarak, her biri özenle hazırlanan çiçek aranjmanları ve çikolata konseptleri ile hizmet sunmaktayız. Her siparişi sadece bir ürün değil, duyguların en zarif ifadesi olarak görüyor; sevgi, mutluluk, özlem ve teşekkür gibi hislerin en doğru şekilde yansıtılmasını hedefliyoruz.</p>
<p>Alanında deneyimli ekibimizle birlikte, taze ve kaliteli ürünleri modern tasarım anlayışıyla birleştirerek; doğum günü, yıldönümü, kutlama ve özel günler başta olmak üzere her ana özel çözümler üretiyoruz.</p>
<p>Adıyaman’da faaliyet gösteren işletmemiz, hızlı ve güvenilir teslimat hizmetiyle müşterilerine en iyi deneyimi sunmayı amaçlamaktadır. Gelişen trendleri yakından takip ederek ürün çeşitliliğimizi sürekli güncelliyor, her zevke hitap eden tasarımlar sunuyoruz.</p>
<p>Rose Garden Çiçek &amp; Çikolata olarak, en özel anlarınıza eşlik etmekten ve mutluluğunuza dokunmaktan gurur duyuyoruz.</p>
HTML,
                    'en' => <<<'HTML'
<h2>ABOUT US</h2>
<p>Adıyaman Rose Garden Flower &amp; Chocolate is a boutique brand created to make your special moments more meaningful and unforgettable through flowers, gifts and elegant presentation.</p>
<p>Since our founding, we have prioritized quality, trust and customer satisfaction, offering carefully prepared floral arrangements and chocolate concepts. We see every order not merely as a product, but as a refined expression of emotion.</p>
<p>With our experienced team, we combine fresh products with a contemporary design language and create tailored solutions for birthdays, anniversaries, celebrations and special occasions.</p>
<p>Based in Adıyaman, we aim to deliver the best experience with fast and reliable local delivery while keeping our product range fresh and up to date.</p>
HTML,
                    'ku' => <<<'HTML'
<h2>Derbarê Me</h2>
<p>Adıyaman Rose Garden Kulîlk &amp; Çikolata, ji bo ku demên we yên taybet bêtir watedar û bêbîr bibe, li ser sêwirana kulîlkan û diyariyan xizmet dide.</p>
<p>Em bi awayekî baldar li ser kalîte, ewlehî û razîbûna xerîdar dixebitin û ji bo rojbûn, salveger, pîrozbahî û rojên taybet çareseriyên taybet pêşkêş dikin.</p>
HTML,
                ],
                'meta_title' => ['tr' => 'Hakkımızda | Rose Garden Çiçek & Çikolata', 'en' => 'About Us | Rose Garden Flower & Chocolate'],
                'meta_description' => ['tr' => 'Adıyaman Rose Garden Çiçek & Çikolata hakkında bilgi', 'en' => 'About Adıyaman Rose Garden Flower & Chocolate'],
                'is_published' => true,
                'sort_order' => 1,
            ],
            [
                'slug' => 'iade-iptal',
                'title' => ['tr' => 'İade ve İptal Koşulları', 'en' => 'Return & Cancellation Policy', 'ku' => 'Şert û Mercên Vegera û Betalkirinê'],
                'content' => [
                    'tr' => <<<'HTML'
<h2>İADE VE İPTAL KOŞULLARI</h2>
<p><strong>Rose Garden Çiçek &amp; Çikolata - Adıyaman</strong></p>
<p>İşletmemiz tarafından sunulan ürün ve hizmetlerde müşteri memnuniyeti esas olmakla birlikte, çiçek ve benzeri hassas ürünlerin doğası gereği iade ve iptal süreçleri aşağıdaki şartlar çerçevesinde yürütülmektedir.</p>
<h3>1. Sipariş İptali</h3>
<p>Müşteri, siparişini ürün hazırlık sürecine başlanmadan önce iptal etme hakkına sahiptir.</p>
<p>Hazırlık sürecine alınmış veya hazırlanmış siparişlerde iptal işlemi yapılamamaktadır.</p>
<p>Aynı gün teslimatlı siparişlerde iptal talepleri, sipariş yoğunluğu ve hazırlık durumu dikkate alınarak değerlendirilir.</p>
<h3>2. Ürün Görselleri ve Değişiklikler</h3>
<p>İşletmemizde kullanılan çiçek ve yan ürünler mevsimsel ve stok durumuna bağlı olarak sürekli değişiklik gösterebilmektedir.</p>
<p>Bu nedenle siparişlerde kullanılan ürünler, görsellerde yer alan ürünlerle birebir aynı olmayabilir.</p>
<p>Ürün formu, renk tonları veya kullanılan yardımcı materyallerde, ürünün genel tasarımına sadık kalınmak kaydıyla eşdeğer değişiklikler yapılabilir.</p>
<h3>3. İade Koşulları</h3>
<p>Canlı çiçekler ve kişiye özel hazırlanan ürünler, niteliği gereği iade kapsamında değerlendirilememektedir.</p>
<p>Ancak teslim edilen ürünün; hasarlı, eksik veya siparişe uygun olmaması durumunda, teslimatın ardından mümkün olan en kısa sürede işletmemiz ile iletişime geçilmesi gerekmektedir.</p>
<p>Yapılacak inceleme neticesinde, uygun görülmesi halinde ürünün yeniden temini veya telafi edici çözüm sağlanacaktır.</p>
<h3>4. Teslimat ve Sorumluluk</h3>
<p>Sipariş, müşteri tarafından belirtilen adrese teslim edildiği andan itibaren sorumluluk alıcıya aittir.</p>
<p>Teslimat adresinde alıcının bulunamaması durumunda, siparişin teslimi işletme prosedürleri doğrultusunda gerçekleştirilmiş sayılır.</p>
<h3>5. Özel Gün ve Yoğun Dönemler</h3>
<p>Sevgililer Günü, Anneler Günü ve benzeri yoğun dönemlerde sipariş iptal, değişiklik ve iade talepleri sınırlı olarak değerlendirilebilmektedir.</p>
<h3>İletişim</h3>
<p>Telefon: 0552 271 70 67</p>
<p>Adres: Adıyaman</p>
HTML,
                    'en' => <<<'HTML'
<h2>RETURN AND CANCELLATION POLICY</h2>
<p><strong>Rose Garden Flower &amp; Chocolate - Adıyaman</strong></p>
<p>Customer satisfaction is essential for our business; however, due to the perishable nature of flowers and similar products, cancellation and return processes are handled under the conditions below.</p>
<h3>1. Order Cancellation</h3>
<p>Orders may be cancelled before preparation begins.</p>
<p>Orders that have entered preparation or have already been prepared cannot be cancelled.</p>
<p>For same-day deliveries, cancellation requests are evaluated according to preparation status and operational intensity.</p>
<h3>2. Product Images and Changes</h3>
<p>Flowers and supporting materials may vary depending on seasonality and stock availability.</p>
<p>For this reason, delivered products may not match product visuals exactly, although the overall design language is preserved.</p>
<h3>3. Returns</h3>
<p>Fresh flowers and custom-made products are generally outside the return scope due to their nature.</p>
<p>If the delivered item is damaged, incomplete or clearly inconsistent with the order, please contact us as soon as possible after delivery so we can review and offer an appropriate remedy.</p>
HTML,
                ],
                'meta_title' => ['tr' => 'İade ve İptal Koşulları | Rose Garden', 'en' => 'Return & Cancellation Policy | Rose Garden'],
                'meta_description' => ['tr' => 'Rose Garden Çiçek & Çikolata iade ve iptal koşulları', 'en' => 'Rose Garden Flower & Chocolate return and cancellation policy'],
                'is_published' => true,
                'sort_order' => 7,
            ],
            [
                'slug' => 'gizlilik-politikasi',
                'title' => ['tr' => 'Gizlilik Politikası', 'en' => 'Privacy Policy', 'ku' => 'Siyaseta Nepenîtiyê'],
                'content' => [
                    'tr' => <<<'HTML'
<h2>KİŞİSEL VERİLERİN KORUNMASI AYDINLATMA METNİ (KVKK)</h2>
<p>Rose Garden Çiçek Çikolata olarak, 6698 sayılı Kişisel Verilerin Korunması Kanunu (“KVKK”) kapsamında kişisel verilerinizin güvenliğine büyük önem vermekteyiz. Bu aydınlatma metni ile, kişisel verilerinizin hangi amaçlarla işlendiği ve haklarınız hakkında sizleri bilgilendirmekteyiz.</p>
<h3>1. Veri Sorumlusu</h3>
<ul>
<li><strong>Unvan:</strong> Rose Garden Çiçek Çikolata</li>
<li><strong>Adres:</strong> Yeni Sanayi Mah. 2819 Sk. No: 70/2B K.A.06 Adıyaman Merkez</li>
<li><strong>V.D. / T.C. No:</strong> 18343232668</li>
<li><strong>Telefon:</strong> 0552 271 70 67</li>
</ul>
<h3>2. İşlenen Kişisel Veriler</h3>
<p>Kimlik ve iletişim verileri, sipariş ve teslimat kayıtları, IP adresi, çerez kayıtları, kullanım logları ve destek süreçlerinde paylaştığınız içerikler gibi kişisel verileriniz işlenebilmektedir.</p>
<h3>3. İşleme Amaçları</h3>
<ul>
<li>Siparişlerin alınması, hazırlanması ve teslim edilmesi</li>
<li>Müşteri hizmetleri, iletişim ve şikâyet yönetimi</li>
<li>Web sitesi ve hizmetlerin geliştirilmesi</li>
<li>Bilgi güvenliğinin sağlanması</li>
<li>Yasal yükümlülüklerin yerine getirilmesi</li>
</ul>
<h3>4. Aktarım</h3>
<p>Kişisel verileriniz, yasal zorunluluklar kapsamında yetkili kurumlara; hosting, ödeme altyapısı, kargo ve iletişim hizmeti sağlayıcılarına KVKK’ya uygun şekilde aktarılabilmektedir.</p>
<h3>5. Hukuki Sebep ve Toplama Yöntemi</h3>
<p>Verileriniz; web sitemiz, sipariş formları, iletişim kanalları ve çağrı süreçleri üzerinden otomatik veya kısmen otomatik yollarla toplanmakta olup KVKK’nın 5. ve 6. maddelerindeki hukuki sebeplere dayanılarak işlenmektedir.</p>
<h3>6. Haklarınız</h3>
<p>KVKK’nın 11. maddesi uyarınca verilerinizin işlenip işlenmediğini öğrenme, bilgi talep etme, düzeltme, silme, yok etme ve zarar hâlinde giderim isteme haklarına sahipsiniz.</p>
<h3>7. İletişim</h3>
<p>KVKK kapsamındaki taleplerinizi yukarıdaki iletişim kanallarından bize iletebilirsiniz.</p>
HTML,
                    'en' => <<<'HTML'
<h2>PRIVACY POLICY</h2>
<p>Rose Garden Flower &amp; Chocolate places great importance on the protection of personal data within the scope of Turkish data protection law.</p>
<p>Your personal data may be processed for order fulfilment, delivery, customer support, site improvement, security and legal compliance, and may be transferred to trusted service providers when necessary.</p>
<p>You may contact us to exercise your rights regarding access, correction, deletion and other applicable requests.</p>
HTML,
                ],
                'meta_title' => ['tr' => 'Gizlilik Politikası | Rose Garden', 'en' => 'Privacy Policy | Rose Garden'],
                'meta_description' => ['tr' => 'Rose Garden KVKK aydınlatma metni ve gizlilik politikası', 'en' => 'Rose Garden KVKK disclosure and privacy policy'],
                'is_published' => true,
                'sort_order' => 3,
            ],
            [
                'slug' => 'cerez-politikasi',
                'title' => ['tr' => 'Çerez Politikası', 'en' => 'Cookie Policy', 'ku' => 'Siyaseta Çerezê'],
                'content' => [
                    'tr' => <<<'HTML'
<h2>ÇEREZ POLİTİKASI</h2>
<p>Rose Garden Çiçek Çikolata web sitesinde kullanıcı deneyimini geliştirmek, site performansını artırmak ve size kişiselleştirilmiş içerik sunmak amacıyla çerezler kullanılmaktadır.</p>
<h3>1. Çerez Nedir?</h3>
<p>Çerezler, bir web sitesini ziyaret ettiğinizde tarayıcınıza kaydedilen küçük veri dosyalarıdır. Siteyi tekrar ziyaret ettiğinizde tercihlerinizi hatırlamak ve deneyimi iyileştirmek için kullanılır.</p>
<h3>2. Kullanılan Çerez Türleri</h3>
<ul>
<li><strong>Zorunlu çerezler:</strong> Sepet, oturum ve güvenlik gibi temel işlevler için gereklidir.</li>
<li><strong>Analitik çerezler:</strong> Site trafiğini ölçmek ve hizmeti geliştirmek için kullanılır.</li>
<li><strong>Fonksiyonel çerezler:</strong> Dil ve tema gibi tercihlerinizi hatırlamak için kullanılır.</li>
<li><strong>Pazarlama çerezleri:</strong> İlgi alanınıza uygun içerik ve reklam sunmak için kullanılabilir.</li>
</ul>
<h3>3. Çerezlerin Yönetimi</h3>
<p>Tarayıcı ayarlarınızdan çerezleri reddedebilir veya silebilirsiniz. Bazı çerezlerin kapatılması halinde sepet, oturum veya kişiselleştirme özellikleri sınırlanabilir.</p>
<h3>4. KVKK ve Kişisel Veriler</h3>
<p>Çerezler aracılığıyla elde edilen veriler KVKK kapsamında işlenebilir. Haklarınız için <a href="/sayfa/gizlilik-politikasi">Gizlilik Politikası</a> sayfamıza başvurabilirsiniz.</p>
<h3>5. İletişim</h3>
<p>Telefon: 0552 271 70 67</p>
<p>Adres: Yeni Sanayi Mah. 2819 Sk. No: 70/2B K.A.06 Adıyaman Merkez</p>
HTML,
                    'en' => <<<'HTML'
<h2>COOKIE POLICY</h2>
<p>Rose Garden Flower &amp; Chocolate uses cookies to improve user experience, remember preferences and enhance performance.</p>
<p>Essential, analytics, functional and, where applicable, marketing cookies may be used on the site. You can manage your preferences through your browser or our consent interface.</p>
HTML,
                ],
                'meta_title' => ['tr' => 'Çerez Politikası | Rose Garden', 'en' => 'Cookie Policy | Rose Garden'],
                'meta_description' => ['tr' => 'Rose Garden çerez politikası', 'en' => 'Rose Garden cookie policy'],
                'is_published' => true,
                'sort_order' => 4,
            ],
            [
                'slug' => 'kvkk-aydinlatma',
                'title' => ['tr' => 'KVKK Aydınlatma Metni', 'en' => 'KVKK Disclosure Text', 'ku' => 'Metna Ronîkirina KVKK'],
                'content' => [
                    'tr' => <<<'HTML'
<h2>6698 SAYILI KVKK KAPSAMINDA AYDINLATMA METNİ</h2>
<p>6698 sayılı Kişisel Verilerin Korunması Kanunu (“KVKK”) uyarınca, kişisel verilerinizin işlenmesi hakkında sizleri bilgilendirmekteyiz.</p>
<h3>1. Veri Sorumlusu</h3>
<ul>
<li><strong>Unvan:</strong> Rose Garden Çiçek Çikolata</li>
<li><strong>Adres:</strong> Yeni Sanayi Mah. 2819 Sk. No: 70/2B K.A.06 Adıyaman Merkez</li>
<li><strong>V.D. / T.C. No:</strong> 18343232668</li>
<li><strong>Telefon:</strong> 0552 271 70 67</li>
<li><strong>E-posta:</strong> info@rosegardencicekcilik.com.tr</li>
</ul>
<h3>2. İşlenen Veriler ve Amaçlar</h3>
<p>Kimlik, iletişim, sipariş, teslimat, kullanım ve başvuru süreçlerinde paylaştığınız veriler; sözleşmenin ifası, iletişim, güvenlik, hizmet geliştirme ve yasal yükümlülüklerin yerine getirilmesi amaçlarıyla işlenebilir.</p>
<h3>3. Aktarım ve Saklama</h3>
<p>Verileriniz, yasal zorunluluklar kapsamında yetkili kurumlara ve hizmet alınan iş ortaklarına ilgili mevzuata uygun şekilde aktarılabilir; gerekli süre boyunca saklanır ve süre sonunda silinir, yok edilir veya anonimleştirilir.</p>
<h3>4. Haklarınız</h3>
<p>KVKK’nın 11. maddesi kapsamındaki bilgi alma, düzeltme, silme, yok etme, itiraz ve zararın giderilmesini talep etme haklarına sahipsiniz.</p>
<h3>5. Başvuru</h3>
<p>Haklarınızı kullanmak için yukarıdaki iletişim kanallarından bize başvurabilirsiniz. Başvurular ilgili mevzuat çerçevesinde sonuçlandırılır.</p>
HTML,
                    'en' => <<<'HTML'
<h2>KVKK DISCLOSURE TEXT</h2>
<p>This notice explains the processing of personal data by Rose Garden Flower &amp; Chocolate under Turkish Personal Data Protection Law No. 6698.</p>
<p>You may contact us for access, correction, deletion and other requests relating to your personal data.</p>
HTML,
                ],
                'meta_title' => ['tr' => 'KVKK Aydınlatma Metni | Rose Garden', 'en' => 'KVKK Disclosure | Rose Garden'],
                'meta_description' => ['tr' => 'Rose Garden KVKK kapsamında kişisel verilerin işlenmesine ilişkin aydınlatma metni', 'en' => 'Rose Garden KVKK disclosure on personal data processing'],
                'is_published' => true,
                'sort_order' => 5,
            ],
            [
                'slug' => 'iletisim',
                'title' => ['tr' => 'İletişim', 'en' => 'Contact', 'ku' => 'Pêwendî'],
                'content' => [
                    'tr' => <<<'HTML'
<h2>Bize Ulaşın</h2>
<p><strong>Adres:</strong> Yeni Sanayi Mah. 2819 Sk. No: 70/2B K.A.06 Adıyaman Merkez</p>
<p><strong>Telefon:</strong> 0552 271 70 67</p>
<p><strong>E-posta:</strong> info@rosegardencicekcilik.com.tr</p>
<p><strong>Çalışma Saatleri:</strong> Her gün 08:00 - 21:00</p>
HTML,
                    'en' => <<<'HTML'
<h2>Get in Touch</h2>
<p><strong>Address:</strong> Yeni Sanayi Mah. 2819 Sk. No: 70/2B K.A.06 Adıyaman Merkez</p>
<p><strong>Phone:</strong> 0552 271 70 67</p>
<p><strong>Email:</strong> info@rosegardencicekcilik.com.tr</p>
<p><strong>Working Hours:</strong> Every day 08:00 - 21:00</p>
HTML,
                    'ku' => <<<'HTML'
<h2>Bi Me re Têkilî Daynin</h2>
<p><strong>Navnîşan:</strong> Yeni Sanayi Mah. 2819 Sk. No: 70/2B K.A.06 Adıyaman Merkez</p>
<p><strong>Telefon:</strong> 0552 271 70 67</p>
<p><strong>E-posta:</strong> info@rosegardencicekcilik.com.tr</p>
<p><strong>Saetên Xebatê:</strong> Her roj 08:00 - 21:00</p>
HTML,
                ],
                'is_published' => true,
                'sort_order' => 9,
            ],
            [
                'slug' => 'mesafeli-satis-sozlesmesi',
                'title' => ['tr' => 'Mesafeli Satış Sözleşmesi', 'en' => 'Distance Sales Agreement', 'ku' => 'Peymannameya Firotina ji Dûr ve'],
                'content' => [
                    'tr' => <<<'HTML'
<h2>MESAFELİ SATIŞ SÖZLEŞMESİ</h2>
<p>Bu sözleşme, 6502 sayılı Tüketicinin Korunması Hakkında Kanun ve Mesafeli Sözleşmeler Yönetmeliği hükümleri çerçevesinde satıcı ile alıcı arasında elektronik ortamda kurulmuştur.</p>
<h3>1. Satıcı Bilgileri</h3>
<ul>
<li><strong>Unvan:</strong> Rose Garden Çiçek Çikolata</li>
<li><strong>Adres:</strong> Yeni Sanayi Mah. 2819 Sk. No: 70/2B K.A.06 Adıyaman Merkez</li>
<li><strong>Telefon:</strong> 0552 271 70 67</li>
<li><strong>E-posta:</strong> info@rosegardencicekcilik.com.tr</li>
<li><strong>V.D. / T.C. No:</strong> 18343232668</li>
</ul>
<h3>2. Sözleşmenin Konusu</h3>
<p>İnternet sitesi üzerinden sipariş verdiğiniz ürün ve hizmetlerin satışı ile teslimatına ilişkin tarafların hak ve yükümlülükleri bu sözleşmenin konusunu oluşturur.</p>
<h3>3. Ödeme ve Teslimat</h3>
<p>Ödeme, sipariş anında belirtilen güvenli ödeme altyapıları üzerinden tahsil edilir. Teslimat süresi ve koşulları sipariş akışında açıkça bildirilir.</p>
<h3>4. Cayma Hakkı</h3>
<p>Çabuk bozulabilen veya kişiye özel hazırlanan ürünlerde, ilgili mevzuat kapsamında cayma hakkı istisnası uygulanabilir. Ayrıntılar için <a href="/sayfa/iade-iptal">İade ve İptal Koşulları</a> sayfasını inceleyebilirsiniz.</p>
<h3>5. Uyuşmazlıklar</h3>
<p>Uyuşmazlıklarda tüketici hakem heyetleri ve tüketici mahkemeleri ile diğer yasal başvuru yolları saklıdır.</p>
HTML,
                    'en' => <<<'HTML'
<h2>DISTANCE SALES AGREEMENT</h2>
<p>This agreement governs the electronic sale and delivery of products offered by Rose Garden Flower &amp; Chocolate.</p>
<p>Key commercial terms, payment, delivery conditions and withdrawal exceptions for perishable or custom-made products are presented during checkout and in the Turkish legal text.</p>
HTML,
                ],
                'meta_title' => ['tr' => 'Mesafeli Satış Sözleşmesi | Rose Garden', 'en' => 'Distance Sales Agreement | Rose Garden'],
                'meta_description' => ['tr' => 'Rose Garden mesafeli satış sözleşmesi metni', 'en' => 'Rose Garden distance sales agreement'],
                'is_published' => true,
                'sort_order' => 6,
            ],
        ];

        foreach ($pages as $data) {
            Page::updateOrCreate(['slug' => $data['slug']], $data);
        }
    }
}
