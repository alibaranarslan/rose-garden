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
                ->description('Admin panelden girilen değerler .env değerlerini override eder.')
                ->schema([
                    TextInput::make('paytr_merchant_id')->label('Merchant ID'),
                    TextInput::make('paytr_merchant_key')->label('Merchant Key')->password()->revealable(),
                    TextInput::make('paytr_merchant_salt')->label('Merchant Salt')->password()->revealable(),
                ])->columns(3),

            Section::make('Havale / EFT Bilgileri')->schema([
                TextInput::make('bank_name')->label('Banka Adı'),
                TextInput::make('bank_iban')->label('IBAN'),
                TextInput::make('bank_account_holder')->label('Hesap Sahibi'),
                TextInput::make('transfer_timeout_hours')->label('Havale Zaman Aşımı (Saat)')->numeric(),
            ])->columns(2),
        ])->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        foreach ($data as $key => $value) {
            Setting::set('payment', $key, $value);
        }
        Notification::make()->success()->title('Ödeme ayarları kaydedildi')->send();
    }
}
