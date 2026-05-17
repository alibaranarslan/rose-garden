<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

class CachePage
{
    public function handle(Request $request, Closure $next, int $ttl = 300): Response
    {
        // Only cache GET requests for guests
        if (! $request->isMethod('GET') || auth()->check()) {
            return $next($request);
        }

        $key = 'page_cache_'.md5($request->fullUrl().'_'.app()->getLocale().'_'.$this->surfaceVersion());

        if (Cache::has($key)) {
            $cached = Cache::get($key);
            // Cached HTML embeds CSRF + Livewire snapshots tied to the user who warmed the cache.
            // Serving it to other sessions causes 419 and Livewire's "page has expired" dialog.
            if ($this->htmlContainsLivewire($cached['content'] ?? '')) {
                Cache::forget($key);
            } else {
                return response($cached['content'], 200, $cached['headers']);
            }
        }

        $response = $next($request);

        // Only cache successful HTML responses
        if (
            $response->isSuccessful()
            && str_contains($response->headers->get('Content-Type', ''), 'text/html')
        ) {
            $content = $response->getContent();
            if (! $this->htmlContainsLivewire($content)) {
                Cache::put($key, [
                    'content' => $content,
                    'headers' => ['Content-Type' => 'text/html; charset=UTF-8'],
                ], $ttl);
            }
        }

        return $response;
    }

    private function htmlContainsLivewire(string $html): bool
    {
        return str_contains($html, 'wire:snapshot')
            || str_contains($html, 'wire:id=');
    }

    private function surfaceVersion(): string
    {
        try {
            if (! Schema::hasTable('settings')) {
                return '0';
            }

            return implode(':', [
                (string) Setting::get('system', 'header_theme_version', '0'),
                (string) Setting::get('system', 'layout_version', '0'),
                (string) Setting::get('system', 'storefront_content_version', '0'),
            ]);
        } catch (\Throwable) {
            return '0';
        }
    }
}
