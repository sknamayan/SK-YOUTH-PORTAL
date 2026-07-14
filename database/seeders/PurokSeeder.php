<?php

namespace Database\Seeders;

use App\Models\Purok;
use Illuminate\Database\Seeder;

class PurokSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $puroks = [
            ['id' => 1, 'purok_name' => 'J. RIZAL', 'purok_code' => 'JPR', 'street_name' => 'J. RIZAL'],
            ['id' => 2, 'purok_name' => 'Macalintal Compound', 'purok_code' => null, 'street_name' => 'J. RIZAL'],
            ['id' => 3, 'purok_name' => 'Sunny Ridge Residences', 'purok_code' => 'SRR', 'street_name' => 'J. RIZAL'],
            ['id' => 4, 'purok_name' => 'RJP Compound', 'purok_code' => null, 'street_name' => 'J. RIZAL'],
            ['id' => 5, 'purok_name' => 'C. CASTANEDA', 'purok_code' => 'CCA', 'street_name' => 'C. CASTANEDA'],
            ['id' => 6, 'purok_name' => 'DONA BASILISA YANGCO', 'purok_code' => 'DBY', 'street_name' => 'DONA BASILISA YANGCO'],
            ['id' => 7, 'purok_name' => '1ST ST, CASTANEDA', 'purok_code' => '1ST', 'street_name' => '1ST ST, CASTANEDA'],
            ['id' => 8, 'purok_name' => '2ND ST, CASTANEDA', 'purok_code' => '2ND', 'street_name' => '2ND ST, CASTANEDA'],
            ['id' => 9, 'purok_name' => '3RD ST, CASTANEDA', 'purok_code' => '3RD', 'street_name' => '3RD ST, CASTANEDA'],
            ['id' => 10, 'purok_name' => '4TH ST, CASTANEDA', 'purok_code' => '4TH', 'street_name' => '4TH ST, CASTANEDA'],
            ['id' => 11, 'purok_name' => '5TH ST, CASTANEDA', 'purok_code' => '5TH', 'street_name' => '5TH ST, CASTANEDA'],
            ['id' => 12, 'purok_name' => 'GK CASTANEDA', 'purok_code' => 'GKC', 'street_name' => 'GK CASTANEDA'],
            ['id' => 13, 'purok_name' => 'GK MARTHA', 'purok_code' => 'GKM', 'street_name' => 'GK MARTHA'],
            ['id' => 14, 'purok_name' => 'CIRCLE', 'purok_code' => 'CST', 'street_name' => 'CIRCLE'],
            ['id' => 15, 'purok_name' => 'GENEROSO PASCUAL', 'purok_code' => null, 'street_name' => 'GENEROSO PASCUAL'],
            ['id' => 16, 'purok_name' => 'ALLEY 1', 'purok_code' => 'AL1', 'street_name' => 'ALLEY 1'],
            ['id' => 17, 'purok_name' => 'ALLEY 2', 'purok_code' => 'AL2', 'street_name' => 'ALLEY 2'],
            ['id' => 18, 'purok_name' => 'ALLEY 3', 'purok_code' => 'AL3', 'street_name' => 'ALLEY 3'],
            ['id' => 19, 'purok_name' => 'ALLEY 4', 'purok_code' => 'AL4', 'street_name' => 'ALLEY 4'],
            ['id' => 20, 'purok_name' => 'ALLEY 5', 'purok_code' => 'AL5', 'street_name' => 'ALLEY 5'],
            ['id' => 21, 'purok_name' => 'ALLEY 6', 'purok_code' => 'AL6', 'street_name' => 'ALLEY 6'],
            ['id' => 22, 'purok_name' => 'ALLEY 7', 'purok_code' => 'AL7', 'street_name' => 'ALLEY 7'],
            ['id' => 23, 'purok_name' => 'ALLEY 8', 'purok_code' => 'AL8', 'street_name' => 'ALLEY 8'],
            ['id' => 24, 'purok_name' => 'Dreamland Subdivision', 'purok_code' => 'DLS', 'street_name' => 'JUPITER ST.'],
            ['id' => 25, 'purok_name' => 'Dreamland Subdivision', 'purok_code' => 'DLS', 'street_name' => 'MARS ST.'],
        ];

        foreach ($puroks as $purok) {
            Purok::updateOrCreate(['id' => $purok['id']], $purok);
        }
    }
}
