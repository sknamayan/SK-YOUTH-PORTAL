<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\CalendarEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CalendarManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function actingAsStaff()
    {
        $user = User::factory()->create(['role' => 'staff']);
        $this->actingAs($user);
        return $user;
    }

    public function test_guest_cannot_access_calendar()
    {
        $response = $this->get(route('dashboard.calendar.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_staff_can_view_calendar_index()
    {
        $this->actingAsStaff();

        $response = $this->get(route('dashboard.calendar.index'));
        $response->assertStatus(200);
    }

    public function test_authenticated_staff_can_fetch_events_json()
    {
        $this->actingAsStaff();

        $event = CalendarEvent::create([
            'title' => 'Sample Community Clean Up',
            'start_time' => now()->addDays(2),
            'status' => 'active'
        ]);

        $response = $this->get(route('dashboard.calendar.events', [
            'start' => now()->startOfMonth()->toDateString(),
            'end' => now()->endOfMonth()->toDateString()
        ]));

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'title' => 'Event: Sample Community Clean Up'
        ]);
    }

    public function test_non_admin_staff_cannot_mutate_calendar_events()
    {
        $this->actingAsStaff();

        // Try Store
        $response = $this->postJson(route('dashboard.calendar.events.store'), [
            'title' => 'Unauthorized Event',
            'start_time' => now()->addDays(1)->toDateTimeString()
        ]);
        $response->assertStatus(403);

        // Try Update
        $event = CalendarEvent::create([
            'title' => 'Original Title',
            'start_time' => now()->addDays(2)
        ]);
        $response = $this->putJson(route('dashboard.calendar.events.update', $event->id), [
            'title' => 'Hacked Title',
            'start_time' => now()->addDays(2)->toDateTimeString()
        ]);
        $response->assertStatus(403);

        // Try Destroy
        $response = $this->deleteJson(route('dashboard.calendar.events.destroy', $event->id));
        $response->assertStatus(403);
    }

    public function test_admin_can_crud_calendar_events()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        // 1. Store
        $response = $this->postJson(route('dashboard.calendar.events.store'), [
            'title' => 'Youth Assembly',
            'description' => 'Annual discussion for local youth projects.',
            'start_time' => now()->addDays(3)->format('Y-m-d H:i')
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $this->assertDatabaseHas('calendar_events', [
            'title' => 'Youth Assembly',
            'user_id' => $admin->id
        ]);

        $event = CalendarEvent::where('title', 'Youth Assembly')->first();

        // 2. Update
        $response = $this->putJson(route('dashboard.calendar.events.update', $event->id), [
            'title' => 'Updated Youth Assembly',
            'description' => 'Updated assembly location and agenda.',
            'start_time' => now()->addDays(3)->format('Y-m-d H:i')
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $this->assertDatabaseHas('calendar_events', [
            'id' => $event->id,
            'title' => 'Updated Youth Assembly',
            'processed_by' => $admin->id
        ]);

        // 3. Destroy
        $response = $this->deleteJson(route('dashboard.calendar.events.destroy', $event->id));
        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $this->assertSoftDeleted('calendar_events', [
            'id' => $event->id
        ]);
    }
}
