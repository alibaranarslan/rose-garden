<?php

namespace Tests\Unit\Support;

use App\Models\HeaderTheme;
use App\Models\SpecialOccasion;
use App\Support\HeaderThemeResolver;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class HeaderThemeResolverTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        CarbonImmutable::setTestNow();

        parent::tearDown();
    }

    public function test_fixed_theme_matches_preview_date(): void
    {
        $theme = $this->createTheme([
            'slug' => 'sevgililer-gunu',
            'theme_type' => HeaderTheme::TYPE_FIXED,
            'month' => 2,
            'day' => 14,
        ]);

        CarbonImmutable::setTestNow('2026-02-14 10:00:00');

        $resolved = app(HeaderThemeResolver::class)->resolve(Request::create('/', 'GET'));

        $this->assertSame($theme->slug, data_get($resolved, 'id'));
    }

    public function test_nth_weekday_theme_matches_preview_date(): void
    {
        $theme = $this->createTheme([
            'slug' => 'anneler-gunu',
            'theme_type' => HeaderTheme::TYPE_NTH_WEEKDAY,
            'month' => 5,
            'weekday' => 0,
            'nth_week' => 2,
        ]);

        CarbonImmutable::setTestNow('2026-05-10 09:00:00');

        $resolved = app(HeaderThemeResolver::class)->resolve(Request::create('/', 'GET'));

        $this->assertSame($theme->slug, data_get($resolved, 'id'));
    }

    public function test_range_theme_matches_preview_date(): void
    {
        $theme = $this->createTheme([
            'slug' => 'ramazan-bayrami',
            'theme_type' => HeaderTheme::TYPE_RANGE,
            'starts_at' => '2026-03-20',
            'ends_at' => '2026-03-22',
        ]);

        CarbonImmutable::setTestNow('2026-03-21 12:00:00');

        $resolved = app(HeaderThemeResolver::class)->resolve(Request::create('/', 'GET'));

        $this->assertSame($theme->slug, data_get($resolved, 'id'));
    }

    public function test_manual_mode_overrides_automatic_theme(): void
    {
        $automatic = $this->createTheme([
            'slug' => 'sevgililer-gunu',
            'theme_type' => HeaderTheme::TYPE_FIXED,
            'month' => 2,
            'day' => 14,
            'priority' => 120,
        ]);

        $manual = $this->createTheme([
            'slug' => 'ogretmenler-gunu',
            'mode' => HeaderTheme::MODE_MANUAL_ON,
            'theme_type' => HeaderTheme::TYPE_FIXED,
            'month' => 11,
            'day' => 24,
            'priority' => 90,
        ]);

        CarbonImmutable::setTestNow('2026-02-14 10:00:00');

        $resolved = app(HeaderThemeResolver::class)->resolve(Request::create('/', 'GET'));

        $this->assertSame($manual->slug, data_get($resolved, 'id'));
        $this->assertNotSame($automatic->slug, data_get($resolved, 'id'));
    }

    public function test_disabled_theme_is_suppressed(): void
    {
        $this->createTheme([
            'slug' => 'sevgililer-gunu',
            'theme_type' => HeaderTheme::TYPE_FIXED,
            'month' => 2,
            'day' => 14,
            'mode' => HeaderTheme::MODE_DISABLED,
        ]);

        CarbonImmutable::setTestNow('2026-02-14 10:00:00');

        $resolved = app(HeaderThemeResolver::class)->resolve(Request::create('/', 'GET'));

        $this->assertNull($resolved);
    }

    public function test_locale_falls_back_to_turkish_campaign_copy_when_translation_missing(): void
    {
        $this->createTheme([
            'slug' => 'sevgililer-gunu',
            'theme_type' => HeaderTheme::TYPE_FIXED,
            'month' => 2,
            'day' => 14,
            'banner_message' => ['tr' => 'Türkçe mesaj', 'en' => '', 'ku' => ''],
            'headline' => ['tr' => 'Türkçe başlık', 'en' => '', 'ku' => ''],
        ]);

        CarbonImmutable::setTestNow('2026-02-14 10:00:00');
        app()->setLocale('en');

        $resolved = app(HeaderThemeResolver::class)->resolve(Request::create('/', 'GET'));

        $this->assertSame('Türkçe mesaj', data_get($resolved, 'message'));
        $this->assertSame('Türkçe mesaj', data_get($resolved, 'seasonal_message'));
        $this->assertSame('Türkçe başlık', data_get($resolved, 'headline'));
    }

    public function test_campaign_payload_resolves_special_occasion_cta_and_visuals(): void
    {
        SpecialOccasion::query()->create([
            'slug' => 'sevgililer-gunu',
            'name' => ['tr' => 'Sevgililer Günü'],
            'date_month' => 2,
            'date_day' => 14,
            'is_active' => true,
        ]);

        $this->createTheme([
            'slug' => 'sevgililer-gunu',
            'theme_type' => HeaderTheme::TYPE_FIXED,
            'month' => 2,
            'day' => 14,
            'banner_message' => ['tr' => 'Sevgililer seçkisi'],
            'headline' => ['tr' => 'Kampanya başlığı'],
            'subline' => ['tr' => 'Kampanya açıklaması'],
            'cta_label' => ['tr' => 'Seçkiyi gör'],
            'special_occasion_slug' => 'sevgililer-gunu',
            'campaign_image' => 'images/products/kirmizi-gul-buketi.jpg',
        ]);

        CarbonImmutable::setTestNow('2026-02-14 10:00:00');

        $resolved = app(HeaderThemeResolver::class)->resolve(Request::create('/', 'GET'));

        $this->assertSame('Kampanya başlığı', data_get($resolved, 'headline'));
        $this->assertSame('Kampanya açıklaması', data_get($resolved, 'subline'));
        $this->assertSame('Sevgililer seçkisi', data_get($resolved, 'seasonal_message'));
        $this->assertSame('Seçkiyi gör', data_get($resolved, 'cta_label'));
        $this->assertSame('Seçkiyi gör', data_get($resolved, 'seasonal_cta.label'));
        $this->assertSame(data_get($resolved, 'cta_url'), data_get($resolved, 'seasonal_cta.url'));
        $this->assertSame('header_skin', data_get($resolved, 'banner_layout'));
        $this->assertStringContainsString('/ozel-gunler/sevgililer-gunu', data_get($resolved, 'seasonal_cta.url'));
        $this->assertNotEmpty(data_get($resolved, 'campaign_visuals'));
        $this->assertGreaterThanOrEqual(1, count(data_get($resolved, 'campaign_visuals')));
        $this->assertLessThanOrEqual(3, count(data_get($resolved, 'campaign_visuals')));
        $this->assertSame(data_get($resolved, 'campaign_image'), data_get($resolved, 'seasonal_visual'));
    }

    private function createTheme(array $overrides = []): HeaderTheme
    {
        return HeaderTheme::query()->create(array_merge([
            'slug' => 'varsayilan-tema',
            'name' => ['tr' => 'Varsayılan Tema', 'en' => 'Default Theme', 'ku' => 'Tema Bingehîn'],
            'theme_type' => HeaderTheme::TYPE_FIXED,
            'month' => 1,
            'day' => 1,
            'priority' => 100,
            'is_enabled' => true,
            'mode' => HeaderTheme::MODE_AUTOMATIC,
            'banner_message' => ['tr' => 'Varsayılan mesaj', 'en' => 'Default message', 'ku' => 'Peyama bingehîn'],
            'headline' => ['tr' => 'Varsayılan başlık', 'en' => 'Default headline', 'ku' => 'Sernavê bingehîn'],
            'subline' => ['tr' => 'Varsayılan açıklama', 'en' => 'Default subline', 'ku' => 'Raveya bingehîn'],
            'cta_label' => ['tr' => 'Koleksiyonu keşfet', 'en' => 'Explore the collection', 'ku' => 'Koleksiyonê bibîne'],
            'style_variant' => 'tribute',
            'illustration_mode' => 'inline_svg',
            'illustration_asset' => null,
            'decor_intensity' => 'medium',
        ], $overrides));
    }
}
