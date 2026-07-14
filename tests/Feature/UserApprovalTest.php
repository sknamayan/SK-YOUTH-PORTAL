<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserApprovalTest extends TestCase
{
    use RefreshDatabase;

    public function test_newly_registered_users_start_approved_and_logged_in_immediately(): void
    {
        $response = $this->post('/register', [
            'first_name' => 'John',
            'last_name' => 'Citizen',
            'birthdate' => '2000-01-01',
            'email' => 'john@namayan.local',
            'password' => 'Citizen@12345!',
            'password_confirmation' => 'Citizen@12345!',
        ]);

        // Assert redirect to landing page (not login page) and is authenticated
        $response->assertRedirect('/');
        $this->assertTrue(auth()->check());

        $this->assertDatabaseHas('users', [
            'email' => 'john@namayan.local',
            'role' => 'user',
            'is_approved' => true,
        ]);

        // Assert session has success message
        $response->assertSessionHas('success', 'Account created successfully! Welcome to the SK Namayan Youth Portal.');
    }

    public function test_approved_users_can_login_successfully(): void
    {
        $user = User::factory()->create([
            'email' => 'approved@namayan.local',
            'role' => 'user',
            'is_approved' => true,
        ]);

        $response = $this->post('/login', [
            'email' => 'approved@namayan.local',
            'password' => 'password', // default factory password
        ]);

        $response->assertRedirect('/');
        $this->assertTrue(auth()->check());
    }

    public function test_superadmin_can_approve_pending_users(): void
    {
        $superadmin = User::factory()->create(['role' => 'superadmin', 'is_approved' => true]);
        $pendingUser = User::factory()->create(['role' => 'user', 'is_approved' => false]);

        // Superadmin approves pendingUser
        $response = $this->actingAs($superadmin)->patch("/admin/users/{$pendingUser->id}/approve");
        $response->assertRedirect();
        $this->assertTrue($pendingUser->fresh()->is_approved);
    }

    public function test_non_superadmins_cannot_approve_users(): void
    {
        $roles = ['admin', 'staff', 'dpo', 'user'];
        $pendingUser = User::factory()->create(['role' => 'user', 'is_approved' => false]);

        foreach ($roles as $role) {
            $user = User::factory()->create(['role' => $role, 'is_approved' => true]);
            $response = $this->actingAs($user)->patch("/admin/users/{$pendingUser->id}/approve");
            $response->assertStatus(403);
        }
        $this->assertFalse($pendingUser->fresh()->is_approved);
    }

    public function test_superadmin_can_update_roles_to_dpo_staff_or_admin(): void
    {
        $superadmin = User::factory()->create(['role' => 'superadmin', 'is_approved' => true]);
        $targetUser = User::factory()->create(['role' => 'user', 'is_approved' => true]);

        // Superadmin changes target to staff
        $response1 = $this->actingAs($superadmin)->patch("/admin/users/{$targetUser->id}/role", [
            'role' => 'staff',
        ]);
        $response1->assertRedirect();
        $this->assertEquals('staff', $targetUser->fresh()->role);

        // Superadmin changes target to admin
        $response2 = $this->actingAs($superadmin)->patch("/admin/users/{$targetUser->id}/role", [
            'role' => 'admin',
        ]);
        $response2->assertRedirect();
        $this->assertEquals('admin', $targetUser->fresh()->role);
    }

    public function test_non_superadmins_cannot_update_roles(): void
    {
        $roles = ['admin', 'staff', 'dpo', 'user'];
        $targetUser = User::factory()->create(['role' => 'user', 'is_approved' => true]);

        foreach ($roles as $role) {
            $user = User::factory()->create(['role' => $role, 'is_approved' => true]);
            $response = $this->actingAs($user)->patch("/admin/users/{$targetUser->id}/role", [
                'role' => 'admin',
            ]);
            $response->assertStatus(403);
        }
        $this->assertEquals('user', $targetUser->fresh()->role);
    }

    public function test_superadmin_can_filter_users_by_role_but_others_cannot_access_index(): void
    {
        $superadmin = User::factory()->create(['role' => 'superadmin', 'is_approved' => true]);
        
        $citizen = User::factory()->create(['role' => 'user', 'name' => 'Citizen User', 'is_approved' => true]);
        $staff = User::factory()->create(['role' => 'staff', 'name' => 'Staff Member', 'is_approved' => true]);
        $dpo = User::factory()->create(['role' => 'dpo', 'name' => 'Privacy Officer', 'is_approved' => true]);

        // 1. Superadmin accesses index with no filter (should see all)
        $responseAll = $this->actingAs($superadmin)->get('/admin/users');
        $responseAll->assertOk();
        $responseAll->assertSee('Citizen User');
        $responseAll->assertSee('Staff Member');
        $responseAll->assertSee('Privacy Officer');

        // 2. Superadmin accesses index with role=user filter
        $responseUser = $this->actingAs($superadmin)->get('/admin/users?role=user');
        $responseUser->assertOk();
        $responseUser->assertSee('Citizen User');
        $responseUser->assertDontSee('Staff Member');
        $responseUser->assertDontSee('Privacy Officer');

        // 3. Other roles cannot access index
        $nonSuperadminRoles = ['admin', 'staff', 'dpo', 'user'];
        foreach ($nonSuperadminRoles as $role) {
            $user = User::factory()->create(['role' => $role, 'is_approved' => true]);
            $response = $this->actingAs($user)->get('/admin/users');
            $response->assertStatus(403);
        }
    }

    public function test_view_shares_pending_counts_correctly(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_approved' => true]);
        
        // Create 2 unapproved users
        User::factory()->create(['role' => 'user', 'is_approved' => false]);
        User::factory()->create(['role' => 'user', 'is_approved' => false]);

        // Create a pending health request
        \App\Models\HealthRequest::create([
            'first_name' => 'Test',
            'last_name' => 'User',
            'age' => 25,
            'gender' => 'Male',
            'email' => 'test@namayan.local',
            'contact_number' => '09123456789',
            'preferred_date' => now()->addDays(2),
            'preferred_time' => '10:00 AM',
            'concerns' => 'Checkup',
            'status' => 'pending'
        ]);

        $response = $this->actingAs($admin)->get('/dashboard');
        $response->assertOk();
        
        $response->assertViewHas('pendingUserApprovalsCount', 2);
        $response->assertViewHas('pendingServiceRequestsCount', 1);
    }
}
