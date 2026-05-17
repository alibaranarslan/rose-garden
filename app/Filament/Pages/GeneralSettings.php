<?php

namespace App\Filament\Pages;

use App\Models\Product;
use App\Models\Setting;
use App\Support\LocalizedSettings;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class GeneralSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationLabel = 'Genel';
    protected static ?string $navigationGroup = 'Ayarlar';
    protected static ?string $title = 'Genel Ayarlar';
    protected static ?int $navigationSort = 20;
    protected static string $view = 'filament.pages.settings';

    public array $data = [];

    public function mount(): void
    {
        $this->data = [
            'site_name' => LocalizedSettings::decodeText(Setting::get('general', 'site_name', 'Rose Garden')),
            'site_tagline' => LocalizedSettings::decodeText(Setting::get('general', 'site_tagline', '')),
            'logo_path' => Setting::get('general', 'logo_path', ''),
            'favicon_path' => Setting::get('general', 'favicon_path', ''),
            'contact_email' => Setting::get('contact', 'contact_email', Setting::get('general', 'contact_email', '')),
            'contact_phone' => Setting::get('contact', 'contact_phone', Setting::get('general', 'contact_phone', '')),
            'address' => LocalizedSettings::decodeText(Setting::get('contact', 'address', Setting::get('general', 'address', ''))),
            'social_links' => $this->decodeJsonSetting('social', 'links'),
            'hero_heading' => LocalizedSettings::decodeText(Setting::get('storefront', 'hero_heading', '')),
            'hero_subheading' => LocalizedSettings::decodeText(Setting::get('storefront', 'hero_subheading', '')),
            'hero_highlights' => LocalizedSettings::decodeRepeater(Setting::get('storefront', 'hero_highlights', '[]'), ['label', 'value']),
            'home_intro_heading' => LocalizedSettings::decodeText(Setting::get('storefront', 'home_intro_heading', '')),
            'home_intro_body' => LocalizedSettings::decodeText(Setting::get('storefront', 'home_intro_body', '')),
            'home_intro_points' => LocalizedSettings::decodeRepeater(Setting::get('storefront', 'home_intro_points', '[]'), ['title', 'text']),
            'showcase_heading' => LocalizedSettings::decodeText(Setting::get('storefront', 'showcase_heading', '')),
            'showcase_body' => LocalizedSettings::decodeText(Setting::get('storefront', 'showcase_body', '')),
            'showcase_points' => LocalizedSettings::decodeRepeater(Setting::get('storefront', 'showcase_points', '[]'), ['title', 'text']),
            'best_sellers_heading' => LocalizedSettings::decodeText(Setting::get('storefront', 'best_sellers_heading', '')),
            'best_sellers_body' => LocalizedSettings::decodeText(Setting::get('storefront', 'best_sellers_body', '')),
            'hero_spotlight_mode' => Setting::get('storefront', 'hero_spotlight_mode', 'best_seller'),
            'hero_spotlight_product_id' => filled(Setting::get('storefront', 'hero_spotlight_product_id'))
                ? (int) Setting::get('storefront', 'hero_spotlight_product_id')
                : null,
        ];

        $this->form->fill($this->data);
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Marka ve İletişim')
                ->description('Bu alanlar public header, footer ve meta katmanında doğrudan görünür. Türkçe alan zorunludur; diğer diller boş bırakılırsa kontrollü fallback kullanılır.')
                ->schema([
                    $this->localizedTextTabs(
                        'site_name',
                        'Site Adı',
                        'Header, footer ve tarayıcı başlıklarında marka adı olarak kullanılır.',
                        required: true,
                    ),
                    $this->localizedTextTabs(
                        'site_tagline',
                        'Slogan',
                        'Header logo altında ve footer marka alanında görünür.',
                    ),
                    FileUpload::make('logo_path')->label('Logo')
                        ->helperText('Kaydedildiğinde site logosu public yüzde hemen bu dosya ile güncellenir.')
                        ->image()->directory('settings')
                        ->maxSize(5120)
                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/gif'])
                        ->getUploadedFileNameForStorageUsing(fn (\Livewire\Features\SupportFileUploads\TemporaryUploadedFile $file): string => \Illuminate\Support\Str::random(40).'.'.$file->getClientOriginalExtension()),
                    FileUpload::make('favicon_path')->label('Favicon')
                        ->helperText('Tarayıcı sekmesi ve yer imi ikonunda kullanılır.')
                        ->image()->directory('settings')
                        ->maxSize(5120)
                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/gif'])
                        ->getUploadedFileNameForStorageUsing(fn (\Livewire\Features\SupportFileUploads\TemporaryUploadedFile $file): string => \Illuminate\Support\Str::random(40).'.'.$file->getClientOriginalExtension()),
                ])->columns(2),

            Section::make('İletişim')
                ->description('Footer iletişim alanında ve destek akışlarında kullanılır. Adres alanı çok dillidir.')
                ->schema([
                    TextInput::make('contact_email')->label('E-posta')
                        ->email()
                        ->maxLength(190)
                        ->dehydrateStateUsing(fn (mixed $state): string => mb_strtolower(trim((string) $state)))
                        ->helperText('Footer iletişim alanında görünür.'),
                    TextInput::make('contact_phone')->label('Telefon')
                        ->tel()
                        ->maxLength(32)
                        ->regex('/^\+?[0-9\s().-]{10,32}$/')
                        ->dehydrateStateUsing(fn (mixed $state): string => trim((string) preg_replace('/\s+/', ' ', (string) $state)))
                        ->helperText('Footer iletişim alanında görünür.'),
                    $this->localizedTextTabs(
                        'address',
                        'Adres',
                        'Footer iletişim alanında ve iletişim sayfasında görünür.',
                        isTextarea: true,
                        rows: 3,
                    ),
                ])->columns(2),

            Section::make('Sosyal Medya')
                ->description('Bu bağlantılar footer sosyal ikonlarında görünür.')
                ->schema([
                    Repeater::make('social_links')->label('Sosyal Medya')
                        ->schema([
                            Select::make('platform')->label('Platform')
                                ->options([
                                    'facebook' => 'Facebook',
                                    'instagram' => 'Instagram',
                                    'twitter' => 'Twitter',
                                    'youtube' => 'YouTube',
                                ]),
                            TextInput::make('url')->label('URL')
                                ->maxLength(500)
                                ->dehydrateStateUsing(fn (mixed $state): string => trim((string) $state)),
                        ])
                        ->columns(2),
                ]),

            Section::make('Anasayfa İçerikleri')
                ->description('Bu alanlar anasayfadaki metin bloklarını besler. Türkçe içerik zorunludur; diğer diller boşsa sistem ilgili locale için kontrollü fallback kullanır.')
                ->schema([
                    $this->localizedTextTabs('hero_heading', 'Hero Başlığı', 'Hero alanındaki ana başlıkta görünür.'),
                    $this->localizedTextTabs('hero_subheading', 'Hero Açıklaması', 'Hero alanındaki yardımcı metinde görünür.', isTextarea: true, rows: 3),
                    Repeater::make('hero_highlights')
                        ->label('Hero Bilgi Kartları')
                        ->schema([
                            $this->localizedRepeaterTabs(
                                'hero_highlight_locales',
                                [
                                    'label' => ['label' => 'Kısa Başlık', 'type' => 'text', 'required' => true],
                                    'value' => ['label' => 'Açıklama', 'type' => 'text', 'required' => true],
                                ],
                            ),
                        ])
                        ->columnSpanFull(),
                    $this->localizedTextTabs('home_intro_heading', 'Koleksiyon Bölümü Başlığı', 'Kategori keşif alanının başlığında görünür.'),
                    $this->localizedTextTabs('home_intro_body', 'Koleksiyon Bölümü Açıklaması', 'Kategori keşif alanının açıklamasında görünür.', isTextarea: true, rows: 3),
                    Repeater::make('home_intro_points')
                        ->label('Koleksiyon Bölümü Destek Kartları')
                        ->schema([
                            $this->localizedRepeaterTabs(
                                'home_intro_point_locales',
                                [
                                    'title' => ['label' => 'Kart Başlığı', 'type' => 'text', 'required' => true],
                                    'text' => ['label' => 'Kart Metni', 'type' => 'textarea', 'rows' => 2, 'required' => true],
                                ],
                            ),
                        ])
                        ->columnSpanFull(),
                    $this->localizedTextTabs('showcase_heading', 'Seçkin Vitrin Başlığı', 'Seçkin vitrin modülünün başlığında görünür.'),
                    $this->localizedTextTabs('showcase_body', 'Seçkin Vitrin Açıklaması', 'Seçkin vitrin modülünün açıklamasında görünür.', isTextarea: true, rows: 3),
                    Repeater::make('showcase_points')
                        ->label('Seçkin Vitrin Destek Kartları')
                        ->schema([
                            $this->localizedRepeaterTabs(
                                'showcase_point_locales',
                                [
                                    'title' => ['label' => 'Kart Başlığı', 'type' => 'text', 'required' => true],
                                    'text' => ['label' => 'Kart Metni', 'type' => 'textarea', 'rows' => 2, 'required' => true],
                                ],
                            ),
                        ])
                        ->columnSpanFull(),
                    $this->localizedTextTabs('best_sellers_heading', 'Çok Satanlar Başlığı', 'Çok satanlar modülünün başlığında görünür.'),
                    $this->localizedTextTabs('best_sellers_body', 'Çok Satanlar Açıklaması', 'Çok satanlar modülünün açıklamasında görünür.', isTextarea: true, rows: 3),
                ])
                ->columns(2),

            Section::make('Anasayfa Vitrini')->schema([
                Select::make('hero_spotlight_mode')
                    ->label('Vitrin Ürünü Kaynağı')
                    ->options([
                        'manual' => 'Panelden manuel seç',
                        'best_seller' => 'Çok satanlardan otomatik seç',
                        'featured' => 'Öne çıkan ürünlerden otomatik seç',
                        'newest' => 'Yeni ürünlerden otomatik seç',
                    ])
                    ->native(false)
                    ->live()
                    ->default('best_seller')
                    ->required()
                    ->helperText('Anasayfa hero alanındaki vitrin ürünü bu kurala göre belirlenir.'),
                Select::make('hero_spotlight_product_id')
                    ->label('Manuel Vitrin Ürünü')
                    ->searchable()
                    ->preload()
                    ->native(false)
                    ->visible(fn (callable $get): bool => $get('hero_spotlight_mode') === 'manual')
                    ->helperText('Manuel modda seçilen ürün vitrin kartında gösterilir.')
                    ->getSearchResultsUsing(fn (string $search): array => $this->searchProducts($search))
                    ->getOptionLabelUsing(fn ($value): ?string => $this->resolveProductLabel($value)),
            ])->columns(2),
        ])->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $data['contact_email'] = $this->normalizeEmail($data['contact_email'] ?? '');
        $data['contact_phone'] = $this->normalizePhone($data['contact_phone'] ?? '');
        $data['social_links'] = $this->normalizeSocialLinks($data['social_links'] ?? []);

        if (! $this->validateGeneralState($data)) {
            Notification::make()
                ->danger()
                ->title('Genel ayarlar kaydedilmedi')
                ->body('Eksik veya hatalı alanları düzeltip tekrar deneyin.')
                ->send();

            return;
        }

        $heroSpotlightMode = (string) ($data['hero_spotlight_mode'] ?? 'best_seller');
        $heroSpotlightProductId = filled($data['hero_spotlight_product_id'] ?? null)
            ? (int) $data['hero_spotlight_product_id']
            : null;
        $heroSpotlightProduct = $heroSpotlightProductId
            ? Product::query()->storefrontReady()->find($heroSpotlightProductId)
            : null;

        if ($heroSpotlightMode === 'manual' && ! $heroSpotlightProduct) {
            $heroSpotlightMode = 'best_seller';
            $heroSpotlightProductId = null;

            Notification::make()
                ->warning()
                ->title('Manuel vitrin ürünü bulunamadı')
                ->body('Seçilen ürün storefront-ready olmadığı için güvenli otomatik vitrine dönüldü.')
                ->send();
        }

        Setting::set('general', 'site_name', LocalizedSettings::encodeText($data['site_name'] ?? []));
        Setting::set('general', 'site_tagline', LocalizedSettings::encodeText($data['site_tagline'] ?? []));
        Setting::set('general', 'logo_path', $data['logo_path'] ?? '');
        Setting::set('general', 'favicon_path', $data['favicon_path'] ?? '');

        Setting::set('contact', 'contact_email', $data['contact_email'] ?? '');
        Setting::set('contact', 'contact_phone', $data['contact_phone'] ?? '');
        Setting::set('contact', 'address', LocalizedSettings::encodeText($data['address'] ?? []));

        Setting::set('general', 'contact_email', $data['contact_email'] ?? '');
        Setting::set('general', 'contact_phone', $data['contact_phone'] ?? '');
        Setting::set('general', 'address', LocalizedSettings::encodeText($data['address'] ?? []));

        Setting::set('social', 'links', json_encode($data['social_links'] ?? [], JSON_UNESCAPED_UNICODE));

        foreach ([
            'hero_heading',
            'hero_subheading',
            'home_intro_heading',
            'home_intro_body',
            'showcase_heading',
            'showcase_body',
            'best_sellers_heading',
            'best_sellers_body',
        ] as $key) {
            Setting::set('storefront', $key, LocalizedSettings::encodeText($data[$key] ?? []));
        }

        Setting::set('storefront', 'hero_highlights', LocalizedSettings::encodeRepeater($data['hero_highlights'] ?? [], ['label', 'value']));
        Setting::set('storefront', 'home_intro_points', LocalizedSettings::encodeRepeater($data['home_intro_points'] ?? [], ['title', 'text']));
        Setting::set('storefront', 'showcase_points', LocalizedSettings::encodeRepeater($data['showcase_points'] ?? [], ['title', 'text']));

        Setting::set('storefront', 'hero_spotlight_mode', $heroSpotlightMode);
        Setting::set('storefront', 'hero_spotlight_product_id', $heroSpotlightProductId ?? '');
        Setting::forgetStorefrontCaches();
        Setting::bumpStorefrontContentVersion();

        Notification::make()->success()->title('Ayarlar kaydedildi')->send();
    }

    private function normalizeEmail(mixed $value): string
    {
        return mb_strtolower(trim((string) $value));
    }

    private function normalizePhone(mixed $value): string
    {
        return trim((string) preg_replace('/\s+/', ' ', (string) $value));
    }

    private function normalizeSocialLinks(mixed $links): array
    {
        if (! is_array($links)) {
            return [];
        }

        return collect($links)
            ->filter(fn (mixed $link): bool => is_array($link))
            ->map(fn (array $link): array => [
                'platform' => trim((string) ($link['platform'] ?? '')),
                'url' => trim((string) ($link['url'] ?? '')),
            ])
            ->reject(fn (array $link): bool => $link['platform'] === '' && $link['url'] === '')
            ->values()
            ->all();
    }

    private function validateGeneralState(array $data): bool
    {
        $valid = true;

        if (filled($data['contact_email'] ?? '') && filter_var($data['contact_email'], FILTER_VALIDATE_EMAIL) === false) {
            $this->addError('data.contact_email', 'Geçerli bir e-posta adresi girin.');
            $valid = false;
        }

        if (filled($data['contact_phone'] ?? '') && ! preg_match('/^\+?[0-9\s().-]{10,32}$/', $data['contact_phone'])) {
            $this->addError('data.contact_phone', 'Geçerli bir telefon numarası girin.');
            $valid = false;
        }

        foreach ($data['social_links'] ?? [] as $index => $link) {
            if (blank($link['platform'] ?? '') || blank($link['url'] ?? '')) {
                $this->addError("data.social_links.{$index}.url", 'Platform ve URL birlikte girilmelidir.');
                $valid = false;

                continue;
            }

            if (! $this->isValidHttpUrl((string) $link['url'])) {
                $this->addError("data.social_links.{$index}.url", 'Sosyal medya URL adresi http/https ile başlamalıdır.');
                $valid = false;
            }
        }

        return $valid;
    }

    private function isValidHttpUrl(string $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_URL) !== false
            && in_array(parse_url($value, PHP_URL_SCHEME), ['http', 'https'], true);
    }

    private function decodeJsonSetting(string $group, string $key): array
    {
        return json_decode(Setting::get($group, $key, '[]'), true) ?? [];
    }

    private function localizedTextTabs(
        string $field,
        string $label,
        string $helperText,
        bool $required = false,
        bool $isTextarea = false,
        int $rows = 2,
    ): Tabs {
        return Tabs::make($label)
            ->columnSpanFull()
            ->tabs(
                collect(LocalizedSettings::localeLabels())
                    ->map(
                        fn (string $localeLabel, string $locale): Tab => Tab::make($localeLabel)
                            ->schema([
                                $isTextarea
                                    ? Textarea::make("{$field}.{$locale}")
                                        ->label($label)
                                        ->rows($rows)
                                        ->helperText($helperText)
                                        ->required($required && $locale === 'tr')
                                    : TextInput::make("{$field}.{$locale}")
                                        ->label($label)
                                        ->helperText($helperText)
                                        ->required($required && $locale === 'tr'),
                            ])
                    )
                    ->values()
                    ->all()
            );
    }

    private function localizedRepeaterTabs(string $name, array $fields): Tabs
    {
        return Tabs::make($name)
            ->columnSpanFull()
            ->tabs(
                collect(LocalizedSettings::localeLabels())
                    ->map(function (string $localeLabel, string $locale) use ($fields): Tab {
                        $schema = [];

                        foreach ($fields as $field => $config) {
                            $component = ($config['type'] ?? 'text') === 'textarea'
                                ? Textarea::make("{$field}.{$locale}")->rows((int) ($config['rows'] ?? 2))
                                : TextInput::make("{$field}.{$locale}");

                            $schema[] = $component
                                ->label($config['label'])
                                ->required(($config['required'] ?? false) && $locale === 'tr');
                        }

                        return Tab::make($localeLabel)->schema($schema);
                    })
                    ->values()
                    ->all()
            );
    }

    private function searchProducts(string $search): array
    {
        return Product::query()
            ->storefrontReady()
            ->with('images')
            ->orderByDesc('updated_at')
            ->get()
            ->filter(function (Product $product) use ($search) {
                $query = mb_strtolower(trim($search));
                if ($query === '') {
                    return true;
                }

                $haystack = implode(' ', array_filter([
                    mb_strtolower((string) ($product->getTranslation('name', app()->getLocale()) ?: '')),
                    mb_strtolower((string) ($product->slug ?: '')),
                    mb_strtolower((string) ($product->sku ?: '')),
                ]));

                return str_contains($haystack, $query);
            })
            ->take(40)
            ->mapWithKeys(fn (Product $product) => [$product->id => $this->formatProductLabel($product)])
            ->all();
    }

    private function resolveProductLabel($value): ?string
    {
        if (! filled($value)) {
            return null;
        }

        $product = Product::query()->storefrontReady()->find($value);

        return $product ? $this->formatProductLabel($product) : null;
    }

    private function formatProductLabel(Product $product): string
    {
        return $product->getTranslation('name', app()->getLocale())
            ?: $product->getTranslation('name', 'tr')
            ?: $product->slug;
    }
}
