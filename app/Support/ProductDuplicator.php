<?php

namespace App\Support;

use App\Models\Product;
use Illuminate\Support\Str;

class ProductDuplicator
{
    public static function duplicate(Product $product): Product
    {
        $product->loadMissing(['categories', 'tags', 'specialOccasions', 'images', 'variants']);

        $clone = $product->replicate();
        $clone->slug = $product->slug.'-kopya-'.now()->format('His');
        $clone->sku = $product->sku
            ? Str::limit($product->sku.'-'.now()->format('His'), 100, '')
            : null;
        $clone->status = 'draft';
        $clone->is_featured = false;
        $clone->view_count = 0;
        $clone->save();

        $clone->categories()->sync($product->categories->modelKeys());
        $clone->tags()->sync($product->tags->modelKeys());
        $clone->specialOccasions()->sync($product->specialOccasions->modelKeys());

        foreach ($product->images as $image) {
            $clone->images()->create([
                'image_path' => $image->image_path,
                'alt_text' => $image->alt_text,
                'is_primary' => $image->is_primary,
                'sort_order' => $image->sort_order,
            ]);
        }

        foreach ($product->variants as $variant) {
            $clone->variants()->create([
                'name' => $variant->getTranslations('name'),
                'price' => $variant->price,
                'sale_price' => $variant->sale_price,
                'stock_status' => $variant->stock_status,
                'sort_order' => $variant->sort_order,
                'is_active' => $variant->is_active,
            ]);
        }

        $clone->ensurePrimaryImage();

        return $clone->refresh()->load(['categories', 'tags', 'specialOccasions', 'images', 'variants']);
    }
}
