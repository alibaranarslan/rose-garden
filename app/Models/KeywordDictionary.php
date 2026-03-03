<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KeywordDictionary extends Model
{
    protected $table = 'keyword_dictionary';

    protected $fillable = [
        'keyword',
        'event_type',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public static function detectEventType(string $text): ?string
    {
        $keywords = static::active()->get();

        foreach ($keywords as $keyword) {
            if (stripos($text, $keyword->keyword) !== false) {
                return $keyword->event_type;
            }
        }

        return null;
    }
}
