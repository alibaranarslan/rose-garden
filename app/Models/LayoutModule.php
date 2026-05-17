<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LayoutModule extends Model
{
    protected $fillable = [
        'key',
        'name',
        'is_active',
        'sort_order',
        'settings',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
            'settings' => 'array',
        ];
    }
}
