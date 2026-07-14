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
        Schema::create('medicine_requests', function (Blueprint $table) {
            $table->id();
            $table->string('requestor_first_name');
            $table->string('requestor_last_name');
            $table->tinyInteger('requestor_age')->unsigned();
            $table->enum('requestor_gender', ['Male', 'Female', 'Prefer not to say']);
            $table->string('email');
            $table->string('contact_number', 20);
            $table->text('complete_address');
            $table->enum('status', ['pending', 'review', 'approved', 'declined'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medicine_requests');
    }
};
