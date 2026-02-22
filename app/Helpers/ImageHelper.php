<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;

class ImageHelper
{
    /**
     * Get the URL for an image stored on S3 or local storage
     * 
     * @param string $path
     * @return string
     */
    public static function getImageUrl($path)
    {
        if (!$path) {
            return '';
        }

        // If it's S3 path (contains 'properties/' or 'videos/' prefix)
        if (strpos($path, 'properties/') === 0 || strpos($path, 'videos/') === 0) {
            return Storage::disk('s3')->url($path);
        }

        // If it's old local storage path format (storage/listings/...)
        if (strpos($path, 'storage/') === 0) {
            return asset($path);
        }

        // Default fallback
        return asset($path);
    }
}
