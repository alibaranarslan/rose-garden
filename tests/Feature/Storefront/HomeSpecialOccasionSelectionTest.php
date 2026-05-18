<?php

namespace Tests\Feature\Storefront;

use App\Models\SpecialOccasion;
use App\Services\HomeModuleDataService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class HomeSpecialOccasionSelectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_occasion_spotlight_uses_next_calendar_occurrence_not_past_current_month_date(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-05-19 12:00:00'));

        try {
            SpecialOccasion::query()->create([
                'name' => ['tr' => 'Anneler Günü'],
                'slug' => 'anneler-gunu',
                'date_month' => 5,
                'date_day' => 11,
                'is_active' => true,
            ]);

            SpecialOccasion::query()->create([
                'name' => ['tr' => 'Kurban Bayramı'],
                'slug' => 'kurban-bayrami',
                'date_month' => 5,
                'date_day' => 27,
                'is_active' => true,
            ]);

            SpecialOccasion::query()->create([
                'name' => ['tr' => 'Yılbaşı'],
                'slug' => 'yilbasi',
                'date_month' => 12,
                'date_day' => 31,
                'is_active' => true,
            ]);

            $payload = app(HomeModuleDataService::class)->collect([
                'modules' => [
                    [
                        'key' => 'occasion_spotlight',
                        'is_active' => true,
                        'settings' => ['content_limit' => 4],
                    ],
                ],
            ]);

            $this->assertSame('kurban-bayrami', $payload['activeOccasion']?->slug);
            $this->assertSame(8, $payload['activeOccasion']?->daysUntil());
        } finally {
            Carbon::setTestNow();
        }
    }
}
