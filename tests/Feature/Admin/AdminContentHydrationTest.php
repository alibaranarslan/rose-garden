<?php

namespace Tests\Feature\Admin;

use App\Filament\Resources\BlogCategoryResource\Pages\CreateBlogCategory;
use App\Filament\Resources\BlogPostResource\Pages\CreateBlogPost;
use App\Filament\Resources\PageResource\Pages\CreatePage;
use App\Filament\Resources\SpecialOccasionResource\Pages\CreateSpecialOccasion;
use App\Models\BlogPost;
use App\Models\BlogCategory;
use App\Models\Page;
use App\Models\SpecialOccasion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminContentHydrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_blog_rich_content_persists_through_filament_create_state(): void
    {
        $admin = $this->adminUser();

        Livewire::actingAs($admin)
            ->test(CreateBlogPost::class)
            ->set('data.title', 'Hydration Blog')
            ->set('data.slug', 'hydration-blog')
            ->set('data.content', '<p>Hydration blog body</p>')
            ->set('data.status', 'published')
            ->call('create')
            ->assertHasNoErrors();

        $post = BlogPost::query()->where('slug', 'hydration-blog')->firstOrFail();

        $this->assertSame('<p>Hydration blog body</p>', $post->getTranslation('content', 'tr'));
    }

    public function test_page_rich_content_persists_through_filament_create_state(): void
    {
        $admin = $this->adminUser();

        Livewire::actingAs($admin)
            ->test(CreatePage::class)
            ->set('data.title', 'Hydration Page')
            ->set('data.slug', 'hydration-page')
            ->set('data.content', '<p>Hydration page body</p>')
            ->set('data.is_published', true)
            ->call('create')
            ->assertHasNoErrors();

        $page = Page::query()->where('slug', 'hydration-page')->firstOrFail();

        $this->assertSame('<p>Hydration page body</p>', $page->getTranslation('content', 'tr'));
    }

    public function test_special_occasion_date_select_values_persist_as_integers(): void
    {
        $admin = $this->adminUser();

        Livewire::actingAs($admin)
            ->test(CreateSpecialOccasion::class)
            ->set('data.name', 'Hydration Occasion')
            ->set('data.slug', 'hydration-occasion')
            ->set('data.date_month', '5')
            ->set('data.date_day', '9')
            ->set('data.is_active', true)
            ->call('create')
            ->assertHasNoErrors();

        $occasion = SpecialOccasion::query()->where('slug', 'hydration-occasion')->firstOrFail();

        $this->assertSame(5, $occasion->date_month);
        $this->assertSame(9, $occasion->date_day);
    }

    public function test_special_occasion_rejects_invalid_calendar_and_multiplier_values(): void
    {
        $admin = $this->adminUser();

        Livewire::actingAs($admin)
            ->test(CreateSpecialOccasion::class)
            ->set('data.name', 'Invalid Occasion')
            ->set('data.slug', 'invalid occasion')
            ->set('data.date_month', '2')
            ->set('data.date_day', '31')
            ->set('data.loyalty_multiplier', 99)
            ->set('data.is_active', true)
            ->call('create')
            ->assertHasErrors(['data.slug', 'data.date_day', 'data.loyalty_multiplier']);
    }

    public function test_blog_category_rejects_invalid_slug_and_sort_order(): void
    {
        $admin = $this->adminUser();

        Livewire::actingAs($admin)
            ->test(CreateBlogCategory::class)
            ->set('data.name', 'Invalid Blog Category')
            ->set('data.slug', 'invalid blog category')
            ->set('data.sort_order', -1)
            ->call('create')
            ->assertHasErrors(['data.slug', 'data.sort_order']);

        $this->assertFalse(BlogCategory::query()->where('slug', 'invalid blog category')->exists());
    }

    public function test_published_page_requires_content_and_rejects_invalid_slug(): void
    {
        $admin = $this->adminUser();

        Livewire::actingAs($admin)
            ->test(CreatePage::class)
            ->set('data.title', 'Invalid Published Page')
            ->set('data.slug', 'invalid published page')
            ->set('data.is_published', true)
            ->set('data.sort_order', -1)
            ->call('create')
            ->assertHasErrors(['data.slug', 'data.content', 'data.sort_order']);
    }

    private function adminUser(): User
    {
        return User::factory()->create([
            'is_admin' => true,
            'is_active' => true,
        ]);
    }
}
