<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\SportsRegistration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Verify the request state‑machine workflow and observer side‑effects.
 */
class RequestStateMachineTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Artisan::call('migrate');
    }

    #[Test]
    public function admin_can_move_a_pending_request_to_under_review_and_observer_sets_processed_by(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        
        $request = SportsRegistration::create([
            'first_name' => 'Juan',
            'last_name' => 'Cruz',
            'age' => 20,
            'gender' => 'Male',
            'email' => 'juan@example.com',
            'contact_number' => '09123456789',
            'sport' => 'Basketball',
            'division' => 'Senior',
            'event_date' => now()->toDateString(),
            'status' => 'pending',
            'processed_by' => null,
        ]);

        $this->actingAs($admin)
            ->patch(route('admin.sports-league.status', [
                'id' => $request->id,
                'status' => 'under review',
            ]))
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('sports_registrations', [
            'id' => $request->id,
            'status' => 'review',
            'processed_by' => $admin->id,
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $admin->id,
            'action' => 'status_updated',
            'subject_type' => SportsRegistration::class,
            'subject_id' => $request->id,
        ]);

        $log = ActivityLog::where('subject_id', $request->id)
            ->where('action', 'status_updated')
            ->first();
        $payload = json_decode($log->payload, true);
        $this->assertEquals('pending', $payload['old_status']);
        $this->assertEquals('review', $payload['new_status']);
    }
}
