<?php

namespace App\Traits;

use App\Services\FileUploadService;
use Illuminate\Http\UploadedFile;

trait OptimizesFileUploads
{
    /**
     * Compress, resize and store an uploaded file to public storage.
     *
     * @param UploadedFile $file
     * @param string $directory
     * @param string $disk
     * @return string
     */
    protected function storeOptimizedFile(UploadedFile $file, string $directory, string $disk = 'public'): string
    {
        return FileUploadService::storeOptimized($file, $directory, $disk);
    }
}
