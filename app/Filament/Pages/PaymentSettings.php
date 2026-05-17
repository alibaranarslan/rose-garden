<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class PaymentSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    protected static ?string $navigationLabel = 'Ödeme';
    protected static ?string $navigationGroup = 'Ayarlar';
    protected static ?string $title = 'Ödeme Ayarları';
    protected static ?int $navigationSort = 21;
    protected static string $view = 'filament.pages.settings';

    public array $data = [];

    public function mount(): void
    {
        $this->data = [
            'paytr_merchant_id' => Setting::get('payment', 'paytr_merchant_id', ''),
            'paytr_merchant_key' => Setting::get('payment', 'paytr_merchant_key', ''),
            'paytr_merchant_salt' => Setting::get('payment', 'paytr_merchant_salt', ''),
            'bank_name' => Setting::get('payment', 'bank_name', ''),
            'bank_iban' => Setting::get('payment', 'bank_iban', ''),
            'bank_account_holder' => Setting::get('payment', 'bank_account_holder', ''),
            'transfer_timeout_hours' => Setting::get('payment', 'transfer_timeout_hours', '72'),
        ];
        $this->form->fill($this->data);
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Section::make('PayTR Entegrasyonu')
                ->description('Admin panelden girilen değerler .env değerlerini override eder ve checkout akışını doğrudan etkiler.')
                ->schema([
                    TextInput::make('paytr_merchant_id')
                        ->label('Merchant ID')
                        ->maxLength(255)
                        ->helperText('Kart ile ödeme akışında kullanılır. PayTR kullanılmayacaksa boş bırakın.'),
                    TextInput::make('paytr_merchant_key')
                        ->label('Merchant Key')
                        ->password()
                        ->revealable()
                        ->maxLength(255)
                        ->helperText('Merchant ID girildiyse bu alan da zorunludur.'),
                    TextInput::make('paytr_merchant_salt')
                        ->label('Merchant Salt')
                        ->password()
                        ->revealable()
                        ->maxLength(255)
                        ->helperText('Merchant ID girildiyse bu alan da zorunludur.'),
                ])->columns(3),

            Section::make('Havale / EFT Bilgileri')
                ->description('Checkout, sipariş onay e-postası ve bildirimlerde aynı veri sözleşmesiyle kullanılır.')
                ->schema([
                    TextInput::make('bank_name')
                        ->label('Banka Adı')
                        ->maxLength(255)
                        ->helperText('Havale seçildiğinde checkout ve e-postada görünür. Kullanılmayacaksa tüm havale alanlarını boş bırakın.'),
                    TextInput::make('bank_iban')
                        ->label('IBAN')
                        ->maxLength(34)
                        ->helperText('TR ile başlayan 26 karakterlik IBAN girin. Boşluklar otomatik temizlenir.'),
                    TextInput::make('bank_account_holder')
                        ->label('Hesap Sahibi')
                        ->maxLength(255)
                        ->helperText('Havale seçildiğinde checkout ve e-postada görünür.'),
                    TextInput::make('transfer_timeout_hours')
                        ->label('Havale Zaman Aşımı (Saat)')
                        ->numeric()
                        ->minValue(1)
                        ->maxValue(168)
                        ->helperText('Sipariş onay e-postasındaki zaman aşımı metnini belirler.'),
                ])->columns(2),
        ])->statePath('data');
    }

    public function save(): void
    {
        $data = $this->normalizedState();

        if (! $this->validatePaytrGroup($data) || ! $this->validateBankGroup($data)) {
            Notification::make()
                ->danger()
                ->title('Ödeme ayarları kaydedilmedi')
                ->body('Eksik veya hatalı alanları düzeltip tekrar deneyin.')
                ->send();

            return;
        }

        foreach ($data as $key => $value) {
            Setting::set('payment', $key, $value);
        }

        Setting::forgetStorefrontCaches();
        Setting::bumpStorefrontContentVersion();

        Notification::make()->success()->title('Ödeme ayarları kaydedildi')->send();
    }

    private function normalizedState(): array
    {
        $data = $this->form->getState();

        $data['paytr_merchant_id'] = trim((string) ($data['paytr_merchant_id'] ?? ''));
        $data['paytr_merchant_key'] = trim((string) ($data['paytr_merchant_key'] ?? ''));
        $data['paytr_merchant_salt'] = trim((string) ($data['paytr_merchant_salt'] ?? ''));
        $data['bank_name'] = trim((string) ($data['bank_name'] ?? ''));
        $data['bank_iban'] = strtoupper(preg_replace('/\s+/', '', (string) ($data['bank_iban'] ?? '')));
        $data['bank_account_holder'] = trim((string) ($data['bank_account_holder'] ?? ''));
        $data['transfer_timeout_hours'] = min(168, max(1, (int) ($data['transfer_timeout_hours'] ?? 72)));

        return $data;
    }

    private function validatePaytrGroup(array $data): bool
    {
        $fields = ['paytr_merchant_id', 'paytr_merchant_key', 'paytr_merchant_salt'];
        $hasAny = collect($fields)->contains(fn (string $field): bool => filled($data[$field]));

        if (! $hasAny) {
            return true;
        }

        $valid = true;

        foreach ($fields as $field) {
            if (blank($data[$field])) {
                $this->addError('data.'.$field, 'PayTR kullanılacaksa bu alan zorunludur.');
                $valid = false;
            }
        }

        return $valid;
    }

    private function validateBankGroup(array $data): bool
    {
        $fields = ['bank_name', 'bank_iban', 'bank_account_holder'];
        $hasAny = collect($fields)->contains(fn (string $field): bool => filled($data[$field]));

        if (! $hasAny) {
            return true;
        }

        $valid = true;

        foreach ($fields as $field) {
            if (blank($data[$field])) {
                $this->addError('data.'.$field, 'Havale kullanılacaksa bu alan zorunludur.');
                $valid = false;
            }
        }

        if (filled($data['bank_iban']) && ! preg_match('/^TR\d{24}$/', $data['bank_iban'])) {
            $this->addError('data.bank_iban', 'TR ile başlayan 26 karakterlik geçerli formatta IBAN girin.');
            $valid = false;
        }

        return $valid;
    }
}
