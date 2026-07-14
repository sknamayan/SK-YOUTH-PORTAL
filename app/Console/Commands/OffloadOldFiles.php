<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class OffloadOldFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:offload-files 
                            {--disk=s3 : The target cloud storage disk configured in config/filesystems.php}
                            {--age=30 : The minimum age of records in days to be considered old/resolved}
                            {--dry-run : Run the migration as a simulation without actually moving files or updating the database}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate attachments of old or resolved records to cloud storage to free up local disk space.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $cloudDiskName = $this->option('disk');
        $ageDays = intval($this->option('age'));
        $dryRun = $this->option('dry-run');

        $this->info("Initializing Cloud Offloader Strategy...");
        $this->info("Target Cloud Disk: {$cloudDiskName}");
        $this->info("Age Threshold: {$ageDays} days");

        if ($dryRun) {
            $this->info("[DRY RUN] Simulation mode active. No changes will be written.");
        }

        // Check if the cloud disk is configured in Laravel filesystems
        if (!config("filesystems.disks.{$cloudDiskName}")) {
            $this->error("The storage disk '{$cloudDiskName}' is not defined in config/filesystems.php.");
            $this->comment("Please configure the disk details (AWS_BUCKET, AWS_KEY, etc.) in your .env file.");
            return 1;
        }

        $localDisk = Storage::disk('public');
        $cloudDisk = Storage::disk($cloudDiskName);

        $migratedCount = 0;
        $failedCount = 0;
        $freedBytes = 0;

        $cutoffDate = now()->subDays($ageDays);

        // 1. Offload Accomplishment Reports (older than threshold)
        $reports = DB::table('accomplishment_reports')
            ->where('created_at', '<', $cutoffDate)
            ->whereNotNull('file_path')
            ->where('file_path', 'not like', 'http%')
            ->get();

        foreach ($reports as $report) {
            $this->comment("Offloading Accomplishment Report ID: {$report->id} -> {$report->file_path}");
            if ($this->migrateFile($report->file_path, 'reports', $localDisk, $cloudDisk, $dryRun)) {
                $cloudUrl = $cloudDisk->url('reports/' . basename($report->file_path));
                if (!$dryRun) {
                    DB::table('accomplishment_reports')
                        ->where('id', $report->id)
                        ->update(['file_path' => $cloudUrl]);
                }
                $migratedCount++;
            } else {
                $failedCount++;
            }
        }

        // 2. Offload Transparency Posts documents (older than threshold)
        $posts = DB::table('transparency_posts')
            ->where('created_at', '<', $cutoffDate)
            ->where(function($query) {
                $query->whereNotNull('file_path')->where('file_path', 'not like', 'http%')
                      ->orWhereNotNull('image_path')->where('image_path', 'not like', 'http%');
            })
            ->get();

        foreach ($posts as $post) {
            if ($post->file_path && !str_starts_with($post->file_path, 'http')) {
                $this->comment("Offloading Transparency Document ID: {$post->id} -> {$post->file_path}");
                if ($this->migrateFile($post->file_path, 'transparency', $localDisk, $cloudDisk, $dryRun)) {
                    $cloudUrl = $cloudDisk->url('transparency/' . basename($post->file_path));
                    if (!$dryRun) {
                        DB::table('transparency_posts')
                            ->where('id', $post->id)
                            ->update(['file_path' => $cloudUrl]);
                    }
                    $migratedCount++;
                } else {
                    $failedCount++;
                }
            }

            if ($post->image_path && !str_starts_with($post->image_path, 'http')) {
                $this->comment("Offloading Transparency Image ID: {$post->id} -> {$post->image_path}");
                if ($this->migrateFile($post->image_path, 'transparency', $localDisk, $cloudDisk, $dryRun)) {
                    $cloudUrl = $cloudDisk->url('transparency/' . basename($post->image_path));
                    if (!$dryRun) {
                        DB::table('transparency_posts')
                            ->where('id', $post->id)
                            ->update(['image_path' => $cloudUrl]);
                    }
                    $migratedCount++;
                } else {
                    $failedCount++;
                }
            }
        }

        // 3. Offload Comment attachments (associated with older comments)
        $comments = DB::table('comments')
            ->where('created_at', '<', $cutoffDate)
            ->whereNotNull('attachment_path')
            ->where('attachment_path', 'not like', 'http%')
            ->get();

        foreach ($comments as $comment) {
            $this->comment("Offloading Comment Attachment ID: {$comment->id} -> {$comment->attachment_path}");
            if ($this->migrateFile($comment->attachment_path, 'comments', $localDisk, $cloudDisk, $dryRun)) {
                $cloudUrl = $cloudDisk->url('comments/' . basename($comment->attachment_path));
                if (!$dryRun) {
                    DB::table('comments')
                        ->where('id', $comment->id)
                        ->update(['attachment_path' => $cloudUrl]);
                }
                $migratedCount++;
            } else {
                $failedCount++;
            }
        }

        // 4. Offload Sports and Dynamic registration response attachments
        $responses = DB::table('registration_responses')
            ->where('created_at', '<', $cutoffDate)
            ->where('answers', 'like', '%sports-registrations%')
            ->get();

        foreach ($responses as $response) {
            $answers = json_decode($response->answers, true);
            if (is_array($answers)) {
                $updatedAnswers = false;
                foreach ($answers as $key => $value) {
                    if (is_string($value) && str_contains($value, 'sports-registrations') && !str_starts_with($value, 'http')) {
                        $this->comment("Offloading Sports Form File ID {$response->id} field '{$key}': {$value}");
                        if ($this->migrateFile($value, 'sports-registrations', $localDisk, $cloudDisk, $dryRun)) {
                            $cloudUrl = $cloudDisk->url('sports-registrations/' . basename($value));
                            $answers[$key] = $cloudUrl;
                            $updatedAnswers = true;
                            $migratedCount++;
                        } else {
                            $failedCount++;
                        }
                    }
                }

                if ($updatedAnswers && !$dryRun) {
                    DB::table('registration_responses')
                        ->where('id', $response->id)
                        ->update(['answers' => json_encode($answers)]);
                }
            }
        }

        $this->info("--------------------------------------------------");
        $this->info("Cloud Offloading Completed!");
        $this->info("Migrated Records: {$migratedCount}");
        $this->info("Failed / Skipped: {$failedCount}");

        return 0;
    }

    /**
     * Upload a file to cloud storage, verify upload, and delete local copy.
     */
    protected function migrateFile(string $relativePath, string $folder, $localDisk, $cloudDisk, bool $dryRun): bool
    {
        if (!$localDisk->exists($relativePath)) {
            $this->warn("Local file not found on disk: {$relativePath}");
            return false;
        }

        $fileContents = $localDisk->get($relativePath);
        $fileName = basename($relativePath);
        $cloudPath = $folder . '/' . $fileName;

        try {
            if (!$dryRun) {
                // Upload to Cloud disk
                $cloudDisk->put($cloudPath, $fileContents, 'public');

                // Verify file upload succeeded and matches size
                if ($cloudDisk->exists($cloudPath)) {
                    // Delete local copy
                    $localDisk->delete($relativePath);
                    return true;
                } else {
                    $this->error("Verification failed: file not found on cloud disk after upload: {$cloudPath}");
                    return false;
                }
            } else {
                $this->info("[DRY RUN] Would copy {$relativePath} to cloud as {$cloudPath} and remove local file.");
                return true;
            }
        } catch (\Exception $e) {
            $this->error("Failed to migrate file '{$relativePath}': " . $e->getMessage());
            return false;
        }
    }
}
