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
        Schema::create('leagues', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('sport'); // Basketball, Volleyball, etc.
            $table->string('status')->default('Active'); // Active, Completed, Draft
            $table->timestamps();
        });

        Schema::create('registration_forms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('league_id')->constrained()->cascadeOnDelete();
            $table->string('division_name'); // e.g. Basketball Senior, Women’s Volleyball
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('form_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registration_form_id')->constrained()->cascadeOnDelete();
            $table->string('field_label');
            $table->string('field_name');
            $table->string('field_type'); // text, number, select, radio, checkbox, file
            $table->boolean('is_required')->default(false);
            $table->json('options')->nullable(); // Options list
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('registration_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registration_form_id')->constrained()->cascadeOnDelete();
            $table->string('citizen_name')->nullable();
            $table->string('citizen_email')->nullable();
            $table->json('answers'); // JSON structure
            $table->string('status')->default('Pending'); // Pending, Approved, Declined
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registration_responses');
        Schema::dropIfExists('form_fields');
        Schema::dropIfExists('registration_forms');
        Schema::dropIfExists('leagues');
    }
};
