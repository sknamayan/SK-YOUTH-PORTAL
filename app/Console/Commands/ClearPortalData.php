<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

/**
 * Clear all development / mock data from the portal.
 *
 * Keeps:
 *   – Super‑admin users
 *   – System settings
 *   – Purok data
 *   – Any other tables that are not explicitly listed below
 *
 * Logs the action to the `audit_logs` table.
 */
class ClearPortalData extends Command
{
    /** The name / signature of the console command. */
    protected $signature = 'portal:clear-data';

    /** The console command description. */
    protected $description = 'Wipes all development / mock data while preserving admin accounts and core configuration.';

    /** Tables that should be completely emptied. */
    private $tables = [
        // Core portal data that is safe to delete
        'initiatives',
        'registration_forms',
        'registration_responses',
        'consultation_requests',
        'health_requests',
        'medicine_requests',
        'silid_karunungan_requests',
        'custom_requests',
        'complaint_messages',
        'user_notifications',
        'kk_profiles',
        'sports_registrations',
        'activity_logs',
        // Optional – also clear ancillary tables that are purely mock data
        'form_fields',
        'leagues',
        'news_articles',
        'partners',
        'accomplishment_reports',
        'carousel_slides',
        'transparency_posts',
        'calendar_events',
    ];

    /** Tables that **must never** be truncated. */
    private $protectedTables = [
        'users',      // keep superadmin accounts
        'settings',   // system configuration
        'puroks',     // geographic reference data
    ];

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // -----------------------------------------------------------------
        // 1️⃣  Safety confirmation
        // -----------------------------------------------------------------
        if (! $this->confirm('⚠️  Are you absolutely sure you want to DELETE ALL development/mock data? This action cannot be undone.', false)) {
            $this->info('✅  Operation cancelled – no data was touched.');
            return Command::SUCCESS;
        }

        $this->line('🚧  Disabling foreign‑key checks...');
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // -----------------------------------------------------------------
        // 2️⃣  Truncate each target table & reset its auto‑increment
        // -----------------------------------------------------------------
        foreach ($this->tables as $table) {
            // Guard against accidental truncation of protected tables
            if (in_array($table, $this->protectedTables, true)) {
                continue;
            }

            $this->info("🗑  Truncating `{$table}` …");
            DB::table($table)->truncate();

            // Reset the auto‑increment to 1 (MySQL/MariaDB syntax)
            DB::statement("ALTER TABLE `{$table}` AUTO_INCREMENT = 1");
        }

        $this->line('🔧  Re‑enabling foreign‑key checks...');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // -----------------------------------------------------------------
        // 3️⃣  Log the wipe to activity_logs
        // -----------------------------------------------------------------
        $userId = Auth::id(); // may be null if run from CLI
        $description = 'System data wiped for production preparation';

        DB::table('activity_logs')->insert([
            'user_id'      => $userId,
            'action'       => 'data_wipe',
            'subject_type' => self::class,
            'subject_id'   => 0,
            'payload'      => json_encode(['description' => $description]),
            'ip_address'   => '127.0.0.1',
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);

        $this->info('✅  Data wipe complete and logged.');
        return Command::SUCCESS;
    }
}
