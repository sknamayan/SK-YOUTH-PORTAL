<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class CompressImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:compress-images {--dry-run : Run the command without saving changes or updating the database}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scan public storage for heavy images (JPEG, PNG), compress them to WebP, and update database references.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        if ($dryRun) {
            $this->info('[DRY RUN] Running in simulation mode. No files will be modified.');
        }

        $publicPath = storage_path('app/public');
        if (!is_dir($publicPath)) {
            $this->error("Public storage directory does not exist: {$publicPath}");
            return 1;
        }

        $this->info("Scanning public storage: {$publicPath}...");

        $directory = new RecursiveDirectoryIterator($publicPath);
        $iterator = new RecursiveIteratorIterator($directory);
        $filesProcessed = 0;
        $totalSavings = 0;
        $dbUpdatesCount = 0;

        foreach ($iterator as $fileInfo) {
            if ($fileInfo->isDir()) {
                continue;
            }

            $filePath = $fileInfo->getRealPath();
            $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

            if (!in_array($extension, ['jpg', 'jpeg', 'png'])) {
                continue;
            }

            $originalSize = filesize($filePath);
            
            // Skip extremely small files
            if ($originalSize < 1024) {
                continue;
            }

            // Get relative path for database matching (e.g. news/abc.jpg)
            $relativePath = str_replace($publicPath . DIRECTORY_SEPARATOR, '', $filePath);
            $relativePath = str_replace('\\', '/', $relativePath);

            $this->comment("Processing: {$relativePath} (" . number_format($originalSize / 1024, 2) . " KB)...");

            // Convert image to WebP
            $image = null;
            if ($extension === 'jpg' || $extension === 'jpeg') {
                $image = @imagecreatefromjpeg($filePath);
            } elseif ($extension === 'png') {
                $image = @imagecreatefrompng($filePath);
            }

            if (!$image) {
                $this->warn("Failed to read image: {$relativePath}");
                continue;
            }

            $webpPath = pathinfo($filePath, PATHINFO_DIRNAME) . DIRECTORY_SEPARATOR . pathinfo($filePath, PATHINFO_FILENAME) . '.webp';
            $webpRelativePath = pathinfo($relativePath, PATHINFO_DIRNAME);
            if ($webpRelativePath === '.') {
                $webpRelativePath = pathinfo($relativePath, PATHINFO_FILENAME) . '.webp';
            } else {
                $webpRelativePath = $webpRelativePath . '/' . pathinfo($relativePath, PATHINFO_FILENAME) . '.webp';
            }

            $compressionSuccess = false;
            if (!$dryRun) {
                // Convert to WebP and save with 80% quality
                $compressionSuccess = @imagewebp($image, $webpPath, 80);
            } else {
                $compressionSuccess = true;
            }

            imagedestroy($image);

            if (!$compressionSuccess) {
                $this->error("Failed to write WebP image for: {$relativePath}");
                continue;
            }

            $newSize = $dryRun ? $originalSize * 0.3 : filesize($webpPath); // estimate 70% savings in dry run
            $savings = $originalSize - $newSize;
            $totalSavings += $savings;
            $filesProcessed++;

            $this->info("Compressed: {$relativePath} -> " . pathinfo($relativePath, PATHINFO_FILENAME) . ".webp (" . number_format($newSize / 1024, 2) . " KB, " . number_format(($savings / $originalSize) * 100, 1) . "% saved)");

            // Delete original file and update database
            if (!$dryRun) {
                @unlink($filePath);
                $dbUpdatesCount += $this->updateDatabaseReferences($relativePath, $webpRelativePath);
            } else {
                $this->info("[DRY RUN] Would delete: {$relativePath} and update database references.");
            }
        }

        $this->info("--------------------------------------------------");
        $this->info("Execution Complete!");
        $this->info("Total Files Processed: {$filesProcessed}");
        $this->info("Total Storage Saved: " . number_format($totalSavings / (1024 * 1024), 2) . " MB");
        if (!$dryRun) {
            $this->info("Total Database Records Updated: {$dbUpdatesCount}");
        }

        return 0;
    }

    /**
     * Update database columns that point to the original image path.
     */
    protected function updateDatabaseReferences(string $oldPath, string $newPath): int
    {
        $updated = 0;

        // 1. Carousel Slides
        $updated += DB::table('carousel_slides')
            ->where('image_path', $oldPath)
            ->update(['image_path' => $newPath]);

        // 2. News Articles
        $updated += DB::table('news_articles')
            ->where('image_path', $oldPath)
            ->update(['image_path' => $newPath]);

        // 3. SK Officials
        $updated += DB::table('sk_officials')
            ->where('photo_path', $oldPath)
            ->update(['photo_path' => $newPath]);

        // 4. Partners
        $updated += DB::table('partners')
            ->where('logo_path', $oldPath)
            ->update(['logo_path' => $newPath]);

        // 5. Transparency Posts
        $updated += DB::table('transparency_posts')
            ->where('image_path', $oldPath)
            ->update(['image_path' => $newPath]);
        $updated += DB::table('transparency_posts')
            ->where('file_path', $oldPath)
            ->update(['file_path' => $newPath]);

        // 6. Comments
        $updated += DB::table('comments')
            ->where('attachment_path', $oldPath)
            ->update(['attachment_path' => $newPath]);

        // 7. Sports / Dynamic Registration Responses (JSON column answers)
        $responses = DB::table('registration_responses')
            ->where('answers', 'like', '%' . $oldPath . '%')
            ->get();

        foreach ($responses as $response) {
            $answers = json_decode($response->answers, true);
            if (is_array($answers)) {
                array_walk_recursive($answers, function (&$value) use ($oldPath, $newPath) {
                    if ($value === $oldPath) {
                        $value = $newPath;
                    }
                });
                
                DB::table('registration_responses')
                    ->where('id', $response->id)
                    ->update(['answers' => json_encode($answers)]);
                $updated++;
            }
        }

        return $updated;
    }
}
