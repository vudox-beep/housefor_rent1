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
                // For cloud storage paths (without 'storage/' prefix), construct full URL
                if (strpos($path, 'storage/') !== 0) {
                    return rtrim($appUrl, '/') . '/storage/' . $path;
                }
                // If it already has storage/ prefix, use asset()
                return asset($path);
            }

            // For local development, check if path already has storage/ prefix
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
