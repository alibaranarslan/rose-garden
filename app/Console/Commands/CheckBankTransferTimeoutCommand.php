<?php

namespace App\Console\Commands;

use App\Jobs\CheckBankTransferTimeoutJob;
use Illuminate\Console\Command;

class CheckBankTransferTimeoutCommand extends Command
{
    protected $signature = 'transfers:check-timeout';
    protected $description = 'Havale bekleme süresi dolan siparişleri kontrol et ve işle';

    public function handle(): int
    {
        CheckBankTransferTimeoutJob::dispatch();

        $this->info('Havale timeout kontrolü kuyruğa eklendi.');

        return self::SUCCESS;
    }
}
