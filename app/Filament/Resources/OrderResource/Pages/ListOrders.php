<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Tümü'),
            'pending' => Tab::make('Bekleyen')
                ->modifyQueryUsing(fn (Builder $q) => $q->pending()),
            'awaiting_payment' => Tab::make('Ödeme Bekleyen')
                ->modifyQueryUsing(fn (Builder $q) => $q->where('status', 'awaiting_payment')),
            'bank_transfer' => Tab::make('Havale Onayı')
                ->modifyQueryUsing(fn (Builder $q) => $q->awaitingBankTransfer()),
            'today' => Tab::make('Bugünün Teslimatları')
                ->modifyQueryUsing(fn (Builder $q) => $q->deliveryDate(today()->format('Y-m-d'))),
        ];
    }
}
