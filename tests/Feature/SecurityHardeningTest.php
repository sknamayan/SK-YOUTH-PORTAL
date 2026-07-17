<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\Comment;
use App\Models\HealthRequest;
use App\Models\KkProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SecurityHardeningTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test the SecureHeaders middleware injects the correct CSP and security headers.
     */
    public function test_secure_headers_middleware_adds_csp_and_frame_protection()
    {
        $response = $this->get('/');

        $response->assertHeader('Content-Security-Policy');
        $response->assertHeader('X-Frame-Options', 'DENY');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
    }

    /**
     * Test the PreventIdor middleware blocks unauthorized request access.
     */
    public function test_prevent_idor_middleware_blocks_unauthorized_citizens()
    {
        $citizenA = User::factory()->create([
            'email' => 'citizen.a@example.com',
            'role' => 'citizen',
        ]);

        $citizenB = User::factory()->create([
            'email' => 'citizen.b@example.com',
            'role' => 'citizen',
        ]);

        // Create a request owned by Citizen B
        $requestRecord = HealthRequest::create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'age' => 20,
            'gender' => 'Male',
            'email' => 'citizen.b@example.com',
            'contact_number' => '09123456789',
            'concerns' => 'Fever',
            'preferred_date' => now()->addDays(2),
            'preferred_time' => '10:00 AM',
            'status' => 'pending',
        ]);

        // Accessing B's request while logged in as A should return 403 Forbidden
        $response = $this->actingAs($citizenA)
            ->get(route('track.edit', ['type' => 'health', 'id' => $requestRecord->id]));

        $response->assertStatus(403);

        // Verify that an IDOR anomaly activity log was written
        $logExists = ActivityLog::where('action', 'security_idor_blocked')
            ->where('subject_type', HealthRequest::class)
            ->where('subject_id', $requestRecord->id)
            ->exists();

        $this->assertTrue($logExists);
    }

    /**
     * Test database encrypted casts for KK profiles and requests.
     */
    public function test_pii_fields_are_encrypted_at_rest()
    {
        $purokId = DB::table('puroks')->insertGetId([
            'purok_name' => 'Purok 1',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $profile = KkProfile::create([
            'surname' => 'Rizal',
            'first_name' => 'Jose',
            'middle_name' => 'Mercado', // encrypted
            'ext' => null,
            'age' => 35,
            'sex' => 'Male',
            'gender' => 'Male',
            'dob' => '1861-06-19',
            'civil_status' => 'Single',
            'purok_id' => $purokId,
            'street_address' => 'Calamba Street', // encrypted
            'youth_classification' => 'Student',
            'contact_number' => '09998887777', // encrypted
            'email' => 'jose@example.com',
            'registered_sk_voter' => true,
            'registered_national_voter' => true,
            'attended_kk_assembly' => true,
            'part_of_youth_org' => false,
            'interested_in_joining' => false,
            'part_of_lgbtqia' => false,
            'pwd' => false,
            'registered_disability' => null,
            'highest_educational_attainment' => 'College Graduate',
            'consent_given' => true,
            'status' => 'approved',
        ]);

        // Fetch raw database value using DB query to verify it is encrypted
        $rawRecord = DB::table('kk_profiles')->where('id', $profile->id)->first();

        // The raw value should not contain the plain text string
        $this->assertNotEquals('Mercado', $rawRecord->middle_name);
        $this->assertNotEquals('Calamba Street', $rawRecord->street_address);
        $this->assertNotEquals('09998887777', $rawRecord->contact_number);

        // Accessing via Eloquent model decrypts automatically
        $decryptedProfile = KkProfile::withoutGlobalScopes()->find($profile->id);
        $this->assertEquals('Mercado', $decryptedProfile->middle_name);
        $this->assertEquals('Calamba Street', $decryptedProfile->street_address);
        $this->assertEquals('09998887777', $decryptedProfile->contact_number);
    }

    /**
     * Test secure download controller requires authorization.
     */
    public function test_secure_attachments_require_auth_and_ownership()
    {
        Storage::fake('local');
        Storage::disk('local')->put('comment-attachments/attach.pdf', 'confidential data');

        $citizenA = User::factory()->create([
            'email' => 'citizen.a@example.com',
            'role' => 'citizen',
        ]);

        // Attempting to download unauthenticated should redirect to login
        $response = $this->get('/attachments/comment-attachments/attach.pdf');
        $response->assertRedirect('/login');

        // Accessing authenticated as Citizen A (who does not own it) should return 403 Forbidden
        $response = $this->actingAs($citizenA)
            ->get('/attachments/comment-attachments/attach.pdf');

        $response->assertStatus(403);
    }
}
