<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Schedule::useCache(env('SCHEDULE_CACHE_STORE', 'file'));

$onScheduleFailure = function (\Illuminate\Console\Scheduling\ScheduledTaskFailed $event): void {
    $command = $event->task->command ?? $event->task->description;
    Log::channel('daily')->error('[SCHEDULER_FAILURE] Scheduled task failed', [
        'command' => $command,
        'exception' => $event->exception->getMessage(),
    ]);
    if (app()->bound('sentry')) {
        \Sentry\withScope(function (\Sentry\State\Scope $scope) use ($command, $event): void {
            $scope->setTag('scheduler.command', $command);
            \Sentry\captureException($event->exception);
        });
    }
};

Schedule::command('cart:detect-abandoned')->hourly()
    ->onFailure($onScheduleFailure);

if (filter_var(env('SCHEDULE_QUEUE_WORKER', false), FILTER_VALIDATE_BOOL)) {
    Schedule::command('queue:work database --queue=default,analytics --sleep=1 --tries=3 --max-time=50 --stop-when-empty')
        ->everyMinute()
        ->withoutOverlapping(5)
        ->onFailure($onScheduleFailure);
}

Schedule::command('cart:send-reminders')->cron('0 */6 * * *')
    ->onFailure($onScheduleFailure);

Schedule::command('events:send-reminders')->dailyAt('09:00')
    ->onFailure($onScheduleFailure);

Schedule::command('loyalty:expire')->dailyAt('02:00')
    ->onFailure($onScheduleFailure);

Schedule::command('transfers:check-timeout')->hourly()
    ->onFailure($onScheduleFailure);

Schedule::command('sitemap:generate')->dailyAt('04:00')
    ->onFailure($onScheduleFailure);
