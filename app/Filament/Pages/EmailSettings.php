<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use App\Support\DynamicMailConfig;
use Filament\Actions\Action;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Mail;

class EmailSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';
    protected static ?string $navigationLabel = 'E-posta';
    protected static ?string $navigationGroup = 'Ayarlar';
    protected static ?string $title = 'E-posta Ayarları';
    protected static ?int $navigationSort = 23;
    protected static string $view = 'filament.pages.settings';

    public array $data = [];

    public function mount(): void
    {
        $this->data = [
            'smtp_host' => Setting::get('email', 'smtp_host', ''),
            'smtp_port' => Setting::get('email', 'smtp_port', '587'),
            'smtp_username' => Setting::get('email', 'smtp_username', ''),
            'smtp_password' => Setting::get('email', 'smtp_password', ''),
            'smtp_encryption' => Setting::get('email', 'smtp_encryption', 'tls'),
            'from_name' => Setting::get('email', 'from_name', ''),
            'from_email' => Setting::get('email', 'from_email', ''),
        ];
        $this->form->fill($this->data);
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Section::make('SMTP Ayarları')
                ->description('Bu ayarlar sipariş e-postaları, test gönderimleri ve bildirim şablonlarının e-posta kanalını doğrudan etkiler.')
                ->schema([
                    TextInput::make('smtp_host')
                        ->label('SMTP Host')
                        ->maxLength(255)
                        ->helperText('Müşteriye giden e-posta akışının çıkış sunucusunu belirler. SMTP kullanılmayacaksa tüm SMTP alanlarını boş bırakın.'),
                    TextInput::make('smtp_port')
                        ->label('Port')
                        ->numeric()
                        ->minValue(1)
                        ->maxValue(65535),
                    TextInput::make('smtp_username')
                        ->label('Kullanıcı Adı')
                        ->maxLength(255),
                    TextInput::make('smtp_password')
                        ->label('Şifre')
                        ->password()
                        ->revealable()
                        ->maxLength(255),
                    Select::make('smtp_encryption')
                        ->label('Şifreleme')
                        ->options(['tls' => 'TLS', 'ssl' => 'SSL', 'none' => 'Yok']),
                ])->columns(2),

            Section::make('Gönderici Bilgileri')
                ->description('Kaydedilen gönderici adı ve adresi müşteriye giden e-postalarda görünür.')
                ->schema([
                    TextInput::make('from_name')
                        ->label('Gönderici Adı')
                        ->maxLength(255)
                        ->helperText('Sipariş ve bildirim e-postalarında görünen marka adıdır.'),
                    TextInput::make('from_email')
                        ->label('Gönderici E-posta')
                        ->email()
                        ->maxLength(255)
                        ->helperText('Müşteriye giden e-postalarda görünen gönderici adresidir.'),
                ])->columns(2),
        ])->statePath('data');
    }

    public function save(): void
    {
        $data = $this->normalizedState();

        if (! $this->validateSmtpGroup($data)) {
            Notification::make()
                ->danger()
                ->title('E-posta ayarları kaydedilmedi')
                ->body('Eksik veya hatalı SMTP alanlarını düzeltip tekrar deneyin.')
                ->send();

            return;
        }

        foreach ($data as $key => $value) {
            Setting::set('email', $key, $value);
        }

        DynamicMailConfig::apply();

        Notification::make()->success()->title('E-posta ayarları kaydedildi')->send();
    }

    public function sendTestEmail(): void
    {
        try {
            $recipient = auth()->user()?->email;

            if (blank($recipient)) {
                Notification::make()
                    ->danger()
                    ->title('Test e-postası gönderilemedi')
                    ->body('Testi alacak yönetici hesabında geçerli bir e-posta adresi yok.')
                    ->send();

                return;
            }

            DynamicMailConfig::apply();

            if (! $this->smtpReady()) {
                Notification::make()
                    ->danger()
                    ->title('Test e-postası gönderilemedi')
                    ->body('Önce SMTP host, port, kullanıcı adı ve şifre alanlarını tamamlayın.')
                    ->send();

                return;
            }

            Mail::raw('Bu bir test e-postasıdır.', fn ($message) => $message->to($recipient)->subject('Rose Garden Test'));
            Notification::make()->success()->title('Test e-postası gönderildi')->send();
        } catch (\Exception $e) {
            Notification::make()->danger()->title('Hata: '.$e->getMessage())->send();
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('send_test_email')
                ->label('Test E-postası Gönder')
                ->icon('heroicon-o-paper-airplane')
                ->color('gray')
                ->requiresConfirmation()
                ->action('sendTestEmail'),
        ];
    }

    private function normalizedState(): array
    {
        $data = $this->form->getState();

        $data['smtp_host'] = trim((string) ($data['smtp_host'] ?? ''));
        $data['smtp_port'] = trim((string) ($data['smtp_port'] ?? '587'));
        $data['smtp_username'] = trim((string) ($data['smtp_username'] ?? ''));
        $data['smtp_password'] = trim((string) ($data['smtp_password'] ?? ''));
        $data['smtp_encryption'] = in_array(($data['smtp_encryption'] ?? 'tls'), ['tls', 'ssl', 'none'], true)
            ? $data['smtp_encryption']
            : 'tls';
        $data['from_name'] = trim((string) ($data['from_name'] ?? ''));
        $data['from_email'] = mb_strtolower(trim((string) ($data['from_email'] ?? '')));

        return $data;
    }

    private function validateSmtpGroup(array $data): bool
    {
        $fields = ['smtp_host', 'smtp_username', 'smtp_password'];
        $hasAny = collect($fields)->contains(fn (string $field): bool => filled($data[$field]));

        if (! $hasAny) {
            return true;
        }

        $valid = true;

        foreach ($fields as $field) {
            if (blank($data[$field])) {
                $this->addError('data.'.$field, 'SMTP kullanılacaksa bu alan zorunludur.');
                $valid = false;
            }
        }

        if (filled($data['smtp_port']) && (! ctype_digit((string) $data['smtp_port']) || (int) $data['smtp_port'] < 1 || (int) $data['smtp_port'] > 65535)) {
            $this->addError('data.smtp_port', 'SMTP portu 1 ile 65535 arasında olmalıdır.');
            $valid = false;
        }

        if (filled($data['from_email']) && filter_var($data['from_email'], FILTER_VALIDATE_EMAIL) === false) {
            $this->addError('data.from_email', 'Geçerli bir gönderici e-posta adresi girin.');
            $valid = false;
        }

        return $valid;
    }

    private function smtpReady(): bool
    {
        return config('mail.default') === 'smtp'
            && filled(config('mail.mailers.smtp.host'))
            && filled(config('mail.mailers.smtp.port'))
            && filled(config('mail.mailers.smtp.username'))
            && filled(config('mail.mailers.smtp.password'));
    }
}
