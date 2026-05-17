<?php

namespace Tests\Unit\Support;

use App\Support\StorefrontLocale;
use Illuminate\Http\Request;
use Tests\TestCase;

class StorefrontLocaleTest extends TestCase
{
    public function test_it_generates_prefixed_non_default_storefront_routes_without_locale_query_string(): void
    {
        $url = StorefrontLocale::route('home', ['locale' => 'en']);

        $this->assertSame('/en', parse_url($url, PHP_URL_PATH));
        $this->assertStringNotContainsString('?locale=en', $url);
    }

    public function test_it_keeps_default_locale_routes_canonical_without_prefix(): void
    {
        $url = StorefrontLocale::route('products.index', ['locale' => 'tr']);

        $this->assertSame('/urunler', parse_url($url, PHP_URL_PATH));
        $this->assertStringNotContainsString('/tr/urunler', $url);
    }

    public function test_it_rewrites_current_request_url_and_preserves_query_string(): void
    {
        $request = Request::create('/en/arama', 'GET', ['q' => 'gul', 'page' => 2]);
        app()->instance('request', $request);

        $rewritten = StorefrontLocale::currentRequestUrl('ku', true);

        $this->assertSame('/ku/arama', parse_url($rewritten, PHP_URL_PATH));
        $this->assertSame('q=gul&page=2', parse_url($rewritten, PHP_URL_QUERY));
    }
}
