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
        Schema::table('initiatives', function (Blueprint $table) {
            $table->json('custom_fields')->nullable();
        });

        Schema::table('health_requests', function (Blueprint $table) {
            $table->json('custom_fields')->nullable();
        });

        Schema::table('medicine_requests', function (Blueprint $table) {
            $table->json('custom_fields')->nullable();
        });

        Schema::table('silid_karunungan_requests', function (Blueprint $table) {
            $table->json('custom_fields')->nullable();
        });

        Schema::table('sports_registrations', function (Blueprint $table) {
            $table->json('custom_fields')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('initiatives', function (Blueprint $table) {
            $table->dropColumn('custom_fields');
        });

        Schema::table('health_requests', function (Blueprint $table) {
            $table->dropColumn('custom_fields');
        });

        Schema::table('medicine_requests', function (Blueprint $table) {
            $table->dropColumn('custom_fields');
        });

        Schema::table('silid_karunungan_requests', function (Blueprint $table) {
            $table->dropColumn('custom_fields');
        });

        Schema::table('sports_registrations', function (Blueprint $table) {
            $table->dropColumn('custom_fields');
        });
    }
};
