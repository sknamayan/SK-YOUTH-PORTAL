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
        Schema::table('users', function (Blueprint $table) {
            $table->string('contact_number')->nullable()->after('email');
            $table->string('avatar')->nullable()->after('contact_number');
            $table->string('theme')->default('system')->after('avatar'); // 'light', 'dark', 'system'
            $table->string('language')->default('en')->after('theme'); // 'en', 'fil'
            $table->boolean('notify_request_status')->default(true)->after('language');
            $table->boolean('notify_announcements')->default(true)->after('notify_request_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'contact_number',
                'avatar',
                'theme',
                'language',
                'notify_request_status',
                'notify_announcements',
            ]);
        });
    }
};
