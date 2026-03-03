<?php

namespace App\Http\Middleware;

use App\Jobs\RecordPageViewJob;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackPageView
{
    private const BOT_PATTERNS = [
        'bot', 'crawler', 'spider', 'scraper', 'curl', 'wget', 'python', 'java',
        'googlebot', 'bingbot', 'slurp', 'duckduckbot', 'baiduspider', 'yandexbot',
        'facebookexternalhit', 'twitterbot', 'linkedinbot', 'whatsapp',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($this->shouldTrack($request)) {
            RecordPageViewJob::dispatch($this->buildPayload($request))->onQueue('analytics');
        }

        return $response;
    }

    private function shouldTrack(Request $request): bool
    {
        if (!$request->isMethod('GET')) return false;
        if ($request->is('admin*')) return false;
        if ($request->is('api/*')) return false;

        $ua = strtolower($request->userAgent() ?? '');
        foreach (self::BOT_PATTERNS as $pattern) {
            if (str_contains($ua, $pattern)) return false;
        }

        return true;
    }

    private function buildPayload(Request $request): array
    {
        $ua         = $request->userAgent() ?? '';
        $deviceType = $this->detectDeviceType($ua);

        return [
            'viewable_type' => null,
            'viewable_id'   => null,
            'url'           => $request->path(),
            'session_id'    => session()->getId(),
            'ip_address'    => $request->ip(),
            'user_agent'    => substr($ua, 0, 500),
            'referer'       => substr($request->headers->get('referer', '') ?? '', 0, 500),
            'device_type'   => $deviceType,
            'user_id'       => auth()->id(),
            'created_at'    => now(),
        ];
    }

    private function detectDeviceType(string $ua): string
    {
        $ua = strtolower($ua);
        if (str_contains($ua, 'tablet') || str_contains($ua, 'ipad')) return 'tablet';
        if (str_contains($ua, 'mobile') || str_contains($ua, 'android') || str_contains($ua, 'iphone')) return 'mobile';
        return 'desktop';
    }
}
