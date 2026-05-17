<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\QueryException;

class Order extends Model
{
    protected $fillable = [
        'order_number',
        'user_id',
        'status',
        'subtotal',
        'delivery_fee',
        'discount_amount',
        'loyalty_points_used',
        'total',
        'payment_method',
        'sender_name',
        'sender_phone',
        'sender_email',
        'recipient_name',
        'recipient_phone',
        'recipient_address',
        'recipient_district',
        'delivery_zone_id',
        'delivery_date',
        'delivery_time_slot_id',
        'delivery_note',
        'coupon_id',
        'admin_note',
        'ip_address',
        'cancelled_at',
        'cancellation_reason',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'delivery_fee' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'loyalty_points_used' => 'decimal:2',
            'total' => 'decimal:2',
            'delivery_date' => 'date',
            'cancelled_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function deliveryZone(): BelongsTo
    {
        return $this->belongsTo(DeliveryZone::class);
    }

    public function deliveryTimeSlot(): BelongsTo
    {
        return $this->belongsTo(DeliveryTimeSlot::class);
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function statusHistory(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class)->orderBy('created_at');
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function couponUsage(): HasOne
    {
        return $this->hasOne(CouponUsage::class);
    }

    public function scopePending($query)
    {
        return $query->whereIn('status', ['pending', 'awaiting_payment', 'paid', 'preparing']);
    }

    public function scopeAwaitingBankTransfer($query)
    {
        return $query->where('status', 'awaiting_payment')
            ->where('payment_method', 'bank_transfer');
    }

    public function scopeDeliveryDate($query, string $date)
    {
        return $query->whereDate('delivery_date', $date);
    }

    public function isPaid(): bool
    {
        return in_array($this->status, ['paid', 'preparing', 'on_the_way', 'delivered']);
    }

    public function isCancellable(): bool
    {
        return in_array($this->status, ['pending', 'awaiting_payment']);
    }

    protected static function booted(): void
    {
        static::creating(function (Order $order) {
            if (! $order->order_number) {
                $order->order_number = static::nextGeneratedOrderNumber();
            }
        });
    }

    public static function createWithGeneratedNumber(array $attributes, int $maxAttempts = 5): self
    {
        if (filled($attributes['order_number'] ?? null)) {
            /** @var self $order */
            $order = static::query()->create($attributes);

            return $order;
        }

        $attempt = 0;

        while (true) {
            try {
                /** @var self $order */
                $order = static::query()->create($attributes);

                return $order;
            } catch (QueryException $exception) {
                $attempt++;

                if ($attempt >= $maxAttempts || ! static::isDuplicateOrderNumberException($exception)) {
                    throw $exception;
                }
            }
        }
    }

    public static function nextGeneratedOrderNumber(?DateTimeInterface $date = null): string
    {
        $resolvedDate = $date ? now()->setTimestamp($date->getTimestamp()) : now();
        $prefix = 'RG-'.$resolvedDate->format('Ymd').'-';

        $latestOrderNumber = static::query()
            ->where('order_number', 'like', $prefix.'%')
            ->orderByDesc('order_number')
            ->value('order_number');

        $nextSequence = $latestOrderNumber
            ? ((int) substr((string) $latestOrderNumber, strrpos((string) $latestOrderNumber, '-') + 1)) + 1
            : 1;

        return $prefix.str_pad((string) $nextSequence, 4, '0', STR_PAD_LEFT);
    }

    private static function isDuplicateOrderNumberException(QueryException $exception): bool
    {
        $sqlState = $exception->errorInfo[0] ?? null;
        $message = strtolower($exception->getMessage());

        return in_array($sqlState, ['23000', '23505'], true)
            && str_contains($message, 'order_number');
    }
}
