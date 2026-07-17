<?php

namespace Tests\Feature;

use App\Models\League;
use App\Models\RegistrationForm;
use App\Models\FormField;
use App\Models\SportsRegistration;
use App\Models\KkProfile;
use App\Models\Purok;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SportsFormBuilderTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Purok::firstOrCreate([
            'id' => 1,
        ], [
            'purok_name' => 'J. RIZAL',
            'purok_code' => 'JPR',
            'street_name' => 'J. RIZAL'
        ]);
    }

    /**
     * Test superadmin can access create form builder page.
     */
    public function test_admin_can_access_form_builder(): void
    {
        $admin = User::factory()->create(['role' => 'superadmin']);

        $response = $this->actingAs($admin)->get(route('admin.sports-league.form-builder.create'));

        $response->assertStatus(200);
        $response->assertSee('SIKLAB Form Builder');
    }

    /**
     * Test admin can store sports form schema.
     */
    public function test_admin_can_store_sports_form_schema(): void
    {
        $admin = User::factory()->create(['role' => 'superadmin']);

        $response = $this->actingAs($admin)->post(route('admin.sports-league.form-builder.store'), [
            'league_name' => 'SKILAB',
            'sport' => 'Basketball',
            'division_name' => 'Midget Division',
            'description' => 'Ages 12 to 17',
            'custom_fields' => [
                [
                    'label' => 'Team Jersey Name',
                    'name' => 'team_jersey_name',
                    'type' => 'text',
                    'placeholder' => 'Enter your jersey name',
                    'required' => '1',
                ],
                [
                    'label' => 'Jersey Size',
                    'name' => 'jersey_size',
                    'type' => 'select',
                    'placeholder' => 'Select size',
                    'required' => '0',
                    'options' => ['S', 'M', 'L', 'XL'],
                ]
            ]
        ]);

        $response->assertRedirect(route('admin.sports-league.index'));
        $this->assertDatabaseHas('leagues', [
            'name' => 'SIKLAB',
            'sport' => 'Basketball',
        ]);

        $this->assertDatabaseHas('registration_forms', [
            'type' => 'sports',
            'division_name' => 'Midget Division',
            'description' => 'Ages 12 to 17',
        ]);

        $this->assertDatabaseHas('form_fields', [
            'field_label' => 'Team Jersey Name',
            'field_name' => 'team_jersey_name',
            'field_type' => 'text',
            'is_required' => true,
        ]);
    }

    /**
     * Test public can view the static sports registration form.
     */
    public function test_public_can_view_dynamic_form(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        KkProfile::create([
            'email' => $user->email,
            'status' => 'approved',
            'surname' => 'User',
            'first_name' => 'Test',
            'age' => 20,
            'sex' => 'Female',
            'gender' => 'Female',
            'dob' => '2006-07-02',
            'civil_status' => 'Single',
            'purok_id' => 1,
            'street_address' => '123 Street',
            'youth_classification' => 'ISY',
            'contact_number' => '09123456789',
            'consent_given' => true,
            'registered_sk_voter' => true,
            'registered_national_voter' => false,
            'attended_kk_assembly' => true,
            'part_of_youth_org' => false,
            'interested_in_joining' => true,
            'part_of_lgbtqia' => false,
            'pwd' => false,
            'highest_educational_attainment' => 'High School Student',
        ]);

        $response = $this->actingAs($user)->get(route('forms.sports.create'));

        $response->assertStatus(200);
        $response->assertSee('SIKLAB Registration');
        $response->assertSee('League Category Details');
    }

    /**
     * Test minor submission requires guardian details.
     */
    public function test_public_can_submit_dynamic_registration(): void
    {
        Storage::fake('public');

        $user = User::factory()->create(['role' => 'user']);
        KkProfile::create([
            'email' => $user->email,
            'status' => 'approved',
            'surname' => 'User',
            'first_name' => 'Test',
            'age' => 15,
            'sex' => 'Male',
            'gender' => 'Male',
            'dob' => '2011-07-02',
            'civil_status' => 'Single',
            'purok_id' => 1,
            'street_address' => '123 Street',
            'youth_classification' => 'ISY',
            'contact_number' => '09123456789',
            'consent_given' => true,
            'registered_sk_voter' => false,
            'registered_national_voter' => false,
            'attended_kk_assembly' => true,
            'part_of_youth_org' => false,
            'interested_in_joining' => true,
            'part_of_lgbtqia' => false,
            'pwd' => false,
            'highest_educational_attainment' => 'High School Student',
        ]);

        $pic = UploadedFile::fake()->create('profile.jpg', 500, 'image/jpeg');
        $govId = UploadedFile::fake()->create('id.pdf', 500, 'application/pdf');

        $response = $this->actingAs($user)->post(route('forms.sports.store'), [
            'sport' => 'Basketball',
            'division' => 'Midget',
            'position' => 'Guard',
            'first_name' => 'Juan',
            'last_name' => 'Cruz',
            'birthdate' => '2011-07-02',
            'age' => 15,
            'gender' => 'Male',
            'email' => 'minor@example.com',
            'contact_number' => '09123456789',
            'address' => '123 Namayan Street',
            'kk_profiling_status' => 'No',
            'profile_picture' => $pic,
            'guardian_first_name' => 'Pedro',
            'guardian_last_name' => 'Cruz',
            'guardian_age' => 45,
            'guardian_relation' => 'Father',
            'guardian_contact_number' => '09123456780',
            'guardian_address' => '123 Namayan Street',
            'guardian_gov_id' => $govId,
            'health_declaration' => 'None',
            'consent_waiver' => 1,
        ]);

        $response->assertRedirect(route('forms.sports.create'));
        $response->assertSessionHas('success');

        $submission = SportsRegistration::first();
        $this->assertNotNull($submission);
        $this->assertEquals('Juan', $submission->first_name);
        $this->assertEquals(15, $submission->age);
        $this->assertEquals('Pedro', $submission->guardian_first_name);

        Storage::disk('public')->assertExists($submission->profile_picture);
        Storage::disk('public')->assertExists($submission->guardian_gov_id);
    }

    /**
     * Test citizen cannot submit registration twice.
     */
    public function test_citizen_cannot_register_twice(): void
    {
        Storage::fake('public');

        $user = User::factory()->create(['role' => 'user']);
        KkProfile::create([
            'email' => $user->email,
            'status' => 'approved',
            'surname' => 'User',
            'first_name' => 'Test',
            'age' => 20,
            'sex' => 'Male',
            'gender' => 'Male',
            'dob' => '2006-07-02',
            'civil_status' => 'Single',
            'purok_id' => 1,
            'street_address' => '123 Street',
            'youth_classification' => 'ISY',
            'contact_number' => '09123456789',
            'consent_given' => true,
            'registered_sk_voter' => true,
            'registered_national_voter' => false,
            'attended_kk_assembly' => true,
            'part_of_youth_org' => false,
            'interested_in_joining' => true,
            'part_of_lgbtqia' => false,
            'pwd' => false,
            'highest_educational_attainment' => 'High School Student',
        ]);

        SportsRegistration::create([
            'first_name' => 'Juan',
            'last_name' => 'Cruz',
            'age' => 20,
            'gender' => 'Male',
            'email' => $user->email,
            'contact_number' => '09123456789',
            'sport' => 'Basketball',
            'division' => 'Seniors',
            'position' => 'Guard',
            'event_date' => now()->toDateString(),
            'status' => 'pending',
        ]);

        $pic = UploadedFile::fake()->create('profile.jpg', 500, 'image/jpeg');
        $cert = UploadedFile::fake()->create('cert.pdf', 500, 'application/pdf');

        $response = $this->actingAs($user)->post(route('forms.sports.store'), [
            'sport' => 'Volleyball',
            'division' => 'Mens',
            'position' => 'Libero',
            'first_name' => 'Juan',
            'last_name' => 'Cruz',
            'birthdate' => '2006-07-02',
            'age' => 20,
            'gender' => 'Male',
            'email' => $user->email,
            'contact_number' => '09123456789',
            'address' => '123 Namayan Street',
            'kk_profiling_status' => 'Yes',
            'profile_picture' => $pic,
            'voter_cert' => $cert,
            'health_declaration' => 'None',
            'consent_waiver' => 1,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertEquals(1, SportsRegistration::count());
    }

    /**
     * Test admin can view registration details.
     */
    public function test_admin_can_view_registration(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $registration = SportsRegistration::create([
            'first_name' => 'Juan',
            'last_name' => 'Cruz',
            'age' => 20,
            'gender' => 'Male',
            'email' => 'juan@example.com',
            'contact_number' => '09123456789',
            'sport' => 'Basketball',
            'division' => 'Seniors',
            'position' => 'Guard',
            'event_date' => now()->toDateString(),
            'status' => 'pending',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.sports-league.show', $registration->id));

        $response->assertStatus(200);
        $response->assertSee('Juan');
        $response->assertSee('Cruz');
    }

    /**
     * Test admin can edit and update registration details.
     */
    public function test_admin_can_update_registration(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $registration = SportsRegistration::create([
            'first_name' => 'Juan',
            'last_name' => 'Cruz',
            'age' => 20,
            'gender' => 'Male',
            'email' => 'juan@example.com',
            'contact_number' => '09123456789',
            'sport' => 'Basketball',
            'division' => 'Seniors',
            'position' => 'Guard',
            'event_date' => now()->toDateString(),
            'status' => 'pending',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.sports-league.edit', $registration->id));
        $response->assertStatus(200);

        $response = $this->actingAs($admin)->put(route('admin.sports-league.update', $registration->id), [
            'first_name' => 'Juanito',
            'last_name' => 'Cruz',
            'age' => 20,
            'gender' => 'Male',
            'email' => 'juan@example.com',
            'contact_number' => '09123456789',
            'sport' => 'Basketball',
            'division' => 'Seniors',
            'position' => 'Forward',
            'address' => '123 Namayan Street',
            'kk_profiling_status' => 'Yes',
            'remarks' => 'Updated by admin',
        ]);

        $response->assertRedirect(route('admin.sports-league.index'));

        $registration->refresh();
        $this->assertEquals('Juanito', $registration->first_name);
        $this->assertEquals('Forward', $registration->position);
    }

    /**
     * Test admin can register citizen directly.
     */
    public function test_admin_can_register_citizen_directly(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get(route('admin.sports-league.create'));
        $response->assertStatus(200);

        $response = $this->actingAs($admin)->post(route('admin.sports-league.store'), [
            'first_name' => 'Direct',
            'last_name' => 'Citizen',
            'age' => 20,
            'gender' => 'Female',
            'email' => 'direct@example.com',
            'contact_number' => '09123456789',
            'sport' => 'Volleyball',
            'division' => 'Womens',
            'position' => 'Setter',
            'address' => '123 Namayan Street',
            'kk_profiling_status' => 'Yes',
            'health_declaration' => 'Fit',
            'consent_waiver' => 1,
        ]);

        $response->assertRedirect(route('admin.sports-league.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('sports_registrations', [
            'email' => 'direct@example.com',
            'sport' => 'Volleyball',
            'division' => 'Womens',
            'status' => 'approved',
            'processed_by' => $admin->id,
        ]);
    }
}
