<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

class SpecialOccasion extends Model
{
    use HasTranslations;

    public array $translatable = ['name'];

    protected $fillable = [
        'name',
        'slug',
        'date_month',
        'date_day',
        'category_id',
        'loyalty_multiplier',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'date_month' => 'integer',
            'date_day' => 'integer',
            'loyalty_multiplier' => 'decimal:1',
            'is_active' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function isUpcoming(int $daysAhead = 30): bool
    {
        $targetDate = now()->setMonth($this->date_month)->setDay($this->date_day);
        if ($targetDate->isPast()) {
            $targetDate->addYear();
        }
        return $targetDate->diffInDays(now()) <= $daysAhead;
    }
}
