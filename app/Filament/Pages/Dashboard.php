<?php

namespace App\Filament\Pages;

use App\Services\RgControlCenterService;
use App\Support\ControlCenter\RgControlCenterPresenter;
use Filament\Forms\Components\Select;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Actions\FilterAction;
use Filament\Pages\Dashboard\Concerns\HasFiltersAction;

class Dashboard extends BaseDashboard
{
    use HasFiltersAction;

    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static ?string $navigationLabel = 'Operasyon Masası';

    protected static ?string $title = 'Operasyon Masası';

    protected static string $view = 'filament.pages.dashboard-operations';

    public static function getNavigationLabel(): string
    {
        return 'Operasyon Masası';
    }

    public function getTitle(): string
    {
        return 'Operasyon Masası';
    }

    public function getColumns(): int | string | array
    {
        return 1;
    }

    protected function getHeaderActions(): array
    {
        return [
            FilterAction::make()
                ->label('Görünüm')
                ->form([
                    Select::make('window')
                        ->label('Zaman aralığı')
                        ->options([
                            'today' => 'Bugün',
                            '7d' => 'Son 7 gün',
                            '30d' => 'Son 30 gün',
                        ])
                        ->default('7d')
                        ->native(false),
                    Select::make('lens')
                        ->label('Operasyon odağı')
                        ->options([
                            'all' => 'Tüm siparişler',
                            'payments' => 'Ödeme odaklı',
                            'delivery' => 'Teslimat odaklı',
                        ])
                        ->default('all')
                        ->native(false),
                ]),
        ];
    }

    public function getViewData(): array
    {
        $snapshot = app(RgControlCenterService::class)->snapshot($this->filters ?? [], auth()->user());

        return app(RgControlCenterPresenter::class)->present($snapshot);
    }
}
