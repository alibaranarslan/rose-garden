<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Page extends Model
{
    use HasTranslations;

    public array $translatable = ['title', 'content', 'meta_title', 'meta_description'];

    protected $fillable = [
        'title',
        'slug',
        'content',
        'meta_title',
        'meta_description',
        'is_published',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function pageViews()
    {
        return $this->morphMany(AnalyticsPageView::class, 'viewable');
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }
}
