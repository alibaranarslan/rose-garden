<?php

namespace App\Console\Commands;

use App\Services\LoyaltyService;
use Illuminate\Console\Command;

class ExpireLoyaltyPointsCommand extends Command
{
    protected $signature = 'loyalty:expire';
    protected $description = 'Süresi dolan sadakat puanlarını sıfırla';

    public function handle(LoyaltyService $loyaltyService): int
    {
        $loyaltyService->expirePoints();

        $this->info('Süresi dolan puanlar işlendi.');

        return self::SUCCESS;
    }
}
