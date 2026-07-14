<?php

if (!function_exists('storage_url')) {
    /**
     * Resolve a storage path to a fully qualified URL.
     * Supports local relative paths and absolute cloud storage URLs seamlessly.
     *
     * @param string|null $path
     * @param string $disk
     * @return string|null
     */
    function storage_url(?string $path, string $disk = 'public'): ?string
    {
        if (!$path) {
            return null;
        }

        // If it starts with http:// or https://, it's an offloaded external cloud link
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        // Default to local storage URL
        return asset('storage/' . $path);
    }
}
