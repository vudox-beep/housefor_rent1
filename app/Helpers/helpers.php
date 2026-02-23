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
                
                // Laravel Cloud Object Storage auto-configuration
                // Laravel Cloud provides these environment variables automatically:
                $laravelCloudBucket = env('LARAVEL_CLOUD_OBJECT_STORAGE_BUCKET');
                $laravelCloudEndpoint = env('LARAVEL_CLOUD_OBJECT_STORAGE_ENDPOINT');
                $awsBucket = env('AWS_BUCKET');
                $awsEndpoint = env('AWS_ENDPOINT');
                
                // Use Laravel Cloud variables first, then fallback to AWS variables
                $bucketName = $laravelCloudBucket ?: $awsBucket;
                $endpoint = $laravelCloudEndpoint ?: $awsEndpoint;
                
                if ($bucketName && $endpoint) {
                    // Use the endpoint provided by Laravel Cloud
                    return rtrim($endpoint, '/') . '/' . $cleanPath;
                } elseif ($bucketName === 'uploads') {
                    // If bucket name is 'uploads' (your bucket name), construct URL
                    return "https://uploads.r2.cloudflarestorage.com/{$cleanPath}";
                } elseif ($bucketName) {
                    // Fallback: construct R2 URL format
                    return "https://{$bucketName}.r2.cloudflarestorage.com/{$cleanPath}";
                }
                
                // Final fallback: use asset() with the clean path
                 
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
