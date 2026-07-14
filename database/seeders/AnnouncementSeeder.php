<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\User;
use Illuminate\Database\Seeder;

class AnnouncementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('email', 'admin@sk.local')->first();

        if (!$admin) {
            return;
        }

        // 1. Success type announcement
        Announcement::updateOrCreate(
            ['title' => 'Silid Karunungan Schedule Active'],
            [
                'body' => 'We are pleased to announce that our educational study center is now accepting scheduling reservations. Book your study spots with high-speed internet access directly online today.',
                'type' => 'success',
                'is_active' => true,
                'published_at' => now(),
                'created_by' => $admin->id,
            ]
        );

        // 2. Info type announcement
        Announcement::updateOrCreate(
            ['title' => 'Pabili Medicine Delivery Expansion'],
            [
                'body' => 'The Barangay SK council has expanded the pabili medicine delivery zones to cover all puroks in Barangay Namayan. Feel free to register medicine requests online.',
                'type' => 'info',
                'is_active' => true,
                'published_at' => now(),
                'created_by' => $admin->id,
            ]
        );

        // 3. Warning type announcement
        Announcement::updateOrCreate(
            ['title' => 'Sports Tournament Age Eligibility'],
            [
                'body' => 'Please note that the upcoming SK Sports Tournament registrations are strictly limited to citizens between 10 and 30 years old. Team compositions will be verified during coordination meetings.',
                'type' => 'warning',
                'is_active' => true,
                'published_at' => now(),
                'created_by' => $admin->id,
            ]
        );
    }
}
