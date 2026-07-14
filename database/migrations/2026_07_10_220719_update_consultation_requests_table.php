<?php
 
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
 
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('consultation_requests', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('id')->constrained('users')->onDelete('set null');
            $table->string('status')->default('Open')->change();
        });
 
        // Migrate existing status values
        DB::table('consultation_requests')->where('status', 'Pending')->update(['status' => 'Open']);
        DB::table('consultation_requests')->where('status', 'In Review')->update(['status' => 'In Progress']);
    }
 
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('consultation_requests', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
            $table->string('status')->default('Pending')->change();
        });
 
        // Revert status values
        DB::table('consultation_requests')->where('status', 'Open')->update(['status' => 'Pending']);
        DB::table('consultation_requests')->where('status', 'In Progress')->update(['status' => 'In Review']);
    }
};
