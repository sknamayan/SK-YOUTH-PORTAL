<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', [
            'first_name' => 'Test',
            'last_name' => 'User',
            'birthdate' => '2000-01-01',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);
 
        $this->assertAuthenticated();
        $response->assertRedirect('/');
    }
 
    public function test_registration_links_existing_profile(): void
    {
        // Create Purok and pre-encoded KkProfile record
        $purok = \App\Models\Purok::create(['purok_name' => 'Purok 1']);
        $profile = \App\Models\KkProfile::create([
            'first_name' => 'Jane',
            'surname' => 'Doe', // surname matches last_name
            'dob' => '2005-05-15',
            'email' => 'jane.doe@example.com',
            'age' => 21,
            'sex' => 'Female',
            'civil_status' => 'Single',
            'purok_id' => $purok->id,
            'youth_classification' => 'ISY',
            'contact_number' => '09123456789',
            'registered_sk_voter' => true,
            'registered_national_voter' => true,
            'attended_kk_assembly' => false,
            'part_of_youth_org' => false,
            'interested_in_joining' => true,
            'part_of_lgbtqia' => false,
            'pwd' => false,
            'highest_educational_attainment' => 'High School',
            'consent_given' => true,
            'status' => 'pending',
            'user_id' => null,
        ]);
 
        // Perform registration with matching name and birthdate (case-insensitive)
        $response = $this->post('/register', [
            'first_name' => 'JANE',
            'last_name' => 'DOE',
            'birthdate' => '2005-05-15',
            'email' => 'jane.doe@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);
 
        $this->assertAuthenticated();
        $user = auth()->user();
 
        // Assert the profile was linked and approved
        $profile->refresh();
        $this->assertEquals($user->id, $profile->user_id);
        $this->assertEquals('approved', $profile->status);
    }
}
