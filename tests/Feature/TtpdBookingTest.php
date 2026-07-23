<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Purok;
use App\Models\KkProfile;
use App\Models\SilidKarununganRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TtpdBookingTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create(['role' => 'user']);
        
        Purok::create([
            'id' => 1,
            'purok_name' => 'J. RIZAL',
            'purok_code' => 'JPR',
            'street_name' => 'J. RIZAL'
        ]);

        KkProfile::create([
            'surname' => 'Doe',
            'first_name' => 'Jane',
            'age' => 20,
            'sex' => 'Female',
            'dob' => '2006-05-20',
            'civil_status' => 'Single',
            'purok_id' => 1,
            'youth_classification' => 'ISY',
            'contact_number' => '09171234567',
            'email' => $this->user->email,
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
    }

    public function test_booking_fails_outside_operational_hours(): void
    {
        $response = $this->actingAs($this->user)->post('/forms/silid-karunungan', [
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'middle_name' => 'None',
            'age' => 20,
            'email' => $this->user->email,
            'contact_number' => '09171234567',
            'preferred_date' => date('Y-m-d', strtotime('+1 day')),
            'preferred_time' => '09:00 AM', // 9:00 AM (too early)
        ]);

        $response->assertSessionHasErrors(['preferred_time']);
    }

    public function test_booking_succeeds_inside_operational_hours(): void
    {
        $response = $this->actingAs($this->user)->post('/forms/silid-karunungan', [
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'middle_name' => 'None',
            'age' => 20,
            'email' => $this->user->email,
            'contact_number' => '09171234567',
            'preferred_date' => date('Y-m-d', strtotime('+1 day')),
            'preferred_time' => '12:00 PM', // 12:00 PM (valid)
        ]);

        $response->assertSessionHasNoErrors();
    }

    public function test_booking_fails_on_double_booking_slot(): void
    {
        $date = date('Y-m-d', strtotime('+2 days'));
        $time = '02:00 PM';

        // Pre-create a booked request
        SilidKarununganRequest::create([
            'user_id' => $this->user->id,
            'reference_number' => 'REF-12345',
            'requestor_first_name' => 'Other',
            'requestor_last_name' => 'Person',
            'requestor_middle_name' => 'None',
            'requestor_age' => 22,
            'email' => 'other@local.com',
            'contact_number' => '09170000000',
            'preferred_date' => $date,
            'preferred_time' => $time,
            'status' => 'approved',
        ]);

        // Attempt to book same slot
        $response = $this->actingAs($this->user)->post('/forms/silid-karunungan', [
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'middle_name' => 'None',
            'age' => 20,
            'email' => $this->user->email,
            'contact_number' => '09171234567',
            'preferred_date' => $date,
            'preferred_time' => $time,
        ]);

        $response->assertSessionHasErrors(['preferred_time']);
    }
}
