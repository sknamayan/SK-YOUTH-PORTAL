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
            if (!Schema::hasColumn('initiatives', 'picture_path')) {
                $table->string('picture_path')->nullable()->after('description');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('initiatives', function (Blueprint $table) {
            if (Schema::hasColumn('initiatives', 'picture_path')) {
                $table->dropColumn('picture_path');
            }
        });
    }
};
