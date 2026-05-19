<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_occasion');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public static function nearestActive(?CarbonInterface $from = null, array $with = []): ?self
    {
        return self::query()
            ->active()
            ->with($with)
            ->get()
            ->sortBy(fn (self $occasion): int => $occasion->daysUntil($from))
            ->first();
    }

    public static function nearestActiveUpcoming(?CarbonInterface $from = null, int $daysAhead = 90, array $with = []): ?self
    {
        return self::query()
            ->active()
            ->with($with)
            ->get()
            ->filter(fn (self $occasion): bool => $occasion->daysUntil($from) <= $daysAhead)
            ->sortBy(fn (self $occasion): int => $occasion->daysUntil($from))
            ->first();
    }

    public function nextOccurrence(?CarbonInterface $from = null)
    {
        $from = $from ? $from->copy()->startOfDay() : now()->startOfDay();

        $targetDate = $from->copy()
            ->setMonth($this->date_month)
            ->setDay($this->date_day);

        if ($targetDate->lt($from)) {
            $targetDate->addYear();
        }

        return $targetDate;
    }

    public function daysUntil(?CarbonInterface $from = null): int
    {
        $from = $from ? $from->copy()->startOfDay() : now()->startOfDay();

        return $from->diffInDays($this->nextOccurrence($from), false);
    }

    public function isToday(?CarbonInterface $from = null): bool
    {
        return $this->daysUntil($from) === 0;
    }

    public function isUpcoming(int $daysAhead = 30): bool
    {
        return $this->daysUntil() <= $daysAhead;
    }
}
