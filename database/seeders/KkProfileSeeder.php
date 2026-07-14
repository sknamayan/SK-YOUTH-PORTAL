<?php

namespace Database\Seeders;

use App\Models\KkProfile;
use App\Models\Purok;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class KkProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $purokIds = Purok::pluck('id')->toArray();
        if (empty($purokIds)) {
            return;
        }

        $surnames = ['Dela Cruz', 'Santos', 'Reyes', 'Gonzales', 'Bautista', 'Aquino', 'Garcia', 'Lopez', 'Torres', 'Perez', 'Castro', 'Hernandez', 'Mercado', 'Flores'];
        $firstNames = ['Juan', 'Maria', 'Jose', 'Ana', 'Pedro', 'Lisa', 'David', 'Emma', 'Carlos', 'Sofia', 'Michael', 'Chloe', 'Daniel', 'Grace', 'James', 'Mia'];
        $middleNames = ['Santiago', 'San Pablo', 'Aquino', 'Del Rosario', 'Cruz', 'Santos', 'Reyes', 'Gonzales', null];
        $extensions = ['Jr.', 'Sr.', 'III', null, null, null, null];
        
        $classifications = ['ISY', 'OSY', 'WY'];
        $civilStatuses = ['Single', 'Married', 'Widowed', 'Divorced', 'Separated'];
        $attainments = ['High School Student', 'High School Graduate', '1st year College', '2nd year College', 'College Graduate', 'Vocational Course'];
        $genders = ['Male', 'Female', 'Non-binary', 'LGBTQIA+'];

        for ($i = 0; $i < 25; $i++) {
            $dob = Carbon::now()->subYears(rand(15, 30))->subMonths(rand(0, 11))->subDays(rand(1, 28));
            $age = $dob->age;
            
            $sex = rand(0, 1) ? 'Male' : 'Female';
            $gender = $sex === 'Male' ? ($genders[rand(0, 2) === 0 ? 0 : 3]) : ($genders[rand(0, 2) === 0 ? 1 : 3]);
            $isLgbt = in_array($gender, ['Non-binary', 'LGBTQIA+']);
            
            $partOfOrg = (bool)rand(0, 1);
            $orgName = $partOfOrg ? 'Namayan Youth Association ' . chr(rand(65, 70)) : null;
            $interested = !$partOfOrg ? (bool)rand(0, 1) : false;

            $pwd = rand(0, 9) === 0; // 10% chance PWD
            $disability = $pwd ? 'Visual Impairment' : null;

            KkProfile::create([
                'surname' => $surnames[array_rand($surnames)],
                'first_name' => $firstNames[array_rand($firstNames)],
                'middle_name' => $middleNames[array_rand($middleNames)],
                'ext' => $extensions[array_rand($extensions)],
                'age' => $age,
                'sex' => $sex,
                'gender' => $gender,
                'dob' => $dob->toDateString(),
                'civil_status' => $civilStatuses[array_rand($civilStatuses)],
                'purok_id' => $purokIds[array_rand($purokIds)],
                'street_address' => rand(1, 200) . ' Street Road',
                'youth_classification' => $classifications[array_rand($classifications)],
                'contact_number' => '0917' . rand(1000000, 9999999),
                'email' => strtolower('citizen' . $i . '@namayan.local'),
                
                'registered_sk_voter' => (bool)rand(0, 1),
                'registered_national_voter' => (bool)rand(0, 1),
                'attended_kk_assembly' => (bool)rand(0, 1),
                'part_of_youth_org' => $partOfOrg,
                'youth_org_name' => $orgName,
                'interested_in_joining' => $interested,
                
                'part_of_lgbtqia' => $isLgbt,
                'pwd' => $pwd,
                'registered_disability' => $disability,
                'highest_educational_attainment' => $attainments[array_rand($attainments)],
                'consent_given' => true,
            ]);
        }
    }
}
