<?php

namespace App\Data;

/**
 * Görsel dosya adı → ürün tanımı (products:import-incoming).
 * Canlı vitrin kaynağı veritabanıdır; bu sınıf yalnızca toplu içe aktarma için varsayılan katalogdur.
 * Üretimde `storage/app/product-import/catalog.json` kullanılırsa kod değişmeden katalog güncellenir.
 * Fiyatlar tahmini başlangıç değerleridir; panelden güncellenmelidir.
 */
final class ProductIncomingDefinitions
{
    /**
     * @return array<string, array{slug:string,category_slug:string,price:float,tags:list<string>,name:array{tr:string,en:string,ku:string},short_description:array{tr:string,en:string,ku:string},description:array{tr:string,en:string,ku:string},delivery_note:array{tr:string,en:string,ku:string},meta_title:array{tr:string,en:string,ku:string},meta_description:array{tr:string,en:string,ku:string},is_featured?:bool}>
     */
    public static function catalog(): array
    {
        return array_merge(self::bouquets(), self::pottedPlants());
    }

    private static function t(string $tr, string $en, string $ku): array
    {
        return ['tr' => $tr, 'en' => $en, 'ku' => $ku];
    }

    /**
     * @param  list<string>  $tags
     * @return array{slug:string,category_slug:string,price:float,tags:list<string>,name:array{tr:string,en:string,ku:string},short_description:array{tr:string,en:string,ku:string},description:array{tr:string,en:string,ku:string},delivery_note:array{tr:string,en:string,ku:string},meta_title:array{tr:string,en:string,ku:string},meta_description:array{tr:string,en:string,ku:string},is_featured?:bool}
     */
    private static function bouquet(
        string $slug,
        float $price,
        array $tags,
        array $name,
        array $short,
        array $desc,
        array $delivery,
        array $metaTitle,
        array $metaDesc,
        bool $featured = false
    ): array {
        $row = [
            'slug' => $slug,
            'category_slug' => 'cicek-buketleri',
            'price' => $price,
            'tags' => $tags,
            'name' => $name,
            'short_description' => $short,
            'description' => $desc,
            'delivery_note' => $delivery,
            'meta_title' => $metaTitle,
            'meta_description' => $metaDesc,
        ];
        if ($featured) {
            $row['is_featured'] = true;
        }

        return $row;
    }

    /**
     * @param  list<string>  $tags
     * @return array{slug:string,category_slug:string,price:float,tags:list<string>,name:array{tr:string,en:string,ku:string},short_description:array{tr:string,en:string,ku:string},description:array{tr:string,en:string,ku:string},delivery_note:array{tr:string,en:string,ku:string},meta_title:array{tr:string,en:string,ku:string},meta_description:array{tr:string,en:string,ku:string}}
     */
    private static function plant(
        string $slug,
        float $price,
        array $tags,
        array $name,
        array $short,
        array $desc,
        array $delivery,
        array $metaTitle,
        array $metaDesc
    ): array {
        return [
            'slug' => $slug,
            'category_slug' => 'saksi-cicekleri',
            'price' => $price,
            'tags' => $tags,
            'name' => $name,
            'short_description' => $short,
            'description' => $desc,
            'delivery_note' => $delivery,
            'meta_title' => $metaTitle,
            'meta_description' => $metaDesc,
        ];
    }

