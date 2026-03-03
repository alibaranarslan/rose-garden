<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
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
            'site_name' => Setting::get('general', 'site_name', 'Rose Garden'),
            'site_tagline' => Setting::get('general', 'site_tagline', ''),
            'logo_path' => Setting::get('general', 'logo_path', ''),
            'favicon_path' => Setting::get('general', 'favicon_path', ''),
            'contact_email' => Setting::get('general', 'contact_email', ''),
            'contact_phone' => Setting::get('general', 'contact_phone', ''),
            'address' => Setting::get('general', 'address', ''),
            'social_links' => json_decode(Setting::get('social', 'links', '[]'), true) ?? [],
        ];
        $this->form->fill($this->data);
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Site Bilgileri')->schema([
                TextInput::make('site_name')->label('Site Adı')->required(),
                TextInput::make('site_tagline')->label('Slogan'),
                FileUpload::make('logo_path')->label('Logo')->image()->directory('settings')
                    ->maxSize(5120)
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/gif', 'application/pdf'])
                    ->getUploadedFileNameForStorageUsing(fn (\Livewire\Features\SupportFileUploads\TemporaryUploadedFile $file): string => \Illuminate\Support\Str::random(40) . '.' . $file->getClientOriginalExtension()),
                FileUpload::make('favicon_path')->label('Favicon')->image()->directory('settings')
                    ->maxSize(5120)
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/gif', 'application/pdf'])
                    ->getUploadedFileNameForStorageUsing(fn (\Livewire\Features\SupportFileUploads\TemporaryUploadedFile $file): string => \Illuminate\Support\Str::random(40) . '.' . $file->getClientOriginalExtension()),
            ])->columns(2),

            Section::make('İletişim')->schema([
                TextInput::make('contact_email')->label('E-posta')->email(),
                TextInput::make('contact_phone')->label('Telefon'),
                Textarea::make('address')->label('Adres')->rows(3)->columnSpanFull(),
            ])->columns(2),

            Section::make('Sosyal Medya')->schema([
                Repeater::make('social_links')->label('Sosyal Medya')
                    ->schema([
                        Select::make('platform')->label('Platform')
                            ->options(['facebook' => 'Facebook', 'instagram' => 'Instagram', 'twitter' => 'Twitter', 'youtube' => 'YouTube']),
                        TextInput::make('url')->label('URL')->url(),
                    ])->columns(2),
            ]),
        ])->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        foreach (['site_name', 'site_tagline', 'logo_path', 'favicon_path', 'contact_email', 'contact_phone', 'address'] as $key) {
            Setting::set('general', $key, $data[$key]);
        }
        Setting::set('social', 'links', json_encode($data['social_links']));
        Notification::make()->success()->title('Ayarlar kaydedildi')->send();
    }
}
