<?php

namespace App\Traits;

trait HasOptimizedStorage
{
    /**
     * Resolve a storage path attribute to a fully qualified URL.
     *
     * @param string|null $path
     * @return string|null
     */
    public function getStorageUrl(?string $path): ?string
    {
        return storage_url($path);
    }
}
