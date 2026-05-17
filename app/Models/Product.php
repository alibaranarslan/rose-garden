<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Translatable\HasTranslations;

class Product extends Model implements HasMedia
{
    use HasTranslations, InteractsWithMedia, SoftDeletes;

    public array $translatable = ['name', 'short_description', 'description', 'delivery_note', 'meta_title', 'meta_description', 'product_highlights'];

    protected $fillable = [
        'name',
        'slug',
        'short_description',
        'description',
        'sku',
        'price',
        'sale_price',
        'sale_start',
        'sale_end',
        'stock_status',
        'status',
        'is_featured',
        'is_new',
        'delivery_note',
        'product_highlights',
        'meta_title',
        'meta_description',
        'view_count',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'sale_price' => 'decimal:2',
            'sale_start' => 'datetime',
            'sale_end' => 'datetime',
            'is_featured' => 'boolean',
            'is_new' => 'boolean',
            'view_count' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('images')
            ->registerMediaConversions(function (Media $media) {
                $this->addMediaConversion('thumb')
                    ->width(200)
                    ->height(200)
                    ->format('webp')
                    ->quality(80);
                $this->addMediaConversion('medium')
                    ->width(600)
                    ->height(600)
                    ->format('webp')
                    ->quality(80);
                $this->addMediaConversion('large')
                    ->width(1200)
                    ->height(1200)
                    ->format('webp')
                    ->quality(85);
            });
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'product_category');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'product_tag');
    }

    public function specialOccasions(): BelongsToMany
    {
        return $this->belongsToMany(SpecialOccasion::class, 'product_occasion');
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class)->orderBy('sort_order');
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    public function blogPosts(): BelongsToMany
    {
        return $this->belongsToMany(BlogPost::class, 'blog_post_product');
    }

    public function pageViews()
    {
        return $this->morphMany(AnalyticsPageView::class, 'viewable');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeStorefrontReady($query)
    {
        return $query->active()->where(function ($builder): void {
            $builder->whereDoesntHave('images')
                ->orWhereHas('images', function ($imageQuery): void {
                    $imageQuery->where(function ($pathQuery): void {
                        $pathQuery->whereNull('image_path')
                            ->orWhereRaw("lower(image_path) not like 'http%'");
                    });
                });
        });
    }

    public function scopeInStock($query)
    {
        return $query->where('stock_status', 'in_stock');
    }

    public function getPrimaryImageAttribute(): ?string
    {
        if ($this->relationLoaded('images')) {
            $images = $this->images->sortBy('sort_order')->values();

            return $images->firstWhere('is_primary', true)?->image_path
                ?? $images->first()?->image_path;
        }

        return $this->images()
            ->orderByDesc('is_primary')
            ->orderBy('sort_order')
            ->value('image_path');
    }

    public function getCurrentPriceAttribute(): float
    {
        if ($this->sale_price && $this->isOnSale()) {
            return $this->sale_price;
        }

        return $this->price;
    }

    public function isOnSale(): bool
    {
        if (! $this->sale_price) {
            return false;
        }

        $now = now();
        $afterStart = ! $this->sale_start || $now->gte($this->sale_start);
        $beforeEnd = ! $this->sale_end || $now->lte($this->sale_end);

        return $afterStart && $beforeEnd;
    }

    /**
     * @return list<array{icon:string,title:string,body:string,sort_order:int}>
     */
    public function localizedHighlights(?string $locale = null): array
    {
        $locale = $locale ?: app()->getLocale();

        $highlights = $this->getTranslation('product_highlights', $locale, false);

        if (! is_array($highlights)) {
            $highlights = $this->getTranslation('product_highlights', 'tr', false);
        }

        if (! is_array($highlights)) {
            return [];
        }

        return collect($highlights)
            ->filter(fn ($item) => is_array($item) && filled($item['title'] ?? null) && filled($item['body'] ?? null))
            ->values()
            ->map(function (array $item, int $index): array {
                return [
                    'icon' => (string) ($item['icon'] ?? 'sparkles'),
                    'title' => trim((string) $item['title']),
                    'body' => trim((string) $item['body']),
                    'sort_order' => (int) ($item['sort_order'] ?? ($index + 1)),
                ];
            })
            ->sortBy('sort_order')
            ->values()
            ->all();
    }

    public function ensurePrimaryImage(): void
    {
        $images = $this->images()->orderBy('sort_order')->get();

        if ($images->isEmpty()) {
            return;
        }

        $primary = $images->firstWhere('is_primary', true) ?? $images->first();

        $this->images()
            ->whereKeyNot($primary->id)
            ->update(['is_primary' => false]);

        if (! $primary->is_primary) {
            $primary->forceFill(['is_primary' => true])->saveQuietly();
        }
    }

    /**
     * PLP / kart vitrininde gösterilecek tutar (varyantlı ürünlerde en düşük seçenek).
     *
     * @return array{current: float, compare: ?float, show_from: bool}
     */
    public function cardPriceDisplay(): array
    {
        $variants = $this->relationLoaded('variants')
            ? $this->variants->where('is_active', true)->sortBy('sort_order')->values()
            : $this->variants()->where('is_active', true)->orderBy('sort_order')->get();

        if ($variants->isEmpty()) {
            $onSale = $this->sale_price && $this->isOnSale();

            return [
                'current' => (float) ($onSale ? $this->sale_price : $this->price),
                'compare' => $onSale ? (float) $this->price : null,
                'show_from' => false,
            ];
        }

        $effective = $variants->map(fn (ProductVariant $v) => (float) ($v->sale_price ? $v->sale_price : $v->price));
        $minEffective = $effective->min();
        $maxEffective = $effective->max();

        $compare = null;
        foreach ($variants as $v) {
            $eff = (float) ($v->sale_price ? $v->sale_price : $v->price);
            if (abs($eff - $minEffective) < 0.009 && $v->sale_price) {
                $compare = (float) $v->price;
                break;
            }
        }

        return [
            'current' => $minEffective,
            'compare' => $compare !== null && $compare > $minEffective ? $compare : null,
            'show_from' => abs($maxEffective - $minEffective) > 0.009,
        ];
    }
}
