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
        // Modify kk_profiles table columns to text type for encrypted values
        Schema::table('kk_profiles', function (Blueprint $table) {
            $table->text('contact_number')->change();
            $table->text('street_address')->nullable()->change();
            $table->text('middle_name')->nullable()->change();
        });

        // Modify health_requests table columns to text type for encrypted values
        Schema::table('health_requests', function (Blueprint $table) {
            $table->text('contact_number')->change();
        });

        // Modify medicine_requests table columns to text type for encrypted values
        Schema::table('medicine_requests', function (Blueprint $table) {
            $table->text('contact_number')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert columns in kk_profiles to their original length limits
        Schema::table('kk_profiles', function (Blueprint $table) {
            $table->string('contact_number', 20)->change();
            $table->string('street_address', 255)->nullable()->change();
            $table->string('middle_name', 255)->nullable()->change();
        });

        // Revert columns in health_requests
        Schema::table('health_requests', function (Blueprint $table) {
            $table->string('contact_number', 20)->change();
        });

        // Revert columns in medicine_requests
        Schema::table('medicine_requests', function (Blueprint $table) {
            $table->string('contact_number', 20)->change();
        });
    }
};
