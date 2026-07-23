<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            'registration_responses',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table) && !Schema::hasColumn($table, 'user_id')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->foreignId('user_id')->nullable()->after('id')->constrained('users')->nullOnDelete();
                });
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
            'registration_responses',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'user_id')) {
                Schema::table($table, function (Blueprint $table) {
                    try {
                        $table->dropForeign([$table . '_user_id_foreign']);
                    } catch (\Throwable $e) {
                        // Ignore foreign drop errors (e.g. SQLite / raw MySQL differences)
                    }
                    $table->dropColumn('user_id');
                });
            }
        }
    }
};
