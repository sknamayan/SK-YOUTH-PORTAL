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
        Schema::create('sports_registrations', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('middle_name')->nullable();
            $table->tinyInteger('age')->unsigned();
            $table->enum('gender', ['Male', 'Female', 'Prefer not to say']);
            $table->string('email');
            $table->string('contact_number', 20);
            $table->string('sport');
            $table->string('team_name')->nullable();
            $table->date('event_date');
            $table->text('remarks')->nullable();
            $table->enum('status', ['pending', 'review', 'approved', 'declined'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sports_registrations');
    }
};
