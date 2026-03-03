<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AbandonedCart extends Model
{
    protected $fillable = [
        'user_id',
        'session_id',
        'email',
        'phone',
        'cart_data',
        'total_value',
        'reminder_count',
        'last_reminded_at',
        'recovered',
        'recovered_order_id',
        'abandoned_at',
    ];

    protected function casts(): array
    {
        return [
            'cart_data' => 'array',
            'total_value' => 'decimal:2',
            'reminder_count' => 'integer',
            'recovered' => 'boolean',
            'last_reminded_at' => 'datetime',
            'abandoned_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function recoveredOrder(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'recovered_order_id');
    }

    public function scopeNotRecovered($query)
    {
        return $query->where('recovered', false);
    }

    public function scopeEligibleForReminder($query)
    {
        return $query->notRecovered()
            ->where(fn ($q) => $q->whereNull('last_reminded_at')
                ->orWhere('last_reminded_at', '<=', now()->subHours(24)));
    }

    public function getItemCountAttribute(): int
    {
        return count($this->cart_data ?? []);
    }
}
