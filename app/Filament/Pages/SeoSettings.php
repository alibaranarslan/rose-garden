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
    protected static ?string $title = 'SEO Ayarlari';
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
                Forms\Components\Section::make('Genel SEO Ayarlari')
                    ->schema([
                        Forms\Components\TextInput::make('meta_title_suffix')
                            ->label('Meta Title Eki')
                            ->helperText('Her sayfanin title etiketinin sonuna eklenir. Ornek: "| Rose Garden"'),
                        Forms\Components\Textarea::make('meta_description_default')
                            ->label('Varsayilan Meta Aciklama')
                            ->rows(3)
                            ->helperText('Sayfa bazli aciklama yoksa kullanilir'),
                        Forms\Components\TextInput::make('og_default_image')
                            ->label('Varsayilan OG Gorseli URL')
                            ->helperText('Sayfa gorseli yoksa kullanilir'),
                        Forms\Components\TextInput::make('canonical_domain')
                            ->label('Canonical Domain')
                            ->placeholder('https://www.rosegarden.com.tr')
                            ->helperText('Canonical URL olusturmak icin kullanilir'),
                    ]),
                Forms\Components\Section::make('Google Entegrasyonlari')
                    ->schema([
                        Forms\Components\TextInput::make('google_analytics_id')
                            ->label('Google Analytics Measurement ID')
                            ->placeholder('G-XXXXXXXXXX')
                            ->helperText('GA4 olcum kimligi'),
                        Forms\Components\TextInput::make('google_search_console_code')
                            ->label('Google Search Console Dogrulama Kodu')
                            ->helperText('google-site-verification content degeri'),
                    ]),
                Forms\Components\Section::make('robots.txt Ek Kurallari')
                    ->schema([
                        Forms\Components\Textarea::make('robots_txt_extra')
                            ->label('Ek robots.txt Kurallari')
                            ->rows(5)
                            ->helperText('Varsayilan robots.txt icerigine eklenecek kurallar'),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        foreach ($data as $key => $value) {
            Setting::set('seo', $key, $value);
        }

        Notification::make()
            ->title('SEO ayarlari kaydedildi')
            ->success()
            ->send();
    }
}
