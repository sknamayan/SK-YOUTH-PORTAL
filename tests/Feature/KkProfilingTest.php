<?php

namespace Tests\Feature;

use App\Models\KkProfile;
use App\Models\Purok;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KkProfilingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Seed default Purok
        Purok::create([
            'id' => 1,
            'purok_name' => 'J. RIZAL',
            'purok_code' => 'JPR',
            'street_name' => 'J. RIZAL'
        ]);
    }

    public function test_guest_cannot_access_profiling_dashboard(): void
    {
        $response = $this->get('/dashboard/profiling');
        $response->assertRedirect('/login');
    }

    public function test_staff_and_admin_can_access_profiling_dashboard(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_approved' => true]);
        $staff = User::factory()->create(['role' => 'staff', 'is_approved' => true]);

        $response1 = $this->actingAs($admin)->get('/dashboard/profiling');
        $response1->assertOk();
        $response1->assertSee('Youth Profiling Registry');

        $response2 = $this->actingAs($staff)->get('/dashboard/profiling');
        $response2->assertOk();
    }

    public function test_profiling_filters_and_search_work_correctly(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_approved' => true]);

        // Create secondary purok
        $purok2 = Purok::create([
            'purok_name' => 'Sunny Ridge Residences',
            'purok_code' => 'SRR',
            'street_name' => 'J. RIZAL'
        ]);

        // Create profile 1 (ISY, J. Rizal)
        KkProfile::create([
            'surname' => 'DelaCruzUnique',
            'first_name' => 'Juan',
            'middle_name' => 'Santiago',
            'ext' => null,
            'age' => 18,
            'sex' => 'Male',
            'gender' => 'Male',
            'dob' => '2008-01-20',
            'civil_status' => 'Single',
            'purok_id' => 1,
            'street_address' => '594 J.P Rizal Street',
            'youth_classification' => 'ISY',
            'contact_number' => '09171234567',
            'email' => 'juan@example.com',
            'registered_sk_voter' => true,
            'registered_national_voter' => false,
            'attended_kk_assembly' => true,
            'part_of_youth_org' => false,
            'interested_in_joining' => true,
            'part_of_lgbtqia' => false,
            'pwd' => false,
            'highest_educational_attainment' => 'High School Student'
        ]);

        // Create profile 2 (OSY, Sunny Ridge)
        KkProfile::create([
            'surname' => 'Santos',
            'first_name' => 'Maria',
            'middle_name' => null,
            'ext' => null,
            'age' => 22,
            'sex' => 'Female',
            'gender' => 'Female',
            'dob' => '2004-05-15',
            'civil_status' => 'Single',
            'purok_id' => $purok2->id,
            'street_address' => 'Sunny Ridge Unit 10A',
            'youth_classification' => 'OSY',
            'contact_number' => '09187654321',
            'email' => 'maria@example.com',
            'registered_sk_voter' => false,
            'registered_national_voter' => true,
            'attended_kk_assembly' => false,
            'part_of_youth_org' => true,
            'youth_org_name' => 'Local Club',
            'interested_in_joining' => false,
            'part_of_lgbtqia' => false,
            'pwd' => false,
            'highest_educational_attainment' => 'High School Graduate'
        ]);

        // Search Test
        $responseSearch = $this->actingAs($admin)->get('/dashboard/profiling?search=Juan');
        $responseSearch->assertSee('DelaCruzUnique');
        $responseSearch->assertDontSee('Santos');

        // Purok Filter Test
        $responsePurok = $this->actingAs($admin)->get('/dashboard/profiling?purok=' . $purok2->id);
        $responsePurok->assertSee('Santos');
        $responsePurok->assertDontSee('DelaCruzUnique');

        // Classification Filter Test
        $responseClass = $this->actingAs($admin)->get('/dashboard/profiling?classification=ISY');
        $responseClass->assertSee('DelaCruzUnique');
        $responseClass->assertDontSee('Santos');

        // Year Filter Test
        $currentYear = date('Y');
        $responseYear = $this->actingAs($admin)->get("/dashboard/profiling?year={$currentYear}");
        $responseYear->assertSee('DelaCruzUnique');
        $responseYear->assertSee('Santos');

        // Sex Filter Test
        $responseSex = $this->actingAs($admin)->get('/dashboard/profiling?sex=Female');
        $responseSex->assertSee('Santos');
        $responseSex->assertDontSee('DelaCruzUnique');

        // Page Size Limit Test
        $responseLimit = $this->actingAs($admin)->get('/dashboard/profiling?limit=10');
        $responseLimit->assertOk();
    }

    public function test_storing_profile_validates_required_fields(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_approved' => true]);

        $response = $this->actingAs($admin)->post('/dashboard/profiling', []);
        $response->assertSessionHasErrors([
            'surname', 'first_name', 'age', 'sex', 'dob', 'civil_status', 
            'purok_id', 'youth_classification', 'contact_number', 'email',
            'registered_sk_voter', 'registered_national_voter', 'attended_kk_assembly',
            'part_of_youth_org', 'interested_in_joining', 'part_of_lgbtqia', 
            'pwd', 'highest_educational_attainment', 'consent_given'
        ]);
    }

    public function test_storing_profile_succeeds_and_creates_audit_log(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_approved' => true]);

        $payload = [
            'surname' => 'Reyes',
            'first_name' => 'Jose',
            'middle_name' => 'Cruz',
            'ext' => 'Jr.',
            'age' => 19,
            'sex' => 'Male',
            'gender' => 'Male',
            'dob' => '2007-11-12',
            'civil_status' => 'Single',
            'purok_id' => 1,
            'street_address' => '123 Rizal St',
            'youth_classification' => 'WY',
            'contact_number' => '09191112222',
            'email' => 'jose@example.com',
            
            'registered_sk_voter' => 1,
            'registered_national_voter' => 1,
            'attended_kk_assembly' => 0,
            'part_of_youth_org' => 1,
            'youth_org_name' => 'Namayan Basketball Association',
            'interested_in_joining' => 0,
            
            'part_of_lgbtqia' => 0,
            'pwd' => 0,
            'highest_educational_attainment' => '1st year College',
            'consent_given' => 1,
        ];

        $response = $this->actingAs($admin)->post('/dashboard/profiling', $payload);
        $response->assertRedirect('/dashboard/profiling');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('kk_profiles', [
            'surname' => 'REYES',
            'first_name' => 'JOSE',
            'email' => 'jose@example.com',
            'purok_id' => 1,
            'processed_by' => $admin->id,
        ]);

        // Assert Activity Log was recorded
        $this->assertDatabaseHas('activity_logs', [
            'action' => 'kk_profile_created',
            'subject_type' => KkProfile::class,
        ]);
    }

    public function test_dashboard_shows_kk_profiling_charts_data(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_approved' => true]);

        // Create a few profiles with different classifications
        KkProfile::create([
            'surname' => 'A', 'first_name' => 'B', 'age' => 18, 'sex' => 'Male',
            'dob' => '2008-01-20', 'civil_status' => 'Single', 'purok_id' => 1,
            'youth_classification' => 'ISY', 'contact_number' => '09171234567', 'email' => 'a@example.com',
            'registered_sk_voter' => true, 'registered_national_voter' => false, 'attended_kk_assembly' => true,
            'part_of_youth_org' => false, 'interested_in_joining' => true, 'part_of_lgbtqia' => false, 'pwd' => false,
            'highest_educational_attainment' => 'High School'
        ]);

        KkProfile::create([
            'surname' => 'C', 'first_name' => 'D', 'age' => 22, 'sex' => 'Female',
            'dob' => '2004-05-15', 'civil_status' => 'Single', 'purok_id' => 1,
            'youth_classification' => 'OSY', 'contact_number' => '09187654321', 'email' => 'c@example.com',
            'registered_sk_voter' => false, 'registered_national_voter' => true, 'attended_kk_assembly' => false,
            'part_of_youth_org' => true, 'interested_in_joining' => false, 'part_of_lgbtqia' => false, 'pwd' => false,
            'highest_educational_attainment' => 'College'
        ]);

        $response = $this->actingAs($admin)->get('/dashboard');
        $response->assertOk();
        $response->assertViewHas('totalYouth', 2);
        $response->assertViewHas('totalIsy', 1);
        $response->assertViewHas('totalOsy', 1);
        $response->assertViewHas('totalWy', 0);
        $response->assertViewHas('totalSkVoters', 1);
        $response->assertViewHas('chartData');
        $response->assertViewHas('classificationDistribution');

        $chartData = $response->viewData('chartData');
        $classificationDistribution = $response->viewData('classificationDistribution');

        $this->assertCount(1, $chartData);
        $this->assertEquals('J. RIZAL', $chartData[0]['purok']);
        $this->assertEquals(2, $chartData[0]['count']);

        $this->assertEquals(1, $classificationDistribution['isy']);
        $this->assertEquals(1, $classificationDistribution['osy']);
        $this->assertEquals(0, $classificationDistribution['wy']);

        $response->assertViewHas('accomplishedByProgram');
        $response->assertViewHas('accomplishmentTrends');

        $accomplishedByProgram = $response->viewData('accomplishedByProgram');
        $accomplishmentTrends = $response->viewData('accomplishmentTrends');

        $this->assertEquals(0, $accomplishedByProgram['health']);
        $this->assertEquals(0, $accomplishedByProgram['medicine']);
        $this->assertEquals(0, $accomplishedByProgram['silid']);
        $this->assertEquals(0, $accomplishedByProgram['sports']);

        $this->assertCount(6, $accomplishmentTrends);
        $this->assertEquals(now()->subMonths(5)->format('M Y'), $accomplishmentTrends[0]['label']);
        $this->assertEquals(0, $accomplishmentTrends[0]['count']);
        $this->assertEquals(now()->format('M Y'), $accomplishmentTrends[5]['label']);
        $this->assertEquals(0, $accomplishmentTrends[5]['count']);
    }

    public function test_citizen_can_self_profile_and_tags_processed_by(): void
    {
        $citizen = User::factory()->create(['role' => 'user', 'is_approved' => true]);

        $payload = [
            'surname' => 'Reyes',
            'first_name' => 'Jose',
            'middle_name' => 'Cruz',
            'ext' => 'Jr.',
            'age' => 19,
            'sex' => 'Male',
            'gender' => 'Male',
            'dob' => '2007-11-12',
            'civil_status' => 'Single',
            'purok_id' => 1,
            'street_address' => '123 Rizal St',
            'youth_classification' => 'WY',
            'contact_number' => '09191112222',
            'email' => 'jose@example.com',

            'registered_sk_voter' => 1,
            'registered_national_voter' => 1,
            'attended_kk_assembly' => 0,
            'part_of_youth_org' => 1,
            'youth_org_name' => 'Basketball Association',
            'interested_in_joining' => 0,

            'part_of_lgbtqia' => 0,
            'pwd' => 0,
            'highest_educational_attainment' => '1st year College',
            'consent_given' => 1,
        ];

        // Citizens can access the self-profiling create screen
        $responseGet = $this->actingAs($citizen)->get('/profile/profiling');
        $responseGet->assertOk();
        $responseGet->assertSee('Katipunan ng Kabataan Self Profiling');

        // Citizens can submit their profile details
        $responsePost = $this->actingAs($citizen)->post('/profile/profiling', $payload);
        $responsePost->assertRedirect('/profile/my-requests');
        $responsePost->assertSessionHas('success');

        // Verify database entry has citizen's own email and is processed_by the citizen
        $this->assertDatabaseHas('kk_profiles', [
            'surname' => 'REYES',
            'first_name' => 'JOSE',
            'email' => $citizen->email,
            'processed_by' => $citizen->id,
        ]);
    }

    public function test_admin_can_export_profiling_data_to_csv(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_approved' => true]);

        KkProfile::create([
            'surname' => 'Garcia',
            'first_name' => 'Maria',
            'middle_name' => 'Santos',
            'ext' => null,
            'age' => 20,
            'sex' => 'Female',
            'gender' => 'Female',
            'dob' => '2006-03-15',
            'civil_status' => 'Single',
            'purok_id' => 1,
            'street_address' => '456 Main St',
            'youth_classification' => 'ISY',
            'contact_number' => '09177654321',
            'email' => 'maria.garcia@example.com',
            'registered_sk_voter' => true,
            'registered_national_voter' => true,
            'attended_kk_assembly' => true,
            'part_of_youth_org' => false,
            'interested_in_joining' => false,
            'part_of_lgbtqia' => false,
            'pwd' => false,
            'highest_educational_attainment' => 'College Student',
            'consent_given' => true,
            'processed_by' => $admin->id,
        ]);

        $response = $this->actingAs($admin)->get('/dashboard/export/profiling');

        $response->assertOk();
        $this->assertStringContainsString('attachment; filename="export_profiling_', $response->headers->get('Content-Disposition'));

        $content = $response->streamedContent();
        $this->assertStringContainsString('Surname', $content);
        $this->assertStringContainsString('First Name', $content);
        $this->assertStringContainsString('Garcia', $content);
        $this->assertStringNotContainsString('********', $content);
        $this->assertStringNotContainsString('maria.garcia@example.com', $content);
    }

    public function test_admin_or_staff_can_update_profile_and_creates_audit_log(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_approved' => true]);

        $profile = KkProfile::create([
            'surname' => 'Dela Cruz',
            'first_name' => 'Juan',
            'age' => 18,
            'sex' => 'Male',
            'dob' => '2008-01-20',
            'civil_status' => 'Single',
            'purok_id' => 1,
            'youth_classification' => 'ISY',
            'contact_number' => '09171234567',
            'email' => 'juan@example.com',
            'registered_sk_voter' => true,
            'registered_national_voter' => false,
            'attended_kk_assembly' => true,
            'part_of_youth_org' => false,
            'interested_in_joining' => true,
            'part_of_lgbtqia' => false,
            'pwd' => false,
            'highest_educational_attainment' => 'High School Student',
            'consent_given' => true,
            'processed_by' => $admin->id,
        ]);

        $payload = [
            'surname' => 'Dela Cruz Updated',
            'first_name' => 'Juan',
            'age' => 19,
            'sex' => 'Male',
            'dob' => '2007-01-20',
            'civil_status' => 'Single',
            'purok_id' => 1,
            'youth_classification' => 'ISY',
            'contact_number' => '09171234567',
            'email' => 'juan@example.com',
            'registered_sk_voter' => 1,
            'registered_national_voter' => 0,
            'attended_kk_assembly' => 1,
            'part_of_youth_org' => 0,
            'interested_in_joining' => 1,
            'part_of_lgbtqia' => 0,
            'pwd' => 0,
            'highest_educational_attainment' => 'College Student',
            'consent_given' => 1,
        ];

        $response = $this->actingAs($admin)->put("/dashboard/profiling/{$profile->id}", $payload);
        $response->assertRedirect('/dashboard/profiling');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('kk_profiles', [
            'id' => $profile->id,
            'surname' => 'DELA CRUZ UPDATED',
            'age' => 19,
            'highest_educational_attainment' => 'COLLEGE STUDENT',
            'processed_by' => $admin->id,
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'action' => 'kk_profile_updated',
            'subject_type' => KkProfile::class,
            'subject_id' => $profile->id,
        ]);
    }

    public function test_admin_or_staff_can_delete_profile_and_creates_audit_log(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_approved' => true]);

        $profile = KkProfile::create([
            'surname' => 'Dela Cruz',
            'first_name' => 'Juan',
            'age' => 18,
            'sex' => 'Male',
            'dob' => '2008-01-20',
            'civil_status' => 'Single',
            'purok_id' => 1,
            'youth_classification' => 'ISY',
            'contact_number' => '09171234567',
            'email' => 'juan@example.com',
            'registered_sk_voter' => true,
            'registered_national_voter' => false,
            'attended_kk_assembly' => true,
            'part_of_youth_org' => false,
            'interested_in_joining' => true,
            'part_of_lgbtqia' => false,
            'pwd' => false,
            'highest_educational_attainment' => 'High School Student',
            'consent_given' => true,
            'processed_by' => $admin->id,
        ]);

        $response = $this->actingAs($admin)->delete("/dashboard/profiling/{$profile->id}");
        $response->assertRedirect('/dashboard/profiling');
        $response->assertSessionHas('success');

        $this->assertSoftDeleted('kk_profiles', [
            'id' => $profile->id,
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'action' => 'kk_profile_deleted',
            'subject_type' => KkProfile::class,
            'subject_id' => $profile->id,
        ]);
    }

    public function test_superadmin_can_view_raw_profiling_pii_and_export_pii(): void
    {
        $superadmin = User::factory()->create(['role' => 'superadmin', 'is_approved' => true]);

        $profile = KkProfile::create([
            'surname' => 'Dela Cruz',
            'first_name' => 'Juan',
            'age' => 18,
            'sex' => 'Male',
            'dob' => '2008-01-20',
            'civil_status' => 'Single',
            'purok_id' => 1,
            'youth_classification' => 'ISY',
            'contact_number' => '09171234567',
            'email' => 'juan@example.com',
            'registered_sk_voter' => true,
            'registered_national_voter' => false,
            'attended_kk_assembly' => true,
            'part_of_youth_org' => false,
            'interested_in_joining' => true,
            'part_of_lgbtqia' => false,
            'pwd' => true,
            'registered_disability' => 'Visual Impairment',
            'highest_educational_attainment' => 'High School Student',
            'consent_given' => true,
        ]);

        // Dashboard List View
        $response = $this->actingAs($superadmin)->get('/dashboard/profiling');
        $response->assertOk();
        $response->assertSee('2008-01-20');
        $response->assertSee('09171234567');
        $response->assertSee('juan@example.com');
        $response->assertSee('Visual Impairment');

        // CSV Export
        $exportResponse = $this->actingAs($superadmin)->get('/dashboard/export/profiling');
        $exportResponse->assertOk();
        $exportContent = $exportResponse->streamedContent();
        $this->assertStringContainsString('2008-01-20', $exportContent);
        $this->assertStringContainsString('09171234567', $exportContent);
        $this->assertStringContainsString('juan@example.com', $exportContent);
        $this->assertStringContainsString('Visual Impairment', $exportContent);
    }

    public function test_admin_and_staff_view_masked_profiling_pii_and_export_masked_pii(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_approved' => true]);
        $staff = User::factory()->create(['role' => 'staff', 'is_approved' => true]);

        $profile = KkProfile::create([
            'surname' => 'Dela Cruz',
            'first_name' => 'Juan',
            'age' => 18,
            'sex' => 'Male',
            'dob' => '2008-11-22',
            'civil_status' => 'Single',
            'purok_id' => 1,
            'youth_classification' => 'ISY',
            'contact_number' => '09998887777',
            'email' => 'secret_citizen@example.com',
            'registered_sk_voter' => true,
            'registered_national_voter' => false,
            'attended_kk_assembly' => true,
            'part_of_youth_org' => false,
            'interested_in_joining' => true,
            'part_of_lgbtqia' => false,
            'pwd' => true,
            'registered_disability' => 'SpecificSecretDisability',
            'highest_educational_attainment' => 'High School Student',
            'consent_given' => true,
        ]);

        // Dashboard List View for Admin
        $response = $this->actingAs($admin)->get('/dashboard/profiling');
        $response->assertOk();
        $response->assertDontSee('2008-11-22');
        $response->assertDontSee('09998887777');
        $response->assertDontSee('secret_citizen@example.com');
        $response->assertDontSee('SpecificSecretDisability');
        $response->assertDontSee('********');
        $response->assertSee('-');

        // CSV Export for Staff
        $exportResponse = $this->actingAs($staff)->get('/dashboard/export/profiling');
        $exportResponse->assertOk();
        $exportContent = $exportResponse->streamedContent();
        $this->assertStringNotContainsString('2008-11-22', $exportContent);
        $this->assertStringNotContainsString('09998887777', $exportContent);
        $this->assertStringNotContainsString('secret_citizen@example.com', $exportContent);
        $this->assertStringNotContainsString('SpecificSecretDisability', $exportContent);
        $this->assertStringNotContainsString('********', $exportContent);
    }

    public function test_admin_or_staff_can_update_profile_without_corrupting_masked_pii(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_approved' => true]);

        $profile = KkProfile::create([
            'surname' => 'Dela Cruz',
            'first_name' => 'Juan',
            'age' => 18,
            'sex' => 'Male',
            'dob' => '2008-01-20',
            'civil_status' => 'Single',
            'purok_id' => 1,
            'youth_classification' => 'ISY',
            'contact_number' => '09171234567',
            'email' => 'juan@example.com',
            'registered_sk_voter' => true,
            'registered_national_voter' => false,
            'attended_kk_assembly' => true,
            'part_of_youth_org' => false,
            'interested_in_joining' => true,
            'part_of_lgbtqia' => false,
            'pwd' => true,
            'registered_disability' => 'Visual Impairment',
            'highest_educational_attainment' => 'High School Student',
            'consent_given' => true,
        ]);

        // Submit update payload with empty values (just like the frontend edit form sends now)
        $payload = [
            'surname' => 'Dela Cruz',
            'first_name' => 'Juan Updated',
            'age' => 18,
            'sex' => 'Male',
            'dob' => '', // Empty/Masked Date of Birth
            'civil_status' => 'Married', // Updated field
            'purok_id' => 1,
            'youth_classification' => 'WY', // Updated field
            'contact_number' => '', // Empty/Masked Contact
            'email' => '', // Empty/Masked Email
            'registered_sk_voter' => 1,
            'registered_national_voter' => 0,
            'attended_kk_assembly' => 1,
            'part_of_youth_org' => 0,
            'interested_in_joining' => 1,
            'part_of_lgbtqia' => 0,
            'pwd' => '', // Empty/Masked PWD
            'registered_disability' => '', // Empty/Masked Disability
            'highest_educational_attainment' => 'High School Student',
            'consent_given' => 1,
        ];

        $response = $this->actingAs($admin)->put("/dashboard/profiling/{$profile->id}", $payload);
        $response->assertRedirect('/dashboard/profiling');

        // Confirm database values are NOT corrupted/overwritten by empty request values
        $updatedProfile = KkProfile::findOrFail($profile->id);
        $this->assertEquals('JUAN UPDATED', $updatedProfile->first_name);
        $this->assertEquals('Married', $updatedProfile->civil_status);
        $this->assertEquals('WY', $updatedProfile->youth_classification);
        $this->assertEquals('2008-01-20', $updatedProfile->dob->format('Y-m-d'));
        $this->assertEquals('09171234567', $updatedProfile->contact_number);
        $this->assertEquals('juan@example.com', $updatedProfile->email);
        $this->assertTrue($updatedProfile->pwd);
        $this->assertEquals('VISUAL IMPAIRMENT', $updatedProfile->registered_disability);
    }

    public function test_registration_requires_first_and_last_name_and_combines_them(): void
    {
        // 1. Submit incomplete registration
        $response1 = $this->post('/register', [
            'email' => 'newuser@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);
        $response1->assertSessionHasErrors(['first_name', 'last_name', 'birthdate']);

        // 2. Submit complete registration
        $response2 = $this->post('/register', [
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'birthdate' => '2005-05-15',
            'email' => 'newuser@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response2->assertRedirect('/');
        $this->assertDatabaseHas('users', [
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'name' => 'Jane Doe',
            'email' => 'newuser@example.com',
        ]);
    }

    public function test_non_profiled_user_cannot_access_forms_and_gets_redirected(): void
    {
        $citizen = User::factory()->create(['role' => 'user', 'is_approved' => true]);

        // Accessing sports registration should redirect to profile creation
        $response = $this->actingAs($citizen)->get('/forms/sports-registration');
        $response->assertRedirect('/profile/profiling');
        $response->assertSessionHas('error');
    }

    public function test_profiled_user_can_access_forms_successfully(): void
    {
        $citizen = User::factory()->create(['role' => 'user', 'is_approved' => true]);

        // Create KK profile matching citizen email
        KkProfile::create([
            'surname' => 'Doe',
            'first_name' => 'John',
            'age' => 20,
            'sex' => 'Male',
            'dob' => '2006-05-20',
            'civil_status' => 'Single',
            'purok_id' => 1,
            'youth_classification' => 'ISY',
            'contact_number' => '09171234567',
            'email' => $citizen->email,
            'registered_sk_voter' => true,
            'registered_national_voter' => false,
            'attended_kk_assembly' => true,
            'part_of_youth_org' => false,
            'interested_in_joining' => true,
            'part_of_lgbtqia' => false,
            'pwd' => false,
            'highest_educational_attainment' => 'High School Student',
            'consent_given' => true,
        ]);

        // Accessing sports registration should load successfully (loads the dedicated page successfully)
        $response = $this->actingAs($citizen)->get('/forms/sports-registration');
        $response->assertOk();
    }

    public function test_citizen_self_profiling_creates_pending_profile_and_cannot_access_forms_until_approved(): void
    {
        $citizen = User::factory()->create(['role' => 'user', 'is_approved' => true]);
        $admin = User::factory()->create(['role' => 'admin', 'is_approved' => true]);

        // Submit self profiling
        $this->actingAs($citizen)->post('/profile/profiling', [
            'surname' => 'Doe',
            'first_name' => 'John',
            'age' => 20,
            'sex' => 'Male',
            'dob' => '2006-05-20',
            'civil_status' => 'Single',
            'purok_id' => 1,
            'youth_classification' => 'ISY',
            'contact_number' => '09171234567',
            'email' => $citizen->email,
            'registered_sk_voter' => true,
            'registered_national_voter' => false,
            'attended_kk_assembly' => true,
            'part_of_youth_org' => false,
            'interested_in_joining' => true,
            'part_of_lgbtqia' => false,
            'pwd' => false,
            'highest_educational_attainment' => 'High School Student',
            'consent_given' => true,
        ]);

        // Verify status is pending
        $profile = KkProfile::where('email', $citizen->email)->first();
        $this->assertNotNull($profile);
        $this->assertEquals('pending', $profile->status);

        // Cannot access sports registration (redirected back to my-requests with warning)
        $response = $this->actingAs($citizen)->get('/forms/sports-registration');
        $response->assertRedirect('/profile/my-requests');
        $response->assertSessionHas('error', 'Your KK Profiling registry is currently pending review by our desk officers. All services will be unlocked once approved.');

        // Approve profile as admin
        $responseApprove = $this->actingAs($admin)->patch("/dashboard/profiling/{$profile->id}/approve");
        $responseApprove->assertRedirect();
        
        $profile->refresh();
        $this->assertEquals('approved', $profile->status);

        // Citizen can now access forms successfully
        $responseSuccess = $this->actingAs($citizen)->get('/forms/sports-registration');
        $responseSuccess->assertOk();
    }

    public function test_citizen_can_resubmit_profiling_if_declined(): void
    {
        $citizen = User::factory()->create(['role' => 'user', 'is_approved' => true]);
        $admin = User::factory()->create(['role' => 'admin', 'is_approved' => true]);

        // Create initial pending profile
        $profile = KkProfile::create([
            'surname' => 'Doe',
            'first_name' => 'John',
            'age' => 20,
            'sex' => 'Male',
            'dob' => '2006-05-20',
            'civil_status' => 'Single',
            'purok_id' => 1,
            'youth_classification' => 'ISY',
            'contact_number' => '09171234567',
            'email' => $citizen->email,
            'registered_sk_voter' => true,
            'registered_national_voter' => false,
            'attended_kk_assembly' => true,
            'part_of_youth_org' => false,
            'interested_in_joining' => true,
            'part_of_lgbtqia' => false,
            'pwd' => false,
            'highest_educational_attainment' => 'High School Student',
            'consent_given' => true,
            'status' => 'pending',
        ]);

        // Admin declines the profile
        $responseDecline = $this->actingAs($admin)->patch("/dashboard/profiling/{$profile->id}/decline");
        $responseDecline->assertRedirect();

        $profile->refresh();
        $this->assertEquals('declined', $profile->status);

        // Citizen tries to access forms - blocked with declined warning
        $responseBlock = $this->actingAs($citizen)->get('/forms/sports-registration');
        $responseBlock->assertRedirect('/profile/my-requests');
        $responseBlock->assertSessionHas('error', 'Your KK Profiling registry has been declined. Please re-submit your profiling details.');

        // Citizen can view self-profiling form again
        $responseForm = $this->actingAs($citizen)->get('/profile/profiling');
        $responseForm->assertOk();

        // Citizen resubmits
        $responseResubmit = $this->actingAs($citizen)->post('/profile/profiling', [
            'surname' => 'Doe-Updated',
            'first_name' => 'John',
            'age' => 20,
            'sex' => 'Male',
            'dob' => '2006-05-20',
            'civil_status' => 'Single',
            'purok_id' => 1,
            'youth_classification' => 'ISY',
            'contact_number' => '09171234567',
            'email' => $citizen->email,
            'registered_sk_voter' => true,
            'registered_national_voter' => false,
            'attended_kk_assembly' => true,
            'part_of_youth_org' => false,
            'interested_in_joining' => true,
            'part_of_lgbtqia' => false,
            'pwd' => false,
            'highest_educational_attainment' => 'High School Student',
            'consent_given' => true,
        ]);
        $responseResubmit->assertRedirect('/profile/my-requests');

        // Assert old record was deleted and new record is pending
        $this->assertSoftDeleted('kk_profiles', ['id' => $profile->id]);
        
        $newProfile = KkProfile::where('email', $citizen->email)->first();
        $this->assertNotNull($newProfile);
        $this->assertEquals('pending', $newProfile->status);
        $this->assertEquals('DOE-UPDATED', $newProfile->surname);
    }
}

