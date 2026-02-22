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
            // On Laravel Cloud, use signed URLs for R2 object storage
            $isProduction = env('APP_ENV') === 'production';
            $appUrl = env('APP_URL', '');
            
            // If we're on Laravel Cloud production, generate signed URLs for R2 storage
            if ($isProduction && str_contains($appUrl, '.laravel.cloud')) {
                // Get clean path without 'storage/' prefix
                $cleanPath = strpos($path, 'storage/') === 0 ? 
                    str_replace('storage/', '', $path) : $path;
                
                // Generate public URL for R2 object storage (bucket is set to Public)
                 // Since bucket is public, we can use direct URL construction
                 $bucketName = env('AWS_BUCKET');
                 $endpoint = env('AWS_ENDPOINT');
                 
                 if ($bucketName && $endpoint) {
                     // Use the endpoint URL directly for public bucket access
                     return rtrim($endpoint, '/') . '/' . $cleanPath;
                 } elseif ($bucketName) {
                     // Fallback: construct R2 URL format
                     return "https://{$bucketName}.r2.cloudflarestorage.com/{$cleanPath}";
                 }
                 
                 // Final fallback: use asset() with the clean path
                 return asset('storage/' . $cleanPath);
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
