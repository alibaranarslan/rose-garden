<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class NotificationTemplate extends Model
{
    use HasTranslations;

    public array $translatable = [
        'name',
        'sms_body',
        'email_subject',
        'email_body',
    ];

    protected $fillable = [
        'key',
        'name',
        'channel',
        'sms_body',
        'email_subject',
        'email_body',
        'variables',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'variables' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function logs(): HasMany
    {
        return $this->hasMany(NotificationLog::class, 'template_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public static function findByKey(string $key): ?static
    {
        return static::where('key', $key)->where('is_active', true)->first();
    }

    public function renderSms(array $variables, ?string $locale = null): string
    {
        return $this->replaceVariables($this->resolveLocalizedValue('sms_body', $locale), $variables);
    }

    public function renderEmailSubject(array $variables, ?string $locale = null, string $fallback = ''): string
    {
        $subject = $this->resolveLocalizedValue('email_subject', $locale);

        return $this->replaceVariables($subject !== '' ? $subject : $fallback, $variables);
    }

    public function renderEmailBody(array $variables, ?string $locale = null): string
    {
        return $this->replaceVariables($this->resolveLocalizedValue('email_body', $locale), $variables);
    }

    private function resolveLocalizedValue(string $attribute, ?string $locale = null): string
    {
        $locale ??= app()->getLocale();

        $value = $this->getTranslation($attribute, $locale, false)
            ?: $this->getTranslation($attribute, 'tr', false)
            ?: $this->getTranslation($attribute, 'en', false)
            ?: $this->getTranslation($attribute, 'ku', false)
            ?: $this->getAttributeValue($attribute);

        return is_string($value) ? $value : '';
    }

    private function replaceVariables(string $text, array $variables): string
    {
        foreach ($variables as $key => $value) {
            $text = str_replace('{'.$key.'}', $value, $text);
        }

        return $text;
    }
}
