<?php

namespace Tests\Feature\Admin;

use App\Filament\Pages\SeoSettings;
use App\Filament\Resources\BlogPostResource\Pages\CreateBlogPost;
use App\Filament\Resources\BlogPostResource\Pages\EditBlogPost;
use App\Filament\Resources\PageResource\Pages\CreatePage;
use App\Filament\Resources\PageResource\Pages\EditPage;
use App\Models\BlogPost;
use App\Models\Page;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Livewire\Livewire;
use Tests\TestCase;

class AdminContentSeoReflectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_created_blog_post_is_visible_on_storefront_blog_surfaces(): void
    {
        $admin = $this->adminUser();

        $this->get(route('blog.index'))
            ->assertOk()
            ->assertDontSeeText('Admin Phase3 Blog');

        Livewire::actingAs($admin)
            ->test(CreateBlogPost::class)
            ->set('data.title', 'Admin Phase3 Blog')
            ->set('data.slug', 'admin-phase3-blog')
            ->set('data.excerpt', 'Admin Phase3 blog excerpt')
            ->set('data.content', '<p>Admin Phase3 blog body</p>')
            ->set('data.status', 'published')
            ->set('data.meta_title', 'Admin Phase3 Blog Meta')
            ->set('data.meta_description', 'Admin Phase3 blog meta description')
            ->call('create')
            ->assertHasNoErrors();

        $this->assertNotSame('', (string) Setting::get('system', 'storefront_content_version', ''));

        $this->get(route('blog.index'))
            ->assertOk()
            ->assertSeeText('Admin Phase3 Blog')
            ->assertSeeText('Admin Phase3 blog excerpt');

        $this->get(route('blog.show', ['slug' => 'admin-phase3-blog']))
            ->assertOk()
            ->assertSeeText('Admin Phase3 Blog')
            ->assertSeeText('Admin Phase3 blog body')
            ->assertSee('Admin Phase3 Blog Meta', false)
            ->assertSee('Admin Phase3 blog meta description', false);
    }

    public function test_blog_post_form_rejects_invalid_slug_and_oversized_seo_fields(): void
    {
        $admin = $this->adminUser();

        Livewire::actingAs($admin)
            ->test(CreateBlogPost::class)
            ->set('data.title', 'Invalid Blog Post')
            ->set('data.slug', 'invalid blog post')
            ->set('data.excerpt', str_repeat('x', 261))
            ->set('data.content', '<p>Valid body</p>')
            ->set('data.status', 'published')
            ->set('data.meta_title', str_repeat('x', 71))
            ->set('data.meta_description', str_repeat('x', 161))
            ->call('create')
            ->assertHasErrors([
                'data.slug',
                'data.excerpt',
                'data.meta_title',
                'data.meta_description',
            ]);
    }

    public function test_admin_blog_edit_replaces_cached_storefront_detail(): void
    {
        $admin = $this->adminUser();
        $post = BlogPost::query()->create([
            'title' => ['tr' => 'Cached Blog Title'],
            'slug' => 'cached-blog-title',
            'excerpt' => ['tr' => 'Cached blog excerpt'],
            'content' => ['tr' => '<p>Cached blog body</p>'],
            'status' => 'published',
        ]);

        $this->get(route('blog.show', ['slug' => $post->slug]))
            ->assertOk()
            ->assertSeeText('Cached blog body');

        Livewire::actingAs($admin)
            ->test(EditBlogPost::class, ['record' => $post->getRouteKey()])
            ->set('data.content', '<p>Updated admin blog body</p>')
            ->call('save')
            ->assertHasNoErrors();

        $this->get(route('blog.show', ['slug' => $post->slug]))
            ->assertOk()
            ->assertSeeText('Updated admin blog body')
            ->assertDontSeeText('Cached blog body');
    }

    public function test_admin_created_page_is_visible_and_edit_replaces_cached_storefront_page(): void
    {
        $admin = $this->adminUser();

        Livewire::actingAs($admin)
            ->test(CreatePage::class)
            ->set('data.title', 'Admin Phase3 Page')
            ->set('data.slug', 'admin-phase3-page')
            ->set('data.content', '<p>Admin Phase3 page body</p>')
            ->set('data.meta_title', 'Admin Phase3 Page Meta')
            ->set('data.meta_description', 'Admin Phase3 page meta description')
            ->set('data.is_published', true)
            ->call('create')
            ->assertHasNoErrors();

        $page = Page::query()->where('slug', 'admin-phase3-page')->firstOrFail();

        $this->get(route('page.show', ['slug' => $page->slug]))
            ->assertOk()
            ->assertSeeText('Admin Phase3 Page')
            ->assertSeeText('Admin Phase3 page body')
            ->assertSee('Admin Phase3 Page Meta', false);

        Livewire::actingAs($admin)
            ->test(EditPage::class, ['record' => $page->getRouteKey()])
            ->set('data.content', '<p>Updated admin page body</p>')
            ->call('save')
            ->assertHasNoErrors();

        $this->get(route('page.show', ['slug' => $page->slug]))
            ->assertOk()
            ->assertSeeText('Updated admin page body')
            ->assertDontSeeText('Admin Phase3 page body');
    }

    public function test_seo_settings_refresh_cached_pages_and_update_robots_output(): void
    {
        $admin = $this->adminUser();

        $this->get(route('home'))
            ->assertOk()
            ->assertDontSee('https://phase3.example.test', false);

        Livewire::actingAs($admin)
            ->test(SeoSettings::class)
            ->set('data.meta_title_suffix', '| Phase3 SEO')
            ->set('data.meta_description_default', 'Phase3 default SEO description')
            ->set('data.og_default_image', '/images/product-placeholder.svg')
            ->set('data.google_analytics_id', 'G-PHASE3SEO1')
            ->set('data.google_search_console_code', 'phase3-search-console-code')
            ->set('data.robots_txt_extra', "Disallow: /phase3-private\nAllow: /phase3-public")
            ->set('data.canonical_domain', 'phase3.example.test/path')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame('https://phase3.example.test', Setting::get('seo', 'canonical_domain'));

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('<link rel="canonical" href="https://phase3.example.test">', false)
            ->assertSee('phase3-search-console-code', false)
            ->assertSee('G-PHASE3SEO1', false);

        $this->get('/robots.txt')
            ->assertOk()
            ->assertSeeText('Sitemap: https://phase3.example.test/sitemap.xml')
            ->assertSeeText('Disallow: /phase3-private')
            ->assertSeeText('Allow: /phase3-public');
    }

    public function test_sitemap_generation_includes_published_blog_and_pages_with_canonical_domain(): void
    {
        Setting::set('seo', 'canonical_domain', 'https://phase3-sitemap.example.test');

        BlogPost::query()->create([
            'title' => ['tr' => 'Sitemap Blog'],
            'slug' => 'sitemap-blog',
            'content' => ['tr' => '<p>Sitemap blog body</p>'],
            'status' => 'published',
        ]);

        Page::query()->create([
            'title' => ['tr' => 'Sitemap Page'],
            'slug' => 'sitemap-page',
            'content' => ['tr' => '<p>Sitemap page body</p>'],
            'is_published' => true,
        ]);

        $this->artisan('sitemap:generate')->assertExitCode(0);

        $sitemap = File::get(public_path('sitemap.xml'));

        $this->assertStringContainsString('https://phase3-sitemap.example.test/blog/sitemap-blog', $sitemap);
        $this->assertStringContainsString('https://phase3-sitemap.example.test/sayfa/sitemap-page', $sitemap);
    }

    private function adminUser(): User
    {
        return User::factory()->create([
            'is_admin' => true,
            'is_active' => true,
        ]);
    }
}
