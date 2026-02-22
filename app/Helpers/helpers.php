<?php

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Config;

if (!function_exists('imageUrl')) {
    /**
     * Get the URL for an image stored on S3, R2, or local storage
     * Supports Cloudflare R2, Laravel Cloud object storage, and local storage
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
            $defaultDisk = Config::get('filesystems.default');
            
            // If it's a cloud storage path (S3 or R2 format: properties/... or videos/...)
            if (strpos($path, 'properties/') === 0 || strpos($path, 'videos/') === 0) {
                // Use configured cloud disk if credentials exist
                if (in_array($defaultDisk, ['s3', 'r2']) && env('AWS_ACCESS_KEY_ID')) {
                    return Storage::disk($defaultDisk)->url($path);
                }
                // Fallback for S3/R2 paths when not configured
                return asset('storage/' . $path);
            }

            // If it's old local storage path format (storage/listings/... or storage/videos/...)
            if (strpos($path, 'storage/') === 0) {
                return asset($path);
            }

            // Default fallback
            return asset($path);
        } catch (\Throwable $e) {
            // If any error occurs, fallback to asset path
            \Illuminate\Support\Facades\Log::warning('imageUrl helper error: ' . $e->getMessage());
            return asset($path);
        }
    }
}
