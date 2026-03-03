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
            'sms_username' => Setting::get('sms', 'username', ''),
            'sms_password' => Setting::get('sms', 'password', ''),
            'sms_subscriber_no' => Setting::get('sms', 'subscriber_no', ''),
            'sms_sender_title' => Setting::get('sms', 'sender_title', ''),
        ];
        $this->form->fill($this->data);
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Section::make('SMS Sağlayıcı Ayarları')->schema([
                TextInput::make('sms_username')->label('Kullanıcı Adı'),
                TextInput::make('sms_password')->label('Şifre')->password()->revealable(),
                TextInput::make('sms_subscriber_no')->label('Abone No'),
                TextInput::make('sms_sender_title')->label('Gönderici Başlığı')->maxLength(11),
            ])->columns(2),
        ])->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        Setting::set('sms', 'username', $data['sms_username']);
        Setting::set('sms', 'password', $data['sms_password']);
        Setting::set('sms', 'subscriber_no', $data['sms_subscriber_no']);
        Setting::set('sms', 'sender_title', $data['sms_sender_title']);
        Notification::make()->success()->title('SMS ayarları kaydedildi')->send();
    }
}
