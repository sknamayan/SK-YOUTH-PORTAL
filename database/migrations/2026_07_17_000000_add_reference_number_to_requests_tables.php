<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tables = [
            'health_requests',
            'medicine_requests',
            'silid_karunungan_requests',
            'sports_registrations',
            'custom_requests',
            'registration_responses',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                // Add column if it doesn't exist
                if (!Schema::hasColumn($table, 'reference_number')) {
                    Schema::table($table, function (Blueprint $table) {
                        $table->string('reference_number')->nullable()->unique()->after('id');
                    });
                }

                // Populate existing records with unique reference IDs
                $records = DB::table($table)->whereNull('reference_number')->get();
                foreach ($records as $record) {
                    do {
                        $ref = 'SK-REQ-' . strtoupper(Str::random(8));
                    } while (DB::table($table)->where('reference_number', $ref)->exists());

                    DB::table($table)->where('id', $record->id)->update([
                        'reference_number' => $ref
                    ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'health_requests',
            'medicine_requests',
            'silid_karunungan_requests',
            'sports_registrations',
            'custom_requests',
            'registration_responses',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'reference_number')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->dropColumn('reference_number');
                });
            }
        }
    }
};
