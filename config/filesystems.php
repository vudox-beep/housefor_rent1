<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application for file storage.
    |
    */

    'default' => env('FILESYSTEM_DISK', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Below you may configure as many filesystem disks as necessary, and you
    | may even configure multiple disks for the same driver. Examples for
    | most supported storage drivers are configured here for reference.
    |
    | Supported drivers: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app/private'),
            'serve' => true,
            'throw' => false,
            'report' => false,
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => rtrim(env('APP_URL', 'http://localhost'), '/').'/storage',
            'visibility' => 'public',
            'throw' => false,
            'report' => false,
        ],

        // Laravel Cloud Object Storage will auto-configure this disk
        // No manual AWS credentials needed - Laravel Cloud manages everything
        // Just reference the disk name you selected in the Infrastructure Canvas
        // Example: if you named it 'r2' or 'properties-storage'

        // Laravel Cloud Object Storage - S3-compatible configuration
        // Laravel Cloud automatically provides these environment variables:
        // - LARAVEL_CLOUD_OBJECT_STORAGE_KEY (AWS_ACCESS_KEY_ID)
        // - LARAVEL_CLOUD_OBJECT_STORAGE_SECRET (AWS_SECRET_ACCESS_KEY) 
        // - LARAVEL_CLOUD_OBJECT_STORAGE_BUCKET (AWS_BUCKET)
        // - LARAVEL_CLOUD_OBJECT_STORAGE_REGION (AWS_DEFAULT_REGION)
        // - LARAVEL_CLOUD_OBJECT_STORAGE_ENDPOINT (AWS_ENDPOINT)
        'uploads' => [
            'driver' => 's3',
            'key' => env('LARAVEL_CLOUD_OBJECT_STORAGE_KEY', env('AWS_ACCESS_KEY_ID')),
            'secret' => env('LARAVEL_CLOUD_OBJECT_STORAGE_SECRET', env('AWS_SECRET_ACCESS_KEY')),
            'region' => env('LARAVEL_CLOUD_OBJECT_STORAGE_REGION', env('AWS_DEFAULT_REGION', 'auto')),
            'bucket' => env('LARAVEL_CLOUD_OBJECT_STORAGE_BUCKET', env('AWS_BUCKET')),
            'url' => env('LARAVEL_CLOUD_OBJECT_STORAGE_URL', env('AWS_URL')),
            'endpoint' => env('LARAVEL_CLOUD_OBJECT_STORAGE_ENDPOINT', env('AWS_ENDPOINT')),
            'use_path_style_endpoint' => false,
            'visibility' => 'public',
            'throw' => false,
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION', 'auto'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => true,
            'visibility' => 'private',
            'throw' => false,
        ],



    ],

    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    |
    | Here you may configure the symbolic links that will be created when the
    | `storage:link` Artisan command is executed. The array keys should be
    | the locations of the links and the values should be their targets.
    |
    */

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];
