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
        Schema::table('sports_registrations', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('health_requests', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('medicine_requests', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('silid_karunungan_requests', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('consultation_requests', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sports_registrations', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('health_requests', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('medicine_requests', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('silid_karunungan_requests', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('consultation_requests', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
