<?php

namespace App\Filament\Pages;

use App\Models\LoyaltyPoint;
use App\Models\LoyaltyTransaction;
use App\Models\Setting;
use App\Models\User;
use App\Services\LoyaltyService;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
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
    public array $manualData = [];

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
            Section::make('Manuel Puan İşlemi')->schema([
                Select::make('user_id')
                    ->label('Müşteri')
                    ->options(fn () => User::where('is_admin', false)->orWhereNull('is_admin')
                        ->orderBy('name')
                        ->pluck('name', 'id'))
                    ->searchable()
                    ->required(),

                TextInput::make('points')
                    ->label('Puan Miktarı (₺)')
                    ->numeric()
                    ->minValue(0.01)
                    ->required(),

                Select::make('operation')
                    ->label('İşlem')
                    ->options([
                        'add'    => 'Ekle',
                        'remove' => 'Çıkar',
                    ])
                    ->default('add')
                    ->required(),

                TextInput::make('reason')
                    ->label('Açıklama')
                    ->required()
                    ->placeholder('Örn: Kampanya hediyesi'),
            ])->columns(2)->statePath('manualData'),

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
        $formState = $this->form->getState();
        $data = $formState; // statePath('data') fields are nested
        Setting::set('loyalty', 'earn_rate', $data['earn_rate'] ?? $this->data['earn_rate']);
        Setting::set('loyalty', 'min_use_amount', $data['min_use_amount'] ?? $this->data['min_use_amount']);
        Setting::set('loyalty', 'expiry_months', $data['expiry_months'] ?? $this->data['expiry_months']);
        Notification::make()->success()->title('Puan kuralları kaydedildi')->send();
    }

    public function processManualPoints(): void
    {
        $this->validate([
            'manualData.user_id'   => ['required', 'exists:users,id'],
            'manualData.points'    => ['required', 'numeric', 'min:0.01'],
            'manualData.operation' => ['required', 'in:add,remove'],
            'manualData.reason'    => ['required', 'string'],
        ]);

        $userId    = $this->manualData['user_id'];
        $amount    = (float) $this->manualData['points'];
        $operation = $this->manualData['operation'];
        $reason    = $this->manualData['reason'];

        $loyaltyPoint = LoyaltyPoint::firstOrCreate(
            ['user_id' => $userId],
            ['balance' => 0, 'total_earned' => 0, 'total_spent' => 0]
        );

        if ($operation === 'add') {
            $loyaltyPoint->addPoints($amount, "[Admin] {$reason}");
            Notification::make()->success()->title("{$amount} puan eklendi")->send();
        } else {
            if ($loyaltyPoint->balance < $amount) {
                Notification::make()->danger()->title('Yetersiz puan bakiyesi')->send();
                return;
            }
            $loyaltyPoint->spendPoints($amount, "[Admin] {$reason}");
            Notification::make()->success()->title("{$amount} puan çıkarıldı")->send();
        }

        $this->manualData = [];
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
