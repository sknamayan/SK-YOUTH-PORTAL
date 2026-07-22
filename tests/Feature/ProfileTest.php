<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/profile/edit');

        $response->assertOk();
        $response->assertSee('Display Name', false);
    }

    public function test_profile_information_can_be_updated(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile/info', [
                'first_name' => 'Test',
                'last_name' => 'User',
                'email' => 'test@example.com',
                'contact_number' => '09171234567',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile/edit?tab=account');

        $user->refresh();

        $this->assertSame('TEST USER', $user->name);
        $this->assertSame('test@example.com', $user->email);
        $this->assertSame('09171234567', $user->contact_number);
        $this->assertNull($user->email_verified_at);
    }

    public function test_preferences_can_be_updated(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post('/profile/preferences', [
                'settings_tab' => 'display',
                'theme' => 'dark',
                'language' => 'fil',
                'notify_request_status' => '1',
                'notify_announcements' => '1',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile/edit?tab=display');

        $user->refresh();

        $this->assertSame('dark', $user->theme);
        $this->assertSame('fil', $user->language);
        $this->assertTrue($user->notify_request_status);
        $this->assertTrue($user->notify_announcements);
        $this->assertSame('fil', session('locale'));
    }

    public function test_display_preferences_do_not_force_enable_notifications(): void
    {
        $user = User::factory()->create([
            'notify_request_status' => false,
            'notify_announcements' => false,
        ]);

        $response = $this
            ->actingAs($user)
            ->post('/profile/preferences', [
                'settings_tab' => 'display',
                'theme' => 'light',
                'language' => 'en',
                'notify_request_status' => '0',
                'notify_announcements' => '0',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile/edit?tab=display');

        $user->refresh();

        $this->assertFalse($user->notify_request_status);
        $this->assertFalse($user->notify_announcements);
    }

    public function test_notification_preferences_can_be_updated_from_notifications_tab(): void
    {
        $user = User::factory()->create([
            'theme' => 'system',
            'language' => 'en',
            'notify_request_status' => true,
            'notify_announcements' => true,
        ]);

        $response = $this
            ->actingAs($user)
            ->post('/profile/preferences', [
                'settings_tab' => 'notifications',
                'theme' => 'system',
                'language' => 'en',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile/edit?tab=notifications');

        $user->refresh();

        $this->assertFalse($user->notify_request_status);
        $this->assertFalse($user->notify_announcements);
    }

    public function test_password_can_be_updated(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->put('/profile/password', [
                'settings_tab' => 'security',
                'current_password' => 'password',
                'password' => 'new-password-123',
                'password_confirmation' => 'new-password-123',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile/edit?tab=security');

        $this->assertTrue(Hash::check('new-password-123', $user->refresh()->password));
    }

    public function test_filipino_settings_labels_are_translated(): void
    {
        app()->setLocale('fil');

        $this->assertSame('Mga Setting ng Portal', __('Settings Portal'));
        $this->assertSame('Personal na Impormasyon', __('Personal Info'));
        $this->assertSame('Display at Tema', __('Display & Theme'));
        $this->assertSame('Mga Setting ng Seguridad', __('Security Settings'));
        $this->assertSame('Mga Notipikasyon', __('Notifications'));
        $this->assertSame('Privacy at Data', __('Privacy & Data'));
    }

    public function test_avatar_can_be_uploaded_and_cropped(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();

        // 1x1 transparent PNG base64 string
        $base64Image = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==';

        $response = $this
            ->actingAs($user)
            ->post('/profile/avatar', [
                'avatar_base64' => $base64Image,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile/edit?tab=account');

        $user->refresh();

        $this->assertNotNull($user->avatar);
        Storage::assertExists('public/avatars/' . $user->avatar);
    }

    public function test_user_can_logout_of_other_sessions(): void
    {
        $user = User::factory()->create();

        // Seed a fake "other" session in the DB
        DB::table('sessions')->insert([
            [
                'id' => 'other_session_id',
                'user_id' => $user->id,
                'ip_address' => '192.168.1.1',
                'user_agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 14_0 like Mac OS X)',
                'payload' => 'payload2',
                'last_activity' => time() - 3600,
            ]
        ]);

        $response = $this
            ->actingAs($user)
            ->post('/profile/sessions/logout-others', [
                'password' => 'password',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile/edit?tab=security');

        // Verify other session was deleted
        $this->assertFalse(DB::table('sessions')->where('id', 'other_session_id')->exists());
    }

    public function test_user_can_download_personal_data_archive(): void
    {
        $user = User::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
            'contact_number' => '09170001111',
        ]);

        $response = $this
            ->actingAs($user)
            ->get('/profile/download-data');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/json');

        $json = json_decode($response->streamedContent(), true);

        $this->assertSame('John', $json['account']['first_name']);
        $this->assertSame('Doe', $json['account']['last_name']);
        $this->assertSame('john.doe@example.com', $json['account']['email']);
        $this->assertSame('09170001111', $json['account']['contact_number']);
        $this->assertArrayHasKey('requests', $json);
    }

    public function test_email_verification_status_is_unchanged_when_the_email_address_is_unchanged(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile/info', [
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile/edit?tab=account');

        $this->assertNotNull($user->refresh()->email_verified_at);
    }

    public function test_user_can_delete_their_account(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->delete('/profile', [
                'password' => 'password',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/');

        $this->assertGuest();
        $this->assertNull($user->fresh());
    }

    public function test_correct_password_must_be_provided_to_delete_account(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from('/profile/edit')
            ->delete('/profile', [
                'settings_tab' => 'privacy',
                'password' => 'wrong-password',
            ]);

        $response
            ->assertSessionHasErrors('password')
            ->assertRedirect('/profile/edit');

        $this->assertSame('privacy', old('settings_tab'));

        $this->assertNotNull($user->fresh());
    }
}
