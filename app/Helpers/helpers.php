<?php

use Illuminate\Support\Facades\Storage;

if (!function_exists('imageUrl')) {
    /**
     * Get the URL for an image stored on any disk
     * Supports Laravel Cloud Object Storage, S3, local storage, etc.
     * 
     * @param string $path
     * @return string
     */
    function imageUrl($path)
    {
        if (!$path) {
            return '';
        }

        try {
            // If it's a local storage path (contains 'storage/' prefix)
            if (strpos($path, 'storage/') === 0) {
                return asset($path);
            }

            // Otherwise it's a cloud disk path (Laravel Cloud, S3, etc.)
            // Use the configured disk from FILESYSTEM_DISK env
            $disk = env('FILESYSTEM_DISK', 'public');
            
            if ($disk !== 'local' && $disk !== 'public') {
                return Storage::disk($disk)->url($path);
            }
            
            // Fallback to asset
            return asset($path);
        } catch (\Throwable $e) {
            // If any error occurs, fallback to asset path
            \Illuminate\Support\Facades\Log::warning('imageUrl helper error: ' . $e->getMessage());
            return asset($path);
        }
    }
}
