<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\NewsArticle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class NewsManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_news_management(): void
    {
        $response = $this->get('/admin/news');
        $response->assertRedirect('/login');

        $responseCreate = $this->get('/admin/news/create');
        $responseCreate->assertRedirect('/login');
    }

    public function test_non_admin_roles_cannot_access_news_management(): void
    {
        // 'user' and 'staff' roles should be blocked with 403 Forbidden
        $unauthorizedRoles = ['user', 'staff'];

        foreach ($unauthorizedRoles as $role) {
            $user = User::factory()->create(['role' => $role]);

            $response = $this->actingAs($user)->get('/admin/news');
            $response->assertStatus(403);

            $responseCreate = $this->actingAs($user)->get('/admin/news/create');
            $responseCreate->assertStatus(403);

            $file = UploadedFile::fake()->create('news.png', 100, 'image/png');
            $responseStore = $this->actingAs($user)->post('/admin/news', [
                'title' => 'Unauthorized Post',
                'category' => 'General',
                'read_time' => 5,
                'excerpt' => 'This post should not be created.',
                'content' => 'Content...',
                'image' => $file,
            ]);
            $responseStore->assertStatus(403);
        }
    }

    public function test_admin_and_superadmin_can_access_news_management(): void
    {
        $authorizedRoles = ['admin', 'superadmin'];

        foreach ($authorizedRoles as $role) {
            $user = User::factory()->create(['role' => $role]);

            $response = $this->actingAs($user)->get('/admin/news');
            $response->assertOk();

            $responseCreate = $this->actingAs($user)->get('/admin/news/create');
            $responseCreate->assertRedirect('/admin/news');
        }
    }

    public function test_admin_can_perform_full_news_article_crud(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'admin']);

        // 1. Create Article (POST)
        $file = UploadedFile::fake()->create('namayan_news.jpg', 300, 'image/jpeg');
        $response = $this->actingAs($admin)->post('/admin/news', [
            'title' => 'Incredible Basketball Championship Win',
            'category' => 'Sports',
            'read_time' => 8,
            'excerpt' => 'The local team wins the grand championship trophy in style.',
            'content' => 'Full article detailing the historical basketball match and local players celebrating.',
            'image' => $file,
            'is_featured' => 1,
            'is_trending' => 1,
        ]);

        $response->assertRedirect('/admin/news');
        $response->assertSessionHas('success', 'News article published successfully.');

        $this->assertDatabaseHas('news_articles', [
            'title' => 'Incredible Basketball Championship Win',
            'category' => 'Sports',
            'read_time' => 8,
            'is_featured' => true,
            'is_trending' => true,
        ]);

        $article = NewsArticle::where('title', 'Incredible Basketball Championship Win')->firstOrFail();
        $this->assertNotNull($article->image_path);
        Storage::disk('public')->assertExists($article->image_path);

        // 2. View in Index (GET)
        $responseIndex = $this->actingAs($admin)->get('/admin/news');
        $responseIndex->assertOk();
        $responseIndex->assertSee('Incredible Basketball Championship Win');

        // 3. Edit Article (GET & PUT)
        $responseEdit = $this->actingAs($admin)->get("/admin/news/{$article->id}/edit");
        $responseEdit->assertRedirect('/admin/news');

        $updatedFile = UploadedFile::fake()->create('updated_news.webp', 400, 'image/webp');
        $responseUpdate = $this->actingAs($admin)->put("/admin/news/{$article->id}", [
            'title' => 'Incredible Basketball Championship Win - Updated Title',
            'category' => 'Athletics',
            'read_time' => 12,
            'excerpt' => 'Updated teaser.',
            'content' => 'Updated content story.',
            'image' => $updatedFile,
            'is_featured' => 0,
            'is_trending' => 0,
        ]);

        $responseUpdate->assertRedirect('/admin/news');
        $this->assertDatabaseHas('news_articles', [
            'id' => $article->id,
            'title' => 'Incredible Basketball Championship Win - Updated Title',
            'category' => 'Athletics',
            'read_time' => 12,
            'is_featured' => false,
            'is_trending' => false,
        ]);

        // Old file deleted, new file exists
        Storage::disk('public')->assertMissing($article->image_path);
        $updatedArticle = $article->fresh();
        Storage::disk('public')->assertExists($updatedArticle->image_path);

        // 4. Delete Article (DELETE)
        $responseDelete = $this->actingAs($admin)->delete("/admin/news/{$article->id}");
        $responseDelete->assertRedirect('/admin/news');
        $this->assertDatabaseMissing('news_articles', [
            'id' => $article->id,
        ]);
        Storage::disk('public')->assertMissing($updatedArticle->image_path);
    }

    public function test_featured_status_is_exclusive_to_one_article(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Storage::fake('public');

        // Create first featured article
        $file1 = UploadedFile::fake()->create('news1.jpg', 200, 'image/jpeg');
        $this->actingAs($admin)->post('/admin/news', [
            'title' => 'First Featured News',
            'category' => 'General',
            'read_time' => 5,
            'excerpt' => 'Teaser 1',
            'content' => 'Content 1',
            'image' => $file1,
            'is_featured' => 1,
        ]);

        $article1 = NewsArticle::where('title', 'First Featured News')->firstOrFail();
        $this->assertTrue($article1->is_featured);

        // Create second featured article - should reset first one
        $file2 = UploadedFile::fake()->create('news2.jpg', 200, 'image/jpeg');
        $this->actingAs($admin)->post('/admin/news', [
            'title' => 'Second Featured News',
            'category' => 'General',
            'read_time' => 5,
            'excerpt' => 'Teaser 2',
            'content' => 'Content 2',
            'image' => $file2,
            'is_featured' => 1,
        ]);

        $article1 = $article1->fresh();
        $article2 = NewsArticle::where('title', 'Second Featured News')->firstOrFail();

        $this->assertFalse($article1->is_featured);
        $this->assertTrue($article2->is_featured);
    }

    public function test_unique_slug_generation(): void
    {
        $article1 = NewsArticle::create([
            'title' => 'Duplicated Headline Name',
            'slug' => '',
            'category' => 'News',
            'read_time' => 5,
            'excerpt' => 'Excerpt 1',
            'content' => 'Content 1',
            'image_path' => 'news/test1.jpg',
            'is_featured' => false,
            'is_trending' => false,
        ]);

        $article2 = NewsArticle::create([
            'title' => 'Duplicated Headline Name',
            'slug' => '',
            'category' => 'News',
            'read_time' => 5,
            'excerpt' => 'Excerpt 2',
            'content' => 'Content 2',
            'image_path' => 'news/test2.jpg',
            'is_featured' => false,
            'is_trending' => false,
        ]);

        $this->assertEquals('duplicated-headline-name', $article1->slug);
        $this->assertEquals('duplicated-headline-name-1', $article2->slug);
    }

    public function test_public_pages_load_fallback_news_when_database_is_empty(): void
    {
        $response = $this->get('/');
        $response->assertOk();
        
        // Assert we see empty state on landing
        $response->assertSee('No news articles posted yet.');

        // Assert we see trending empty state on the news page
        $responseNews = $this->get('/news');
        $responseNews->assertOk();
        $responseNews->assertSee('No trending news articles posted yet.');
        
        // Assert fallback show page returns 404 since there is no mock data
        $responseShow = $this->get('/news/record-breaking-swimmer-victory');
        $responseShow->assertStatus(404);
    }

    public function test_public_pages_load_database_news_when_available(): void
    {
        $article = NewsArticle::create([
            'title' => 'Custom Database Headline Story',
            'slug' => 'custom-database-headline-story',
            'category' => 'Community',
            'read_time' => 4,
            'excerpt' => 'Brief overview of db record.',
            'content' => 'Full text description of this database record.',
            'image_path' => 'news/db_pic.jpg',
            'is_featured' => true,
            'is_trending' => true,
        ]);

        // Check landing page (Featured/Recent)
        $response = $this->get('/');
        $response->assertOk();
        $response->assertSee('Custom Database Headline Story');
        
        // Should not see fallback anymore since db is not empty
        $response->assertDontSee('Record-Breaking, Stunning Performance Leads The Swimmer');

        // Check news page (Trending)
        $responseNews = $this->get('/news');
        $responseNews->assertOk();
        $responseNews->assertSee('Custom Database Headline Story');
        $responseNews->assertDontSee('Innovative Farming Technology Transforms Local Agriculture Practices');

        $responseShow = $this->get('/news/custom-database-headline-story');
        $responseShow->assertOk();
        $responseShow->assertSee('Custom Database Headline Story');
        $responseShow->assertSee('Full text description of this database record.');
    }
}
