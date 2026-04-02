@props(['product'])
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Product",
    "name": @json($product->getTranslation('name', app()->getLocale())),
    "description": @json(\Illuminate\Support\Str::limit(strip_tags($product->getTranslation('description', app()->getLocale())), 200)),
    "image": @json($product->images->first()?->image_path ? asset($product->images->first()->image_path) : ''),
    "sku": @json($product->sku),
    "offers": {
        "@type": "Offer",
        "price": @json((string) $product->current_price),
        "priceCurrency": "TRY",
        "availability": @json($product->stock_status === 'in_stock' ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock'),
        "seller": {
            "@type": "Organization",
            "name": "Rose Garden"
        }
    }
}
</script>
