<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class SeoDefaults
{
    public function handle(Request $request, Closure $next): Response
    {
        $noindex = $request->filled('page') || $request->filled('q');
        $canonicalDomain = $this->normalizeCanonicalDomain(\App\Models\Setting::get('seo', 'canonical_domain', ''));

        if ($canonicalDomain !== '') {
            $canonical = \App\Support\StorefrontLocale::currentRequestUrl(
                \App\Support\StorefrontLocale::current(),
                false,
                $canonicalDomain
            );
        } else {
            $path = $request->path();
            $defaultLocale = config('app.locale', 'tr');

            if (preg_match('#^' . preg_quote($defaultLocale, '#') . '(/|$)#', $path)) {
                $stripped = preg_replace('#^' . preg_quote($defaultLocale, '#') . '(/|$)#', '', $path, 1);
                $canonical = url('/' . ltrim($stripped, '/'));
            } else {
                $canonical = $request->url();
            }
        }

        View::share('canonical', $canonical);
        View::share('noindex', $noindex);

        return $next($request);
    }

    private function normalizeCanonicalDomain(mixed $value): string
    {
        $value = trim((string) $value);

        if ($value === '') {
            return '';
        }

        if (! str_starts_with($value, 'http://') && ! str_starts_with($value, 'https://')) {
            $value = 'https://'.$value;
        }

        $parts = parse_url($value);

        if (! is_array($parts) || empty($parts['host'])) {
            return '';
        }

        $scheme = $parts['scheme'] ?? 'https';
        $port = isset($parts['port']) ? ':'.$parts['port'] : '';

        return "{$scheme}://{$parts['host']}{$port}";
    }
}
