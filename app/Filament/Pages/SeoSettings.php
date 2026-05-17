<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class SeoSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-magnifying-glass';
    protected static ?string $navigationGroup = 'Ayarlar';
    protected static ?string $title = 'SEO Ayarları';
    protected static ?int $navigationSort = 21;
    protected static string $view = 'filament.pages.seo-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'meta_title_suffix' => Setting::get('seo', 'meta_title_suffix', '| Rose Garden'),
            'meta_description_default' => Setting::get('seo', 'meta_description_default', ''),
            'og_default_image' => Setting::get('seo', 'og_default_image', ''),
            'google_analytics_id' => Setting::get('seo', 'google_analytics_id', ''),
            'google_search_console_code' => Setting::get('seo', 'google_search_console_code', ''),
            'robots_txt_extra' => Setting::get('seo', 'robots_txt_extra', ''),
            'canonical_domain' => Setting::get('seo', 'canonical_domain', ''),
        ]);
    }

    public function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Genel SEO Ayarları')
                    ->schema([
                        Forms\Components\TextInput::make('meta_title_suffix')
                            ->label('Meta Title Eki')
                            ->maxLength(120)
                            ->helperText('Her sayfanın title etiketinin sonuna eklenir. Örnek: "| Rose Garden"'),
                        Forms\Components\Textarea::make('meta_description_default')
                            ->label('Varsayılan Meta Açıklama')
                            ->rows(3)
                            ->maxLength(320)
                            ->helperText('Sayfaya özel açıklama yoksa bu metin kullanılır. İdeal uzunluk 120-160 karakterdir.'),
                        Forms\Components\TextInput::make('og_default_image')
                            ->label('Varsayılan OG Görseli URL')
                            ->maxLength(500)
                            ->helperText('Sayfa görseli yoksa kullanılacak paylaşım görseli. Boş, kök göreli (/images/...) veya tam URL olabilir.'),
                        Forms\Components\TextInput::make('canonical_domain')
                            ->label('Canonical Domain')
                            ->placeholder('https://adiyamancicekcisi.com.tr')
                            ->maxLength(255)
                            ->helperText('Canonical URL üretiminde kullanılacak ana domain. Alan adını protokolle ya da protokolsüz girebilirsiniz; kayıtta origin olarak saklanır.'),
                    ]),
                Forms\Components\Section::make('Google Entegrasyonları')
                    ->schema([
                        Forms\Components\TextInput::make('google_analytics_id')
                            ->label('Google Analytics Measurement ID')
                            ->placeholder('G-XXXXXXXXXX')
                            ->helperText('GA4 ölçüm kimliği. Boş bırakılabilir.'),
                        Forms\Components\TextInput::make('google_search_console_code')
                            ->label('Google Search Console Doğrulama Kodu')
                            ->helperText('google-site-verification meta değeri. Boş bırakılabilir.'),
                    ]),
                Forms\Components\Section::make('robots.txt Ek Kuralları')
                    ->schema([
                        Forms\Components\Textarea::make('robots_txt_extra')
                            ->label('Ek robots.txt Kuralları')
                            ->rows(5)
                            ->maxLength(2000)
                            ->helperText('Varsayılan robots.txt içeriğine eklenecek satırlar. Sitemap satırı otomatik korunur.'),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->normalizedState();

        if (! $this->validateSeoState($data)) {
            Notification::make()
                ->danger()
                ->title('SEO ayarları kaydedilmedi')
                ->body('Eksik veya hatalı alanları düzeltip tekrar deneyin.')
                ->send();

            return;
        }

        foreach ($data as $key => $value) {
            Setting::set('seo', $key, $value);
        }

        Setting::forgetStorefrontCaches();
        Setting::bumpStorefrontContentVersion();

        Notification::make()
            ->title('SEO ayarları kaydedildi')
            ->success()
            ->send();
    }

    private function normalizedState(): array
    {
        $data = $this->form->getState();

        $data['meta_title_suffix'] = trim((string) ($data['meta_title_suffix'] ?? ''));
        $data['meta_description_default'] = trim((string) ($data['meta_description_default'] ?? ''));
        $data['og_default_image'] = trim((string) ($data['og_default_image'] ?? ''));
        $data['google_analytics_id'] = strtoupper(trim((string) ($data['google_analytics_id'] ?? '')));
        $data['google_search_console_code'] = trim((string) ($data['google_search_console_code'] ?? ''));
        $data['robots_txt_extra'] = $this->normalizeRobotsExtra($data['robots_txt_extra'] ?? '');
        $data['canonical_domain'] = $this->normalizeCanonicalDomain($data['canonical_domain'] ?? '');

        return $data;
    }

    private function validateSeoState(array $data): bool
    {
        $valid = true;

        if (filled($this->form->getState()['canonical_domain'] ?? '') && blank($data['canonical_domain'])) {
            $this->addError('data.canonical_domain', 'Geçerli bir canonical domain girin.');
            $valid = false;
        }

        if (filled($data['og_default_image']) && ! $this->isValidImageUrl($data['og_default_image'])) {
            $this->addError('data.og_default_image', 'OG görseli kök göreli (/images/...) veya http/https URL olmalıdır.');
            $valid = false;
        }

        if (filled($data['google_analytics_id']) && ! preg_match('/^G-[A-Z0-9]{6,20}$/', $data['google_analytics_id'])) {
            $this->addError('data.google_analytics_id', 'GA4 Measurement ID G- ile başlamalıdır.');
            $valid = false;
        }

        if (filled($data['google_search_console_code']) && str_contains($data['google_search_console_code'], '<')) {
            $this->addError('data.google_search_console_code', 'Yalnız meta doğrulama değerini girin; HTML etiketi kullanmayın.');
            $valid = false;
        }

        return $valid;
    }

    private function normalizeCanonicalDomain(mixed $value): string
    {
        $value = trim((string) $value);

        if ($value === '') {
            return '';
        }

        if (preg_match('/^[a-z][a-z0-9+\-.]*:/i', $value) && ! str_starts_with($value, 'http://') && ! str_starts_with($value, 'https://')) {
            return '';
        }

        if (! str_starts_with($value, 'http://') && ! str_starts_with($value, 'https://')) {
            $value = 'https://'.$value;
        }

        $parts = parse_url($value);

        if (! is_array($parts) || blank($parts['host'] ?? null)) {
            return '';
        }

        $scheme = strtolower($parts['scheme'] ?? 'https');

        if (! in_array($scheme, ['http', 'https'], true)) {
            return '';
        }

        $host = strtolower((string) $parts['host']);
        $port = isset($parts['port']) ? ':'.$parts['port'] : '';

        return "{$scheme}://{$host}{$port}";
    }

    private function normalizeRobotsExtra(mixed $value): string
    {
        $lines = preg_split('/\R/', trim((string) $value)) ?: [];

        return collect($lines)
            ->map(fn (string $line): string => trim($line))
            ->reject(fn (string $line): bool => $line === '')
            ->reject(fn (string $line): bool => str_starts_with(strtolower($line), 'sitemap:'))
            ->implode("\n");
    }

    private function isValidImageUrl(string $value): bool
    {
        if (str_starts_with($value, '/')) {
            return ! str_starts_with($value, '//');
        }

        return filter_var($value, FILTER_VALIDATE_URL) !== false
            && in_array(parse_url($value, PHP_URL_SCHEME), ['http', 'https'], true);
    }
}
