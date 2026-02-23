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
            // Check if it's a full URL (e.g. from previous cloud uploads)
            if (filter_var($path, FILTER_VALIDATE_URL)) {
                return $path;
            }

            // If path already starts with storage/, just return the asset URL
            if (strpos($path, 'storage/') === 0) {
                return asset($path);
            }

            // If path doesn't start with storage/, assume it needs the prefix for local storage
            // This covers 'properties/...' and 'videos/...' stored in 'public' disk
            // but not saved with 'storage/' prefix in DB (though ListingController currently adds it)
            return asset('storage/' . $path);

        } catch (\Throwable $e) {
            Log::warning('imageUrl helper error: ' . $e->getMessage());
            return asset('storage/' . $path);
        }
    }
}
