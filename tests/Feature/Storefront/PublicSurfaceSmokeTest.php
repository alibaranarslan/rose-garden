<?php

namespace Tests\Feature\Storefront;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Setting;
use Tests\TestCase;

class PublicSurfaceSmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_health_endpoint_returns_json_and_ok_status(): void
    {
        $response = $this->getJson('/health');

        $response->assertOk();
        $response->assertJsonPath('status', 'ok');
        $response->assertJsonPath('app', 'rose-garden');
        $response->assertJsonPath('database', 'ok');
    }

    public function test_sitemap_xml_route_matches_public_file_presence(): void
    {
        $path = public_path('sitemap.xml');
        $response = $this->get('/sitemap.xml');

        if (is_file($path)) {
            $response->assertOk();
            $response->assertHeader('Content-Type', 'application/xml');
        } else {
            $response->assertNotFound();
        }
    }

    public function test_robots_txt_includes_extra_rules_and_current_sitemap_url(): void
    {
        $this->assertFalse(
            is_file(public_path('robots.txt')),
            'public/robots.txt must not exist because it shadows the dynamic Laravel robots route in local and production web servers.'
        );

        config(['app.url' => 'https://example.test']);

        Setting::set('seo', 'robots_txt', "User-agent: *\nAllow: /\nSitemap: https://old.example/sitemap.xml");
        Setting::set('seo', 'robots_txt_extra', "Disallow: /admin");

        $this->get('/robots.txt')
            ->assertOk()
            ->assertHeader('Content-Type', 'text/plain; charset=utf-8')
            ->assertSee('User-agent: *', false)
            ->assertSee('Disallow: /admin', false)
            ->assertSee('Sitemap: https://example.test/sitemap.xml', false);
    }

    public function test_locale_prefixed_storefront_home_returns_success(): void
    {
        foreach (['tr', 'en', 'ku'] as $locale) {
            $this->get('/'.$locale.'/')
                ->assertOk();
        }
    }

    public function test_locale_prefixed_core_paths_return_success(): void
    {
        $paths = ['urunler', 'blog', 'sepet', 'iletisim', 'ozel-gunler', 'siparis-takip', 'sss'];
        foreach (['en', 'ku'] as $locale) {
            foreach ($paths as $path) {
                $this->get('/'.$locale.'/'.$path)
                    ->assertOk();
            }
        }
    }

    public function test_contact_page_refreshes_do_not_hit_form_submission_rate_limit(): void
    {
        foreach (range(1, 5) as $attempt) {
            $this->get('/tr/iletisim')
                ->assertOk();
        }
    }

    public function test_locale_prefixed_product_listing_does_not_confuse_locale_with_category_slug(): void
    {
        // Regression: {locale} must bind to ProductController::index $locale, not $slug (was 404 for /en/urunler).
        $this->get('/en/urunler')->assertOk();
        $this->get('/ku/urunler')->assertOk();
    }

    public function test_locale_prefixed_checkout_success_and_fail_render(): void
    {
        foreach (['tr', 'en', 'ku'] as $locale) {
            $this->get('/'.$locale.'/odeme/basarili')
                ->assertOk();
            $this->get('/'.$locale.'/odeme/basarisiz')
                ->assertOk();
        }
    }

    public function test_cookie_consent_json_endpoint_accepts_valid_categories(): void
    {
        $this->withoutMiddleware(VerifyCsrfToken::class);

        $response = $this->postJson(route('cookie-consent.store'), [
            'categories' => ['necessary', 'analytics'],
        ]);

        $response->assertOk();
        $response->assertJsonPath('status', 'ok');
        $this->assertDatabaseHas('cookie_consents', [
            'session_id' => session()->getId(),
        ]);
    }

    public function test_checkout_success_and_fail_pages_render(): void
    {
        $this->get('/odeme/basarili')
            ->assertOk();

        $this->get('/odeme/basarisiz')
            ->assertOk();
    }

    public function test_shell_fast_track_surfaces_keep_opaque_language_switcher_and_checkout_logo_shell(): void
    {
        $this->get('/en/')
            ->assertOk()
            ->assertSee('bg-white/96', false)
            ->assertSee('bg-[#21162c]', false)
            ->assertSee('bg-[#1b1226]', false)
            ->assertSee('shadow-[0_20px_44px_rgba(34,24,40,0.18)]', false);

        $this->get('/odeme')
            ->assertOk()
            ->assertSee('bg-white/92', false)
            ->assertSee('dark:bg-[#1f1429]', false)
            ->assertSee('dark:drop-shadow-[0_2px_10px_rgba(255,255,255,0.08)]', false);
    }

    public function test_guest_loyalty_prompt_renders_on_public_shell(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSeeText('Üye ol, puan biriktir')
            ->assertSeeText('Daha sonra')
            ->assertSeeText('Üye ol');
    }
}
