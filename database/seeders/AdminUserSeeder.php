<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Super Admin
        User::updateOrCreate(
            ['email' => 'superadmin@sk.local'],
            [
                'name' => 'SK Super Admin',
                'password' => Hash::make('Superadmin@12345!'),
                'role' => 'superadmin',
                'is_approved' => true,
            ]
        );

        // Admin
        User::updateOrCreate(
            ['email' => 'admin@sk.local'],
            [
                'name' => 'SK Admin User',
                'password' => Hash::make('Admin@12345!'),
                'role' => 'admin',
                'is_approved' => true,
            ]
        );

        // Staff
        User::updateOrCreate(
            ['email' => 'staff@sk.local'],
            [
                'name' => 'SK Staff Member',
                'password' => Hash::make('Staff@12345!'),
                'role' => 'staff',
                'is_approved' => true,
            ]
        );
    }
}
