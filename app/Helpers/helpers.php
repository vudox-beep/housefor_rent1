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
            
            // If we're on Laravel Cloud production OR forcing cloud storage locally
            if (($isProduction && str_contains($appUrl, '.laravel.cloud')) || env('FORCE_CLOUD_STORAGE', false)) {
                // Get clean path without 'storage/' prefix
                $cleanPath = strpos($path, 'storage/') === 0 ? 
                    str_replace('storage/', '', $path) : $path;
                
                try {
                    // Use temporary signed URLs for secure, time-limited access
                    // This is required for private R2 buckets on Laravel Cloud
                    /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
                    $disk = Storage::disk('uploads');
                    
                    return $disk->temporaryUrl(
                        $cleanPath,
                        now()->addMinutes(60)
                    );
                } catch (\Throwable $e) {
                    // Log the error but try to return a public URL as fallback
                    Log::error('R2 temporaryUrl generation failed: ' . $e->getMessage());
                    
                    // Fallback to direct URL if signed URL fails (unlikely to work for private buckets)
                    /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
                    $disk = Storage::disk('uploads');
                    return $disk->url($cleanPath);
                }
            }

            // For local development, use local storage with asset()
            if (strpos($path, 'storage/') !== 0) {
                return asset('storage/' . $path);
            }
            
            return asset($path);
        } catch (\Throwable $e) {
            Log::warning('imageUrl helper error: ' . $e->getMessage());
            
            if (strpos($path, 'storage/') !== 0) {
                return asset('storage/' . $path);
            }
            return asset($path);
        }
    }
}
