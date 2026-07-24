<?php

namespace Tests\Feature;

use App\Models\Announcement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnnouncementManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_announcements_index(): void
    {
        $response = $this->get(route('admin.announcements.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_non_admin_cannot_access_announcements_index(): void
    {
        $user = User::factory()->create(['role' => 'citizen']);
        $response = $this->actingAs($user)->get(route('admin.announcements.index'));
        $response->assertStatus(403);
    }

    public function test_admin_can_manage_announcements(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        // 1. Create Announcement
        $response = $this->actingAs($admin)->post(route('admin.announcements.store'), [
            'title' => 'Important General Assembly',
            'body' => 'Everyone is invited to join our public assembly.',
            'type' => 'success',
            'is_active' => 1,
            'published_at' => now()->format('Y-m-d H:i:s'),
        ]);

        $response->assertRedirect(route('admin.announcements.index'));
        $this->assertDatabaseHas('announcements', [
            'title' => 'Important General Assembly',
            'type' => 'success',
        ]);

        $announcement = Announcement::first();

        // 2. Update Announcement
        $response = $this->actingAs($admin)->put(route('admin.announcements.update', $announcement->id), [
            'title' => 'Updated Assembly Info',
            'body' => 'Change of time for public assembly.',
            'type' => 'warning',
            'is_active' => 1,
            'published_at' => now()->format('Y-m-d H:i:s'),
        ]);

        $response->assertRedirect(route('admin.announcements.index'));
        $this->assertDatabaseHas('announcements', [
            'title' => 'Updated Assembly Info',
            'type' => 'warning',
        ]);

        // 3. Delete Announcement
        $response = $this->actingAs($admin)->delete(route('admin.announcements.destroy', $announcement->id));
        $response->assertRedirect(route('admin.announcements.index'));
        $this->assertDatabaseMissing('announcements', [
            'id' => $announcement->id,
        ]);
    }
}
