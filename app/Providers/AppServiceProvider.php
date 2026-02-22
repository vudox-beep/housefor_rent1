<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);

        if (env('APP_ENV') !== 'local') {
            URL::forceScheme('https');
        }
        // Force HTTPS for Herd/Valet even in local if using .test domain with SSL
        if (str_contains(config('app.url'), 'https://')) {
            URL::forceScheme('https');
        }

        // Ensure storage symlink exists for public image access
        $link = public_path('storage');
        $target = storage_path('app/public');
        
        // Create target directories if they don't exist
        if (!is_dir($target)) {
            mkdir($target, 0755, true);
        }
        if (!is_dir(storage_path('app/public/listings'))) {
            mkdir(storage_path('app/public/listings'), 0755, true);
        }
        if (!is_dir(storage_path('app/public/videos'))) {
            mkdir(storage_path('app/public/videos'), 0755, true);
        }

        // Create symlink if it doesn't exist
        if (!is_link($link) && !is_dir($link)) {
            try {
                symlink($target, $link);
            } catch (\Exception $e) {
                // Symlink creation failed - may need manual php artisan storage:link
            }
        }
    }
}