    /**
     * @return array<string, array>
     */
    private static function pottedPlants(): array
    {
        $d = self::t(
            'Saksı bitkilerinde aynı gün veya randevulu teslimat için ekibimizle iletişime geçebilirsiniz.',
            'Same-day or scheduled delivery available for potted plants in Adıyaman center.',
            'Ji bo nebatên saksî radestî di heman rojê an bi demnameyê.'
        );

        $rows = [
            ['2li-beyaz-orkide-çiçekçi.png', '2li-beyaz-orkide', 3890, self::t('2 Dal Beyaz Orkide', 'White Phalaenopsis (2 stems)', '2 dal orkîdeya spî'), ['Özel Gün', 'Lüks']],
            ['2li-marginata-adiyaman-çiçekçi.png', '2li-dracaena-marginata-a', 1890, self::t('2 Gövdeli Dracaena Marginata', 'Dracaena marginata (twin stem)', 'Dracaena marginata du govde'), ['Mevsimlik']],
            ['2li-marginata-adiyaman-çiçekçisi.png', '2li-dracaena-marginata-b', 1890, self::t('2 Gövdeli Dracaena Marginata (Özel Seçim)', 'Dracaena marginata twin stem (select)', 'Dracaena marginata taybet'), ['Mevsimlik']],
            ['2li-masengene-adıyaman-çiçekçisi.png', '2li-dracaena-massangeana', 2290, self::t('2 Gövdeli Dracaena Massangeana', 'Corn plant (2 stems)', 'Dracaena massangeana du govde'), ['Mevsimlik']],
            ['2li-mavi-orkide-adıyaman-tasarım.png', '2li-mavi-orkide-tasarim', 4290, self::t('2 Dal Mavi Orkide Tasarım', 'Blue dyed orchid design (2 stems)', 'Orkîdeya şîn 2 dal'), ['Özel Gün', 'Lüks']],
            ['2li-mor-orkide-adiyaman-cicekcisi.png', '2li-mor-orkide-a', 3990, self::t('2 Dal Mor Orkide', 'Purple Phalaenopsis (2 stems)', 'Orkîdeya mor 2 dal'), ['Romantik', 'Lüks']],
            ['2li-mor-orkide-adiyamana-çiçek.png', '2li-mor-orkide-b', 3950, self::t('2 Dal Mor Orkide Hediye', 'Purple orchid gift (2 stems)', 'Diyariya orkîdeya mor'), ['Romantik']],
            ['2li-pembe-orkide-doğum-günü-çiçek.png', '2li-pembe-orkide-dogum-gunu', 4190, self::t('2 Dal Pembe Orkide', 'Pink Phalaenopsis (2 stems)', 'Orkîdeya pembe 2 dal'), ['Doğum Günü', 'Özel Gün']],
            ['2li-yukka-adıyamanın-çiçekçisi.png', '2li-yukka', 2390, self::t('2 Gövdeli Yukka', 'Yucca (twin stem)', 'Yucca du govde'), ['Mevsimlik']],
            ['2li-şeflera-adiyaman-çiçek.png', '2li-seflera', 2140, self::t('2 Gövdeli Şeflera', 'Schefflera (umbrella tree)', 'Schefflera du govde'), ['Mevsimlik']],
            ['3lü-massengena-en-hızlı-çiçekçi.png', '3lu-dracaena-massangeana', 3190, self::t('3 Gövdeli Dracaena Massangeana', 'Corn plant (triple stem)', 'Dracaena sê govde'), ['Lüks', 'Mevsimlik']],
            ['areka-en-uygun-çiçekçi-adıyaman-merkez.png', 'areka-palm-merkez', 2790, self::t('Areca Palm (Merkez)', 'Areca palm — centerpiece', 'Areca palm navend'), ['Mevsimlik']],
            ['areka-en-uygun-çiçekçi-adıyaman.png', 'areka-palm-standart', 2690, self::t('Areca Palm', 'Areca palm indoor', 'Areca palm'), ['Mevsimlik', 'Ekonomik']],
            ['Başlıksız-1.png', 'orgulu-benjamin-ficus-premium', 3290, self::t('Örgülü Benjamin Ficus (Seramik)', 'Braided Ficus benjamina in ceramic', 'Ficusê benjaminê girêdayî'), ['Lüks', 'Mevsimlik']],
            ['benjamin-adıyaman-tasarım.png', 'benjamin-ficus-saksi', 1490, self::t('Benjamin Ficus Saksı', 'Ficus benjamina potted', 'Ficus benjamina'), ['Mevsimlik', 'Ekonomik']],
            ['büyük-tropik-adıyaman-rose-garden.png', 'buyuk-tropik-bitki', 4590, self::t('Büyük Tropik Yeşil Bitki', 'Large tropical foliage plant', 'Nebata tropîkî mezin'), ['Lüks']],
            ['dalmaçyalı-orkide-ucuz-çiçekçi.png', 'dalmacyali-orkide-2dal', 3790, self::t('Dalmaçyalı Orkide (2 Dal)', 'Cymbidium-style orchid (2 stems)', 'Orkîdeya Dalmaçyayî'), ['Özel Gün']],
            ['guzmanya-adıyamana-çiçek-gönder.png', 'guzmanya-saksi', 1690, self::t('Guzmania Saksı Çiçeği', 'Guzmania bromeliad', 'Guzmania'), ['Mevsimlik', 'Renkli']],
            ['ithal-antoryum-adıyaman-butik-çiçekçisi.png', 'ithal-antoryum-saksi', 920, self::t('İthal Antoryum Saksı', 'Anthurium import pot', 'Anthurium'), ['Mevsimlik']],
            ['orta-boy-parış-çiçeği-adıyaman-çiçekçisi.png', 'orta-boy-mevsim-saksi', 780, self::t('Orta Boy Mevsim Saksısı', 'Mid seasonal flowering pot', 'Saksiya demsal navîn'), ['Mevsimlik', 'Ekonomik']],
            ['patos-adıyaman-gölbaşına-çiçek-gönder.png', 'patos-sarmaşık-saksi', 540, self::t('Patos (Sarmaşık) Saksı', 'Pothos trailing pot', 'Pothos'), ['Mevsimlik', 'Ekonomik']],
            ['sklamen-en-ucuz-çiçekçi-adıyaman.png', 'siklamen-saksi', 690, self::t('Siklamen Saksı', 'Cyclamen pot', 'Cyclamen'), ['Mevsimlik', 'Ekonomik']],
            ['tekli-yukka-adıyaman-çiçek.png', 'tekli-yukka', 1240, self::t('Tek Gövde Yukka', 'Single stem Yucca', 'Yucca yek govde'), ['Mevsimlik']],
            ['örgülü-patos-adıyaman-çiçekçisi.png', 'orgulu-patos', 790, self::t('Örgülü Patos', 'Braided Pothos pole', 'Pothosê girêdayî'), ['Mevsimlik']],
        ];

        $out = [];
        foreach ($rows as $r) {
            [$file, $slug, $price, $name, $tags] = $r;
            $shortTr = $name['tr'].' — salon ve ofis için canlı saksı teslimatı.';
            $descTr = '<p>'.e($name['tr']).' taze ve özenle hazırlanmış saksıda sunulur. Işık ve sulama önerileri için sipariş sonrası ekibimizden bilgi alabilirsiniz.</p>';
            $out[$file] = self::plant(
                $slug,
                (float) $price,
                $tags,
                $name,
                self::t($shortTr, $name['en'].' — potted, ready for home or office.', $name['ku'].' — di saksî de.'),
                self::t($descTr, '<p>Potted plant, freshly prepared. Ask our team for light and watering tips.</p>', '<p>Nebata saksî, nûjen hatiye amadekirin.</p>'),
                $d,
                self::t($name['tr'].' | Rose Garden', $name['en'].' | Rose Garden', $name['ku'].' | Rose Garden'),
                self::t(
                    'Adıyaman Rose Garden’da '.$name['tr'].' — aynı gün teslimat seçenekleri.',
                    'Rose Garden Adıyaman — '.$name['en'].', delivery options.',
                    'Rose Garden — '.$name['ku']
                )
            );
        }

        return $out;
    }

