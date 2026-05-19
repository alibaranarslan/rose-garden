<?php

namespace App\Filament\Pages;

use App\Models\LoyaltyPoint;
use App\Models\LoyaltyTransaction;
use App\Models\Setting;
use App\Models\User;
use App\Support\AdminActionLogger;
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
    protected static ?string $navigationGroup = 'Sadakat ve Kampanya';
    protected static ?string $navigationLabel = 'Sadakat Puanları';
    protected static ?string $title = 'Sadakat Puan Yönetimi';
    protected static ?int $navigationSort = 3;
    protected static string $view = 'filament.pages.loyalty-management';

    public string $activeTab = 'rules';
    public array $data = [];
    public array $manualData = [];

    public function mount(): void
    {
        $storedEarnRate = Setting::get('loyalty', 'earn_rate', '0.05');

        $this->data = [
            'earn_rate' => $this->formatEarnRateForDisplay($storedEarnRate),
            'min_use_amount' => Setting::get('loyalty', 'min_use_amount', '50'),
            'expiry_months' => Setting::get('loyalty', 'expiry_months', '12'),
        ];
        $this->form->fill($this->data);
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Manuel puan işlemi')->schema([
                Select::make('user_id')
                    ->label('Müşteri')
                    ->options(fn () => User::where('is_admin', false)->orWhereNull('is_admin')->orderBy('name')->pluck('name', 'id'))
                    ->searchable()
                    ->required(),
                TextInput::make('points')
                    ->label('Puan tutarı (₺)')
                    ->numeric()
                    ->minValue(0.01)
                    ->maxValue(999999)
                    ->required(),
                Select::make('operation')
                    ->label('İşlem')
                    ->options([
                        'add' => 'Ekle',
                        'remove' => 'Çıkar',
                    ])
                    ->default('add')
                    ->required(),
                TextInput::make('reason')
                    ->label('Açıklama')
                    ->maxLength(255)
                    ->required()
                    ->placeholder('Örn: Kampanya hediyesi'),
            ])->columns(2)->statePath('manualData'),
            Section::make('Puan kuralları')->schema([
                TextInput::make('earn_rate')
                    ->label('Kazanım oranı (%)')
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(100)
                    ->helperText('Her 100 ₺ harcamada kaç ₺ puan kazanılacağını belirler.'),
                TextInput::make('min_use_amount')
                    ->label('Minimum kullanım tutarı (₺)')
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(999999)
                    ->helperText('Puan kullanımına izin veren minimum sipariş toplamı.'),
                TextInput::make('expiry_months')
                    ->label('Son kullanma süresi (ay)')
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(120)
                    ->helperText('0 değeri puanların süresiz kalacağını belirtir.'),
            ])->columns(3),
        ])->statePath('data');
    }

    public function saveRules(): void
    {
        $this->validate([
            'data.earn_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'data.min_use_amount' => ['nullable', 'numeric', 'min:0', 'max:999999'],
            'data.expiry_months' => ['nullable', 'integer', 'min:0', 'max:120'],
        ]);

        Setting::set('loyalty', 'earn_rate', $this->normalizeEarnRateForStorage($this->data['earn_rate'] ?? '5'));
        Setting::set('loyalty', 'min_use_amount', $this->data['min_use_amount'] ?? '50');
        Setting::set('loyalty', 'expiry_months', $this->data['expiry_months'] ?? '12');
        AdminActionLogger::record('loyalty.rules_save', null, [
            'earn_rate_percent' => $this->data['earn_rate'] ?? null,
            'min_use_amount' => $this->data['min_use_amount'] ?? null,
            'expiry_months' => $this->data['expiry_months'] ?? null,
        ]);
        Notification::make()->success()->title('Puan kuralları kaydedildi')->send();
    }

    private function formatEarnRateForDisplay(mixed $value): string
    {
        $rate = (float) $value;

        if ($rate <= 0) {
            return '5';
        }

        return (string) ($rate <= 1 ? $rate * 100 : $rate);
    }

    private function normalizeEarnRateForStorage(mixed $value): string
    {
        $rate = max(0, (float) $value);
        $normalized = $rate > 1 ? $rate / 100 : $rate;

        return rtrim(rtrim(number_format($normalized, 4, '.', ''), '0'), '.');
    }

    public function processManualPoints(): void
    {
        if ($this->manualData === [] && isset($this->data['manualData']) && is_array($this->data['manualData'])) {
            $this->manualData = $this->data['manualData'];
        }

        $this->validate([
            'manualData.user_id' => ['required', 'exists:users,id'],
            'manualData.points' => ['required', 'numeric', 'min:0.01', 'max:999999'],
            'manualData.operation' => ['required', 'in:add,remove'],
            'manualData.reason' => ['required', 'string', 'max:255'],
        ]);

        $userId = $this->manualData['user_id'];
        $amount = (float) $this->manualData['points'];
        $operation = $this->manualData['operation'];
        $reason = $this->manualData['reason'];

        $loyaltyPoint = LoyaltyPoint::firstOrCreate(
            ['user_id' => $userId],
            ['balance' => 0, 'total_earned' => 0, 'total_spent' => 0]
        );

        if ($operation === 'add') {
            $loyaltyPoint->addPoints($amount, "[Admin] {$reason}");
            Notification::make()->success()->title("{$amount} puan eklendi")->send();
        } else {
            if ($loyaltyPoint->balance < $amount) {
                AdminActionLogger::record('loyalty.manual_points_failed', $loyaltyPoint, [
                    'user_id' => $userId,
                    'operation' => $operation,
                    'amount' => $amount,
                    'reason' => 'insufficient_balance',
                ]);

                Notification::make()->danger()->title('Yetersiz puan bakiyesi')->send();

                return;
            }

            $loyaltyPoint->spendPoints(abs($amount), "[Admin] {$reason}");
            Notification::make()->success()->title("{$amount} puan çıkarıldı")->send();
        }

        AdminActionLogger::record('loyalty.manual_points', $loyaltyPoint, [
            'user_id' => $userId,
            'operation' => $operation,
            'amount' => $amount,
        ]);

        $this->manualData = [];
        $this->data['manualData'] = [];
    }

    public function getViewData(): array
    {
        $totalDistributed = LoyaltyTransaction::where('type', 'earned')->sum('amount');
        $totalUsed = LoyaltyTransaction::where('type', 'spent')->sum('amount');
        $pendingBalance = LoyaltyPoint::sum('balance');
        $usageRate = $totalDistributed > 0 ? round(($totalUsed / $totalDistributed) * 100, 1) : 0;
        $topUsers = LoyaltyPoint::with('user')->where('balance', '>', 0)->orderByDesc('balance')->limit(10)->get();

        return compact('totalDistributed', 'totalUsed', 'pendingBalance', 'usageRate', 'topUsers');
    }
}
