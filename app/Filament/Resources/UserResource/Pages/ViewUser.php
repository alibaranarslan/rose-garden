<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\LoyaltyPoint;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewUser extends ViewRecord
{
    protected static string $resource = UserResource::class;
    protected static string $view = 'filament.pages.view-user';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('add_points')
                ->label('Manuel Puan Ekle/Çıkar')
                ->icon('heroicon-o-plus-circle')
                ->form([
                    TextInput::make('amount')
                        ->label('Puan Tutarı (₺ — eksi değer çıkarır)')
                        ->numeric()
                        ->required(),
                    TextInput::make('description')
                        ->label('Açıklama')
                        ->required(),
                ])
                ->action(function (array $data) {
                    $points = $this->record->loyaltyPoints ?? LoyaltyPoint::create([
                        'user_id' => $this->record->id,
                        'balance' => 0,
                        'total_earned' => 0,
                        'total_spent' => 0,
                    ]);

                    if ($data['amount'] > 0) {
                        $points->addPoints($data['amount'], $data['description']);
                    } else {
                        $points->spendPoints(abs($data['amount']), $data['description']);
                    }

                    Notification::make()->success()->title('Puan güncellendi')->send();
                }),
        ];
    }
}
