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
            $isProduction = env('APP_ENV') === 'production';
            $appUrl = env('APP_URL', '');
            
            if (($isProduction && str_contains($appUrl, '.laravel.cloud')) || env('FORCE_CLOUD_STORAGE', false)) {
                $cleanPath = strpos($path, 'storage/') === 0 ? 
                    str_replace('storage/', '', $path) : $path;
                
                try {
                    $disk = Storage::disk('uploads');
                    if ($disk instanceof \Illuminate\Filesystem\FilesystemAdapter && method_exists($disk, 'temporaryUrl')) {
                        return $disk->temporaryUrl(
                            $cleanPath,
                            now()->addMinutes(60)
                        );
                    }

                    if ($disk instanceof \Illuminate\Filesystem\FilesystemAdapter && method_exists($disk, 'url')) {
                        return $disk->url($cleanPath);
                    }

                    return '';
                } catch (\Throwable $e) {
                    Log::error('R2 temporaryUrl generation failed: ' . $e->getMessage());
                    $disk = Storage::disk('uploads');
                    if ($disk instanceof \Illuminate\Filesystem\FilesystemAdapter && method_exists($disk, 'url')) {
                        return $disk->url($cleanPath);
                    }

                    return '';
                }
            }

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
