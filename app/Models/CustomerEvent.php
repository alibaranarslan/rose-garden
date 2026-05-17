<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerEvent extends Model
{
    protected $fillable = [
        'user_id',
        'event_type',
        'event_label',
        'recipient_name',
        'recipient_address',
        'event_month',
        'event_day',
        'detected_from',
        'source_order_id',
        'reminder_days_before',
        'is_active',
        'last_reminded_at',
    ];

    protected function casts(): array
    {
        return [
            'event_month' => 'integer',
            'event_day' => 'integer',
            'reminder_days_before' => 'integer',
            'is_active' => 'boolean',
            'last_reminded_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sourceOrder(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'source_order_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function isDueForReminder(): bool
    {
        if (! checkdate((int) $this->event_month, (int) $this->event_day, (int) now()->year)) {
            return false;
        }

        $targetDate = now()->setMonth($this->event_month)->setDay($this->event_day);
        if ($targetDate->isPast()) {
            $targetDate->addYear();
        }
        $daysUntil = $targetDate->diffInDays(now());
        return $daysUntil <= $this->reminder_days_before;
    }
}
