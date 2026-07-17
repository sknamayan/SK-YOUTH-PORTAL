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
            $table->string('category')->default('sk_youth')->after('consent_given');
            $table->index(['category', 'age']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kk_profiles', function (Blueprint $table) {
            $table->dropIndex(['category', 'age']);
            $table->dropColumn('category');
        });
    }
};
