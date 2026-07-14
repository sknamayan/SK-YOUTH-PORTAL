<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\HealthRequest;
use App\Models\MedicineRequest;
use App\Models\SilidKarununganRequest;
use App\Models\SportsRegistration;
use Carbon\Carbon;

class ArchivePendingRequests extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sk:archive-pending-requests {--days=90 : The number of days before pending requests are archived}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Bulk decline pending requests older than specified days';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $days = (int) $this->option('days');
        $cutoffDate = Carbon::now()->subDays($days);

        $this->info("Archiving (declining) pending requests older than {$days} days (Cutoff: {$cutoffDate->toDateTimeString()})...");

        $models = [
            HealthRequest::class,
            MedicineRequest::class,
            SilidKarununganRequest::class,
            SportsRegistration::class,
        ];

        foreach ($models as $modelClass) {
            $updated = $modelClass::whereIn('status', ['pending', 'review'])
                ->where('created_at', '<', $cutoffDate)
                ->update(['status' => 'declined']);

            $basename = class_basename($modelClass);
            $this->line("{$basename}: Declined {$updated} pending requests.");
        }

        $this->info('Archiving process complete.');

        return Command::SUCCESS;
    }
}
