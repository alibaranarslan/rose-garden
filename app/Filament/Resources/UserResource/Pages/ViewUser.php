<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\LoyaltyPoint;
use App\Support\AdminPrivileges;
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
                ->label('Manuel puan ekle / çıkar')
                ->icon('heroicon-o-plus-circle')
                ->visible(fn (): bool => AdminPrivileges::canPublishConfiguration(auth()->user()))
                ->form([
                    TextInput::make('amount')
                        ->label('Puan tutarı (₺, eksi değer düşer)')
                        ->numeric()
                        ->minValue(-999999)
                        ->maxValue(999999)
                        ->notIn([0])
                        ->required(),
                    TextInput::make('description')
                        ->label('Açıklama')
                        ->maxLength(255)
                        ->dehydrateStateUsing(fn ($state): string => trim((string) $state))
                        ->required(),
                ])
                ->action(function (array $data) {
                    $amount = (float) $data['amount'];
                    $description = trim((string) $data['description']);
                    $points = $this->record->loyaltyPoints ?? LoyaltyPoint::create([
                        'user_id' => $this->record->id,
                        'balance' => 0,
                        'total_earned' => 0,
                        'total_spent' => 0,
                    ]);

                    if ($amount > 0) {
                        $points->addPoints($amount, $description);
                    } else {
                        if (! $points->spendPoints(abs($amount), $description)) {
                            Notification::make()
                                ->danger()
                                ->title('Yetersiz puan bakiyesi')
                                ->body('Müşterinin bakiyesinden fazla puan düşülemez.')
                                ->send();

                            return;
                        }
                    }

                    Notification::make()->success()->title('Puan güncellendi')->send();
                }),
        ];
    }
}
