<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Coupon extends Model
{
    protected $fillable = [
        'code',
        'type',
        'value',
        'min_order_amount',
        'max_uses',
        'max_uses_per_user',
        'used_count',
        'starts_at',
        'expires_at',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'value' => 'decimal:2',
            'min_order_amount' => 'decimal:2',
            'max_uses' => 'integer',
            'max_uses_per_user' => 'integer',
            'used_count' => 'integer',
            'starts_at' => 'datetime',
            'expires_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    public function usages(): HasMany
    {
        return $this->hasMany(CouponUsage::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(fn ($q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', now()))
            ->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>=', now()));
    }

    public function isValid(float $orderTotal = 0, ?int $userId = null): bool
    {
        if (!$this->is_active) return false;
        if ($this->starts_at && now()->lt($this->starts_at)) return false;
        if ($this->expires_at && now()->gt($this->expires_at)) return false;
        if ($this->min_order_amount && $orderTotal < $this->min_order_amount) return false;
        if ($this->max_uses && $this->used_count >= $this->max_uses) return false;

        if ($userId && $this->max_uses_per_user) {
            $userUses = $this->usages()->where('user_id', $userId)->count();
            if ($userUses >= $this->max_uses_per_user) return false;
        }

        return true;
    }

    public function calculateDiscount(float $orderTotal): float
    {
        return match ($this->type) {
            'percentage' => round($orderTotal * ($this->value / 100), 2),
            'fixed_amount' => min($this->value, $orderTotal),
            'free_delivery' => 0,
            default => 0,
        };
    }
}
