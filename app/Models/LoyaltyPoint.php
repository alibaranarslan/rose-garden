<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoyaltyPoint extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'balance',
        'total_earned',
        'total_spent',
        'updated_at',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'balance' => 'decimal:2',
            'total_earned' => 'decimal:2',
            'total_spent' => 'decimal:2',
            'updated_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function addPoints(float $amount, string $description = '', ?int $orderId = null, float $multiplier = 1.0, ?\Carbon\Carbon $expiresAt = null): void
    {
        $earned = round($amount * $multiplier, 2);

        LoyaltyTransaction::create([
            'user_id'    => $this->user_id,
            'order_id'   => $orderId,
            'type'       => 'earned',
            'amount'     => $earned,
            'multiplier' => $multiplier,
            'description' => $description,
            'expires_at' => $expiresAt,
            'created_at' => now(),
        ]);

        $this->increment('balance', $earned);
        $this->increment('total_earned', $earned);
        $this->touch('updated_at');

        // Extend expiry on each new earn
        $expiryMonths = (int) (\App\Models\Setting::get('loyalty', 'expiry_months') ?? 12);
        if ($expiryMonths > 0) {
            $this->update(['expires_at' => now()->addMonths($expiryMonths)]);
        }
    }

    public function spendPoints(float $amount, string $description = '', ?int $orderId = null): bool
    {
        if ($this->balance < $amount) return false;

        LoyaltyTransaction::create([
            'user_id' => $this->user_id,
            'order_id' => $orderId,
            'type' => 'spent',
            'amount' => $amount,
            'description' => $description,
        ]);

        $this->decrement('balance', $amount);
        $this->increment('total_spent', $amount);
        $this->touch('updated_at');

        return true;
    }
}
