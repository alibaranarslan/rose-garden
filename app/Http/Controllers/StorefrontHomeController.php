<?php

namespace App\Http\Controllers;

use App\Models\HeaderTheme;
use App\Models\LayoutRevision;
use App\Services\HomeModuleDataService;
use App\Services\LayoutConfigService;
use App\Support\StorefrontImage;
use App\Support\StorefrontLocale;
use Illuminate\Support\Facades\URL;

/**
 * Canonical live homepage owner for the public storefront.
 *
 * Active route ownership is routes/web.php named `home`, and the render target is
 * resources/views/home/layout-studio.blade.php. Legacy HomeController/home.index remain
 * reference-only and should not be treated as the live homepage path.
 */
class StorefrontHomeController extends Controller
{
    public function __construct(
        private readonly LayoutConfigService $layoutConfigService,
        private readonly HomeModuleDataService $homeModuleDataService,
    ) {
    }

    public function index()
    {
        return $this->renderHome();
    }

    public function preview(LayoutRevision $revision)
    {
        abort_unless($revision->area === LayoutConfigService::AREA_HOME, 404);

        $this->applyPreviewLocale();

        return $this->renderHome($revision);
    }

    public function themePreview(HeaderTheme $headerTheme)
    {
        $this->applyPreviewLocale();

        return $this->renderHome();
    }

    private function renderHome(?LayoutRevision $previewRevision = null)
    {
        // Layout Studio publish state is the live homepage truth source for production storefront output.
        $layoutState = $this->layoutConfigService->resolveState($previewRevision);
        $payload = $this->homeModuleDataService->collect($layoutState);
        $sections = $this->homeModuleDataService->buildSections($layoutState, $payload);
        $heroProduct = $payload['heroProduct'] ?? null;
        $ogImage = $heroProduct
            ? StorefrontImage::resolveProduct(
                $heroProduct->primaryImage,
                $heroProduct->slug,
                $heroProduct->name,
            )
            : StorefrontImage::productPlaceholderImgSrc();

        return view('home.layout-studio', array_merge($payload, [
            'layoutState' => $layoutState,
            'layoutSections' => $sections,
            'layoutPreviewRevision' => $previewRevision,
            'metaTitle' => null,
            'metaDescription' => 'Rose Garden butik storefront deneyimi, Layout Studio ile yayınlanır.',
            'ogImage' => $ogImage,
        ]));
    }

    private function applyPreviewLocale(): void
    {
        $locale = StorefrontLocale::normalize(
            request()->string('locale')->toString(),
            StorefrontLocale::current()
        );

        app()->setLocale($locale);
        URL::defaults(['locale' => $locale]);
    }
}

