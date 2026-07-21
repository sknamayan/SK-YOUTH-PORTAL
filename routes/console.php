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

// Daily task to permanently purge soft-deleted items older than 30 days
Schedule::command('sk:purge-soft-deleted --days=30')
    ->dailyAt('03:00')
    ->withoutOverlapping();
