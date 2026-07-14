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
        Schema::table('kk_profiles', function (Blueprint $table) {
            $table->enum('status', ['pending', 'approved', 'declined'])->default('approved')->after('consent_given');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kk_profiles', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
