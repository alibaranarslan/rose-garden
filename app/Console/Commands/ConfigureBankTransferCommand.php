<?php

namespace App\Console\Commands;

use App\Models\Setting;
use Illuminate\Console\Command;

class ConfigureBankTransferCommand extends Command
{
    protected $signature = 'rg:configure-bank-transfer
        {--bank= : Bank name shown during checkout}
        {--iban= : Turkish IBAN shown during checkout}
        {--account-holder= : Account holder shown during checkout}
        {--timeout=72 : Bank transfer timeout in hours}';

    protected $description = 'Configure the bank transfer / EFT payment details used by checkout and order notifications.';

    public function handle(): int
    {
        $bankName = trim((string) $this->option('bank'));
        $iban = strtoupper(preg_replace('/\s+/', '', (string) $this->option('iban')));
        $accountHolder = trim((string) $this->option('account-holder'));
        $timeoutHours = min(168, max(1, (int) $this->option('timeout')));

        if ($bankName === '' || $iban === '' || $accountHolder === '') {
            $this->error('Bank name, IBAN and account holder are required.');

            return self::FAILURE;
        }

        if (! preg_match('/^TR\d{24}$/', $iban)) {
            $this->error('IBAN must start with TR and contain 26 characters.');

            return self::FAILURE;
        }

        Setting::set('payment', 'bank_name', $bankName);
        Setting::set('payment', 'bank_iban', $iban);
        Setting::set('payment', 'bank_account_holder', $accountHolder);
        Setting::set('payment', 'transfer_timeout_hours', $timeoutHours);

        Setting::forgetStorefrontCaches();
        Setting::bumpStorefrontContentVersion();

        $this->info('Bank transfer / EFT settings updated.');

        $this->table(
            ['Field', 'Value'],
            [
                ['Bank', $bankName],
                ['IBAN', $iban],
                ['Account holder', $accountHolder],
                ['Timeout', $timeoutHours.' hours'],
            ]
        );

        return self::SUCCESS;
    }
}
