<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\KkProfile;
use App\Models\Purok;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RecycleBinTest extends TestCase
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

    /**
     * Non-superadmins cannot access the recycle bin.
     */
    public function test_non_superadmin_cannot_access_recycle_bin()
    {
        $user = User::factory()->create(['role' => 'staff']);

        $response = $this->actingAs($user)->get(route('admin.recycle-bin.index'));

        $response->assertStatus(403);
    }

    /**
     * Superadmin must confirm password to access the recycle bin.
     */
    public function test_superadmin_must_confirm_password_to_access_recycle_bin()
    {
        $superadmin = User::factory()->create(['role' => 'superadmin']);

        // Accessing without confirming password redirects to password confirm page
        $response = $this->actingAs($superadmin)->get(route('admin.recycle-bin.index'));

        $response->assertRedirect(route('password.confirm'));
    }

    /**
     * Superadmin can access recycle bin after confirming password.
     */
    public function test_superadmin_can_access_recycle_bin_with_confirmed_password()
    {
        $superadmin = User::factory()->create(['role' => 'superadmin']);

        // Set password confirmed timestamp in session
        $response = $this->actingAs($superadmin)
            ->withSession(['auth.password_confirmed_at' => time()])
            ->get(route('admin.recycle-bin.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.recycle-bin.index');
    }

    /**
     * Superadmin can restore a soft-deleted item.
     */
    public function test_superadmin_can_restore_soft_deleted_item()
    {
        $superadmin = User::factory()->create(['role' => 'superadmin']);

        $profile = KkProfile::create([
            'surname' => 'DOE',
            'first_name' => 'JOHN',
            'middle_name' => 'SMITH',
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
            'email' => 'john.doe@example.com',
            'registered_sk_voter' => true,
            'registered_national_voter' => false,
            'attended_kk_assembly' => true,
            'part_of_youth_org' => false,
            'interested_in_joining' => true,
            'part_of_lgbtqia' => false,
            'pwd' => false,
            'highest_educational_attainment' => 'High School Student',
            'status' => 'approved',
        ]);
        $profile->delete();

        $this->assertSoftDeleted($profile);

        $response = $this->actingAs($superadmin)
            ->withSession(['auth.password_confirmed_at' => time()])
            ->post(route('admin.recycle-bin.restore', ['type' => 'profiling', 'id' => $profile->id]));

        $response->assertRedirect(route('admin.recycle-bin.index'));
        $this->assertNotSoftDeleted($profile);
    }

    /**
     * Superadmin can permanently delete a soft-deleted item.
     */
    public function test_superadmin_can_permanently_delete_soft_deleted_item()
    {
        $superadmin = User::factory()->create(['role' => 'superadmin']);

        $profile = KkProfile::create([
            'surname' => 'DOE',
            'first_name' => 'JOHN',
            'middle_name' => 'SMITH',
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
            'email' => 'john.doe@example.com',
            'registered_sk_voter' => true,
            'registered_national_voter' => false,
            'attended_kk_assembly' => true,
            'part_of_youth_org' => false,
            'interested_in_joining' => true,
            'part_of_lgbtqia' => false,
            'pwd' => false,
            'highest_educational_attainment' => 'High School Student',
            'status' => 'approved',
        ]);
        $profile->delete();

        $this->assertSoftDeleted($profile);

        $response = $this->actingAs($superadmin)
            ->withSession(['auth.password_confirmed_at' => time()])
            ->delete(route('admin.recycle-bin.force-delete', ['type' => 'profiling', 'id' => $profile->id]));

        $response->assertRedirect(route('admin.recycle-bin.index'));
        $this->assertModelMissing($profile);
    }
}
