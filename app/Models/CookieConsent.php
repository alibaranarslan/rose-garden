<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CookieConsent extends Model
{
    protected $fillable = [
        'session_id',
        'user_id',
        'ip_address',
        'consent_categories',
        'consented_at',
        'revoked_at',
    ];

    protected $casts = [
        'consent_categories' => 'array',
        'consented_at' => 'datetime',
        'revoked_at' => 'datetime',
    ];
}
