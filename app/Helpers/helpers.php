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
                
                // Generate URL for R2 object storage using Laravel Cloud's URL pattern
                 $bucketName = env('AWS_BUCKET');
                 $region = env('AWS_DEFAULT_REGION', 'auto');
                 
                 // Construct R2 URL format: https://<bucket>.<account-id>.r2.cloudflarestorage.com/<path>
                 // For Laravel Cloud, they might use a different pattern, so fallback to asset() if needed
                 if ($bucketName) {
                     return "https://{$bucketName}.r2.cloudflarestorage.com/{$cleanPath}";
                 }
                 
                 // Fallback: use asset() with the clean path
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
