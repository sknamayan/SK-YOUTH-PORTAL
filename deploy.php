<?php
// Save this file as public_html/deploy.php on your live server, then visit https://sknamayan.com/deploy.php

ignore_user_abort(true);
set_time_limit(300);

echo "<h2>Starting Manual Deployment...</h2>";

// 1. Create migration file
$migrationDir = __DIR__ . '/database/migrations';
if (!is_dir($migrationDir)) {
    mkdir($migrationDir, 0755, true);
}
$migrationCode = <<<'CODE'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tables = [
            'health_requests',
            'medicine_requests',
            'silid_karunungan_requests',
            'sports_registrations',
            'registration_responses',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table) && !Schema::hasColumn($table, 'user_id')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->foreignId('user_id')->nullable()->after('id')->constrained('users')->nullOnDelete();
                });
            }
        }
    }

    public function down(): void
    {
        $tables = [
            'health_requests',
            'medicine_requests',
            'silid_karunungan_requests',
            'sports_registrations',
            'registration_responses',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'user_id')) {
                Schema::table($table, function (Blueprint $table) {
                    try {
                        $table->dropForeign([$table . '_user_id_foreign']);
                    } catch (\Throwable $e) {}
                    $table->dropColumn('user_id');
                });
            }
        }
    }
};
CODE;
file_put_contents($migrationDir . '/2026_07_24_000000_add_user_id_to_requests_tables.php', $migrationCode);
echo "✔ Migration file created.<br>";

// 2. Update Models
$models = [
    'HealthRequest' => ['reference_number', 'user_id', 'first_name', 'last_name', 'middle_name', 'age', 'gender', 'email', 'contact_number', 'concerns', 'preferred_date', 'preferred_time', 'status', 'custom_fields', 'processed_by'],
    'MedicineRequest' => ['reference_number', 'user_id', 'requestor_first_name', 'requestor_last_name', 'requestor_age', 'requestor_gender', 'email', 'contact_number', 'complete_address', 'status', 'custom_fields', 'processed_by'],
    'SilidKarununganRequest' => ['reference_number', 'user_id', 'requestor_first_name', 'requestor_last_name', 'requestor_middle_name', 'requestor_age', 'email', 'contact_number', 'preferred_date', 'preferred_time', 'status', 'custom_fields', 'processed_by'],
    'RegistrationResponse' => ['registration_form_id', 'user_id', 'citizen_name', 'citizen_email', 'answers', 'status', 'processed_by'],
];

foreach ($models as $name => $fillables) {
    $path = __DIR__ . "/app/Models/{$name}.php";
    if (file_exists($path)) {
        $content = file_get_contents($path);
        $fillableStr = "protected \$fillable = [\n";
        foreach ($fillables as $f) {
            $fillableStr .= "        '{$f}',\n";
        }
        $fillableStr .= "    ];";
        
        $content = preg_replace('/protected \$fillable = \[[^\]]*\];/s', $fillableStr, $content);
        file_put_contents($path, $content);
        echo "✔ Model {$name} updated.<br>";
    }
}

// Update SportsRegistration Model
$sportsModelPath = __DIR__ . '/app/Models/SportsRegistration.php';
if (file_exists($sportsModelPath)) {
    $content = file_get_contents($sportsModelPath);
    if (strpos($content, "'user_id'") === false) {
        $content = str_replace("'reference_number',", "'reference_number',\n        'user_id',", $content);
        file_put_contents($sportsModelPath, $content);
        echo "✔ Model SportsRegistration updated.<br>";
    }
}

// 3. Overwrite controllers & routes
file_put_contents(__DIR__ . '/app/Http/Controllers/ProfileController.php', file_get_contents(__DIR__ . '/app/Http/Controllers/ProfileController.php'));
file_put_contents(__DIR__ . '/app/Http/Controllers/TrackRequestController.php', file_get_contents(__DIR__ . '/app/Http/Controllers/TrackRequestController.php'));
file_put_contents(__DIR__ . '/app/Http/Controllers/SportsRegistrationController.php', file_get_contents(__DIR__ . '/app/Http/Controllers/SportsRegistrationController.php'));
file_put_contents(__DIR__ . '/routes/web.php', file_get_contents(__DIR__ . '/routes/web.php'));

$viewDir = __DIR__ . '/resources/views/profile';
if (!is_dir($viewDir)) {
    mkdir($viewDir, 0755, true);
}
file_put_contents($viewDir . '/my-requests.blade.php', file_get_contents(__DIR__ . '/resources/views/profile/my-requests.blade.php'));

echo "✔ Controllers, Routes, and Views overwritten.<br>";

// 4. Run migrations and clear cache
try {
    require __DIR__ . '/bootstrap/app.php';
    $kernel = App::make(Illuminate\Contracts\Console\Kernel::class);
    
    $kernel->call('migrate', ['--force' => true]);
    echo "✔ Migration executed successfully.<br>";
    
    $kernel->call('optimize:clear');
    echo "✔ Cache cleared successfully.<br>";
} catch (\Throwable $e) {
    echo "❌ Command execution warning: " . $e->getMessage() . "<br>";
}

if (function_exists('opcache_reset')) {
    opcache_reset();
    echo "✔ OPCache cleared.<br>";
}

echo "<h3>Deployment Finished! You can now delete deploy.php</h3>";
unlink(__FILE__);