    /**
     * @return array<string, array>
     */
    private static function bouquets(): array
    {
        $d = self::t(
            'Çiçek ürünlerinde teslimat saati ve adres teyidi sipariş sırasında alınır.',
            'Delivery time and address are confirmed at checkout.',
            'Dem û navnîşana radestî dema siparîşê tê pejirandin.'
        );

        return [
            '04b77ec1-1cbc-499b-95b3-8f8b12ca1b49.jpg' => self::bouquet(
                'rustik-kirmizi-gul-pamuk-hediye-buket',
                1349,
                ['Romantik', 'Güller', 'Lüks', 'Özel Gün'],
                self::t('Rustik Kırmızı Gül Buketi — Pamuk ve Hediyeli', 'Rustic red roses with cotton & gifts', 'Gulên sor rustîk'),
                self::t('Kırmızı güller, pamuk, kurutulmuş portakal ve minik pelüşlerle özel ambalaj.', 'Red roses, cotton bolls, dried citrus, plush accents.', 'Gul, pamûk û diyari'),
                self::t('<p>Kraft ambalajda yoğun kırmızı gül kompozisyonu; pamuk ve kuru portakal dilimleriyle sıcak bir hediye anlatımı. İçinde küçük pelüş aksesuarlar bulunabilir.</p>', '<p>Luxury kraft wrap, red roses, cotton, dried orange, plush toys.</p>', '<p>Deste gulên sor bi pamûkê.</p>'),
                $d,
                self::t('Rustik Kırmızı Gül Buketi | Rose Garden', 'Rustic red rose bouquet | Rose Garden', 'Deste gul | Rose Garden'),
                self::t('Pamuk ve kuru meyve detaylı romantik buket. Rose Garden Adıyaman.', 'Romantic bouquet with cotton accents. Rose Garden.', 'Rose Garden.'),
                true
            ),
            '0e3a0b85-713d-465f-8a70-92f76d86d109.jpg' => self::bouquet(
                'mor-ruya-karisik-buket',
                920,
                ['Mevsimlik', 'Doğum Günü'],
                self::t('Mor Rüya Karışık Buket', 'Purple dream mixed bouquet', 'Deste rengîn mor'),
                self::t('Beyaz örümcek krizantem, ayçiçeği, lilya ve mor ambalaj.', 'Spider mums, sunflower, lily buds, purple wrap.', 'Krizantem û ayçiçekê.'),
                self::t('<p>Mor desenli ambalajda beyaz krizantem, sarı ayçiçeği ve lilya tomurcuklarıyla neşeli karışık buket.</p>', '<p>Mixed bouquet in purple floral wrap.</p>', '<p>Deste tevlîhev.</p>'),
                $d,
                self::t('Mor Rüya Karışık Buket | Rose Garden', 'Purple dream bouquet | Rose Garden', 'Deste | Rose Garden'),
                self::t('Krizantem, ayçiçeği ve lilya karışımı. Adıyaman çiçekçi.', 'Fresh mixed bouquet.', 'Rose Garden.'),
                true
            ),
            '130b2f86-878e-4594-9c6c-1d6a8cc8ca45.jpg' => self::bouquet(
                'lavanta-pembe-sprey-gul-buket',
                780,
                ['Romantik', 'Mevsimlik'],
                self::t('Lavanta ve Pembe Sprey Gül Buketi', 'Lavender & pink spray rose bouquet', 'Gulên pembe û mor'),
                self::t('Lavanta tonlarında krizantem, sıcak pembe sprey güller ve cipso.', 'Lavender mums, hot pink spray roses, baby’s breath.', 'Krizantem û gul.'),
                self::t('<p>Krem-sarı ambalajda yumuşak lavanta krizantemler, pembe sprey güller ve beyaz cipso ile zarif buket.</p>', '<p>Soft pastel hand-tied bouquet.</p>', '<p>Deste nazik.</p>'),
                $d,
                self::t('Lavanta Pembe Buket | Rose Garden', 'Lavender pink bouquet | Rose Garden', 'Deste | Rose Garden'),
                self::t('Romantik pastel tonlar, el bağlama. Rose Garden.', 'Pastel romantic bouquet.', 'Rose Garden.')
            ),
            '139aefd3-f94f-444e-997f-90746ae544bf.jpg' => self::bouquet(
                'sonbahar-mega-karisik-kraft-buket',
                1180,
                ['Lüks', 'Doğum Günü', 'Özel Gün'],
                self::t('Sonbahar Mega Karışık Kraft Buket', 'Autumn mega mixed kraft bouquet', 'Deste mezin tevlîhev'),
                self::t('Şeftali ve mor güller, ayçiçeği, lilya, kraft ambalaj ve kırmızı kurdele.', 'Peach & purple roses, sunflower, lilies, kraft wrap.', 'Gul û lîlya.'),
                self::t('<p>Kraft kağıtta geniş hacimli karışık buket; gül, lilya, ayçiçeği ve mevsim dolguları. Korsaj detayı ve kırmızı kurdele ile tamamlanır.</p>', '<p>Large mixed bouquet in kraft paper.</p>', '<p>Deste mezin.</p>'),
                $d,
                self::t('Mega Karışık Buket | Rose Garden', 'Mega mixed bouquet | Rose Garden', 'Deste | Rose Garden'),
                self::t('Özel günler için gösterişli karışık çiçek. Rose Garden Adıyaman.', 'Statement mixed bouquet.', 'Rose Garden.'),
                true
            ),
            '198762ab-f473-47c2-98f9-d7c92ef1c259.jpg' => self::bouquet(
                'premium-kirmizi-gul-cipso-kubbe',
                2240,
                ['Güller', 'Romantik', 'Lüks', 'Yıldönümü'],
                self::t('Premium Kırmızı Gül ve Cipso Kubbe Buket', 'Premium red rose & baby’s breath dome', 'Gulên sor premium'),
                self::t('Yoğun kırmızı gül kubbesi, beyaz cipso halkası, siyah şık ambalaj.', 'Dense red rose dome, white gypsophila border, black wrap.', 'Gulên sor bi cipsô.'),
                self::t('<p>Yüksek adet kırmızı gülle kubbe formunda hazırlanmış, dış halkada beyaz cipso kontrastlı lüks buket.</p>', '<p>Premium dome of red roses with gypsophila.</p>', '<p>Deste premium.</p>'),
                $d,
                self::t('Premium Kırmızı Gül Kubbesi | Rose Garden', 'Premium red rose dome | Rose Garden', 'Gul | Rose Garden'),
                self::t('Çok özel anlar için yüksek gül adedi. Rose Garden.', 'High stem count red roses.', 'Rose Garden.'),
                true
            ),
            '1a811f71-6bc5-42aa-a6ff-1ab84b300541.jpg' => self::bouquet(
                'klasik-12-kirmizi-gul-buket',
                720,
                ['Güller', 'Romantik', 'Ekonomik'],
                self::t('Klasik 12 Kırmızı Gül Buketi', 'Classic dozen red roses', '12 gulên sor'),
                self::t('Derin kırmızı güller, beyaz cipso, şeffaf ve altın detaylı ambalaj.', 'Velvet red roses, baby’s breath, gold-edged wrap.', '12 gul sor.'),
                self::t('<p>Romantik jestler için ölçülü on iki kırmızı gül; cipso ile hafifletilmiş zarif sunum.</p>', '<p>Classic dozen red roses bouquet.</p>', '<p>12 gul.</p>'),
                $d,
                self::t('12 Kırmızı Gül | Rose Garden', 'Dozen red roses | Rose Garden', '12 gul | Rose Garden'),
                self::t('Sevgililer günü ve yıldönümü için klasik seçim.', 'Classic romantic gift.', 'Rose Garden.')
            ),
            '25a08788-c096-4cdf-9895-aeda21176f2a.jpg' => self::bouquet(
                'pastel-gerbera-krizantem-buket',
                690,
                ['Doğum Günü', 'Mevsimlik'],
                self::t('Pastel Gerbera ve Krizantem Buketi', 'Pastel gerbera & chrysanthemum bouquet', 'Gerbera û krizantem'),
                self::t('Sarı, lavanta ve mercan krizantemler; şeftali ve pembe gerberalar.', 'Yellow, lavender, coral mums; peach & pink gerberas.', 'Rengên pastel.'),
                self::t('<p>Pembe şeritli ambalajda neşeli pastel karışım; uzun ömürlü krizantem ve gerbera uyumu.</p>', '<p>Cheerful pastel mix bouquet.</p>', '<p>Deste bejî.</p>'),
                $d,
                self::t('Pastel Gerbera Buket | Rose Garden', 'Pastel gerbera bouquet | Rose Garden', 'Deste | Rose Garden'),
                self::t('Doğum günü ve tebrik için renkli buket.', 'Birthday & congrats.', 'Rose Garden.')
            ),
            '3d5d2fdd-9d9e-482a-98b3-1cc09ab9db54.jpg' => self::bouquet(
                'rustik-kirmizi-gul-pamuk-lavanta-tepsi',
                1290,
                ['Romantik', 'Güller', 'Lüks'],
                self::t('Rustik Kırmızı Gül — Pamuk ve Lavanta', 'Rustic red roses, cotton & lavender', 'Gul sor rustîk'),
                self::t('Kraft ambalajda kırmızı güller, pamuk, portakal dilimi ve lavanta detayı.', 'Kraft wrap, roses, cotton, orange, lavender.', 'Gul û pamûk.'),
                self::t('<p>Tezgahta sergilenen yoğun kırmızı gül kompozisyonu; pamuk ve kuru portakal ile rustik şıklık.</p>', '<p>Rustic luxury rose bouquet on counter style.</p>', '<p>Deste rustîk.</p>'),
                $d,
                self::t('Rustik Kırmızı Gül Buketi | Rose Garden', 'Rustic red rose bouquet | Rose Garden', 'Deste | Rose Garden'),
                self::t('Pamuk ve lavanta detaylı özel tasarım.', 'Artisan rustic bouquet.', 'Rose Garden.')
            ),
            '59b56c84-5570-4685-af64-2619273cb458.jpg' => self::bouquet(
                'mor-altin-krizantem-buket',
                650,
                ['Mevsimlik', 'Doğum Günü'],
                self::t('Mor ve Altın Sarı Krizantem Buketi', 'Purple & gold chrysanthemum bouquet', 'Krizantem mor û zêr'),
                self::t('Sarı ve lavanta krizantemler, mor ambalaj, altın kurdele.', 'Yellow & lavender mums, purple wrap, gold ribbon.', 'Krizantem.'),
                self::t('<p>Mor craft ambalajda canlı sarı ve lavanta tonlarında krizantem buket; Rose Garden etiketi ile.</p>', '<p>Vibrant chrysanthemum bouquet.</p>', '<p>Deste.</p>'),
                $d,
                self::t('Mor Krizantem Buketi | Rose Garden', 'Purple chrysanthemum bouquet | Rose Garden', 'Deste | Rose Garden'),
                self::t('Uzun ömürlü krizantem hediye buket.', 'Long-lasting mum bouquet.', 'Rose Garden.')
            ),
            '6b411349-7c30-4a27-851e-d9e2554ce144.jpg' => self::bouquet(
                'ay-isigi-kirmizi-beyaz-gul-buket',
                1680,
                ['Güller', 'Romantik', 'Lüks', 'Yıldönümü'],
                self::t('Ay Işığı Kırmızı ve Beyaz Gül Buketi', 'Moonlight red & white rose bouquet', 'Gulên sor û spî'),
                self::t('Çoklu kırmızı gül hilalinde ortada beyaz güller, siyah ambalaj.', 'Red crescent with white roses inside, black wrap.', 'Gul sor û spî.'),
                self::t('<p>Yüksek adet kırmızı gül üzerinde beyaz gül vurgulu, hacimli romantik buket.</p>', '<p>Large romantic red and white rose design.</p>', '<p>Deste romantîk.</p>'),
                $d,
                self::t('Kırmızı Beyaz Gül Buketi | Rose Garden', 'Red white rose bouquet | Rose Garden', 'Gul | Rose Garden'),
                self::t('Özel tasarım gül düzenlemesi.', 'Signature rose arrangement.', 'Rose Garden.'),
                true
            ),
            '70075dd8-821d-49a6-bd5e-ae4af53cc4c1.jpg' => self::bouquet(
                'kis-masali-ayi-hediye-buket',
                980,
                ['Bebek', 'Özel Gün', 'Yeni'],
                self::t('Kış Masalı Ayıcıklı Hediye Buketi', 'Winter tale bouquet with teddy', 'Deste bi ayî'),
                self::t('Kırmızı gül odaklı, ayıcık, kuru portakal, yıldız ve kış süsleri.', 'Red rose focal, teddy, dried orange, winter accents.', 'Deste zivistanê.'),
                self::t('<p>Zeytin yeşili ambalajda tek güllü kompozisyon; pelüş ayı, kuru portakal ve yılbaşı esintili süslemeler.</p>', '<p>Festive gift bouquet with plush bear.</p>', '<p>Diyari.</p>'),
                $d,
                self::t('Ayıcıklı Kış Buketi | Rose Garden', 'Teddy winter bouquet | Rose Garden', 'Deste | Rose Garden'),
                self::t('Bebek ve yılbaşı hediyesi için özel konsept.', 'Gift bouquet with teddy.', 'Rose Garden.')
            ),
            '74a4aba0-1bdd-446c-836b-7374796b60a1.jpg' => self::bouquet(
                'beyaz-gul-pembe-ambalaj-buket',
                1120,
                ['Güller', 'Romantik', 'Düğün'],
                self::t('Beyaz Gül ve Pembe Ambalaj Buketi', 'White roses with pink wrap', 'Gulên spî'),
                self::t('Bol beyaz gül, cipso, pudra pembesi ambalaj.', 'White roses, baby’s breath, mauve wrap.', 'Gul spî.'),
                self::t('<p>Krem-pembe tonlu ambalajda yoğun beyaz gül ve cipso ile klasik zarafet.</p>', '<p>Elegant white rose bouquet.</p>', '<p>Deste spî.</p>'),
                $d,
                self::t('Beyaz Gül Buketi | Rose Garden', 'White rose bouquet | Rose Garden', 'Gul spî | Rose Garden'),
                self::t('Nişan ve kutlama için beyaz gül seçkisi.', 'Wedding & celebration whites.', 'Rose Garden.')
            ),
            '770fbbe4-3f55-4e24-9150-d36f4e449529.jpg' => self::bouquet(
                'aycicegi-lale-bahar-buket',
                860,
                ['Mevsimlik', 'Doğum Günü'],
                self::t('Ayçiçeği ve Lale Bahar Buketi', 'Sunflower & tulip spring bouquet', 'Ayçiçek û lale'),
                self::t('Sarı ayçiçekleri, pembe ve beyaz laleler, krem ambalaj.', 'Sunflowers, pink & white tulips, cream wrap.', 'Bihar.'),
                self::t('<p>Krem ambalajda ayçiçeği ve lale karışımı; yeşil kurdele ve gül baskılı kağıt detayı.</p>', '<p>Bright spring mix bouquet.</p>', '<p>Deste biharê.</p>'),
                $d,
                self::t('Ayçiçeği Lale Buketi | Rose Garden', 'Sunflower tulip bouquet | Rose Garden', 'Deste | Rose Garden'),
                self::t('Bahar ve mezuniyet için neşeli buket.', 'Cheerful spring bouquet.', 'Rose Garden.')
            ),
            '785c961b-e8cd-4ab6-a986-bc869f6b724e.jpg' => self::bouquet(
                'jumbo-kirmizi-gul-buket-beyaz-kagit',
                2890,
                ['Güller', 'Romantik', 'Lüks'],
                self::t('Jumbo Kırmızı Gül Buketi (Beyaz Ambalaj)', 'Jumbo red rose bouquet white wrap', 'Gulên sor mezin'),
                self::t('50+ kırmızı gül kubbesi, beyaz kağıt, siyah kurdele.', '50+ red roses, white paper, black ribbon.', 'Gul mezin.'),
                self::t('<p>Çok yüksek gül adediyle kubbe formunda hazırlanmış gösterişli kırmızı gül buketi.</p>', '<p>Extra large red rose dome.</p>', '<p>Deste mezin.</p>'),
                $d,
                self::t('Jumbo Kırmızı Gül Buketi | Rose Garden', 'Jumbo red roses | Rose Garden', 'Gul | Rose Garden'),
                self::t('Lüks talepler için yüksek gül adedi.', 'Ultra-premium stem count.', 'Rose Garden.'),
                true
            ),
            '7abbdef0-72a6-493b-86aa-da04ec4db448.jpg' => self::bouquet(
                'altin-zarafet-karisik-buket',
                1240,
                ['Lüks', 'Özel Gün', 'Doğum Günü'],
                self::t('Altın Zarafet Karışık Buket', 'Golden elegance mixed bouquet', 'Zarafet zêrîn'),
                self::t('Altın tonlu ambalajda gerbera, gül, krizantem ve siyah kurdele.', 'Gold wrap, gerberas, roses, mums, black ribbon.', 'Tevlîhev.'),
                self::t('<p>Metalik altın ambalajda geniş karışık buket; pembe gül ve canlı gerbera vurguları, Rose Garden etiketi.</p>', '<p>Premium gold-wrap mixed bouquet.</p>', '<p>Deste.</p>'),
                $d,
                self::t('Altın Karışık Buket | Rose Garden', 'Gold mixed bouquet | Rose Garden', 'Deste | Rose Garden'),
                self::t('Özel günler için göz alıcı tasarım.', 'Show-stopping gift bouquet.', 'Rose Garden.'),
                true
            ),
            '8dd47c97-f9a5-49f5-bc17-c8ba6565f00b.jpg' => self::bouquet(
                'gokkusagi-mavi-detay-karisik-buket',
                1100,
                ['Doğum Günü', 'Özel Gün'],
                self::t('Gökkuşağı Mavi Detay Karışık Buket', 'Rainbow bouquet with blue accents', 'Deste rengîn'),
                self::t('Mavi boyalı çiçekler, pembe gül, ayçiçeği, krizantem.', 'Dyed blue blooms, pink roses, sunflower, mums.', 'Rengên rengîn.'),
                self::t('<p>Beyaz buzlu ambalajda canlı renk paleti; mavi vurgulu özel boyalı çiçekler içerir.</p>', '<p>Vibrant mixed bouquet with specialty blue blooms.</p>', '<p>Deste.</p>'),
                $d,
                self::t('Renkli Karışık Buket | Rose Garden', 'Colorful mixed bouquet | Rose Garden', 'Deste | Rose Garden'),
                self::t('Kutlama ve doğum günü için enerjik seçenek.', 'Celebration bouquet.', 'Rose Garden.')
            ),
            '92113fbf-e716-4779-8894-08fa8b6bd293.jpg' => self::bouquet(
                'asil-ask-siyah-kagit-gul-buket',
                1980,
                ['Güller', 'Romantik', 'Lüks', 'Sevgililer Günü'],
                self::t('Asil Aşk — Siyah Ambalajda Kırmızı Gül', 'Noble love — red roses black wrap', 'Evîn'),
                self::t('40+ kırmızı gül, cipso halkası, siyah craft ambalaj.', '40+ red roses, gypsophila, black craft wrap.', 'Gul sor.'),
                self::t('<p>Siyah mat ambalajda yoğun kırmızı gül ve beyaz cipso kontrastı; kırmızı kurdele detayı.</p>', '<p>Large black-wrap red rose bouquet.</p>', '<p>Deste.</p>'),
                $d,
                self::t('Asil Aşk Gül Buketi | Rose Garden', 'Noble love roses | Rose Garden', 'Gul | Rose Garden'),
                self::t('Evlilik teklifi ve yıldönümü için premium seçim.', 'Proposal & anniversary premium.', 'Rose Garden.'),
                true
            ),
            '9493630f-83db-4bdc-8cf9-bb732188be57.jpg' => self::bouquet(
                'bordo-pembe-gul-karisik-buket',
                890,
                ['Romantik', 'Mevsimlik'],
                self::t('Bordo ve Pembe Gül Karışık Buket', 'Burgundy & pink rose mix', 'Gulên pembe'),
                self::t('Açık pembe güller, mor krizantemler, bordo-pembe çift ton ambalaj.', 'Light pink roses, purple mums, dual-tone wrap.', 'Tevlîhev.'),
                self::t('<p>Rose Garden kurdeleli bordo dış, pembe iç ambalajda zarif karışık buket.</p>', '<p>Branded ribbon, elegant mix.</p>', '<p>Deste.</p>'),
                $d,
                self::t('Bordo Pembe Buket | Rose Garden', 'Burgundy pink bouquet | Rose Garden', 'Deste | Rose Garden'),
                self::t('Hafif romantik tonlar, günlük hediye.', 'Everyday romantic gift.', 'Rose Garden.')
            ),
            '971d5244-70d4-4416-aea0-bbecf080611e.jpg' => self::bouquet(
                'lila-krizantem-zerafet-buket',
                720,
                ['Mevsimlik', 'Anneler Günü'],
                self::t('Lila Krizantem Zarafet Buketi', 'Lilac chrysanthemum elegance', 'Krizantem'),
                self::t('Lila şeffaf ambalaj, mor ve mürdüm krizantemler, kurdele üzerinde mini çiçek.', 'Lilac wrap, purple mums, ribbon accent.', 'Krizantem.'),
                self::t('<p>Lila kenarlı özel ambalajda yoğun krizantem küresi; uzun ömürlü hediye buket.</p>', '<p>Long-lasting mum bouquet in lilac wrap.</p>', '<p>Deste.</p>'),
                $d,
                self::t('Lila Krizantem Buketi | Rose Garden', 'Lilac mum bouquet | Rose Garden', 'Deste | Rose Garden'),
                self::t('Anneler günü ve teşekkür için ekonomik şık seçenek.', 'Mother’s Day friendly.', 'Rose Garden.')
            ),
            '9776f21c-5747-4c88-a56b-955e4c65af5d.jpg' => self::bouquet(
                'premium-siyah-beyaz-kirmizi-gul-dome',
                2180,
                ['Güller', 'Lüks', 'Romantik'],
                self::t('Premium Siyah-Beyaz Ambalajda Kırmızı Gül Kubbesi', 'Premium red rose dome black white wrap', 'Gul sor'),
                self::t('40-50 kırmızı gül, siyah-beyaz katlı ambalaj, mimari kesim.', '40–50 roses, layered black & white wrap.', 'Kubbe gul.'),
                self::t('<p>Tezgahta sergilenen ekstra büyük kırmızı gül kubbesi; modern siyah-beyaz kağıt katmanları.</p>', '<p>XL red rose dome, architectural wrap.</p>', '<p>Deste.</p>'),
                $d,
                self::t('Premium Gül Kubbesi | Rose Garden', 'Premium rose dome | Rose Garden', 'Gul | Rose Garden'),
                self::t('En üst segment romantik hediye.', 'Top-tier romantic gift.', 'Rose Garden.'),
                true
            ),
            'a0f90040-ace5-4cfa-8bdf-fb24672c23a8.jpg' => self::bouquet(
                'lavanta-bahar-karisik-buket',
                820,
                ['Mevsimlik', 'Doğum Günü'],
                self::t('Lavanta Bahar Karışık Buket', 'Lavender spring mixed bouquet', 'Bahar'),
                self::t('Lavanta ambalajda gül, ayçiçeği, krizantem ve cipso.', 'Lavender wrap, roses, sunflower, mums, gypsophila.', 'Tevlîhev.'),
                self::t('<p>Lavanta tonlu craft ambalajda dört mevsim karışık neşeli buket.</p>', '<p>Cheerful four-season mix.</p>', '<p>Deste.</p>'),
                $d,
                self::t('Lavanta Karışık Buket | Rose Garden', 'Lavender mix bouquet | Rose Garden', 'Deste | Rose Garden'),
                self::t('Geniş renk skalası ile kutlama buketi.', 'Colorful celebration bouquet.', 'Rose Garden.')
            ),
            'af6356a3-6171-42d8-97f7-182fb6a75436.jpg' => self::bouquet(
                'bahar-nergis-cipso-buket',
                740,
                ['Mevsimlik', 'Yeni'],
                self::t('Bahar Nergis ve Cipso Buketi', 'Spring daffodil & baby’s breath bouquet', 'Nergis'),
                self::t('Yoğun beyaz-sarı nergis, cipso, sarı ve şeftali ambalaj.', 'White-yellow daffodils, baby’s breath, yellow wrap.', 'Nergis.'),
                self::t('<p>Bahar sezonuna özel nergis buketi; hacimli ve ferah sarı tonlar.</p>', '<p>Seasonal daffodil bouquet.</p>', '<p>Deste nergisê.</p>'),
                $d,
                self::t('Nergis Buketi | Rose Garden', 'Daffodil bouquet | Rose Garden', 'Deste | Rose Garden'),
                self::t('İlkbahar hediyesi için özel seçki.', 'Spring gift selection.', 'Rose Garden.')
            ),
            'c1d25d1d-4585-485a-974c-19050fb9e0bc.jpg' => self::bouquet(
                'pembe-gerbera-mor-karisik-buket',
                850,
                ['Doğum Günü', 'Mevsimlik'],
                self::t('Pembe Gerbera ve Mor Karışık Buket', 'Pink gerbera & purple mix', 'Gerbera'),
                self::t('Pembe gerberalar, mor ve beyaz krizantemler, mor ambalaj, altın kurdele.', 'Pink gerberas, purple & white mums, gold ribbon.', 'Deste.'),
                self::t('<p>Mor craft ambalajda geniş gerbera odaklı karışık buket.</p>', '<p>Gerbera-forward mixed bouquet.</p>', '<p>Deste.</p>'),
                $d,
                self::t('Pembe Gerbera Buketi | Rose Garden', 'Pink gerbera bouquet | Rose Garden', 'Deste | Rose Garden'),
                self::t('Pembe ton sevenler için.', 'Pink lovers bouquet.', 'Rose Garden.')
            ),
            'c63a2414-6998-461f-ac8b-1adab694d861.jpg' => self::bouquet(
                'gece-yarisi-kirmizi-gul-altin-cizgi',
                1380,
                ['Güller', 'Romantik', 'Lüks'],
                self::t('Gece Yarısı Kırmızı Gül — Altın Çizgi', 'Midnight red roses gold trim', 'Gulên şevê'),
                self::t('20-25 kırmızı gül, siyah ambalaj altın kenar, kırmızı kurdele.', '20–25 roses, black wrap gold edge.', 'Gul sor.'),
                self::t('<p>Siyah kağıt ve ince altın şerit detaylı orta-büyük kırmızı gül buketi.</p>', '<p>Black & gold trim rose bouquet.</p>', '<p>Deste.</p>'),
                $d,
                self::t('Gece Yarısı Gül Buketi | Rose Garden', 'Midnight rose bouquet | Rose Garden', 'Gul | Rose Garden'),
                self::t('Şık gece hediyesi konsepti.', 'Elegant evening gift.', 'Rose Garden.')
            ),
            'e01fd5fb-58ed-4cd3-8076-82009135c05b.jpg' => self::bouquet(
                'kral-kirmizi-gul-cipso-siyah-altin',
                2350,
                ['Güller', 'Lüks', 'Romantik'],
                self::t('Kral Kırmızı Gül ve Cipso — Siyah Altın', 'King red rose gypsophila black gold', 'Gul'),
                self::t('40-50 gül, cipso çemberi, siyah ambalaj altın trim.', '40–50 roses, gypsophila halo, black gold wrap.', 'Gul mezin.'),
                self::t('<p>Geniş hacimli kırmızı gül kubbesi; dış halkada beyaz cipso, siyah-altın ambalaj.</p>', '<p>XL rose dome with gypsophila border.</p>', '<p>Deste.</p>'),
                $d,
                self::t('Kral Gül Buketi | Rose Garden', 'King rose bouquet | Rose Garden', 'Gul | Rose Garden'),
                self::t('En gösterişli gül buketlerinden.', 'Flagship rose bouquet.', 'Rose Garden.'),
                true
            ),
            'e04ce48a-84c1-488b-a9b7-04341ad7c907.jpg' => self::bouquet(
                'hosgeldin-bebek-karisik-buket',
                760,
                ['Bebek', 'Doğum Günü'],
                self::t('Hoş Geldin Bebek Karışık Buket', 'Welcome baby mixed bouquet', 'Bebek'),
                self::t('Pembe, sarı ve beyaz krizantemler; beyaz altın ambalaj, not kartı.', 'Pink, yellow, white mums; gold trim wrap.', 'Bebek.'),
                self::t('<p>Yeni doğan kutlamaları için neşeli pastel karışık buket; kişisel mesaj kartı ile gönderilebilir.</p>', '<p>Baby welcome bouquet with message card.</p>', '<p>Deste bebekê.</p>'),
                $d,
                self::t('Hoş Geldin Bebek Buketi | Rose Garden', 'Welcome baby bouquet | Rose Garden', 'Deste | Rose Garden'),
                self::t('Doğum hediyesi için özel seçim.', 'New baby gift flowers.', 'Rose Garden.')
            ),
            'e439a833-df8a-43c8-8b1d-d2813fbd7452.jpg' => self::bouquet(
                'kalbin-ortasi-pembe-gul-buket',
                1420,
                ['Güller', 'Romantik', 'Sevgililer Günü'],
                self::t('Kalbin Ortası Pembe Gül — Kırmızı Gül Buketi', 'Pink heart red rose bouquet', 'Gul'),
                self::t('Ortada tek pembe gül, çevrede 25-30 kırmızı gül, beyaz ambalaj.', 'Center pink rose, red roses around, white wrap.', 'Gul sor û pembe.'),
                self::t('<p>Beyaz ambalajda simgesel pembe gül odaklı kırmızı gül denizi.</p>', '<p>Symbolic pink center in red roses.</p>', '<p>Deste.</p>'),
                $d,
                self::t('Kalbin Ortası Gül Buketi | Rose Garden', 'Heart center rose bouquet | Rose Garden', 'Gul | Rose Garden'),
                self::t('Özel anları anlatan tasarım.', 'Symbolic romantic design.', 'Rose Garden.')
            ),
            'e8cb8297-73c2-4dd6-bf8e-d42178433207.jpg' => self::bouquet(
                'pembe-zambak-gul-buket',
                1280,
                ['Romantik', 'Lüks', 'Anneler Günü'],
                self::t('Pembe Zambak ve Gül Buketi', 'Pink lily & rose bouquet', 'Lîlya û gul'),
                self::t('Beyaz zambaklar, pembe güller, lavanta krizantem, pembe ambalaj.', 'White lilies, pink roses, lavender mums, pink wrap.', 'Zambak.'),
                self::t('<p>Pembe ambalajda hacimli lilya ve gül uyumu; altın kurdele detayı.</p>', '<p>Luxury lily and rose hand-tied bouquet.</p>', '<p>Deste.</p>'),
                $d,
                self::t('Zambak Gül Buketi | Rose Garden', 'Lily rose bouquet | Rose Garden', 'Deste | Rose Garden'),
                self::t('Anneler günü ve yıldönümü için klasik lüks.', 'Mother’s day luxury classic.', 'Rose Garden.'),
                true
            ),
            'ea774e92-b116-4bc0-a3e8-4ca66179af54.jpg' => self::bouquet(
                'beyaz-gul-siyah-altin-ambalaj',
                1180,
                ['Güller', 'Düğün', 'Romantik'],
                self::t('Beyaz Gül Siyah-Altın Ambalaj Buketi', 'White roses black gold wrap', 'Gulên spî'),
                self::t('15-20 beyaz gül, cipso, siyah-altın kesim ambalaj.', '15–20 white roses, gypsophila, black gold wrap.', 'Gul spî.'),
                self::t('<p>Modern siyah ve altın şeritli ambalajda krem beyaz güller ve cipso.</p>', '<p>Modern white rose bouquet.</p>', '<p>Deste spî.</p>'),
                $d,
                self::t('Beyaz Gül Buketi | Rose Garden', 'White rose bouquet | Rose Garden', 'Gul | Rose Garden'),
                self::t('Düğün ve kutlama için sofistike beyaz.', 'Wedding-ready whites.', 'Rose Garden.')
            ),
            'f828ba4c-ad13-4de7-9e98-fb5a7bb656d6.jpg' => self::bouquet(
                'kirmizi-gul-tek-beyaz-vurgu-buket',
                2050,
                ['Güller', 'Romantik', 'Lüks'],
                self::t('Kırmızı Gül Buketi — Tek Beyaz Vurgu', 'Red roses with single white accent', 'Gul sor'),
                self::t('40+ kırmızı gül, ortada krem beyaz gül, palmiye yapraklı taban.', '40+ red roses, one white rose accent, palm leaves.', 'Gul.'),
                self::t('<p>Beyaz-kraft çift kat ambalajda yoğun kırmızı güller ve tek beyaz gül imzası.</p>', '<p>Signature white rose in red sea.</p>', '<p>Deste.</p>'),
                $d,
                self::t('Beyaz Vurgulu Gül Buketi | Rose Garden', 'White accent rose bouquet | Rose Garden', 'Gul | Rose Garden'),
                self::t('Özel mesaj veren lüks gül düzenlemesi.', 'Signature luxury roses.', 'Rose Garden.'),
                true
            ),
            'faa5d25b-d099-4288-8ea3-a706435abfd0.jpg' => self::bouquet(
                'beyaz-zambak-turuncu-gunes-buket',
                1320,
                ['Lüks', 'Doğum Günü', 'Özel Gün'],
                self::t('Beyaz Zambak ve Turuncu Güneş Buketi', 'White lily & orange sunset bouquet', 'Lîlya'),
                self::t('Açık beyaz zambaklar, turuncu krizantem/gerbera, cipso, mavi kurdele.', 'White lilies, orange blooms, baby’s breath, blue ribbon.', 'Lîlya.'),
                self::t('<p>Beyaz ambalajda gösterişli lilya odaklı turuncu kontrastlı buket.</p>', '<p>High-impact lily bouquet.</p>', '<p>Deste.</p>'),
                $d,
                self::t('Zambak Turuncu Buket | Rose Garden', 'Lily orange bouquet | Rose Garden', 'Deste | Rose Garden'),
                self::t('Geniş hacimli özel gün çiçeği.', 'Large special-occasion bouquet.', 'Rose Garden.'),
                true
            ),
        ];
    }
}
