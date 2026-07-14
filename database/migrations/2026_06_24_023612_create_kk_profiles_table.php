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
        Schema::create('kk_profiles', function (Blueprint $table) {
            $table->id();
            
            // Personal Details (Step 1)
            $table->string('surname');
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('ext', 10)->nullable(); // Jr, Sr, III, etc.
            $table->integer('age');
            $table->enum('sex', ['Male', 'Female']);
            $table->string('gender')->nullable();
            $table->date('dob');
            $table->enum('civil_status', ['Single', 'Married', 'Widowed', 'Divorced', 'Separated']);
            $table->foreignId('purok_id')->constrained('puroks')->onDelete('cascade');
            $table->string('street_address')->nullable(); // e.g. "594 J.P Rizal Street"
            $table->enum('youth_classification', ['ISY', 'OSY', 'WY']); // ISY (In School Youth), OSY (Out of School Youth), WY (Working Youth)
            $table->string('contact_number', 20);
            $table->string('email');

            // Affiliations (Step 2)
            $table->boolean('registered_sk_voter');
            $table->boolean('registered_national_voter');
            $table->boolean('attended_kk_assembly');
            $table->boolean('part_of_youth_org');
            $table->string('youth_org_name')->nullable();
            $table->boolean('interested_in_joining');

            // Inclusivity & Education (Step 3)
            $table->boolean('part_of_lgbtqia');
            $table->boolean('pwd');
            $table->string('registered_disability')->nullable();
            $table->string('highest_educational_attainment');
            $table->boolean('consent_given')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kk_profiles');
    }
};
