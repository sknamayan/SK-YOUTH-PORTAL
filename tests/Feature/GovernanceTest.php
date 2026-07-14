<?php

namespace Tests\Feature;

use App\Models\SkOfficial;
use App\Models\TransparencyPost;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class GovernanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_can_view_officials_and_transparency_pages(): void
    {
        SkOfficial::create([
            'name' => 'Test Chair',
            'slug' => 'test-chair',
            'position' => 'SK Chairperson',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        TransparencyPost::create([
            'title' => 'Public Budget Report',
            'slug' => 'public-budget-report',
            'category' => 'budget',
            'excerpt' => 'Summary of SK budget.',
            'published_at' => now(),
            'is_active' => true,
        ]);

        $this->get(route('officials.index'))->assertOk()->assertSee('Test Chair');
        $this->get(route('officials.show', 'test-chair'))->assertOk()->assertSee('SK Chairperson');
        $this->get(route('transparency.index'))->assertOk()->assertSee('Public Budget Report');
        $this->get(route('transparency.show', 'public-budget-report'))->assertOk()->assertSee('Summary of SK budget');
    }

    public function test_admin_can_manage_officials_and_transparency(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'admin']);

        $photo = UploadedFile::fake()->create('official.jpg', 100, 'image/jpeg');

        $this->actingAs($admin)->post(route('admin.officials.store'), [
            'name' => 'New Councilor',
            'position' => 'SK Councilor',
            'bio' => 'Serving the youth.',
            'photo' => $photo,
            'sort_order' => 5,
            'is_active' => 1,
        ])->assertRedirect(route('admin.officials.index'));

        $this->assertDatabaseHas('sk_officials', ['name' => 'New Councilor']);

        $this->actingAs($admin)->post(route('admin.transparency.store'), [
            'title' => 'New Disclosure',
            'category' => 'announcement',
            'excerpt' => 'Public notice for citizens.',
            'content' => 'Full disclosure text.',
            'is_active' => 1,
        ])->assertRedirect(route('admin.transparency.index'));

        $this->assertDatabaseHas('transparency_posts', ['title' => 'New Disclosure']);
    }

    public function test_regular_user_cannot_access_admin_governance(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $this->actingAs($user)->get(route('admin.officials.index'))->assertStatus(403);
        $this->actingAs($user)->get(route('admin.transparency.index'))->assertStatus(403);
    }
}
