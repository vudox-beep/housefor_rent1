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

            // Otherwise it's a cloud disk path (Laravel Cloud 'uploads' disk)
            // Try the 'uploads' disk first (Laravel Cloud Object Storage)
            try {
                $url = Storage::disk('uploads')->url($path);
                if ($url) {
                    return $url;
                }
            } catch (\Exception $e) {
                Log::debug('uploads disk not available: ' . $e->getMessage());
            }
            
            // Fallback to asset for local storage
            return asset($path);
        } catch (\Throwable $e) {
            // If any error occurs, fallback to asset path
            Log::warning('imageUrl helper error: ' . $e->getMessage());
            return asset($path);
        }
    }
}
