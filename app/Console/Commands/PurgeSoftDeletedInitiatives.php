<?php

namespace App\Console\Commands;

use App\Models\Initiative;
use App\Models\KkProfile;
use App\Models\Committee;
use Illuminate\Console\Command;

class PurgeSoftDeletedInitiatives extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sk:purge-soft-deleted {--days=30 : Number of days soft deleted before force purging}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Permanently purge soft-deleted records (initiatives, profiles, committees) older than 30 days.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $days = (int) $this->option('days');
        $cutoff = now()->subDays($days);

        // 1. Force delete soft-deleted initiatives older than 30 days
        $purgedInitiatives = Initiative::onlyTrashed()
            ->where('deleted_at', '<=', $cutoff)
            ->forceDelete();

        // 2. Force delete soft-deleted KK profiles older than 30 days
        $purgedProfiles = KkProfile::onlyTrashed()
            ->where('deleted_at', '<=', $cutoff)
            ->forceDelete();

        // 3. Force delete soft-deleted committees older than 30 days
        $purgedCommittees = Committee::onlyTrashed()
            ->where('deleted_at', '<=', $cutoff)
            ->forceDelete();

        $this->info("Successfully purged {$purgedInitiatives} initiatives, {$purgedProfiles} profiles, and {$purgedCommittees} committees older than {$days} days.");

        return Command::SUCCESS;
    }
}
