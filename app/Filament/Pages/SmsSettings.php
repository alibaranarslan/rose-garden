<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use App\Services\SmsService;
use Filament\Actions\Action;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class SmsSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left';
    protected static ?string $navigationLabel = 'SMS';
    protected static ?string $navigationGroup = 'Ayarlar';
    protected static ?string $title = 'SMS Ayarları';
    protected static ?int $navigationSort = 22;
    protected static string $view = 'filament.pages.settings';

    public array $data = [];

    public function mount(): void
    {
        $this->data = [
            'sms_api_url' => Setting::get('sms', 'api_url', config('services.sms.api_url', '')),
            'sms_username' => Setting::get('sms', 'username', ''),
            'sms_password' => Setting::get('sms', 'password', ''),
            'sms_subscriber_no' => Setting::get('sms', 'subscriber_no', ''),
            'sms_sender_title' => Setting::get('sms', 'sender_title', ''),
            'sms_enabled' => (bool) filter_var(
                Setting::get('sms', 'enabled', config('services.sms.enabled', false)),
                FILTER_VALIDATE_BOOL
            ),
        ];
        $this->form->fill($this->data);
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Section::make('SMS Sağlayıcı Ayarları')
                ->description('Bu ayarlar sipariş, hatırlatma ve test SMS gönderimlerinde doğrudan kullanılır.')
                ->schema([
                    TextInput::make('sms_api_url')
                        ->label('SMS API URL')
                        ->url()
                        ->maxLength(500)
                        ->helperText('SMS sağlayıcısının gönderim endpoint adresi. Paneldeki değer .env değerini override eder.'),
                    TextInput::make('sms_username')
                        ->label('Kullanıcı Adı')
                        ->maxLength(255),
                    TextInput::make('sms_password')
                        ->label('Şifre')
                        ->password()
                        ->revealable()
                        ->maxLength(255),
                    TextInput::make('sms_subscriber_no')
                        ->label('Abone No')
                        ->maxLength(255),
                    TextInput::make('sms_sender_title')
                        ->label('Gönderici Başlığı')
                        ->maxLength(11)
                        ->helperText('Müşteriye giden SMS mesajlarında görünen gönderen başlığıdır. En fazla 11 karakter.'),
                    Toggle::make('sms_enabled')
                        ->label('SMS Gönderimi Aktif')
                        ->helperText('Sipariş ve hatırlatma SMS akışını panelden açıp kapatır. Aktifse tüm sağlayıcı bilgileri zorunludur.'),
                ])->columns(2),
        ])->statePath('data');
    }

    public function save(): void
    {
        $data = $this->normalizedState();

        if (! $this->validateSmsState($data)) {
            Notification::make()
                ->danger()
                ->title('SMS ayarları kaydedilmedi')
                ->body('Eksik veya hatalı alanları düzeltip tekrar deneyin.')
                ->send();

            return;
        }

        Setting::set('sms', 'api_url', $data['sms_api_url']);
        Setting::set('sms', 'username', $data['sms_username']);
        Setting::set('sms', 'password', $data['sms_password']);
        Setting::set('sms', 'subscriber_no', $data['sms_subscriber_no']);
        Setting::set('sms', 'sender_title', $data['sms_sender_title']);
        Setting::set('sms', 'enabled', $data['sms_enabled'] ? '1' : '0');

        app()->forgetInstance(SmsService::class);

        Notification::make()->success()->title('SMS ayarları kaydedildi')->send();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('send_test_sms')
                ->label('Test SMS Gönder')
                ->icon('heroicon-o-paper-airplane')
                ->color('gray')
                ->form([
                    TextInput::make('phone')
                        ->label('Telefon')
                        ->default(auth()->user()?->phone)
                        ->tel()
                        ->regex('/^\+?[0-9\s().-]{10,20}$/')
                        ->required(),
                ])
                ->action(function (array $data): void {
                    $sms = app(SmsService::class);

                    if (! $sms->canSend()) {
                        Notification::make()
                            ->danger()
                            ->title('SMS servisi hazır değil')
                            ->body('Önce ayarları tamamlayıp SMS gönderimini aktifleştirin.')
                            ->send();

                        return;
                    }

                    $sent = $sms->send($data['phone'], 'Bu bir test SMS mesajıdır.');

                    if ($sent) {
                        Notification::make()->success()->title('Test SMS gönderildi')->send();

                        return;
                    }

                    Notification::make()->danger()->title('Test SMS gönderilemedi')->send();
                }),
        ];
    }

    private function normalizedState(): array
    {
        $data = $this->form->getState();

        $data['sms_api_url'] = trim((string) ($data['sms_api_url'] ?? ''));
        $data['sms_username'] = trim((string) ($data['sms_username'] ?? ''));
        $data['sms_password'] = trim((string) ($data['sms_password'] ?? ''));
        $data['sms_subscriber_no'] = trim((string) ($data['sms_subscriber_no'] ?? ''));
        $data['sms_sender_title'] = strtoupper(trim((string) ($data['sms_sender_title'] ?? '')));
        $data['sms_enabled'] = ! empty($data['sms_enabled']);

        return $data;
    }

    private function validateSmsState(array $data): bool
    {
        $requiredFields = ['sms_api_url', 'sms_username', 'sms_password', 'sms_subscriber_no', 'sms_sender_title'];
        $hasAnyProviderValue = collect($requiredFields)->contains(fn (string $field): bool => filled($data[$field]));

        if (! $data['sms_enabled'] && ! $hasAnyProviderValue) {
            return true;
        }

        $valid = true;

        foreach ($requiredFields as $field) {
            if (blank($data[$field])) {
                $this->addError('data.'.$field, 'SMS kullanılacaksa bu alan zorunludur.');
                $valid = false;
            }
        }

        if (filled($data['sms_api_url']) && ! $this->isValidApiUrl($data['sms_api_url'])) {
            $this->addError('data.sms_api_url', 'SMS API URL http veya https ile başlayan geçerli bir URL olmalıdır.');
            $valid = false;
        }

        if (filled($data['sms_sender_title']) && ! preg_match('/^[A-Z0-9]{1,11}$/', $data['sms_sender_title'])) {
            $this->addError('data.sms_sender_title', 'Gönderici başlığı yalnız harf/rakam içermeli ve en fazla 11 karakter olmalıdır.');
            $valid = false;
        }

        return $valid;
    }

    private function isValidApiUrl(string $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_URL) !== false
            && in_array(parse_url($value, PHP_URL_SCHEME), ['http', 'https'], true);
    }
}
