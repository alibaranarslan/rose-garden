<?php

namespace App\Support;

final class ProductHighlightPreset
{
    /**
     * @return array<string, list<array{icon:string,title:string,body:string,sort_order:int}>>
     */
    public static function forCategory(?string $categorySlug): array
    {
        return match ($categorySlug) {
            'saksi-cicekleri' => self::pottedPlant(),
            'cicek-buketleri' => self::bouquet(),
            default => self::generic(),
        };
    }

    /**
     * @return array<string, list<array{icon:string,title:string,body:string,sort_order:int}>>
     */
    public static function generic(): array
    {
        return [
            'tr' => self::rows(
                ['sparkles', 'Butik Hazırlık', 'Her sipariş teslim anı için yeniden hazırlanır.'],
                ['truck', 'Teslimat Akışı', 'Adres ve teslimat notu ekibimiz tarafından sipariş sonrası teyit edilir.'],
                ['chat-bubble-left-right', 'Sipariş Desteği', 'WhatsApp üzerinden hızlı yönlendirme ve ürün önerisi alabilirsiniz.'],
            ),
            'en' => self::rows(
                ['sparkles', 'Boutique Preparation', 'Every order is prepared again for the delivery moment.'],
                ['truck', 'Delivery Flow', 'Address details and delivery notes are confirmed after checkout.'],
                ['chat-bubble-left-right', 'Order Support', 'You can ask for quick guidance and product advice via WhatsApp.'],
            ),
            'ku' => self::rows(
                ['sparkles', 'Amadekirina Butîk', 'Her siparîş ji bo dema radestkirinê ji nû ve tê amadekirin.'],
                ['truck', 'Akîşa Radestkirinê', 'Piştî siparîşê navnîşan û notên radestkirinê têne pejirandin.'],
                ['chat-bubble-left-right', 'Alîkariya Siparîşê', 'Ji WhatsAppê rêberî û pêşniyara zû bistînin.'],
            ),
        ];
    }

    /**
     * @return array<string, list<array{icon:string,title:string,body:string,sort_order:int}>>
     */
    private static function bouquet(): array
    {
        return [
            'tr' => self::rows(
                ['sparkles', 'El İşçiliği Buket', 'Renk dengesi, ambalaj ve not kartı birlikte düşünülerek hazırlanır.'],
                ['truck', 'Aynı Gün Teslimat', 'Uygun saat aralığında Adıyaman içi teslimat için önceliklendirilir.'],
                ['gift', 'Jest Etkisi', 'Kutlama, teşekkür ve romantik anlar için hazır bir sunum dili taşır.'],
            ),
            'en' => self::rows(
                ['sparkles', 'Handcrafted Bouquet', 'Color balance, wrapping and message card are designed together.'],
                ['truck', 'Same-Day Delivery', 'Prioritized for Adiyaman delivery windows when timing is suitable.'],
                ['gift', 'Gift Impact', 'Built for celebration, gratitude and romantic moments.'],
            ),
            'ku' => self::rows(
                ['sparkles', 'Deste Bi Destan', 'Reng, ambalaj û karta notê bi hev re têne fikirîn.'],
                ['truck', 'Radestkirina Heman Rojê', 'Di dema guncaw de ji bo navenda Amediyanê tê pêşxistin.'],
                ['gift', 'Bandora Diyariyê', 'Ji bo pîrozbahî, spas û demên romantîk hatiye amadekirin.'],
            ),
        ];
    }

    /**
     * @return array<string, list<array{icon:string,title:string,body:string,sort_order:int}>>
     */
    private static function pottedPlant(): array
    {
        return [
            'tr' => self::rows(
                ['sparkles', 'Yaşayan Hediye', 'Ev ve ofis için uzun ömürlü, kalıcı bir jest alternatifi sunar.'],
                ['sun', 'Bakım Rehberi', 'Işık ve sulama önerileri teslimat sonrası kolayca paylaşılır.'],
                ['truck', 'Korunaklı Teslim', 'Saksı ve taşıma düzeni bitkiyi güvenli şekilde ulaştıracak biçimde hazırlanır.'],
            ),
            'en' => self::rows(
                ['sparkles', 'Living Gift', 'A long-lasting gesture for home or office settings.'],
                ['sun', 'Care Notes', 'Light and watering guidance is shared after delivery.'],
                ['truck', 'Protected Delivery', 'Prepared to keep the pot and foliage safe during transport.'],
            ),
            'ku' => self::rows(
                ['sparkles', 'Diyariya Zindî', 'Ji bo mal û ofîsê diyariyeke mayîndar e.'],
                ['sun', 'Notên Lênêrînê', 'Rêberiya ronahî û avdanê piştî radestkirinê tê dayîn.'],
                ['truck', 'Radestkirina Parastî', 'Saksi û nebata li ser rê bi ewlehî têne amadekirin.'],
            ),
        ];
    }

    /**
     * @param  array<int, array{0:string,1:string,2:string}>  $rows
     * @return list<array{icon:string,title:string,body:string,sort_order:int}>
     */
    private static function rows(array ...$rows): array
    {
        return collect($rows)
            ->values()
            ->map(fn (array $row, int $index) => [
                'icon' => $row[0],
                'title' => $row[1],
                'body' => $row[2],
                'sort_order' => $index + 1,
            ])
            ->all();
    }
}
