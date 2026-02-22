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
            // On Laravel Cloud, use Laravel Cloud's auto-configured storage URLs
            $isProduction = env('APP_ENV') === 'production';
            $appUrl = env('APP_URL', '');
            
            // If we're on Laravel Cloud production, use Laravel Cloud's storage system
            if ($isProduction && str_contains($appUrl, '.laravel.cloud')) {
                // Laravel Cloud automatically serves files from object storage
                // The path should be relative to the storage root
                if (strpos($path, 'storage/') === 0) {
                    // Remove 'storage/' prefix for cloud storage
                    $cleanPath = str_replace('storage/', '', $path);
                    return rtrim($appUrl, '/') . '/storage/' . $cleanPath;
                }
                // If no storage/ prefix, assume it's already a cloud path
                return rtrim($appUrl, '/') . '/storage/' . $path;
            }

            // For local development, use local storage with asset()
            if (strpos($path, 'storage/') !== 0) {
                return asset('storage/' . $path);
            }
            
            return asset($path);
        } catch (\Throwable $e) {
            // If any error occurs, fallback to asset path
            Log::warning('imageUrl helper error: ' . $e->getMessage());
            
            // Try to construct a URL regardless
            if (strpos($path, 'storage/') !== 0) {
                return asset('storage/' . $path);
            }
            return asset($path);
        }
    }
}
