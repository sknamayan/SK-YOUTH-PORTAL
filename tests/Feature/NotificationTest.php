<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_user_cannot_read_notifications()
    {
        $user = User::factory()->create();
        $notification = Notification::create([
            'user_id' => $user->id,
            'title' => 'Test Notification',
            'message' => 'This is a test notification message.',
        ]);

        $response = $this->patch("/notifications/{$notification->id}/read");
        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_mark_notification_as_read()
    {
        $user = User::factory()->create();
        $notification = Notification::create([
            'user_id' => $user->id,
            'title' => 'Test Notification',
            'message' => 'This is a test notification message.',
            'url' => '/some-url'
        ]);

        $response = $this->actingAs($user)->patch("/notifications/{$notification->id}/read");
        $response->assertRedirect('/some-url');

        $this->assertNotNull($notification->fresh()->read_at);
    }

    public function test_user_cannot_read_others_notifications()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $notification = Notification::create([
            'user_id' => $user1->id,
            'title' => 'Test Notification',
            'message' => 'This is a test notification message.',
        ]);

        $response = $this->actingAs($user2)->patch("/notifications/{$notification->id}/read");
        $response->assertStatus(403);
    }

    public function test_authenticated_user_can_mark_all_notifications_as_read()
    {
        $user = User::factory()->create();
        Notification::create([
            'user_id' => $user->id,
            'title' => 'Notif 1',
            'message' => 'Msg 1',
        ]);
        Notification::create([
            'user_id' => $user->id,
            'title' => 'Notif 2',
            'message' => 'Msg 2',
        ]);

        $response = $this->actingAs($user)->post('/notifications/read-all');
        $response->assertRedirect();

        $this->assertEquals(0, $user->notifications()->whereNull('read_at')->count());
    }

    public function test_unauthenticated_user_cannot_access_notifications_index()
    {
        $response = $this->get('/notifications');
        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_access_notifications_index()
    {
        $user = User::factory()->create();
        Notification::create([
            'user_id' => $user->id,
            'title' => 'Test Notification',
            'message' => 'This is a test notification message.',
        ]);

        $response = $this->actingAs($user)->get('/notifications');
        $response->assertStatus(200);
        $response->assertSee('Test Notification');
    }
}

