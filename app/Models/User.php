<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens, HasFactory, HasRoles, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'avatar',
        'google_id',
        'is_admin',
        'kvkk_accepted_at',
        'marketing_consent',
        'marketing_consent_at',
        'preferred_language',
        'is_active',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'marketing_consent' => 'boolean',
            'is_active' => 'boolean',
            'kvkk_accepted_at' => 'datetime',
            'marketing_consent_at' => 'datetime',
            'last_login_at' => 'datetime',
        ];
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function loyaltyPoints(): HasOne
    {
        return $this->hasOne(LoyaltyPoint::class);
    }

    public function loyaltyTransactions(): HasMany
    {
        return $this->hasMany(LoyaltyTransaction::class);
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    public function customerEvents(): HasMany
    {
        return $this->hasMany(CustomerEvent::class);
    }

    public function abandonedCarts(): HasMany
    {
        return $this->hasMany(AbandonedCart::class);
    }

    public function dataRequests(): HasMany
    {
        return $this->hasMany(DataRequest::class);
    }

    public function adminGuideProgress(): HasMany
    {
        return $this->hasMany(AdminGuideProgress::class);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->is_admin && $this->is_active;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeCustomers($query)
    {
        return $query->where('is_admin', false);
    }

    public function getTotalSpentAttribute(): float
    {
        return $this->orders()->where('status', '!=', 'cancelled')->sum('total');
    }
}
