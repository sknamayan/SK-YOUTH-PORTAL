<?php

namespace App\Console;

use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\ClearPortalData::class,
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule($schedule)
    {
        // Offload old resolved attachments to cloud storage weekly
        $schedule->command('app:offload-files --disk=s3 --age=90')->weekly();

        // Prune expired password reset tokens every 15 minutes
        $schedule->command('auth:clear-resets')->everyFifteenMinutes();

        // Prune expired cache keys from cache driver hourly (supported in Laravel 11+)
        $schedule->command('cache:prune-expired')->hourly();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');
        require base_path('routes/console.php');
    }
}
