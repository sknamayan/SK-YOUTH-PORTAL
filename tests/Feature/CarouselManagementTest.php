<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\CarouselSlide;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CarouselManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_carousel_index(): void
    {
        $response = $this->get('/admin/carousel');
        $response->assertRedirect('/login');
    }

    public function test_non_superadmins_cannot_access_carousel(): void
    {
        $roles = ['user', 'staff', 'admin'];

        foreach ($roles as $role) {
            $user = User::factory()->create(['role' => $role]);

            $response = $this->actingAs($user)->get('/admin/carousel');
            $response->assertStatus(403);

            $file = UploadedFile::fake()->create('test_slide.jpg', 200, 'image/jpeg');
            $responseStore = $this->actingAs($user)->post('/admin/carousel', [
                'title' => 'Title Attempt',
                'description' => 'Description attempt',
                'image' => $file,
            ]);
            $responseStore->assertStatus(403);

            $slide = CarouselSlide::create([
                'image_path' => 'carousel/test.jpg',
                'title' => 'Existing Slide',
                'description' => 'Existing Desc',
            ]);

            $responseUpdate = $this->actingAs($user)->put("/admin/carousel/{$slide->id}", [
                'title' => 'Updated Title Attempt',
                'description' => 'Updated desc attempt',
            ]);
            $responseUpdate->assertStatus(403);

            $responseDestroy = $this->actingAs($user)->delete("/admin/carousel/{$slide->id}");
            $responseDestroy->assertStatus(403);
        }
    }

    public function test_superadmin_can_view_upload_and_delete_carousel_slides(): void
    {
        Storage::fake('public');
        $user = User::factory()->create(['role' => 'superadmin']);

        // 1. GET carousel manager index
        $response = $this->actingAs($user)->get('/admin/carousel');
        $response->assertOk();
        $response->assertSee('Add Carousel Slide');

        // 2. POST create a new slide
        $file = UploadedFile::fake()->create('my_hero_slide.webp', 300, 'image/webp');
        $responseStore = $this->actingAs($user)->post('/admin/carousel', [
            'title' => 'Dynamic Youth Initiative Title',
            'description' => 'Dynamic supporting sub-description for youth programs.',
            'image' => $file,
            'cta_text' => 'Learn More Now',
            'cta_url' => '/forms/sports-registration',
        ]);

        $responseStore->assertRedirect('/admin/carousel');
        $this->assertDatabaseHas('carousel_slides', [
            'title' => 'Dynamic Youth Initiative Title',
            'description' => 'Dynamic supporting sub-description for youth programs.',
            'cta_text' => 'Learn More Now',
            'cta_url' => '/forms/sports-registration',
        ]);

        $slide = CarouselSlide::where('title', 'Dynamic Youth Initiative Title')->first();
        Storage::disk('public')->assertExists($slide->image_path);

        // 3. GET index lists slide
        $responseIndex = $this->actingAs($user)->get('/admin/carousel');
        $responseIndex->assertSee('Dynamic Youth Initiative Title');

        // 3.5 PUT update slide details
        $responseUpdate = $this->actingAs($user)->put("/admin/carousel/{$slide->id}", [
            'title' => 'Updated Youth Initiative Title',
            'description' => 'Updated supporting sub-description for youth programs.',
            'cta_text' => 'Updated CTA',
            'cta_url' => '/updated-url',
        ]);
        $responseUpdate->assertRedirect('/admin/carousel');
        $this->assertDatabaseHas('carousel_slides', [
            'id' => $slide->id,
            'title' => 'Updated Youth Initiative Title',
            'description' => 'Updated supporting sub-description for youth programs.',
            'cta_text' => 'Updated CTA',
            'cta_url' => '/updated-url',
        ]);

        // 4. DELETE slide
        $responseDestroy = $this->actingAs($user)->delete("/admin/carousel/{$slide->id}");
        $responseDestroy->assertRedirect('/admin/carousel');
        $this->assertDatabaseMissing('carousel_slides', [
            'id' => $slide->id,
        ]);
        Storage::disk('public')->assertMissing($slide->image_path);
    }

    public function test_superadmin_can_reorder_carousel_slides(): void
    {
        $user = User::factory()->create(['role' => 'superadmin']);

        $slide1 = CarouselSlide::create([
            'image_path' => 'carousel/slide1.jpg',
            'title' => 'First Slide',
            'description' => 'Desc 1',
            'sort_order' => 1,
        ]);

        $slide2 = CarouselSlide::create([
            'image_path' => 'carousel/slide2.jpg',
            'title' => 'Second Slide',
            'description' => 'Desc 2',
            'sort_order' => 2,
        ]);

        $response = $this->actingAs($user)->postJson('/admin/carousel/reorder', [
            'ids' => [$slide2->id, $slide1->id],
        ]);

        $response->assertOk();
        $response->assertJson(['success' => true]);

        $this->assertEquals(1, $slide2->fresh()->sort_order);
        $this->assertEquals(2, $slide1->fresh()->sort_order);
    }

    public function test_landing_page_shows_custom_slides_and_falls_back_to_defaults(): void
    {
        Storage::fake('public');

        // Case A: No custom slides, fallback to default hardcoded slides
        $responseFallback = $this->get('/');
        $responseFallback->assertOk();
        $responseFallback->assertSee('Empowering Namayan Youth Leaders');
        $responseFallback->assertSee('Silid Karunungan Studying Spaces');

        // Case B: Dynamic custom slides uploaded
        $slide = CarouselSlide::create([
            'image_path' => 'carousel/dynamic_landing.jpg',
            'title' => 'Custom Dynamic Landing Title',
            'description' => 'Custom Dynamic Landing Sub description text',
            'cta_text' => 'Custom CTA text',
            'cta_url' => '/custom-path',
        ]);

        $responseCustom = $this->get('/');
        $responseCustom->assertOk();
        $responseCustom->assertSee('Custom Dynamic Landing Title');
        $responseCustom->assertSee('Custom Dynamic Landing Sub description text');
        $responseCustom->assertSee('Custom CTA text');
        
        // Assert hardcoded ones are NOT shown since custom slides are present
        $responseCustom->assertDontSee('Empowering Namayan Youth Leaders');
    }
}
