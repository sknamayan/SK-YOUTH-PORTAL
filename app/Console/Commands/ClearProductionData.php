<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\ActivityLog;

/**
 * Artisan command to truncate mock data tables while preserving core data.
 *
 * Target tables: initiatives, accomplishment_reports, sk_officials,
 * transparency_posts, activity_logs.
 *
 * Excluded tables (left untouched): users, puroks, committees, projects, migrations.
 */
class ClearProductionData extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'portal:clear-production-data';

    /**
     * The console command description.
     */
    protected $description = 'Truncate mock data tables (initiatives, accomplishment_reports, sk_officials, transparency_posts, activity_logs) while preserving core data.';

    /**
     * List of tables that will be truncated.
     *
     * @var string[]
     */
    protected $targetTables = [
        'initiatives',
        'accomplishment_reports',
        'sk_officials',
        'transparency_posts',
        'activity_logs',
    ];

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('=== Production Data Clearance ===');

        $tables = implode(', ', $this->targetTables);
        $confirm = $this->confirm(
            "This will PERMANENTLY remove all records from the following tables: {$tables}.\n"
            . "Core tables (users, puroks, committees, projects, migrations) will remain untouched.\n"
            . "Do you wish to continue?"
        );

        if (! $confirm) {
            $this->info('Operation cancelled by user.');
            return 0;
        }

        // -----------------------------------------------------------------
        // Disable foreign‑key checks to allow truncation.
        // -----------------------------------------------------------------
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // -----------------------------------------------------------------
        // Truncate each target table with feedback.
        // -----------------------------------------------------------------
        foreach ($this->targetTables as $table) {
            try {
                DB::table($table)->truncate();
                $this->info("✔ Truncated table: <comment>{$table}</comment>");
            } catch (\Throwable $e) {
                $this->error("✘ Failed to truncate {$table}: {$e->getMessage()}");
            }
        }

        // -----------------------------------------------------------------
        // Re‑enable foreign‑key checks.
        // -----------------------------------------------------------------
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // -----------------------------------------------------------------
        // Log the operation using ActivityLog.
        // -----------------------------------------------------------------
        try {
            ActivityLog::create([
                // Adjust column names to fit your ActivityLog schema.
                // Assuming typical fields: user_id, action, description, ip_address.
                'user_id'    => null,
                'action'     => 'production_data_cleared',
                'description'=> 'Mock data tables cleared via portal:clear-production-data command',
            ]);

            $this->info('✔ Activity log entry recorded.');
        } catch (\Throwable $e) {
            $this->error('✘ Could not create activity‑log entry: ' . $e->getMessage());
        }

        $this->info('=== Data clearance completed successfully ===');
        return 0;
    }
}
