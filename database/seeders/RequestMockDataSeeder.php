<?php

namespace Database\Seeders;

use App\Models\HealthRequest;
use App\Models\MedicineRequest;
use App\Models\SilidKarununganRequest;
use App\Models\SportsRegistration;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class RequestMockDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = ['pending', 'approved', 'declined'];
        $genders = ['Male', 'Female', 'Prefer not to say'];
        $sports = ['Basketball', 'Volleyball', 'Badminton', 'Esports', 'Athletics'];

        $firstNames = ['Juan', 'Maria', 'Jose', 'Ana', 'Pedro', 'Lisa', 'David', 'Emma', 'Carlos', 'Sofia', 'Michael', 'Chloe', 'Daniel', 'Grace', 'James', 'Mia'];
        $lastNames = ['Cruz', 'Santos', 'Reyes', 'Gonzales', 'Bautista', 'Aquino', 'Garcia', 'Dela Cruz', 'Lopez', 'Torres', 'Perez', 'Castro', 'Hernandez'];

        // Generate 30 HealthRequests
        for ($i = 0; $i < 30; $i++) {
            $created = Carbon::now()->subMonths(rand(0, 5))->subDays(rand(0, 28))->subHours(rand(0, 23));
            HealthRequest::create([
                'first_name' => $firstNames[array_rand($firstNames)],
                'last_name' => $lastNames[array_rand($lastNames)],
                'middle_name' => rand(0, 1) ? 'M.' : null,
                'age' => rand(15, 30),
                'gender' => $genders[array_rand($genders)],
                'email' => 'citizen' . $i . '@namayan.local',
                'contact_number' => '0917' . rand(1000000, 9999999),
                'concerns' => 'Routine checkup and general physical consultation for sports qualification.',
                'preferred_date' => Carbon::now()->addDays(rand(1, 30))->toDateString(),
                'preferred_time' => sprintf('%02d:00:00', rand(8, 17)),
                'status' => $statuses[array_rand($statuses)],
                'created_at' => $created,
                'updated_at' => $created,
            ]);
        }

        // Generate 30 MedicineRequests
        for ($i = 0; $i < 30; $i++) {
            $created = Carbon::now()->subMonths(rand(0, 5))->subDays(rand(0, 28))->subHours(rand(0, 23));
            MedicineRequest::create([
                'requestor_first_name' => $firstNames[array_rand($firstNames)],
                'requestor_last_name' => $lastNames[array_rand($lastNames)],
                'requestor_age' => rand(18, 65),
                'requestor_gender' => $genders[array_rand($genders)],
                'email' => 'medicine' . $i . '@namayan.local',
                'contact_number' => '0918' . rand(1000000, 9999999),
                'complete_address' => rand(1, 100) . ' Street Name, Barangay Namayan, Mandaluyong City',
                'status' => $statuses[array_rand($statuses)],
                'created_at' => $created,
                'updated_at' => $created,
            ]);
        }

        // Generate 30 SilidKarununganRequests
        for ($i = 0; $i < 30; $i++) {
            $created = Carbon::now()->subMonths(rand(0, 5))->subDays(rand(0, 28))->subHours(rand(0, 23));
            SilidKarununganRequest::create([
                'requestor_first_name' => $firstNames[array_rand($firstNames)],
                'requestor_last_name' => $lastNames[array_rand($lastNames)],
                'requestor_middle_name' => rand(0, 1) ? 'K.' : null,
                'requestor_age' => rand(12, 25),
                'email' => 'silid' . $i . '@namayan.local',
                'contact_number' => '0919' . rand(1000000, 9999999),
                'preferred_date' => Carbon::now()->addDays(rand(1, 30))->toDateString(),
                'preferred_time' => sprintf('%02d:00:00', rand(8, 17)),
                'status' => $statuses[array_rand($statuses)],
                'created_at' => $created,
                'updated_at' => $created,
            ]);
        }

        // Generate 30 SportsRegistrations
        for ($i = 0; $i < 30; $i++) {
            $created = Carbon::now()->subMonths(rand(0, 5))->subDays(rand(0, 28))->subHours(rand(0, 23));
            SportsRegistration::create([
                'first_name' => $firstNames[array_rand($firstNames)],
                'last_name' => $lastNames[array_rand($lastNames)],
                'middle_name' => rand(0, 1) ? 'S.' : null,
                'age' => rand(15, 28),
                'gender' => $genders[array_rand($genders)],
                'email' => 'sports' . $i . '@namayan.local',
                'contact_number' => '0920' . rand(1000000, 9999999),
                'sport' => $sports[array_rand($sports)],
                'team_name' => rand(0, 1) ? 'Team Namayan ' . chr(rand(65, 90)) : null,
                'event_date' => Carbon::now()->addDays(rand(1, 30))->toDateString(),
                'remarks' => 'Looking forward to the tournament!',
                'status' => $statuses[array_rand($statuses)],
                'created_at' => $created,
                'updated_at' => $created,
            ]);
        }
    }
}
