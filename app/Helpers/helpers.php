<?php

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

if (!function_exists('imageUrl')) {
    /**
     * Get the URL for an image stored on Laravel Cloud Object Storage or local disk
     * Supports 'uploads' disk (Laravel Cloud), local storage, etc.
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

            // On Laravel Cloud, use the direct URL construction for cloud storage
            $isProduction = env('APP_ENV') === 'production';
            $appUrl = env('APP_URL', '');
            
            // If we're on Laravel Cloud production, construct the cloud URL directly
            if ($isProduction && str_contains($appUrl, '.laravel.cloud')) {
                // Laravel Cloud Object Storage uses a specific URL pattern
                // The path should be the filename in the cloud bucket
                return rtrim($appUrl, '/') . '/storage/' . $path;
            }

            // For local development, use local storage path
            return asset('storage/' . $path);
        } catch (\Throwable $e) {
            // If any error occurs, fallback to asset path
            Log::warning('imageUrl helper error: ' . $e->getMessage());
            return asset('storage/' . $path);
        }
    }
}
