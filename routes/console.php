<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// Terk edilmiş sepet tespiti — saatlik
Schedule::command('cart:detect-abandoned')->hourly();

// Sepet hatırlatma — 6 saatte bir
Schedule::command('cart:send-reminders')->cron('0 */6 * * *');

// Olay hatırlatma — günlük 09:00
Schedule::command('events:send-reminders')->dailyAt('09:00');

// Puan sona erdirme — günlük 02:00
Schedule::command('loyalty:expire')->dailyAt('02:00');

// Havale zaman aşımı kontrolü — saatlik
Schedule::command('transfers:check-timeout')->hourly();

// Sitemap oluşturma — günlük 04:00
Schedule::command('sitemap:generate')->dailyAt('04:00');
