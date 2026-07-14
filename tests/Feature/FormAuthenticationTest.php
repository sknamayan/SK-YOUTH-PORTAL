<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FormAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login_when_accessing_forms(): void
    {
        $routes = [
            '/forms/health-consultation',
            '/forms/mental-health',
            '/forms/pabili-medicine',
            '/forms/silid-karunungan',
            '/forms/sports-registration',
        ];

        foreach ($routes as $route) {
            $response = $this->get($route);
            $response->assertRedirect('/login');
        }
    }

    public function test_authenticated_users_can_access_forms(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        // Seed default Purok for profile creation
        \App\Models\Purok::create([
            'id' => 1,
            'purok_name' => 'J. RIZAL',
            'purok_code' => 'JPR',
            'street_name' => 'J. RIZAL'
        ]);

        $routes = [
            '/forms/health-consultation' => 'health',
            '/forms/mental-health' => 'mental-health',
            '/forms/pabili-medicine' => 'medicine',
            '/forms/silid-karunungan' => 'silid',
        ];

        foreach ($routes as $route => $formName) {
            $response = $this->actingAs($user)->get($route);
            $response->assertRedirect(route('landing', ['form' => $formName]));
        }

        // 1. Without KK profile, it should redirect to /profile/profiling
        $responseNoProfile = $this->actingAs($user)->get('/forms/sports-registration');
        $responseNoProfile->assertRedirect('/profile/profiling');

        // 2. With approved KK profile, it should load successfully (200)
        \App\Models\KkProfile::create([
            'surname' => 'Doe',
            'first_name' => 'Jane',
            'age' => 20,
            'sex' => 'Female',
            'dob' => '2006-05-20',
            'civil_status' => 'Single',
            'purok_id' => 1,
            'youth_classification' => 'ISY',
            'contact_number' => '09171234567',
            'email' => $user->email,
            'registered_sk_voter' => true,
            'registered_national_voter' => false,
            'attended_kk_assembly' => true,
            'part_of_youth_org' => false,
            'interested_in_joining' => true,
            'part_of_lgbtqia' => false,
            'pwd' => false,
            'highest_educational_attainment' => 'High School Student',
            'consent_given' => true,
            'status' => 'approved',
        ]);

        $responseWithProfile = $this->actingAs($user)->get('/forms/sports-registration');
        $responseWithProfile->assertOk();
    }
}
