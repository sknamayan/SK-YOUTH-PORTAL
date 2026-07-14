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
            $table->string('position')->nullable();
            $table->date('birthdate')->nullable();
            $table->string('address')->nullable();
            $table->string('kk_profiling_status')->default('No');
            $table->string('profile_picture')->nullable();
            
            // Guardian details for minors
            $table->string('guardian_first_name')->nullable();
            $table->string('guardian_middle_name')->nullable();
            $table->string('guardian_last_name')->nullable();
            $table->tinyInteger('guardian_age')->unsigned()->nullable();
            $table->string('guardian_relation')->nullable();
            $table->string('guardian_contact_number', 20)->nullable();
            $table->string('guardian_address')->nullable();
            $table->string('guardian_gov_id')->nullable();
            
            // Adult verification
            $table->string('voter_cert')->nullable();
            
            // Agreements
            $table->text('health_declaration')->nullable();
            $table->boolean('consent_waiver')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sports_registrations', function (Blueprint $table) {
            $table->dropColumn([
                'position',
                'birthdate',
                'address',
                'kk_profiling_status',
                'profile_picture',
                'guardian_first_name',
                'guardian_middle_name',
                'guardian_last_name',
                'guardian_age',
                'guardian_relation',
                'guardian_contact_number',
                'guardian_address',
                'guardian_gov_id',
                'voter_cert',
                'health_declaration',
                'consent_waiver',
            ]);
        });
    }
};
