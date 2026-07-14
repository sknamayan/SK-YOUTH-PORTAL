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
            $table->boolean('is_highlighted')->default(false);
        });

        // Set the three default initiatives as highlighted
        Illuminate\Support\Facades\DB::table('initiatives')
            ->whereIn('form_route', ['forms.health.create', 'forms.silid.create', 'forms.medicine.create'])
            ->update(['is_highlighted' => true]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('initiatives', function (Blueprint $table) {
            $table->dropColumn('is_highlighted');
        });
    }
};
