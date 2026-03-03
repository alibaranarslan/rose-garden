<?php

namespace App\Filament\Pages;

use App\Models\LoyaltyPoint;
use App\Models\LoyaltyTransaction;
use App\Models\Setting;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class LoyaltyManagement extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-star';
    protected static ?string $navigationGroup = 'Kampanyalar';
    protected static ?string $navigationLabel = 'Paraçiçek Puanlar';
    protected static ?string $title = 'Paraçiçek Puan Yönetimi';
    protected static ?int $navigationSort = 3;
    protected static string $view = 'filament.pages.loyalty-management';

    public string $activeTab = 'rules';
    public array $data = [];

    public function mount(): void
    {
        $this->data = [
            'earn_rate' => Setting::get('loyalty', 'earn_rate', '5'),
            'min_use_amount' => Setting::get('loyalty', 'min_use_amount', '50'),
            'expiry_months' => Setting::get('loyalty', 'expiry_months', '12'),
        ];
        $this->form->fill($this->data);
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Puan Kuralları')->schema([
                TextInput::make('earn_rate')
                    ->label('Kazanım Oranı (%)')
                    ->numeric()
                    ->helperText('Her 100₺ harcama için kaç ₺ puan kazanılır'),

                TextInput::make('min_use_amount')
                    ->label('Min. Kullanım Tutarı (₺)')
                    ->numeric()
                    ->helperText('Puan kullanmak için minimum sipariş tutarı'),

                TextInput::make('expiry_months')
                    ->label('Son Kullanma Süresi (Ay)')
                    ->numeric()
                    ->helperText('0 = süresiz'),
            ])->columns(3),
        ])->statePath('data');
    }

    public function saveRules(): void
    {
        $data = $this->form->getState();
        Setting::set('loyalty', 'earn_rate', $data['earn_rate']);
        Setting::set('loyalty', 'min_use_amount', $data['min_use_amount']);
        Setting::set('loyalty', 'expiry_months', $data['expiry_months']);
        Notification::make()->success()->title('Puan kuralları kaydedildi')->send();
    }

    public function getViewData(): array
    {
        $totalDistributed = LoyaltyTransaction::where('type', 'earned')->sum('amount');
        $totalUsed = LoyaltyTransaction::where('type', 'spent')->sum('amount');
        $pendingBalance = LoyaltyPoint::sum('balance');
        $usageRate = $totalDistributed > 0 ? round(($totalUsed / $totalDistributed) * 100, 1) : 0;

        $topUsers = LoyaltyPoint::with('user')
            ->where('balance', '>', 0)
            ->orderByDesc('balance')
            ->limit(10)
            ->get();

        return compact('totalDistributed', 'totalUsed', 'pendingBalance', 'usageRate', 'topUsers');
    }
}
