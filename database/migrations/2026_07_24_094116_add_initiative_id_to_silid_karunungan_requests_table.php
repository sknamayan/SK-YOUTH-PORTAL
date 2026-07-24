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
        Schema::table('silid_karunungan_requests', function (Blueprint $table) {
            $table->foreignId('initiative_id')->nullable()->constrained('initiatives')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('silid_karunungan_requests', function (Blueprint $table) {
            $table->dropConstrainedForeignId('initiative_id');
        });
    }
};
