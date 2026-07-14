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
            'kk_profiles',
            'health_requests',
            'medicine_requests',
            'silid_karunungan_requests',
            'sports_registrations',
        ];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->foreignId('processed_by')
                    ->nullable()
                    ->constrained('users')
                    ->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'kk_profiles',
            'health_requests',
            'medicine_requests',
            'silid_karunungan_requests',
            'sports_registrations',
        ];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->dropForeign(['processed_by']);
                $table->dropColumn('processed_by');
            });
        }
    }
};
