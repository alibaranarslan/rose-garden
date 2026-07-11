<?php

namespace Database\Seeders;

use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Page;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\Tag;
use App\Models\User;
use App\Support\CatalogTaxonomy;
use App\Support\StorefrontIncomingAssets;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DemoContentSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@admin.com')->first()
            ?? User::first();

        $this->seedCategories();
        $this->seedTags();
        $this->seedProducts();
        $this->seedBlog($admin);
        $this->seedCoupons();
        $this->seedPages();
    }

    private function seedCategories(): void
    {
        $parentIds = [];

        foreach (CatalogTaxonomy::categoryBlueprints() as $blueprint) {
            if ($blueprint['parent_slug'] !== null) {
                continue;
            }

            $category = Category::updateOrCreate(
                ['slug' => $blueprint['slug']],
                [
                    'name' => $blueprint['name'],
                    'slug' => $blueprint['slug'],
                    'description' => $blueprint['description'],
                    'image' => StorefrontIncomingAssets::preferredStorageProductsPath($blueprint['cover_product_slugs']),
                    'parent_id' => null,
                    'is_active' => true,
                    'sort_order' => $blueprint['sort_order'],
                ]
            );

            $parentIds[$blueprint['slug']] = $category->id;
        }

        foreach (CatalogTaxonomy::categoryBlueprints() as $blueprint) {
            if ($blueprint['parent_slug'] === null) {
                continue;
            }

            Category::updateOrCreate(
                ['slug' => $blueprint['slug']],
                [
                    'name' => $blueprint['name'],
                    'slug' => $blueprint['slug'],
                    'description' => $blueprint['description'],
                    'image' => StorefrontIncomingAssets::preferredStorageProductsPath($blueprint['cover_product_slugs']),
                    'parent_id' => $parentIds[$blueprint['parent_slug']] ?? null,
                    'is_active' => true,
                    'sort_order' => $blueprint['sort_order'],
                ]
            );
        }
    }

    private function seedTags(): void
    {
        $tags = [
            ['tr' => 'Güller',          'en' => 'Roses',           'ku' => 'Gul'],
            ['tr' => 'Romantik',        'en' => 'Romantic',        'ku' => 'Romantîk'],
            ['tr' => 'Doğum Günü',     'en' => 'Birthday',        'ku' => 'Rojbûn'],
            ['tr' => 'Yıldönümü',      'en' => 'Anniversary',     'ku' => 'Salveger'],
            ['tr' => 'Taziye',          'en' => 'Condolence',      'ku' => 'Şînahî'],
            ['tr' => 'Bebek',           'en' => 'Baby',            'ku' => 'Pitik'],
            ['tr' => 'Düğün',           'en' => 'Wedding',         'ku' => 'Dawet'],
            ['tr' => 'Sevgililer Günü', 'en' => 'Valentine\'s Day', 'ku' => 'Roja Evîndaran'],
            ['tr' => 'Anneler Günü',   'en' => 'Mother\'s Day',   'ku' => 'Roja Dêyan'],
            ['tr' => 'Çikolata',        'en' => 'Chocolate',       'ku' => 'Çikolata'],
            ['tr' => 'Lüks',            'en' => 'Luxury',          'ku' => 'Luks'],
            ['tr' => 'Ekonomik',        'en' => 'Budget',          'ku' => 'Aborî'],
            ['tr' => 'Mevsimlik',       'en' => 'Seasonal',        'ku' => 'Demsalî'],
            ['tr' => 'Özel Gün',       'en' => 'Special Day',     'ku' => 'Roja Taybet'],
            ['tr' => 'Yeni',            'en' => 'New',             'ku' => 'Nû'],
        ];

        foreach ($tags as $tag) {
            Tag::firstOrCreate(
                ['slug' => Str::slug($tag['tr'])],
                ['name' => $tag]
            );
        }
    }

    private function seedProducts(): void
    {
        $categories = Category::where('is_active', true)->get()->keyBy('slug');

        $products = [
            // ÇİÇEK BUKETLERİ
            [
                'name' => ['tr' => 'Imperial Kırmızı Gül Buketi (21 Adet)', 'en' => 'Imperial Red Rose Bouquet (21)', 'ku' => 'Destegula Gulên Sor a Împeryal (21)'],
                'slug' => 'kirmizi-gul-buketi-21-adet',
                'short_description' => ['tr' => '21 adet taze kırmızı gülden hazırlanan özel buket. Aşkınızı en güzel şekilde ifade edin.', 'en' => 'A special bouquet made from 21 fresh red roses. Express your love in the most beautiful way.', 'ku' => 'Desteguleke taybet ya ji 21 gulên sorên taze hatiye amadekirin. Evîna xwe bi awayê herî bedew îfade bikin.'],
                'description' => ['tr' => '<p>21 adet birinci sınıf kırmızı gül, yeşillik ve dekoratif ambalajla hazırlanmış özel buket.</p><p>Güller taze olarak tedarik edilmekte olup, aynı gün teslimat garantisi sunulmaktadır. Buket, zarif kraft kağıdı ve saten kurdele ile ambalajlanır.</p><p><strong>İçerik:</strong> 21 Adet Kırmızı Gül, Cipso, Okaliptüs Yeşilliği</p><p><strong>Raf Ömrü:</strong> Uygun bakım koşullarında 5-7 gün</p>', 'en' => '<p>A special bouquet prepared with 21 first-class red roses, greenery and decorative packaging.</p><p>Roses are sourced fresh and same-day delivery is guaranteed. The bouquet is packaged with elegant kraft paper and satin ribbon.</p><p><strong>Contents:</strong> 21 Red Roses, Gypsophila, Eucalyptus Greens</p><p><strong>Shelf Life:</strong> 5-7 days under appropriate care conditions</p>', 'ku' => '<p>Desteguleke taybet ya ji 21 gulên sorên çîna yekem, keçelî û ambalaja dekoratîf hatiye amadekirin.</p><p>Gul bi tazeyî tên peydakirin û garantiya radestkirina heman rojê tê pêşkêşkirin.</p>'],
                'price' => 949.00,
                'sale_price' => null,
                'category' => 'cicek-buketleri',
                'is_featured' => true,
                'is_new' => false,
                'stock_status' => 'in_stock',
                'tags' => ['guller', 'romantik', 'sevgililer-gunu'],
                'variants' => [
                    ['name' => ['tr' => 'Standart (21 Gül)', 'en' => 'Standard (21 Roses)', 'ku' => 'Standard (21 Gul)'], 'price' => 949.00],
                    ['name' => ['tr' => 'Büyük (41 Gül)',   'en' => 'Large (41 Roses)',    'ku' => 'Mezin (41 Gul)'],   'price' => 1649.00],
                    ['name' => ['tr' => 'Premium (51 Gül)', 'en' => 'Premium (51 Roses)',  'ku' => 'Premyum (51 Gul)'], 'price' => 1999.00],
                ],
            ],
            [
                'name' => ['tr' => 'Şampanya Gülü ve Lisyantus Buketi', 'en' => 'Champagne Rose & Lisianthus Bouquet', 'ku' => 'Destegula Gulê Şampanyayê û Lisyantus'],
                'slug' => 'beyaz-papatya-buketi',
                'short_description' => ['tr' => 'Taze beyaz papatyalardan oluşan doğal ve şık buket. Her ortama uyum sağlar.', 'en' => 'A natural and elegant bouquet of fresh white daisies. Suitable for any occasion.', 'ku' => 'Desteguleke xwezayî û spehî ya ji papatiyan spî yên taze hatiye çêkirin. Li her deverê tê.'],
                'description' => ['tr' => '<p>Doğallığın simgesi beyaz papatyalar ile hazırlanan bu buket, sadeliği ve zarafeti seven herkes için ideal bir hediye seçeneğidir.</p><p><strong>İçerik:</strong> Beyaz Papatyalar, Lavanta Dalları, Yeşillik</p>', 'en' => '<p>This bouquet, prepared with white daisies that symbolize naturalness, is an ideal gift choice for everyone who loves simplicity and elegance.</p><p><strong>Contents:</strong> White Daisies, Lavender Sprigs, Greenery</p>', 'ku' => '<p>Ev destegul, ku bi papatiyan spî yên ku sembola xwezayiyê ne hatiye amadekirin, ji bo hemû kesên ku sadeye û spehîbûnê hez dikin bijarteke diyariyê ya îdeal e.</p>'],
                'price' => 520.00,
                'sale_price' => 465.00,
                'category' => 'cicek-buketleri',
                'is_featured' => false,
                'is_new' => true,
                'stock_status' => 'in_stock',
                'tags' => ['ekonomik', 'mevsimlik'],
                'variants' => [
                    ['name' => ['tr' => 'Küçük Boy', 'en' => 'Small Size',  'ku' => 'Piçûk'], 'price' => 420.00],
                    ['name' => ['tr' => 'Orta Boy',  'en' => 'Medium Size', 'ku' => 'Navîn'], 'price' => 520.00],
                    ['name' => ['tr' => 'Büyük Boy', 'en' => 'Large Size',  'ku' => 'Mezin'], 'price' => 680.00],
                ],
            ],
            [
                'name' => ['tr' => 'Lüks Karışık Mevsim Buketi', 'en' => 'Luxury Mixed Seasonal Bouquet', 'ku' => 'Destegula Demsalî ya Luks a Tevlihev'],
                'slug' => 'karma-mevsim-buketi',
                'short_description' => ['tr' => 'Mevsimin en güzel çiçekleriyle hazırlanan renkli buket.', 'en' => 'A colorful bouquet prepared with the finest flowers of the season.', 'ku' => 'Desteguleke rengîn ya ji kulilkên herî bedew ên demsalê hatiye amadekirin.'],
                'description' => ['tr' => '<p>Her mevsimin en taze ve en güzel çiçekleriyle floristlerimiz tarafından özenle hazırlanan karma buket. Renkler ve çiçek türleri mevsime göre değişiklik gösterebilir.</p><p><strong>İçerik:</strong> Mevsime göre değişen çiçek çeşitleri</p>', 'en' => '<p>A mixed bouquet carefully prepared by our florists with the freshest and most beautiful flowers of each season. Colors and flower types may vary according to the season.</p><p><strong>Contents:</strong> Seasonal flower varieties</p>', 'ku' => '<p>Desteguleke tevlihev a ku ji aliyê floristên me ve bi kulilkên herî taze û bedew ên her demsalê bi hişmendî hatiye amadekirin.</p>'],
                'price' => 1200.00,
                'sale_price' => null,
                'category' => 'cicek-buketleri',
                'is_featured' => false,
                'is_new' => false,
                'stock_status' => 'in_stock',
                'tags' => ['mevsimlik', 'dogum-gunu', 'luks'],
                'variants' => [
                    ['name' => ['tr' => 'Küçük Boy', 'en' => 'Small',  'ku' => 'Piçûk'], 'price' => 950.00],
                    ['name' => ['tr' => 'Orta Boy',  'en' => 'Medium', 'ku' => 'Navîn'], 'price' => 1200.00],
                    ['name' => ['tr' => 'Büyük Boy', 'en' => 'Large',  'ku' => 'Mezin'], 'price' => 1549.00],
                ],
            ],

            // ARANJMANLAR
            [
                'name' => ['tr' => 'Kırmızı Gül Aranjmanı', 'en' => 'Red Rose Arrangement', 'ku' => 'Rêzkirina Gulên Sor'],
                'slug' => 'kirmizi-gul-aranjmani',
                'short_description' => ['tr' => 'Seramik vazo içinde kırmızı güller ve dekoratif yeşilliklerden oluşan şık aranjman.', 'en' => 'An elegant arrangement of red roses and decorative greens in a ceramic vase.', 'ku' => 'Rêzkirineke spehî ya ji gulên sor û keçeliyên dekoratîf di vazoyeke seramîkî de.'],
                'description' => ['tr' => '<p>El yapımı seramik vazo içinde düzenlenen kırmızı gül aranjmanı. Ofis, ev veya özel günler için ideal.</p><p><strong>İçerik:</strong> 15 Kırmızı Gül, Okaliptüs, Cipso, Seramik Vazo</p>', 'en' => '<p>A red rose arrangement organized in a hand-crafted ceramic vase. Ideal for the office, home or special occasions.</p><p><strong>Contents:</strong> 15 Red Roses, Eucalyptus, Gypsophila, Ceramic Vase</p>', 'ku' => '<p>Rêzkirina gulên sor a di vazoyeke seramîkî ya destçêker de rêxistinkiriye. Ji bo ofîs, mal an rojên taybet îdeal e.</p>'],
                'price' => 1320.00,
                'sale_price' => null,
                'category' => 'aranjmanlar',
                'is_featured' => true,
                'is_new' => false,
                'stock_status' => 'in_stock',
                'tags' => ['guller', 'romantik', 'luks'],
                'variants' => [],
            ],
            [
                'name' => ['tr' => 'Vazolu Beyaz Lilyum Aranjmanı', 'en' => 'White Lily Vase Arrangement', 'ku' => 'Rêzkirina Lilyumên Spî di Vazoyê de'],
                'slug' => 'bahar-ruzgari-aranjmani',
                'short_description' => ['tr' => 'Pastel tonlarda çeşitli çiçeklerle hazırlanan ferah ve zarif aranjman.', 'en' => 'A fresh and elegant arrangement prepared with various flowers in pastel tones.', 'ku' => 'Rêzkirineke nû û spehî ya bi kulilkên curbecur ên di tona pastelî de hatiye amadekirin.'],
                'description' => ['tr' => '<p>Pastel pembe, lila ve beyaz tonlarda çeşitli mevsim çiçekleriyle hazırlanan zarif aranjman. Cam vazo içinde sunulur.</p><p><strong>İçerik:</strong> Pembe Güller, Lisianthus, Karanfil, Cipso, Cam Vazo</p>', 'en' => '<p>An elegant arrangement prepared with various seasonal flowers in pastel pink, lilac and white tones. Presented in a glass vase.</p><p><strong>Contents:</strong> Pink Roses, Lisianthus, Carnations, Gypsophila, Glass Vase</p>', 'ku' => '<p>Rêzkirineke spehî ya bi kulilkên demsalê yên curbecur ên di tona pembe yê pastel, lila û spî de hatiye amadekirin. Di vazoyeke camî de tê pêşkêşkirin.</p>'],
                'price' => 850.00,
                'sale_price' => 720.00,
                'category' => 'aranjmanlar',
                'is_featured' => false,
                'is_new' => true,
                'stock_status' => 'in_stock',
                'tags' => ['mevsimlik', 'yeni', 'luks'],
                'variants' => [],
            ],

            // KUTUDA ÇİÇEKLER
            [
                'name' => ['tr' => 'Silindir Kutuda Kırmızı Güller', 'en' => 'Red Roses in Cylinder Box', 'ku' => 'Gulên Sor di Qutiya Silindir de'],
                'slug' => 'silindir-kutuda-kirmizi-guller',
                'short_description' => ['tr' => 'Lüks silindir kutuda düzenlenen kırmızı güller. Premium hediye seçeneği.', 'en' => 'Red roses arranged in a luxury cylinder box. A premium gift option.', 'ku' => 'Gulên sor ên di qutiya silindir a luks de rêxistinkiriye. Bijarteke diyariyê ya premyum.'],
                'description' => ['tr' => '<p>Kadife kaplı silindir kutu içinde özenle dizilen taze kırmızı güller. Her detayı düşünülmüş premium bir hediye.</p><p><strong>İçerik:</strong> 19 Kırmızı Gül, Kadife Silindir Kutu</p>', 'en' => '<p>Fresh red roses carefully arranged in a velvet-covered cylinder box. A premium gift with every detail considered.</p><p><strong>Contents:</strong> 19 Red Roses, Velvet Cylinder Box</p>', 'ku' => '<p>Gulên sorên taze yên bi hişmendî di qutiya silindir a bi kadîfe veşartî de rêzkirî. Diyariyeke premyum a ku her hûrgulî tê de hatiye bifikirîn.</p>'],
                'price' => 1699.00,
                'sale_price' => null,
                'category' => 'kutuda-cicekler',
                'is_featured' => true,
                'is_new' => false,
                'stock_status' => 'in_stock',
                'tags' => ['guller', 'luks', 'ozel-gun'],
                'variants' => [
                    ['name' => ['tr' => 'Küçük Kutu (9 Gül)',  'en' => 'Small Box (9 Roses)',  'ku' => 'Qutiya Piçûk (9 Gul)'],  'price' => 989.00],
                    ['name' => ['tr' => 'Orta Kutu (19 Gül)',  'en' => 'Medium Box (19 Roses)', 'ku' => 'Qutiya Navîn (19 Gul)'], 'price' => 1699.00],
                    ['name' => ['tr' => 'Büyük Kutu (39 Gül)', 'en' => 'Large Box (39 Roses)',  'ku' => 'Qutiya Mezin (39 Gul)'], 'price' => 2690.00],
                ],
            ],
            [
                'name' => ['tr' => 'Prestij Kare Kutuda Karışık Güller', 'en' => 'Prestige Mixed Roses Square Box', 'ku' => 'Gulên Tevlihev di Qutiya Çargoşe ya Prestîj de'],
                'slug' => 'kare-kutuda-renkli-guller',
                'short_description' => ['tr' => 'Karışık renklerde güller, şık kare kutuda sunulur.', 'en' => 'Mixed-color roses presented in an elegant square box.', 'ku' => 'Gulên bi rengên tevlihev di qutiya çargoşeyeke spehî de tê pêşkêşkirin.'],
                'description' => ['tr' => '<p>Pembe, beyaz, sarı ve turuncu güllerin bir arada sunulduğu kare kutu aranjmanı. Doğum günleri ve kutlamalar için ideal.</p>', 'en' => '<p>A square box arrangement featuring pink, white, yellow and orange roses together. Ideal for birthdays and celebrations.</p>', 'ku' => '<p>Rêzkirina qutiya çargoşeyê ya ku lê gulên pembe, spî, zer û porteqalî bi hev re tên pêşkêşkirin. Ji bo rojbûn û pîrozbahiyan îdeal e.</p>'],
                'price' => 1349.00,
                'sale_price' => null,
                'category' => 'kutuda-cicekler',
                'is_featured' => false,
                'is_new' => false,
                'stock_status' => 'in_stock',
                'tags' => ['guller', 'dogum-gunu', 'luks'],
                'variants' => [],
            ],

            // ÇİKOLATA & TATLI
            [
                'name' => ['tr' => 'Artizan Belçika Pralin & Trüf Kutusu', 'en' => 'Artisan Belgian Praline & Truffle Box', 'ku' => 'Qutiya Pralîn û Trûfên Belçîka ya Artizan'],
                'slug' => 'el-yapimi-belcika-cikolata-kutusu',
                'short_description' => ['tr' => '24 adet el yapımı Belçika çikolatası, lüks hediye kutusunda.', 'en' => '24 pieces of handmade Belgian chocolate in a luxury gift box.', 'ku' => '24 parçe çikolata ya Belçîka ya destçêker, di qutiya diyariyê ya luks de.'],
                'description' => ['tr' => '<p>Ustalarımız tarafından özenle hazırlanan 24 adet el yapımı Belçika çikolatası. Bitter, sütlü ve beyaz çikolata çeşitleriyle hazırlanan koleksiyon, şık hediye kutusunda sunulur.</p><p><strong>İçerik:</strong> 8 Bitter, 8 Sütlü, 8 Beyaz Çikolata</p><p><strong>Raf Ömrü:</strong> 30 gün (serin ve kuru yerde)</p>', 'en' => '<p>24 pieces of handmade Belgian chocolate carefully prepared by our masters. The collection, prepared with dark, milk and white chocolate varieties, is presented in an elegant gift box.</p><p><strong>Contents:</strong> 8 Dark, 8 Milk, 8 White Chocolate</p><p><strong>Shelf Life:</strong> 30 days (in a cool and dry place)</p>', 'ku' => '<p>24 parçe çikolata ya Belçîka ya destçêker a ku ji aliyê ustayên me ve bi hişmendî hatiye amadekirin. Koleksiyona ku bi cûreyên çikolata yên tîrî, şîranî û spî hatiye amadekirin, di qutiya diyariyê ya spehî de tê pêşkêşkirin.</p>'],
                'price' => 720.00,
                'sale_price' => null,
                'category' => 'cikolata-tatli',
                'is_featured' => true,
                'is_new' => false,
                'stock_status' => 'in_stock',
                'tags' => ['cikolata', 'luks'],
                'variants' => [
                    ['name' => ['tr' => '12\'li Kutu', 'en' => 'Box of 12', 'ku' => 'Qutiya 12'], 'price' => 429.00],
                    ['name' => ['tr' => '24\'lü Kutu', 'en' => 'Box of 24', 'ku' => 'Qutiya 24'], 'price' => 720.00],
                    ['name' => ['tr' => '48\'li Kutu', 'en' => 'Box of 48', 'ku' => 'Qutiya 48'], 'price' => 1180.00],
                ],
            ],
            [
                'name' => ['tr' => 'Çikolata Kaplı Çilek Buketi', 'en' => 'Chocolate-Dipped Strawberry Bouquet', 'ku' => 'Destegula Tûlên Bi Çikolata Veşartî'],
                'slug' => 'cikolata-kapli-cilek-buketi',
                'short_description' => ['tr' => 'Taze çilekler, Belçika çikolatası ile kaplanarak buket formunda sunulur.', 'en' => 'Fresh strawberries coated with Belgian chocolate and presented in bouquet form.', 'ku' => 'Tûlên taze bi çikolata ya Belçîka ve tên veşartin û di şiklê destegulê de tên pêşkêşkirin.'],
                'description' => ['tr' => '<p>Taze çileklerin Belçika çikolatası ile kaplanarak buket şeklinde sunulduğu özel lezzet. Doğum günleri, yıldönümleri ve sürpriz hediyeler için mükemmel.</p><p><strong>Not:</strong> Aynı gün teslimat önerilir. Soğuk zincir ile teslim edilir.</p>', 'en' => '<p>A special delicacy where fresh strawberries are coated with Belgian chocolate and presented as a bouquet. Perfect for birthdays, anniversaries and surprise gifts.</p><p><strong>Note:</strong> Same-day delivery is recommended. Delivered with cold chain.</p>', 'ku' => '<p>Tûlên taze yên bi çikolata ya Belçîka ve hatine veşartin û weke destegul hatine pêşkêşkirin tama taybet e. Ji bo rojbûn, salveger û diyariyên sûrprizê mûkemmel e.</p>'],
                'price' => 549.00,
                'sale_price' => 479.00,
                'category' => 'cikolata-tatli',
                'is_featured' => false,
                'is_new' => true,
                'stock_status' => 'in_stock',
                'tags' => ['cikolata', 'yeni', 'romantik'],
                'variants' => [],
            ],
            [
                'name' => ['tr' => 'Karışık Baklava Kutusu (1 kg)', 'en' => 'Mixed Baklava Box (1 kg)', 'ku' => 'Qutiya Baklava ya Tevlihev (1 kg)'],
                'slug' => 'karisik-baklava-kutusu',
                'short_description' => ['tr' => 'Adıyaman\'ın meşhur baklavası, özel hediye kutusunda.', 'en' => 'The famous baklava of Adıyaman, in a special gift box.', 'ku' => 'Baklavaya navdar a Adiyamanê, di qutiya diyariyê ya taybet de.'],
                'description' => ['tr' => '<p>Adıyaman\'ın yöresel lezzetlerinden karışık baklava. Fıstıklı, cevizli ve şöbiyet çeşitlerinden oluşan 1 kg\'lık özel kutu.</p>', 'en' => '<p>Mixed baklava from the local delicacies of Adıyaman. A special 1 kg box consisting of pistachio, walnut and şöbiyet varieties.</p>', 'ku' => '<p>Baklava ya tevlihev a ji tamên herêmî yên Adiyamanê. Qutiya taybet a 1 kg ya ji cûreyên fistiqî, gozî û şöbiyet pêk tê.</p>'],
                'price' => 799.00,
                'sale_price' => null,
                'category' => 'cikolata-tatli',
                'is_featured' => false,
                'is_new' => false,
                'stock_status' => 'in_stock',
                'tags' => ['ozel-gun'],
                'variants' => [
                    ['name' => ['tr' => '500 gr', 'en' => '500 gr', 'ku' => '500 gr'], 'price' => 449.00],
                    ['name' => ['tr' => '1 kg',   'en' => '1 kg',   'ku' => '1 kg'],   'price' => 799.00],
                    ['name' => ['tr' => '2 kg',   'en' => '2 kg',   'ku' => '2 kg'],   'price' => 1499.00],
                ],
            ],

            // HEDİYE SETLERİ
            [
                'name' => ['tr' => 'Lüks Romantik Gül & Çikolata Seti', 'en' => 'Luxury Romantic Rose & Chocolate Set', 'ku' => 'Seta Luks a Romantîk a Gul û Çikolatayê'],
                'slug' => 'romantik-hediye-seti',
                'short_description' => ['tr' => 'Kırmızı güller, çikolata kutusu ve peluş ayıcık bir arada.', 'en' => 'Red roses, chocolate box and plush teddy bear all together.', 'ku' => 'Gulên sor, qutiya çikolatayê û hirçê pelûşê bi hev re.'],
                'description' => ['tr' => '<p>Sevdiklerinize en özel hediye! 11 kırmızı gülden oluşan buket, 12\'li el yapımı çikolata kutusu ve 30 cm peluş ayıcık bir arada.</p><p><strong>Set İçeriği:</strong></p><ul><li>11 Kırmızı Gül Buketi</li><li>12\'li Çikolata Kutusu</li><li>30 cm Peluş Ayıcık</li><li>Hediye Kartı</li></ul>', 'en' => '<p>The most special gift for your loved ones! A bouquet of 11 red roses, a 12-piece handmade chocolate box and a 30 cm plush teddy bear all together.</p><p><strong>Set Contents:</strong></p><ul><li>11 Red Rose Bouquet</li><li>12-Piece Chocolate Box</li><li>30 cm Plush Teddy Bear</li><li>Gift Card</li></ul>', 'ku' => '<p>Diyariya herî taybet ji bo hezkiriyên we! Destegula 11 gulên sor, qutiya çikolata ya destçêker a 12 parçe û hirçê pelûşê yê 30 cm bi hev re.</p>'],
                'price' => 1489.00,
                'sale_price' => 1320.00,
                'category' => 'hediye-setleri',
                'is_featured' => true,
                'is_new' => false,
                'stock_status' => 'in_stock',
                'tags' => ['romantik', 'sevgililer-gunu', 'ozel-gun', 'luks'],
                'variants' => [],
            ],
            [
                'name' => ['tr' => 'Doğum Günü Çiçek & Pasta Kutlama Seti', 'en' => 'Birthday Flower & Cake Celebration Set', 'ku' => 'Seta Pîrozbahiya Rojbûnê ya Kulilk û Kekê'],
                'slug' => 'dogum-gunu-surpriz-seti',
                'short_description' => ['tr' => 'Karma çiçek buketi, pasta ve balon sürpriziyle doğum günlerini özel kılın.', 'en' => 'Make birthdays special with a mixed flower bouquet, cake and balloon surprise.', 'ku' => 'Rojbûnan bi destegula kulilkên tevlihev, kekê û sûrpriza balonan taybet bikin.'],
                'description' => ['tr' => '<p>Doğum günü kutlamaları için tasarlanan komple sürpriz seti. Renkli çiçek buketi, mini pasta ve uçan balonlarla unutulmaz anlar yaratın.</p><p><strong>Set İçeriği:</strong></p><ul><li>Karma Renkli Çiçek Buketi</li><li>Mini Doğum Günü Pastası</li><li>5 Adet Helyum Balon</li><li>Doğum Günü Kartı</li></ul>', 'en' => '<p>A complete surprise set designed for birthday celebrations. Create unforgettable moments with a colorful flower bouquet, mini cake and flying balloons.</p><p><strong>Set Contents:</strong></p><ul><li>Mixed Colorful Flower Bouquet</li><li>Mini Birthday Cake</li><li>5 Helium Balloons</li><li>Birthday Card</li></ul>', 'ku' => '<p>Seta sûrprizê ya temam a ji bo pîrozbahiyên rojbûnê hatiye plankirin. Bi destegula kulilkên rengîn, kekê mini û balonên firindî bîranînên ji bîrnekirin biafirînin.</p>'],
                'price' => 1749.00,
                'sale_price' => null,
                'category' => 'hediye-setleri',
                'is_featured' => false,
                'is_new' => true,
                'stock_status' => 'in_stock',
                'tags' => ['dogum-gunu', 'ozel-gun', 'yeni', 'luks'],
                'variants' => [],
            ],
            [
                'name' => ['tr' => 'Hoş Geldin Bebeği — Pastel Çiçek Seti', 'en' => 'Welcome Baby — Pastel Flower Set', 'ku' => 'Bi Xêr Hatî Pitik — Seta Kulilkên Pastelî'],
                'slug' => 'yeni-bebek-hediye-seti',
                'short_description' => ['tr' => 'Pastel çiçekler, bebek çikolatası ve peluş oyuncak.', 'en' => 'Pastel flowers, baby chocolate and plush toy.', 'ku' => 'Kulilkên pastelî, çikolata ya pitikê û lîstoka pelûşê.'],
                'description' => ['tr' => '<p>Yeni doğan bebek için özel hazırlanan hediye seti. Pastel tonlarda çiçek aranjmanı, bebek çikolatası ve sevimli peluş oyuncak.</p>', 'en' => '<p>A special gift set prepared for a newborn baby. A flower arrangement in pastel tones, baby chocolate and a cute plush toy.</p>', 'ku' => '<p>Seta diyariyê ya taybet a ji bo pitikê nûzayî hatiye amadekirin. Rêzkirina kulilkê di tona pastelî, çikolata ya pitikê û lîstoka pelûşê ya şîrîn.</p>'],
                'price' => 1089.00,
                'sale_price' => null,
                'category' => 'hediye-setleri',
                'is_featured' => false,
                'is_new' => false,
                'stock_status' => 'in_stock',
                'tags' => ['bebek', 'ozel-gun'],
                'variants' => [],
            ],

            // SAKSI ÇİÇEKLERİ
            [
                'name' => ['tr' => 'Premium Beyaz Phalaenopsis Orkide', 'en' => 'Premium White Phalaenopsis Orchid', 'ku' => 'Orkîdeya Phalaenopsis a Spî ya Premyum'],
                'slug' => 'orkide-tek-dalli',
                'short_description' => ['tr' => 'Zarif tek dallı orkide, dekoratif saksıda. Uzun ömürlü bir hediye.', 'en' => 'Elegant single-branch orchid in a decorative pot. A long-lasting gift.', 'ku' => 'Orkîdeya spehî ya yek şaxê, di saksiyeke dekoratîf de. Diyariyeke dirêj-jiyan.'],
                'description' => ['tr' => '<p>Phalaenopsis orkide, dekoratif seramik saksıda sunulur. Uzun ömürlü olması ve bakımının kolay olması ile bilinen orkide, her ortama şıklık katar.</p><p><strong>Bakım:</strong> Haftada 1 bardak su, dolaylı güneş ışığı, 18-25°C</p>', 'en' => '<p>Phalaenopsis orchid presented in a decorative ceramic pot. Known for its longevity and ease of care, the orchid adds elegance to any environment.</p><p><strong>Care:</strong> 1 glass of water per week, indirect sunlight, 18-25°C</p>', 'ku' => '<p>Orkîdeya Phalaenopsis di saksiyeke seramîkî ya dekoratîf de tê pêşkêşkirin. Bi jiyana dirêj û hêsaniya lênêrînê tê nasîn.</p><p><strong>Lênêrîn:</strong> Hefteyê 1 qedeh av, ronahiya nerastir, 18-25°C</p>'],
                'price' => 749.00,
                'sale_price' => null,
                'category' => 'saksi-cicekleri',
                'is_featured' => false,
                'is_new' => false,
                'stock_status' => 'in_stock',
                'tags' => ['ekonomik', 'luks'],
                'variants' => [
                    ['name' => ['tr' => 'Tek Dallı',  'en' => 'Single Branch', 'ku' => 'Yek Şax'],  'price' => 749.00],
                    ['name' => ['tr' => 'Çift Dallı', 'en' => 'Double Branch', 'ku' => 'Du Şax'],   'price' => 1120.00],
                    ['name' => ['tr' => 'Üç Dallı',  'en' => 'Triple Branch', 'ku' => 'Sê Şax'],   'price' => 1549.00],
                ],
            ],
            [
                'name' => ['tr' => 'Zelkova Minyatür Bonsai', 'en' => 'Zelkova Miniature Bonsai', 'ku' => 'Bonsaiya Zelkova ya Mînyatûr'],
                'slug' => 'bonsai-agaci',
                'short_description' => ['tr' => 'Minyatür bonsai ağacı, seramik saksıda. Ofis ve ev dekorasyonu için ideal.', 'en' => 'Miniature bonsai tree in a ceramic pot. Ideal for office and home decoration.', 'ku' => 'Dara bonsaiyê ya mînyatûr, di saksiyeke seramîkî de. Ji bo dekorasyona ofîs û malê îdeal.'],
                'description' => ['tr' => '<p>Özenle şekillendirilmiş bonsai ağacı, el yapımı seramik saksıda sunulur. Doğayı yaşam alanınıza taşıyın.</p><p><strong>Bakım:</strong> Düzenli sulama, dolaylı güneş ışığı, budama</p>', 'en' => '<p>A carefully shaped bonsai tree presented in a handmade ceramic pot. Bring nature into your living space.</p><p><strong>Care:</strong> Regular watering, indirect sunlight, pruning</p>', 'ku' => '<p>Dara bonsaiyê ya bi hişmendî hatiye şêlkirin di saksiyeke seramîkî ya destçêker de tê pêşkêşkirin. Xwezayê bînin nav cîhê jiyana xwe.</p>'],
                'price' => 849.00,
                'sale_price' => null,
                'category' => 'saksi-cicekleri',
                'is_featured' => false,
                'is_new' => false,
                'stock_status' => 'in_stock',
                'tags' => ['luks'],
                'variants' => [],
            ],
        ];

        foreach ($products as $i => $data) {
            $category = $categories->get($data['category']);
            if (! $category) {
                continue;
            }

            $product = Product::updateOrCreate(
                ['slug' => $data['slug']],
                [
                    'name' => $data['name'],
                    'slug' => $data['slug'],
                    'short_description' => $data['short_description'],
                    'description' => $data['description'],
                    'sku' => 'RG-'.str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                    'price' => $data['price'],
                    'sale_price' => $data['sale_price'],
                    'stock_status' => $data['stock_status'],
                    'status' => 'active',
                    'is_featured' => $data['is_featured'],
                    'is_new' => $data['is_new'],
                    'view_count' => 50,
                    'sort_order' => $i + 1,
                ]
            );

            $product->images()->delete();
            ProductImage::query()->create([
                'product_id' => $product->id,
                'image_path' => $this->productDemoImageUrl($data['slug']),
                'alt_text' => is_array($data['name']) ? ($data['name']['tr'] ?? null) : null,
                'is_primary' => true,
                'sort_order' => 1,
            ]);

            $product->categories()->syncWithoutDetaching([$category->id]);

            $tagSlugs = $data['tags'] ?? [];
            $tagIds = Tag::whereIn('slug', $tagSlugs)->pluck('id');
            $product->tags()->syncWithoutDetaching($tagIds);

            foreach ($data['variants'] as $vi => $variant) {
                ProductVariant::firstOrCreate(
                    ['product_id' => $product->id, 'name->tr' => $variant['name']['tr']],
                    [
                        'product_id' => $product->id,
                        'name' => $variant['name'],
                        'price' => $variant['price'],
                        'stock_status' => 'in_stock',
                        'sort_order' => $vi + 1,
                        'is_active' => true,
                    ]
                );
            }
        }

        $bestSellerSlugs = [
            'silindir-kutuda-kirmizi-guller',
            'kirmizi-gul-buketi-21-adet',
            'el-yapimi-belcika-cikolata-kutusu',
            'romantik-hediye-seti',
            'kare-kutuda-renkli-guller',
            'karma-mevsim-buketi',
            'cikolata-kapli-cilek-buketi',
            'bahar-ruzgari-aranjmani',
        ];
        $rank = 9800;
        foreach ($bestSellerSlugs as $slug) {
            Product::where('slug', $slug)->update(['view_count' => $rank]);
            $rank -= 520;
        }
    }

    private function productDemoImageUrl(string $slug): string
    {
        return 'storage/products/'.$slug.'.jpg';
    }

    private function blogFeaturedImageUrl(string $slug): string
    {
        $candidates = StorefrontIncomingAssets::blogCoverProductSlugs($slug);
        $preferred = StorefrontIncomingAssets::preferredStorageProductsPath($candidates);

        return $preferred ?? $this->productDemoImageUrl('lavanta-bahar-karisik-buket');
    }

    private function seedBlog(?User $admin): void
    {
        $blogCategories = [
            ['name' => ['tr' => 'Çiçek Bakımı',  'en' => 'Flower Care',    'ku' => 'Lênêrîna Kulilkan'], 'slug' => 'cicek-bakimi',   'sort_order' => 1],
            ['name' => ['tr' => 'Hediye Rehberi', 'en' => 'Gift Guide',     'ku' => 'Rêbera Diyariyê'],   'slug' => 'hediye-rehberi', 'sort_order' => 2],
            ['name' => ['tr' => 'Özel Günler',   'en' => 'Special Days',   'ku' => 'Rojên Taybet'],       'slug' => 'ozel-gunler',    'sort_order' => 3],
        ];

        foreach ($blogCategories as $cat) {
            BlogCategory::firstOrCreate(
                ['slug' => $cat['slug']],
                ['name' => $cat['name'], 'slug' => $cat['slug'], 'sort_order' => $cat['sort_order']]
            );
        }

        $posts = [
            [
                'title' => ['tr' => 'Kesme Çiçeklerin Ömrünü Uzatmanın 7 Yolu', 'en' => '7 Ways to Extend the Life of Cut Flowers', 'ku' => '7 Rêbaz ji bo Dirêjkirina Jiyana Kulilkên Birî'],
                'slug' => 'kesme-ciceklerin-omrunu-uzatmanin-7-yolu',
                'featured_image' => $this->blogFeaturedImageUrl('kesme-ciceklerin-omrunu-uzatmanin-7-yolu'),
                'excerpt' => ['tr' => 'Sevdiklerinizden aldığınız çiçeklerin daha uzun süre taze kalmasını ister misiniz? İşte uzmanlardan 7 pratik öneri.', 'en' => 'Do you want the flowers you receive from your loved ones to stay fresh longer? Here are 7 practical tips from experts.', 'ku' => 'Hûn dixwazin ku kulilkên ku ji hezkiriyên we distînin dirêjtir taze bimînin? Li vir 7 pêşniyarên pratîkî ji pisporên ne.'],
                'content' => ['tr' => '<p>Çiçek almak kadar onu uzun süre taze tutmak da önemlidir. İşte kesme çiçeklerinizin ömrünü uzatmak için uygulayabileceğiniz basit ama etkili yöntemler:</p><h3>1. Sapları Eğik Kesin</h3><p>Çiçek saplarını 45 derece açıyla keserek su emilim yüzeyini artırın.</p><h3>2. Vazo Suyunu Her Gün Değiştirin</h3><p>Temiz su, bakteri oluşumunu önler ve çiçeklerin daha uzun süre taze kalmasını sağlar.</p><h3>3. Doğrudan Güneş Işığından Kaçının</h3><p>Dolaylı ışık alan serin bir yere koyun.</p><h3>4. Çiçek Besini Kullanın</h3><p>Paket içinde gelen çiçek besini tozu gerçekten işe yarar.</p><h3>5. Yaprakları Sudan Uzak Tutun</h3><p>Su altında kalan yapraklar çürüyerek suyu kirletir.</p><h3>6. Meyve ve Sebzelerden Uzak Tutun</h3><p>Olgunlaşan meyveler etilen gazı salarak çiçeklerin solmasını hızlandırır.</p><h3>7. Gece Serin Yere Taşıyın</h3><p>Gece serin bir ortam, çiçeklerin metabolizmasını yavaşlatarak ömrünü uzatır.</p>', 'en' => '<p>Keeping flowers fresh for a long time is as important as buying them. Here are simple but effective methods you can apply to extend the life of your cut flowers:</p><h3>1. Cut Stems at an Angle</h3><p>Increase water absorption surface by cutting flower stems at a 45-degree angle.</p><h3>2. Change Vase Water Daily</h3><p>Clean water prevents bacterial growth and keeps flowers fresh longer.</p><h3>3. Avoid Direct Sunlight</h3><p>Place in a cool location with indirect light.</p><h3>4. Use Flower Food</h3><p>The flower food powder that comes in the package really works.</p><h3>5. Keep Leaves Out of Water</h3><p>Leaves submerged in water rot and contaminate the water.</p><h3>6. Keep Away from Fruits and Vegetables</h3><p>Ripening fruits release ethylene gas which accelerates flower wilting.</p><h3>7. Move to a Cool Place at Night</h3><p>A cool environment at night slows down flower metabolism and extends life.</p>', 'ku' => '<p>Domandina kulilkan bi dirêjî taze bi qasî kirrîna wan girîng e. Li vir rêbazên sade lê bi bandor ên ku hûn dikarin ji bo dirêjkirina jiyana kulilkên xwe yên birî bicîh bînin:</p><h3>1. Kulmên bi Goşeyê Birin</h3><p>Rûyê şirandinê zêde bikin bi birina kulmên kulilkê bi goşeyê 45 pileyî.</p>'],
                'category' => 'cicek-bakimi',
                'status' => 'published',
                'published_at' => now()->subDays(5),
            ],
            [
                'title' => ['tr' => 'Anneler Günü İçin En İyi Çiçek Seçenekleri', 'en' => 'Best Flower Options for Mother\'s Day', 'ku' => 'Bijarteyên Herî Baş ên Kulilkan ji bo Roja Dêyan'],
                'slug' => 'anneler-gunu-icin-en-iyi-cicek-secenekleri',
                'featured_image' => $this->blogFeaturedImageUrl('anneler-gunu-icin-en-iyi-cicek-secenekleri'),
                'excerpt' => ['tr' => 'Anneler Günü yaklaşırken, annenize en anlamlı çiçeği seçmenize yardımcı olacak rehberimizi keşfedin.', 'en' => 'As Mother\'s Day approaches, discover our guide to help you choose the most meaningful flower for your mother.', 'ku' => 'Roja Dêyan nêz dibe, rêbera me ya ku dê ji we re bikarê bijartina kulilka herî manîdar a ji dêya xwe re dike keşf bikin.'],
                'content' => ['tr' => '<p>Anneler Günü, annelerimize olan sevgimizi ve minnettarlığımızı ifade etmek için en güzel fırsatlardan biridir. İşte anneniz için en anlamlı çiçek seçenekleri:</p><h3>Karanfiller</h3><p>Anneler Günü\'nün geleneksel çiçeği olan karanfiller, saf sevgiyi ve minnettarlığı simgeler.</p><h3>Pembe Güller</h3><p>Zarif pembe güller, incelik ve şükran duygularını ifade eder.</p><h3>Orkideler</h3><p>Uzun ömürlü orkideler, annenize uzun süre eşlik edecek şık bir hediyedir.</p><h3>Lavantalar</h3><p>Ferahlatıcı kokusuyla lavantalar, rahatlatıcı ve huzur verici bir hediye seçeneğidir.</p>', 'en' => '<p>Mother\'s Day is one of the most beautiful opportunities to express our love and gratitude to our mothers. Here are the most meaningful flower options for your mother:</p><h3>Carnations</h3><p>Carnations, the traditional flower of Mother\'s Day, symbolize pure love and gratitude.</p><h3>Pink Roses</h3><p>Elegant pink roses express grace and feelings of gratitude.</p><h3>Orchids</h3><p>Long-lived orchids are an elegant gift that will accompany your mother for a long time.</p><h3>Lavenders</h3><p>Lavenders with their refreshing scent are a relaxing and peaceful gift option.</p>', 'ku' => '<p>Roja Dêyan yek ji fırsatên herî bedew e ku evîn û spasdariya xwe ya ji dêyên xwe re îfade bikin. Li vir bijarteyên kulilkan ên herî manîdar ji bo dêya we:</p><h3>Qaranfil</h3><p>Qaranfil, kulilka kevneşopî ya Roja Dêyan, evîna pak û spasdariyê sembolize dike.</p>'],
                'category' => 'hediye-rehberi',
                'status' => 'published',
                'published_at' => now()->subDays(3),
            ],
            [
                'title' => ['tr' => 'Çiçek Diliyle Duygularınızı Anlatın', 'en' => 'Express Your Feelings Through the Language of Flowers', 'ku' => 'Hestên Xwe bi Zimanê Kulilkan Îfade Bikin'],
                'slug' => 'cicek-diliyle-duygularinizi-anlatin',
                'featured_image' => $this->blogFeaturedImageUrl('cicek-diliyle-duygularinizi-anlatin'),
                'excerpt' => ['tr' => 'Her çiçeğin bir anlamı var. Doğru çiçeği seçerek mesajınızı sözsüz iletin.', 'en' => 'Every flower has a meaning. Choose the right flower and convey your message without words.', 'ku' => 'Her kulilkek manayekê heye. Kulilka rast hilbijêrin û peyama xwe bê peyv bigihînin.'],
                'content' => ['tr' => '<p>Çiçekler yüzyıllardır duyguları ifade etmek için kullanılmaktadır. İşte en popüler çiçekler ve anlamları:</p><h3>Kırmızı Gül — Aşk ve Tutku</h3><p>Tartışmasız en bilinen çiçek sembolü. Derin aşk ve tutkuyu ifade eder.</p><h3>Beyaz Gül — Masumiyet ve Saflık</h3><p>Yeni başlangıçlar ve saf niyetleri simgeler.</p><h3>Sarı Gül — Dostluk ve Neşe</h3><p>Arkadaşlık ve mutluluğu temsil eder.</p><h3>Papatya — Sadelik ve Sadakat</h3><p>İçtenlik ve sadık sevgiyi ifade eder.</p><h3>Lale — Mükemmel Aşk</h3><p>Özellikle pembe ve kırmızı laleler derin bir aşk beyanıdır.</p>', 'en' => '<p>Flowers have been used to express feelings for centuries. Here are the most popular flowers and their meanings:</p><h3>Red Rose — Love and Passion</h3><p>Undoubtedly the most well-known flower symbol. It expresses deep love and passion.</p><h3>White Rose — Innocence and Purity</h3><p>Symbolizes new beginnings and pure intentions.</p><h3>Yellow Rose — Friendship and Joy</h3><p>Represents friendship and happiness.</p><h3>Daisy — Simplicity and Loyalty</h3><p>Expresses sincerity and faithful love.</p><h3>Tulip — Perfect Love</h3><p>Especially pink and red tulips are a declaration of deep love.</p>', 'ku' => '<p>Kulilk ji sedsalan ve tên bikaranîn ji bo îfadekrina hestan. Li vir kulilkên herî populer û manayên wan:</p><h3>Gula Sor — Evîn û Heyecan</h3><p>Bêguman sembolê kulilkê yê herî naskirî. Evîn û heyecana kûr îfade dike.</p>'],
                'category' => 'ozel-gunler',
                'status' => 'published',
                'published_at' => now()->subDays(1),
            ],
            [
                'title' => ['tr' => 'Saksı Çiçeği Bakım Rehberi: Orkide', 'en' => 'Potted Flower Care Guide: Orchid', 'ku' => 'Rêbera Lênêrîna Kulilka Saksiyê: Orkîde'],
                'slug' => 'saksi-cicegi-bakim-rehberi-orkide',
                'featured_image' => $this->blogFeaturedImageUrl('saksi-cicegi-bakim-rehberi-orkide'),
                'excerpt' => ['tr' => 'Orkideniz solmuş mu? Panik yapmayın! Bu rehberle orkidenizi tekrar çiçek açtırabilirsiniz.', 'en' => 'Has your orchid wilted? Don\'t panic! With this guide you can get your orchid to bloom again.', 'ku' => 'Orkîdeya we qelibî ye? Xemgîn nebin! Bi vê rêberê hûn dikarin orkîdeya xwe ji nû ve bikin kulilk veke.'],
                'content' => ['tr' => '<p>Orkideler zarif görünümlerine rağmen bakımı nispeten kolay bitkilerdir. İşte orkidenizin uzun yıllar çiçek açması için bilmeniz gerekenler:</p><h3>Sulama</h3><p>Haftada bir kez, saksının altından su akana kadar sulayın. Fazla suyun birikmesine izin vermeyin.</p><h3>Işık</h3><p>Dolaylı, parlak ışık idealdir. Doğrudan güneş ışığı yaprakları yakabilir.</p><h3>Sıcaklık</h3><p>18-25°C arası idealdir. Gece-gündüz sıcaklık farkı çiçeklenmeyi teşvik eder.</p><h3>Gübre</h3><p>Ayda bir orkide gübresi ile besleyin. Çiçeklenme döneminde haftada bir uygulayabilirsiniz.</p>', 'en' => '<p>Despite their elegant appearance, orchids are relatively easy plants to care for. Here is what you need to know to keep your orchid blooming for many years:</p><h3>Watering</h3><p>Water once a week, until water flows out from the bottom of the pot. Do not allow excess water to accumulate.</p><h3>Light</h3><p>Indirect, bright light is ideal. Direct sunlight can burn the leaves.</p><h3>Temperature</h3><p>18-25°C is ideal. Day-night temperature differences encourage blooming.</p><h3>Fertilizer</h3><p>Feed monthly with orchid fertilizer. You can apply weekly during the blooming period.</p>', 'ku' => '<p>Tevî xuyanga spehî ya wan, orkîde nebatên ku lênêrîna wan nisbeten hêsan e ne. Li vir ya ku hûn hewce ne zanibin ji bo ku orkîdeya xwe salên dirêj bike kulilk veke:</p><h3>Avdan</h3><p>Hefteyê carek, heya ku av ji binê saksiyê dest pê bike av bide. Nehêlin av zêde berhev bibe.</p>'],
                'category' => 'cicek-bakimi',
                'status' => 'published',
                'published_at' => now()->subHours(12),
            ],
        ];

        foreach ($posts as $post) {
            $cat = BlogCategory::where('slug', $post['category'])->first();

            BlogPost::updateOrCreate(
                ['slug' => $post['slug']],
                [
                    'title' => $post['title'],
                    'slug' => $post['slug'],
                    'excerpt' => $post['excerpt'],
                    'content' => $post['content'],
                    'featured_image' => $post['featured_image'],
                    'blog_category_id' => $cat?->id,
                    'author_id' => $admin?->id,
                    'status' => $post['status'],
                    'view_count' => rand(20, 500),
                    'published_at' => $post['published_at'],
                ]
            );
        }
    }

    private function seedCoupons(): void
    {
        Coupon::firstOrCreate(
            ['code' => 'HOSGELDIN'],
            [
                'code' => 'HOSGELDIN',
                'type' => 'percentage',
                'value' => 10.00,
                'min_order_amount' => 300.00,
                'max_uses' => 1000,
                'max_uses_per_user' => 1,
                'used_count' => 0,
                'starts_at' => now(),
                'expires_at' => now()->addMonths(3),
                'is_active' => true,
            ]
        );

        Coupon::firstOrCreate(
            ['code' => 'BAHAR50'],
            [
                'code' => 'BAHAR50',
                'type' => 'fixed_amount',
                'value' => 50.00,
                'min_order_amount' => 500.00,
                'max_uses' => 500,
                'max_uses_per_user' => 2,
                'used_count' => 0,
                'starts_at' => now(),
                'expires_at' => now()->addMonth(),
                'is_active' => true,
            ]
        );
    }

    private function seedPages(): void
    {
        $pages = [
            // Hakkımızda, yasal ve iletişim sayfaları CustomerContentSeeder ile doldurulur (yer tutucu metin DB'ye yazılmasın).
            [
                'title' => ['tr' => 'Teslimat Bilgileri', 'en' => 'Delivery Information', 'ku' => 'Agahiyên Radestkirinê'],
                'slug' => 'teslimat-bilgileri',
                'content' => [
                    'tr' => '<h2>Teslimat Bilgileri</h2><h3>Teslimat Bölgeleri</h3><p>Adıyaman il merkezi ve yakın ilçelere teslimat yapılmaktadır.</p><h3>Teslimat Saatleri</h3><p>Siparişler 09:00 - 20:00 saatleri arasında teslim edilir. Saat aralığı seçimi yapabilirsiniz.</p><h3>Teslimat Ücreti</h3><p>Tüm teslimatlarımız ücretsizdir.</p><h3>Aynı Gün Teslimat</h3><p>Saat 16:00\'ya kadar verilen siparişler aynı gün teslim edilir.</p>',
                    'en' => '<h2>Delivery Information</h2><h3>Delivery Areas</h3><p>Delivery is made to Adıyaman city center and nearby districts.</p><h3>Delivery Hours</h3><p>Orders are delivered between 09:00 - 20:00. You can select a time slot.</p><h3>Delivery Fee</h3><p>All our deliveries are free of charge.</p><h3>Same Day Delivery</h3><p>Orders placed before 16:00 are delivered the same day.</p>',
                    'ku' => '<h2>Agahiyên Radestkirinê</h2><h3>Herêmên Radestkirinê</h3><p>Radestkirin ji navenda parêzgeha Adiyamanê û navçeyên nêzê tê kirin.</p><h3>Saetên Radestkirinê</h3><p>Siparîş di navbera 09:00 - 20:00 de tên radest kirin.</p><h3>Mûçeya Radestkirinê</h3><p>Hemû radestkirinên me belaş in.</p>',
                ],
                'is_published' => true,
                'sort_order' => 2,
            ],
            [
                'title' => ['tr' => 'SSS — Sıkça Sorulan Sorular', 'en' => 'FAQ — Frequently Asked Questions', 'ku' => 'PSP — Pirsên Serî Pirsîn'],
                'slug' => 'sss',
                'content' => [
                    'tr' => '<h2>Sıkça Sorulan Sorular</h2><h3>Sipariş verdikten sonra iptal edebilir miyim?</h3><p>Hazırlık aşamasına geçmemiş siparişler iptal edilebilir. Lütfen bize ulaşın.</p><h3>Çiçekler ne kadar taze kalır?</h3><p>Doğru bakımla kesme çiçekler 5-7 gün taze kalır. Saksı çiçekleri çok daha uzun ömürlüdür.</p><h3>Hediye kartı ekleyebilir miyim?</h3><p>Evet! Sipariş sırasında kart mesajınızı girebilirsiniz. Özenle yazılıp bukete eklenir.</p><h3>Paraçiçek puanlarım nasıl kullanılır?</h3><p>Hesabınızdaki puanları bir sonraki siparişinizde indirim olarak kullanabilirsiniz. Her 1000 TL alışverişte 50 TL, her 2000 TL alışverişte 100 TL puan kazanırsınız.</p>',
                    'en' => '<h2>Frequently Asked Questions</h2><h3>Can I cancel after placing an order?</h3><p>Orders that have not entered the preparation stage can be cancelled. Please contact us.</p><h3>How long do flowers stay fresh?</h3><p>With proper care, cut flowers stay fresh for 5-7 days. Potted flowers last much longer.</p><h3>Can I add a gift card?</h3><p>Yes! You can enter your card message during the order process. It is carefully written and added to the bouquet.</p><h3>How are Paraçiçek points used?</h3><p>You can use the points in your account as a discount on your next order. You earn 50 TL for every 1000 TL purchase, 100 TL for every 2000 TL purchase.</p>',
                    'ku' => '<h2>Pirsên Serî Pirsîn</h2><h3>Ma ez dikarim piştî dayîna siparîşê betal bikim?</h3><p>Siparîşên ku neketine qonaxa amadehiyê dikarin bên betal kirin. Ji kerema xwe bi me re têkilî daynin.</p><h3>Kulilk çend demjimêr taze dimînin?</h3><p>Bi lênêrîna rast, kulilkên birî 5-7 rojan taze dimînin. Kulilkên saksiyê pir dirêjtir dijîn.</p>',
                ],
                'is_published' => true,
                'sort_order' => 8,
            ],
        ];

        foreach ($pages as $page) {
            Page::firstOrCreate(
                ['slug' => $page['slug']],
                $page
            );
        }
    }
}
