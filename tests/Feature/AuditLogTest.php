<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\Partner;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditLogTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test guest cannot view audit logs index.
     */
    public function test_guest_cannot_view_audit_logs(): void
    {
        $response = $this->get('/admin/logs');

        $response->assertRedirect('/login');
    }

    /**
     * Test non-superadmins cannot view audit logs index.
     */
    public function test_non_superadmins_cannot_view_audit_logs(): void
    {
        $roles = ['admin', 'staff', 'user'];

        foreach ($roles as $role) {
            $user = User::factory()->create(['role' => $role]);
            $response = $this->actingAs($user)->get('/admin/logs');
            $response->assertStatus(403);
        }
    }

    /**
     * Test superadmin can view audit logs index.
     */
    public function test_superadmin_can_view_audit_logs(): void
    {
        $superadmin = User::factory()->create(['role' => 'superadmin']);

        $response = $this->actingAs($superadmin)->get('/admin/logs');

        $response->assertStatus(200);
        $response->assertSee('System Audit Logs');
    }

    /**
     * Test user login and model mutations generate audit logs.
     */
    public function test_user_actions_generate_audit_logs(): void
    {
        // 1. Test registration / user creation logs
        $admin = User::create([
            'name' => 'Admin Test',
            'email' => 'admintest@example.com',
            'password' => bcrypt('password123'),
            'role' => 'admin',
            'is_approved' => true,
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'action' => 'user_created',
            'subject_type' => User::class,
            'subject_id' => $admin->id
        ]);

        // 2. Test login logs
        $response = $this->post('/login', [
            'email' => 'admintest@example.com',
            'password' => 'password123'
        ]);

        $response->assertRedirect('/dashboard/index');

        $this->assertDatabaseHas('activity_logs', [
            'action' => 'user_login',
            'user_id' => $admin->id
        ]);

        // 3. Test partner creation logs
        $partner = Partner::create([
            'name' => 'Sponsor Inc',
            'logo_path' => 'logos/sponsor.png',
            'website_url' => 'https://sponsor.example.com',
            'is_active' => true
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'action' => 'partner_created',
            'subject_type' => Partner::class,
            'subject_id' => $partner->id
        ]);

        // 4. Test partner update logs
        $partner->update(['name' => 'Sponsor Incorporated']);

        $this->assertDatabaseHas('activity_logs', [
            'action' => 'partner_updated',
            'subject_type' => Partner::class,
            'subject_id' => $partner->id
        ]);

        // 5. Test partner delete logs
        $partner->delete();

        $this->assertDatabaseHas('activity_logs', [
            'action' => 'partner_deleted',
            'subject_type' => Partner::class,
            'subject_id' => $partner->id
        ]);
    }

    /**
     * Test superadmin can view unified audit logs with type dpo.
     */
    public function test_superadmin_can_view_dpo_audit_logs(): void
    {
        $superadmin = User::factory()->create(['role' => 'superadmin']);

        // Create some normal logs and some PII logs
        $normalUser = User::factory()->create(); // creates user_created activity log
        
        // Let's create an activity log with 'pii' keyword
        ActivityLog::create([
            'user_id' => $superadmin->id,
            'action' => 'pii_accessed',
            'subject_type' => User::class,
            'subject_id' => $normalUser->id,
            'ip_address' => '127.0.0.1',
            'payload' => ['details' => 'Accessed sensitive data'],
        ]);

        $response = $this->actingAs($superadmin)->get('/admin/logs?type=dpo');

        $response->assertStatus(200);
        $response->assertSee('DPO Audit Logs');
        $response->assertSee('pii accessed');
    }

    /**
     * Test superadmin can export DPO audit logs as CSV.
     */
    public function test_superadmin_can_export_dpo_audit_logs(): void
    {
        $superadmin = User::factory()->create(['role' => 'superadmin']);

        $response = $this->actingAs($superadmin)->get(route('admin.logs.export', [
            'type' => 'dpo',
            'date_from' => now()->subDay()->toDateString(),
            'date_to' => now()->addDay()->toDateString(),
        ]));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
    }
}
