<?php

namespace Tests\Feature;

use App\Models\HealthRequest;
use App\Models\MedicineRequest;
use App\Models\SilidKarununganRequest;
use App\Models\SportsRegistration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrackRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_track_index_page_can_be_rendered(): void
    {
        $response = $this->get('/track');
        $response->assertStatus(200);
        $response->assertSee('Track Your Requests');
    }

    public function test_track_search_by_email(): void
    {
        // Create health request
        $health = HealthRequest::create([
            'first_name' => 'Alice',
            'last_name' => 'Smith',
            'age' => 28,
            'gender' => 'Female',
            'email' => 'alice@example.com',
            'contact_number' => '09171234567',
            'preferred_date' => now()->addDays(3),
            'preferred_time' => '09:00',
            'concerns' => 'Need advice on nutrition',
            'status' => 'pending',
        ]);

        $response = $this->post('/track', [
            'email' => 'alice@example.com',
        ]);

        $response->assertStatus(200);
        $response->assertSee('Alice Smith');
        $response->assertSee('SK-REQ-' . str_pad($health->id, 5, '0', STR_PAD_LEFT));
        // Verify that the category-icon SVG structure is not in the page
        $response->assertDontSee('w-10 h-10 bg-blue-50');
    }

    public function test_track_search_by_reference(): void
    {
        // Create medicine request
        $med = MedicineRequest::create([
            'requestor_first_name' => 'Bob',
            'requestor_last_name' => 'Jones',
            'requestor_age' => 45,
            'requestor_gender' => 'Male',
            'email' => 'bob@example.com',
            'contact_number' => '09187654321',
            'complete_address' => '123 St, Namayan',
            'status' => 'pending',
        ]);

        $refCode = 'SK-REQ-' . str_pad($med->id, 5, '0', STR_PAD_LEFT);

        $response = $this->post('/track', [
            'email' => $refCode,
        ]);

        $response->assertStatus(200);
        $response->assertSee('Bob Jones');
        $response->assertSee($refCode);
        $response->assertDontSee('w-10 h-10 bg-blue-50');
    }

    public function test_track_search_invalid_reference_or_nonexistent_email(): void
    {
        $response = $this->post('/track', [
            'email' => 'nonexistent@example.com',
        ]);

        $response->assertStatus(200);
        $response->assertSee('No requests found');

        $response2 = $this->post('/track', [
            'email' => 'SK-REQ-99999',
        ]);

        $response2->assertStatus(200);
        $response2->assertSee('No requests found');
    }

    public function test_track_search_validation(): void
    {
        $response = $this->post('/track', [
            'email' => '',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_viewing_pending_request_changes_status_to_review(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_approved' => true]);

        $health = HealthRequest::create([
            'first_name' => 'Alice',
            'last_name' => 'Smith',
            'age' => 28,
            'gender' => 'Female',
            'email' => 'alice@example.com',
            'contact_number' => '09171234567',
            'preferred_date' => now()->addDays(3),
            'preferred_time' => '09:00',
            'concerns' => 'Need advice on nutrition',
            'status' => 'pending',
        ]);

        $this->assertEquals('pending', $health->status);

        $response = $this->actingAs($admin)->get('/dashboard/requests/health/' . $health->id);

        $response->assertStatus(200);

        // Verify status changed in DB
        $health->refresh();
        $this->assertEquals('review', $health->status);

        // Verify activity log was recorded
        $this->assertDatabaseHas('activity_logs', [
            'action' => 'status_changed',
            'subject_type' => HealthRequest::class,
            'subject_id' => $health->id,
        ]);
    }

    public function test_admin_status_change_tags_processed_by(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_approved' => true]);

        $health = HealthRequest::create([
            'first_name' => 'Alice',
            'last_name' => 'Smith',
            'age' => 28,
            'gender' => 'Female',
            'email' => 'alice@example.com',
            'contact_number' => '09171234567',
            'preferred_date' => now()->addDays(3),
            'preferred_time' => '09:00',
            'concerns' => 'Need advice on nutrition',
            'status' => 'pending',
        ]);

        // Change status to approved
        $response = $this->actingAs($admin)->patch('/dashboard/requests/health/' . $health->id . '/status/approved');
        $response->assertRedirect();
        
        $health->refresh();
        $this->assertEquals('approved', $health->status);
        $this->assertEquals($admin->id, $health->processed_by);

        // Reset to pending should nullify processed_by
        $response2 = $this->actingAs($admin)->patch('/dashboard/requests/health/' . $health->id . '/status/pending');
        $response2->assertRedirect();
        
        $health->refresh();
        $this->assertEquals('pending', $health->status);
        $this->assertNull($health->processed_by);
    }
}
