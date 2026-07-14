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
        Schema::create('silid_karunungan_requests', function (Blueprint $table) {
            $table->id();
            $table->string('requestor_first_name');
            $table->string('requestor_last_name');
            $table->string('requestor_middle_name')->nullable();
            $table->tinyInteger('requestor_age')->unsigned();
            $table->string('email');
            $table->string('contact_number', 20);
            $table->date('preferred_date');
            $table->time('preferred_time');
            $table->enum('status', ['pending', 'review', 'approved', 'declined'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('silid_karunungan_requests');
    }
};
