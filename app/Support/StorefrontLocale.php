<?php

namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class StorefrontLocale
{
    private const LABELS = [
        'tr' => 'Türkçe',
        'en' => 'English',
        'ku' => 'Kurdî',
    ];

    private const OG_LOCALES = [
        'tr' => 'tr_TR',
        'en' => 'en_US',
        'ku' => 'ku_TR',
    ];

    private const DEFAULT = 'tr';

    public static function labels(): array
    {
        return self::LABELS;
    }

    /**
     * @return list<string>
     */
    public static function codes(): array
    {
        return array_keys(self::LABELS);
    }

    public static function default(): string
    {
        return self::DEFAULT;
    }

    public static function current(): string
    {
        return self::normalize(app()->getLocale());
    }

    public static function isSupported(?string $locale): bool
    {
        return $locale !== null && array_key_exists($locale, self::LABELS);
    }

    public static function normalize(?string $locale, ?string $fallback = null): string
    {
        if (self::isSupported($locale)) {
            return (string) $locale;
        }

        $resolvedFallback = $fallback !== null && self::isSupported($fallback)
            ? $fallback
            : self::DEFAULT;

        return $resolvedFallback;
    }

    public static function resolveRequestLocale(Request $request): string
    {
        return self::normalize(
            $request->route('locale')
                ?? $request->query('locale')
                ?? $request->session()->get('locale'),
            self::DEFAULT
        );
    }

    public static function routeConstraint(): array
    {
        return ['locale' => self::pattern()];
    }

    public static function pattern(): string
    {
        return implode('|', self::codes());
    }

    /**
     * @return list<string>
     */
    public static function fallbackCandidates(?string $locale = null): array
    {
        $current = self::normalize($locale, self::current());

        return collect([$current, self::DEFAULT])
            ->concat(self::codes())
            ->filter(fn (mixed $candidate) => is_string($candidate) && self::isSupported($candidate))
            ->unique()
            ->values()
            ->all();
    }

    public static function ogLocale(?string $locale = null): string
    {
        $locale = self::normalize($locale, self::current());

        return self::OG_LOCALES[$locale] ?? self::OG_LOCALES[self::DEFAULT];
    }

    public static function stripPrefix(string $path): string
    {
        $trimmed = trim($path);
        $normalized = $trimmed === '' ? '/' : '/'.ltrim($trimmed, '/');

        $segments = explode('/', ltrim($normalized, '/'));
        $first = $segments[0] ?? null;

        if (! self::isSupported($first)) {
            return $normalized;
        }

        array_shift($segments);
        $remaining = implode('/', $segments);

        return $remaining === '' ? '/' : '/'.$remaining;
    }

    public static function path(string $path, ?string $locale = null, bool $prefixDefault = false): string
    {
        $locale = self::normalize($locale, self::current());
        $canonicalPath = self::stripPrefix($path);
        $canonicalPath = $canonicalPath === '' ? '/' : $canonicalPath;

        if ($locale === self::DEFAULT && ! $prefixDefault) {
            return $canonicalPath;
        }

        if ($canonicalPath === '/') {
            return '/'.$locale;
        }

        return '/'.$locale.$canonicalPath;
    }

    public static function currentRequestUrl(?string $locale = null, bool $prefixDefault = false, ?string $root = null): string
    {
        return self::urlForPath(
            request()->getPathInfo(),
            $locale,
            request()->except('locale'),
            $prefixDefault,
            $root
        );
    }

    public static function urlForPath(
        string $path,
        ?string $locale = null,
        array $query = [],
        bool $prefixDefault = false,
        ?string $root = null
    ): string {
        $root = rtrim($root ?: url('/'), '/');
        $localizedPath = self::path($path, $locale, $prefixDefault);
        $queryString = http_build_query(Arr::except($query, ['locale']));

        return $root.($localizedPath === '/' ? '' : $localizedPath).($queryString !== '' ? '?'.$queryString : '');
    }

    public static function route(
        string $name,
        array $parameters = [],
        ?string $locale = null,
        bool $absolute = true,
        bool $prefixDefault = false
    ): string {
        $hasExplicitLocale = array_key_exists('locale', $parameters) || $locale !== null;
        $shouldPreserveCurrentPrefix = ! $hasExplicitLocale && request()->route('locale') !== null;
        $requestedLocale = self::normalize($parameters['locale'] ?? $locale, self::current());
        unset($parameters['locale']);

        $baseUrl = route($name, $parameters, $absolute);
        $parts = parse_url($baseUrl);

        $path = $parts['path'] ?? '/';
        $query = [];
        parse_str($parts['query'] ?? '', $query);
        unset($query['locale']);

        $localizedPath = self::path($path, $requestedLocale, $prefixDefault || $shouldPreserveCurrentPrefix);
        $queryString = http_build_query($query);

        if (! $absolute) {
            return $localizedPath.($queryString !== '' ? '?'.$queryString : '');
        }

        $scheme = $parts['scheme'] ?? request()->getScheme();
        $host = $parts['host'] ?? request()->getHost();
        $port = isset($parts['port']) ? ':'.$parts['port'] : '';

        return "{$scheme}://{$host}{$port}{$localizedPath}".($queryString !== '' ? '?'.$queryString : '');
    }
}
