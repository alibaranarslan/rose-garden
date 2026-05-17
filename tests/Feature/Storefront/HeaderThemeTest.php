<?php

namespace Tests\Feature\Storefront;

use App\Models\HeaderTheme;
use App\Models\SpecialOccasion;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class HeaderThemeTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        CarbonImmutable::setTestNow();

        parent::tearDown();
    }

    public function test_storefront_renders_fixed_campaign_theme_on_matching_day(): void
    {
        SpecialOccasion::query()->create([
            'slug' => 'sevgililer-gunu',
            'name' => ['tr' => 'Sevgililer Günü'],
            'date_month' => 2,
            'date_day' => 14,
            'is_active' => true,
        ]);

        HeaderTheme::query()->create([
            'slug' => 'sevgililer-gunu',
            'name' => ['tr' => 'Sevgililer Günü'],
            'theme_type' => HeaderTheme::TYPE_FIXED,
            'month' => 2,
            'day' => 14,
            'priority' => 160,
            'is_enabled' => true,
            'mode' => HeaderTheme::MODE_AUTOMATIC,
            'banner_message' => ['tr' => '14 Şubat için sınırlı butik seçki'],
            'headline' => ['tr' => 'Romantik buketler hazır.'],
            'subline' => ['tr' => 'Aynı gün teslim özel seçki.'],
            'cta_label' => ['tr' => 'Sevgililer koleksiyonunu keşfet'],
            'special_occasion_slug' => 'sevgililer-gunu',
            'style_variant' => 'romantic',
            'illustration_mode' => 'inline_svg',
            'illustration_asset' => 'hearts',
            'decor_intensity' => 'medium',
        ]);

        CarbonImmutable::setTestNow('2026-02-14 09:00:00');
        Cache::flush();

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('theme-sevgililer-gunu', false)
            ->assertSee('rg-special-day-banner', false)
            ->assertSee('special-day-campaign', false)
            ->assertDontSee('rg-seasonal-header-pill', false)
            ->assertDontSee('special-day-top-bar', false)
            ->assertSee('14 Şubat için sınırlı butik seçki')
            ->assertSee('Romantik buketler hazır.')
            ->assertSee('Sevgililer koleksiyonunu keşfet')
            ->assertSee(route('special-occasions.show', ['slug' => 'sevgililer-gunu']), false);
    }

    public function test_signed_preview_renders_requested_theme_and_preview_badge(): void
    {
        $theme = HeaderTheme::query()->create([
            'slug' => 'ogretmenler-gunu',
            'name' => ['tr' => 'Öğretmenler Günü'],
            'theme_type' => HeaderTheme::TYPE_FIXED,
            'month' => 11,
            'day' => 24,
            'priority' => 120,
            'is_enabled' => true,
            'mode' => HeaderTheme::MODE_DISABLED,
            'banner_message' => ['tr' => 'Öğretmen hediyeleri'],
            'headline' => ['tr' => 'Teşekkürünüzü zarif bir buketle anlatın.'],
            'style_variant' => 'tribute',
            'illustration_mode' => 'inline_svg',
            'illustration_asset' => 'teacher',
            'decor_intensity' => 'soft',
        ]);

        $url = URL::temporarySignedRoute('header-theme.preview.home', now()->addMinutes(30), [
            'headerTheme' => $theme->id,
            'locale' => 'tr',
            'preview_date' => '2026-11-24',
        ]);

        $this->get($url)
            ->assertOk()
            ->assertSee('theme-ogretmenler-gunu', false)
            ->assertSee('Öğretmen hediyeleri')
            ->assertSee('Önizleme');
    }

    public function test_disabled_theme_does_not_render_even_on_matching_day(): void
    {
        HeaderTheme::query()->create([
            'slug' => 'sevgililer-gunu',
            'name' => ['tr' => 'Sevgililer Günü'],
            'theme_type' => HeaderTheme::TYPE_FIXED,
            'month' => 2,
            'day' => 14,
            'priority' => 160,
            'is_enabled' => true,
            'mode' => HeaderTheme::MODE_DISABLED,
            'banner_message' => ['tr' => 'Gizli mesaj'],
            'headline' => ['tr' => 'Gizli kampanya'],
            'style_variant' => 'romantic',
            'illustration_mode' => 'inline_svg',
            'illustration_asset' => 'hearts',
            'decor_intensity' => 'medium',
        ]);

        CarbonImmutable::setTestNow('2026-02-14 09:00:00');
        Cache::flush();

        $this->get(route('home'))
            ->assertOk()
            ->assertDontSee('theme-sevgililer-gunu', false)
            ->assertDontSee('Gizli kampanya');
    }

    public function test_english_locale_uses_translated_campaign_message(): void
    {
        HeaderTheme::query()->create([
            'slug' => 'sevgililer-gunu',
            'name' => ['tr' => 'Sevgililer Günü', 'en' => 'Valentine\'s Day'],
            'theme_type' => HeaderTheme::TYPE_FIXED,
            'month' => 2,
            'day' => 14,
            'priority' => 160,
            'is_enabled' => true,
            'mode' => HeaderTheme::MODE_AUTOMATIC,
            'banner_message' => ['tr' => 'Türkçe mesaj', 'en' => 'English theme message'],
            'headline' => ['tr' => 'Türkçe başlık', 'en' => 'English campaign headline'],
            'cta_label' => ['tr' => 'Koleksiyonu keşfet', 'en' => 'Explore the collection'],
            'style_variant' => 'romantic',
            'illustration_mode' => 'inline_svg',
            'illustration_asset' => 'hearts',
            'decor_intensity' => 'medium',
        ]);

        CarbonImmutable::setTestNow('2026-02-14 09:00:00');
        Cache::flush();

        $this->get('/en/')
            ->assertOk()
            ->assertSee('English theme message')
            ->assertSee('English campaign headline')
            ->assertSee('Explore the collection');
    }
}
