<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tables = ['health_requests', 'medicine_requests', 'silid_karunungan_requests', 'sports_registrations'];

        if (DB::getDriverName() === 'mysql') {
            foreach ($tables as $table) {
                DB::statement("ALTER TABLE {$table} MODIFY COLUMN status ENUM('pending', 'review', 'approved', 'declined') NOT NULL DEFAULT 'pending'");
            }
        } else {
            // SQLite or other driver (e.g. testing)
            foreach ($tables as $tableName) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->string('status')->default('pending')->change();
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = ['health_requests', 'medicine_requests', 'silid_karunungan_requests', 'sports_registrations'];

        if (DB::getDriverName() === 'mysql') {
            foreach ($tables as $table) {
                DB::statement("ALTER TABLE {$table} MODIFY COLUMN status ENUM('pending', 'approved', 'declined') NOT NULL DEFAULT 'pending'");
            }
        } else {
            foreach ($tables as $tableName) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->string('status')->default('pending')->change();
                });
            }
        }
    }
};
