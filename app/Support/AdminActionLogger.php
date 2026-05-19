<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class AdminActionLogger
{
    public static function record(string $action, ?Model $subject = null, array $context = []): void
    {
        Log::info('Admin aksiyonu', [
            'action' => $action,
            'admin_id' => auth()->id(),
            'admin_email' => auth()->user()?->email,
            'subject_type' => $subject ? $subject::class : null,
            'subject_id' => $subject?->getKey(),
            'path' => request()?->path(),
            'ip' => request()?->ip(),
            'context' => $context,
        ]);
    }
}
