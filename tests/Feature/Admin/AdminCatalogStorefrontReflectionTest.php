<?php

namespace Tests\Feature\Admin;

use App\Filament\Pages\LayoutStudio;
use App\Filament\Resources\CategoryResource\Pages\CreateCategory;
use App\Filament\Resources\CategoryResource\Pages\EditCategory;
use App\Filament\Resources\HeaderThemeResource\Pages\CreateHeaderTheme;
use App\Filament\Resources\ProductResource\Pages\CreateProduct;
use App\Filament\Resources\SpecialOccasionResource\Pages\CreateSpecialOccasion;
use App\Models\Category;
use App\Models\HeaderTheme;
use App\Models\LayoutRevision;
use App\Models\Product;
use App\Models\SpecialOccasion;
use App\Models\User;
use App\Services\HomeModuleDataService;
use App\Services\LayoutConfigService;
use Carbon\CarbonImmutable;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Livewire\Livewire;
use Tests\TestCase;

class AdminCatalogStorefrontReflectionTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        CarbonImmutable::setTestNow();

        parent::tearDown();
    }

    public function test_admin_created_category_and_product_are_visible_on_storefront_catalog_surfaces(): void
    {
        $admin = $this->makeAdmin();

        Livewire::actingAs($admin)
            ->test(CreateCategory::class)
            ->set('data.name', 'Admin Vitrin Kategorisi')
            ->set('data.slug', 'admin-vitrin-kategorisi')
            ->set('data.description', 'Admin kategori aciklamasi')
            ->set('data.is_active', true)
            ->set('data.sort_order', 1)
            ->call('create')
            ->assertHasNoErrors();

        $category = Category::query()->where('slug', 'admin-vitrin-kategorisi')->firstOrFail();

        Livewire::actingAs($admin)
            ->test(CreateProduct::class)
            ->set('data.name', 'Admin Vitrin Urunu')
            ->set('data.slug', 'admin-vitrin-urunu')
            ->set('data.short_description', 'Admin tarafindan olusturulan urun.')
            ->set('data.description', '<p>Admin urun detayi.</p>')
            ->set('data.price', 1250)
            ->set('data.status', 'active')
            ->set('data.stock_status', 'in_stock')
            ->set('data.is_featured', true)
            ->set('data.is_new', true)
            ->set('data.categories', [$category->id])
            ->set('data.images', [])
            ->set('data.variants', [])
            ->call('create')
            ->assertHasNoErrors();

        $product = Product::query()->where('slug', 'admin-vitrin-urunu')->firstOrFail();

        $this->assertTrue($product->categories()->whereKey($category->id)->exists());

        $this->get(route('products.show', ['slug' => $product->slug]))
            ->assertOk()
            ->assertSeeText('Admin Vitrin Urunu')
            ->assertSeeText('Admin tarafindan olusturulan urun.');

        $this->get(route('products.category', ['slug' => $category->slug]))
            ->assertOk()
            ->assertSeeText('Admin Vitrin Urunu');
    }

    public function test_admin_created_special_occasion_surfaces_related_products(): void
    {
        $admin = $this->makeAdmin('occasion-admin@example.com');
        $category = Category::query()->create([
            'name' => ['tr' => 'Admin Ozel Gun Kategorisi'],
            'slug' => 'admin-ozel-gun-kategorisi',
            'is_active' => true,
        ]);

        $product = Product::query()->create([
            'name' => ['tr' => 'Ozel Gun Admin Urunu'],
            'slug' => 'ozel-gun-admin-urunu',
            'short_description' => ['tr' => 'Ozel gun icin admin urunu.'],
            'description' => ['tr' => '<p>Ozel gun admin detayi.</p>'],
            'price' => 975,
            'status' => 'active',
            'stock_status' => 'in_stock',
            'is_featured' => true,
        ]);
        $product->categories()->attach($category);

        Livewire::actingAs($admin)
            ->test(CreateSpecialOccasion::class)
            ->set('data.name', 'Admin Ozel Gunu')
            ->set('data.slug', 'admin-ozel-gunu')
            ->set('data.date_month', '6')
            ->set('data.date_day', '15')
            ->set('data.category_id', $category->id)
            ->set('data.loyalty_multiplier', 2)
            ->set('data.is_active', true)
            ->call('create')
            ->assertHasNoErrors();

        $occasion = SpecialOccasion::query()->where('slug', 'admin-ozel-gunu')->firstOrFail();

        $this->get(route('special-occasions.show', ['slug' => $occasion->slug]))
            ->assertOk()
            ->assertSeeText('Admin Ozel Gunu')
            ->assertSeeText('Ozel Gun Admin Urunu');
    }

    public function test_admin_created_header_theme_can_render_on_storefront(): void
    {
        $admin = $this->makeSuperAdmin('theme-admin@example.com');

        Livewire::actingAs($admin)
            ->test(CreateHeaderTheme::class)
            ->set('data.name', 'Admin Header Temasi')
            ->set('data.slug', 'admin-header-temasi')
            ->set('data.mode', HeaderTheme::MODE_AUTOMATIC)
            ->set('data.is_enabled', true)
            ->set('data.priority', 999)
            ->set('data.banner_message', 'Admin header mesaji')
            ->set('data.theme_type', HeaderTheme::TYPE_FIXED)
            ->set('data.month', '7')
            ->set('data.day', '20')
            ->set('data.style_variant', 'tribute')
            ->set('data.illustration_mode', 'inline_svg')
            ->set('data.illustration_asset', 'flowers')
            ->set('data.decor_intensity', 'medium')
            ->call('create')
            ->assertHasNoErrors();

        CarbonImmutable::setTestNow('2026-07-20 10:00:00');
        Cache::flush();

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('theme-admin-header-temasi', false)
            ->assertSeeText('Admin header mesaji');
    }

    public function test_admin_layout_studio_publish_reflects_on_storefront_home(): void
    {
        $admin = $this->makeSuperAdmin('layout-admin@example.com');

        $component = Livewire::actingAs($admin)->test(LayoutStudio::class);
        $modules = $component->get('modules');
        $heroIndex = collect($modules)->search(fn (array $module): bool => ($module['key'] ?? null) === 'hero');

        $this->assertNotFalse($heroIndex);

        foreach ($modules as $index => $module) {
            $component->set("modules.$index.is_active", ($module['key'] ?? null) === 'hero');
        }

        $component
            ->set('selectedModuleKey', 'hero')
            ->set("modules.$heroIndex.settings.title_override.tr", 'Admin Layout Hero Basligi')
            ->set("modules.$heroIndex.settings.subtitle_override.tr", 'Admin layout alt metni')
            ->call('publishDraft')
            ->assertHasNoErrors();

        $publishedHero = collect(LayoutRevision::query()->published()->firstOrFail()->payload['modules'])
            ->first(fn (array $module): bool => ($module['key'] ?? null) === 'hero');

        $this->assertSame('Admin Layout Hero Basligi', data_get($publishedHero, 'settings.title_override.tr'));
        $this->assertSame('Admin layout alt metni', data_get($publishedHero, 'settings.subtitle_override.tr'));
        $resolvedHero = collect(app(LayoutConfigService::class)->getPublishedState()['modules'])
            ->first(fn (array $module): bool => ($module['key'] ?? null) === 'hero');
        $this->assertSame('Admin Layout Hero Basligi', data_get($resolvedHero, 'settings.title_override.tr'));
        $layoutState = app(LayoutConfigService::class)->getPublishedState();
        $homePayload = app(HomeModuleDataService::class)->collect($layoutState);
        $sectionHero = collect(app(HomeModuleDataService::class)->buildSections($layoutState, $homePayload))
            ->first(fn (array $module): bool => ($module['key'] ?? null) === 'hero');
        $this->assertSame('Admin Layout Hero Basligi', data_get($sectionHero, 'settings.title_override.tr'));

        $this->get(route('home'))
            ->assertOk()
            ->assertSeeText('Admin Layout Hero Basligi')
            ->assertSeeText('Admin layout alt metni');
    }

    public function test_product_form_rejects_invalid_sale_price_and_sale_window(): void
    {
        $admin = $this->makeAdmin('catalog-guard@example.com');
        $category = Category::query()->create([
            'name' => ['tr' => 'Kontrol Kategorisi'],
            'slug' => 'kontrol-kategorisi',
            'is_active' => true,
        ]);

        Livewire::actingAs($admin)
            ->test(CreateProduct::class)
            ->set('data.name', 'Kontrol Urunu')
            ->set('data.slug', 'kontrol-urunu')
            ->set('data.price', 100)
            ->set('data.sale_price', 120)
            ->set('data.sale_start', '2026-05-12 10:00:00')
            ->set('data.sale_end', '2026-05-11 10:00:00')
            ->set('data.status', 'active')
            ->set('data.stock_status', 'in_stock')
            ->set('data.categories', [$category->id])
            ->set('data.images', [])
            ->set('data.variants', [])
            ->call('create')
            ->assertHasErrors(['data.sale_price', 'data.sale_end']);
    }

    public function test_product_form_rejects_invalid_price_and_slug(): void
    {
        $admin = $this->makeAdmin('catalog-price-guard@example.com');
        $category = Category::query()->create([
            'name' => ['tr' => 'Fiyat Kontrol Kategorisi'],
            'slug' => 'fiyat-kontrol-kategorisi',
            'is_active' => true,
        ]);

        Livewire::actingAs($admin)
            ->test(CreateProduct::class)
            ->set('data.name', 'Hatalı Urun')
            ->set('data.slug', 'hatalı urun')
            ->set('data.price', -5)
            ->set('data.status', 'active')
            ->set('data.stock_status', 'in_stock')
            ->set('data.categories', [$category->id])
            ->set('data.images', [])
            ->set('data.variants', [])
            ->call('create')
            ->assertHasErrors(['data.slug', 'data.price']);
    }

    public function test_category_form_rejects_invalid_slug_sort_order_and_self_parent(): void
    {
        $admin = $this->makeAdmin('category-guard@example.com');

        Livewire::actingAs($admin)
            ->test(CreateCategory::class)
            ->set('data.name', 'Hatalı Kategori')
            ->set('data.slug', 'hatalı kategori')
            ->set('data.sort_order', -1)
            ->set('data.is_active', true)
            ->call('create')
            ->assertHasErrors(['data.slug', 'data.sort_order']);

        $category = Category::query()->create([
            'name' => ['tr' => 'Kendi Ustu Olamaz'],
            'slug' => 'kendi-ustu-olamaz',
            'is_active' => true,
        ]);

        Livewire::actingAs($admin)
            ->test(EditCategory::class, ['record' => $category->getRouteKey()])
            ->set('data.parent_id', $category->id)
            ->call('save')
            ->assertHasErrors(['data.parent_id']);
    }

    private function makeAdmin(string $email = 'catalog-admin@example.com'): User
    {
        return User::factory()->create([
            'email' => $email,
            'is_admin' => true,
            'is_active' => true,
        ]);
    }

    private function makeSuperAdmin(string $email): User
    {
        $this->seed(RoleSeeder::class);

        $admin = $this->makeAdmin($email);
        $admin->assignRole('super_admin');

        return $admin;
    }
}
