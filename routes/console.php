<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule pending requests archiver daily at 02:00 without overlapping
Schedule::command('sk:archive-pending-requests --days=90')
    ->dailyAt('02:00')
    ->withoutOverlapping();

Schedule::command('sk:generate-lgu-monthly-report')
    ->monthlyOn(1, '06:00')
    ->withoutOverlapping();
