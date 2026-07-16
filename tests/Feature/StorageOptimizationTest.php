<?php

namespace Tests\Feature;

use App\Services\FileUploadService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StorageOptimizationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test the storage_url helper function.
     */
    public function test_storage_url_helper_resolves_correctly()
    {
        // Absolute URL remains unchanged
        $this->assertEquals(
            'https://s3.amazonaws.com/my-bucket/reports/abc.pdf',
            storage_url('https://s3.amazonaws.com/my-bucket/reports/abc.pdf')
        );

        // Relative path resolves to local storage asset link
        $this->assertEquals(
            asset('storage/reports/abc.pdf'),
            storage_url('reports/abc.pdf')
        );

        // Null path returns null
        $this->assertNull(storage_url(null));
    }

    /**
     * Test image upload compression converting to WebP.
     */
    public function test_upload_service_stores_images_as_webp()
    {
        Storage::fake('public');

        $tempPath = tempnam(sys_get_temp_dir(), 'test_image_');
        file_put_contents($tempPath, 'not-a-real-jpeg');

        $file = new UploadedFile(
            $tempPath,
            'avatar.jpg',
            'image/jpeg',
            null,
            true
        );

        $path = FileUploadService::storeOptimized($file, 'avatars', 'public');

        Storage::disk('public')->assertExists($path);

        if (extension_loaded('gd')) {
            $this->assertTrue(str_ends_with($path, '.webp'));
        } else {
            $this->assertFalse(str_ends_with($path, '.webp'));
        }

        @unlink($tempPath);
    }

    /**
     * Test the app:compress-images command.
     */
    public function test_compress_images_artisan_command()
    {
        Storage::fake('public');

        // Set up mock slide record
        DB::table('carousel_slides')->insert([
            'image_path' => 'carousel/slide1.jpg',
            'title' => 'Slide 1',
            'description' => 'Test slide',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $tempPath = tempnam(sys_get_temp_dir(), 'test_image_');
        file_put_contents($tempPath, 'not-a-real-jpeg');

        Storage::disk('public')->put('carousel/slide1.jpg', file_get_contents($tempPath));
        @unlink($tempPath);

        Artisan::call('app:compress-images', ['--dry-run' => true]);
        $this->assertStringContainsString('Simulation mode', Artisan::output());
        Storage::disk('public')->assertExists('carousel/slide1.jpg');

        Artisan::call('app:compress-images');

        if (extension_loaded('gd')) {
            Storage::disk('public')->assertMissing('carousel/slide1.jpg');
            Storage::disk('public')->assertExists('carousel/slide1.webp');

            $slide = DB::table('carousel_slides')->first();
            $this->assertEquals('carousel/slide1.webp', $slide->image_path);
        } else {
            Storage::disk('public')->assertExists('carousel/slide1.jpg');
            Storage::disk('public')->assertMissing('carousel/slide1.webp');

            $slide = DB::table('carousel_slides')->first();
            $this->assertEquals('carousel/slide1.jpg', $slide->image_path);
        }
    }

    /**
     * Test the app:offload-files command.
     */
    public function test_offload_files_artisan_command()
    {
        Storage::fake('public');
        Storage::fake('s3');

        // Insert comment with older attachment
        $commentId = DB::table('comments')->insertGetId([
            'commentable_type' => 'App\Models\NewsArticle',
            'commentable_id' => 1,
            'user_id' => null,
            'body' => 'Test comment',
            'attachment_path' => 'comments/attach.pdf',
            'is_staff' => false,
            'created_at' => now()->subDays(45),
            'updated_at' => now()->subDays(45),
        ]);

        Storage::disk('public')->put('comments/attach.pdf', 'dummy file content');

        // Run offloader command
        Artisan::call('app:offload-files', [
            '--disk' => 's3',
            '--age' => 30,
        ]);

        // Assert local file is deleted, and cloud file exists
        Storage::disk('public')->assertMissing('comments/attach.pdf');
        Storage::disk('s3')->assertExists('comments/attach.pdf');

        // Assert database record path is updated to S3 URL
        $comment = DB::table('comments')->where('id', $commentId)->first();
        $this->assertEquals(Storage::disk('s3')->url('comments/attach.pdf'), $comment->attachment_path);
    }
}
