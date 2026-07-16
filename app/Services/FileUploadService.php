<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileUploadService
{
    /**
     * Compress and store an uploaded file.
     * Automatically converts JPG/PNG images to WebP and resizes them if they exceed maximum bounds.
     * Non-image files are stored directly without modification.
     *
     * @param UploadedFile $file
     * @param string $directory
     * @param string $disk
     * @param int $maxDimension
     * @param int $quality
     * @return string
     */
    public static function storeOptimized(
        UploadedFile $file,
        string $directory,
        string $disk = 'public',
        int $maxDimension = 1600,
        int $quality = 80
    ): string {
        if (!extension_loaded('gd')) {
            return $file->store($directory, $disk);
        }

        $extension = strtolower($file->getClientOriginalExtension());
        $mimeType = $file->getMimeType();

        // Check if the uploaded file is a compressible image format
        if (in_array($extension, ['jpg', 'jpeg', 'png', 'webp']) || str_starts_with($mimeType, 'image/')) {
            $imagePath = $file->getRealPath();

            // Read image resource depending on format
            $image = null;
            if ($extension === 'jpg' || $extension === 'jpeg' || $mimeType === 'image/jpeg') {
                $image = @imagecreatefromjpeg($imagePath);
            } elseif ($extension === 'png' || $mimeType === 'image/png') {
                $image = @imagecreatefrompng($imagePath);
            } elseif ($extension === 'webp' || $mimeType === 'image/webp') {
                $image = @imagecreatefromwebp($imagePath);
            }

            if ($image) {
                // Get original dimensions
                $width = imagesx($image);
                $height = imagesy($image);

                // Perform resizing if dimensions exceed maximum bounds
                if ($width > $maxDimension || $height > $maxDimension) {
                    if ($width > $height) {
                        $newWidth = $maxDimension;
                        $newHeight = (int) ($height * ($maxDimension / $width));
                    } else {
                        $newHeight = $maxDimension;
                        $newWidth = (int) ($width * ($maxDimension / $height));
                    }

                    $resizedImage = imagecreatetruecolor($newWidth, $newHeight);

                    // Preserve alpha transparency for PNGs/WebPs
                    imagealphablending($resizedImage, false);
                    imagesavealpha($resizedImage, true);

                    imagecopyresampled($resizedImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                    imagedestroy($image);
                    $image = $resizedImage;
                }

                // Generate a randomized WebP file name
                $filename = Str::random(40) . '.webp';
                $tempFile = tempnam(sys_get_temp_dir(), 'optimized_');

                // Convert and save image to temporary WebP format
                if (@imagewebp($image, $tempFile, $quality)) {
                    imagedestroy($image);

                    $targetPath = $directory . '/' . $filename;
                    Storage::disk($disk)->put($targetPath, file_get_contents($tempFile));
                    @unlink($tempFile);

                    return $targetPath;
                }

                // Cleanup memory on failure
                imagedestroy($image);
                if (file_exists($tempFile)) {
                    @unlink($tempFile);
                }
            }
        }

        // Default fallback: store unmodified
        return $file->store($directory, $disk);
    }
}
