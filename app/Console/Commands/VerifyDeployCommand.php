<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class VerifyDeployCommand extends Command
{
    protected $signature = 'deploy:verify {--sentry : Send a test event to Sentry}';
    protected $description = 'Post-deploy smoke test: checks app boot, DB, logs, health endpoint';

    public function handle(): int
    {
        $failed = false;

        $this->info('=== Rose Garden Deploy Verification ===');

        $this->check('App boots', fn () => true, $failed);

        $this->check('Database connection', function () {
            DB::connection()->getPdo();
            return true;
        }, $failed);

        $this->check('Log directory writable', function () {
            return is_writable(storage_path('logs'));
        }, $failed);

        $this->check('Cache read/write', function () {
            cache()->put('deploy_verify_test', 'ok', 10);
            return cache()->get('deploy_verify_test') === 'ok';
        }, $failed);

        $appUrl = config('app.url');
        $this->check("Health endpoint ({$appUrl}/health)", function () use ($appUrl) {
            $response = Http::timeout(5)->get("{$appUrl}/health");
            return $response->ok() && $response->json('status') === 'ok';
        }, $failed);

        $this->check("Homepage responds ({$appUrl}/)", function () use ($appUrl) {
            return Http::timeout(10)->get("{$appUrl}/")->successful();
        }, $failed);

        if ($this->option('sentry')) {
            $this->check('Sentry test event', function () {
                if (!config('sentry.dsn')) {
                    $this->warn('  SENTRY_LARAVEL_DSN not set — skipping');
                    return true;
                }
                \Sentry\captureMessage('deploy:verify test event from Rose Garden');
                return true;
            }, $failed);
        }

        $this->newLine();
        if ($failed) {
            $this->error('Some checks FAILED. Review above.');
            return Command::FAILURE;
        }

        $this->info('All checks passed.');
        return Command::SUCCESS;
    }

    private function check(string $name, callable $test, bool &$failed): void
    {
        try {
            if ($test()) {
                $this->line("  [OK] {$name}");
            } else {
                $this->error("  [FAIL] {$name}");
                $failed = true;
            }
        } catch (\Throwable $e) {
            $this->error("  [FAIL] {$name}: {$e->getMessage()}");
            $failed = true;
        }
    }
}
