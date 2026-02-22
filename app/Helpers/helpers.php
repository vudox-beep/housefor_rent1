<?php

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Config;

if (!function_exists('imageUrl')) {
    /**
     * Get the URL for an image stored on S3 or local storage
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
            // If it's S3 path (contains 'properties/' or 'videos/' prefix)
            if (strpos($path, 'properties/') === 0 || strpos($path, 'videos/') === 0) {
                // Check if S3 is configured
                if (Config::get('filesystems.default') === 's3' && env('AWS_ACCESS_KEY_ID')) {
                    return Storage::disk('s3')->url($path);
                }
                // Fallback to asset path if S3 not configured
                return asset('storage/' . $path);
            }

            // If it's old local storage path format (storage/listings/...)
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
