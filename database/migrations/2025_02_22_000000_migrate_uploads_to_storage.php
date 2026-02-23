<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('listings')) {
            return;
        }

        // Update all listing image paths from uploads/ to storage/
        DB::table('listings')->orderBy('id')->chunk(100, function ($listings) {
            foreach ($listings as $listing) {
                if (!empty($listing->images)) {
                    $images = json_decode($listing->images, true);
                    if (is_array($images)) {
                        $updated = false;
                        foreach ($images as &$image) {
                            if (strpos($image, 'uploads/listings/') === 0) {
                                $image = str_replace('uploads/listings/', 'storage/listings/', $image);
                                $updated = true;
                            }
                            if (strpos($image, 'uploads/videos/') === 0) {
                                $image = str_replace('uploads/videos/', 'storage/videos/', $image);
                                $updated = true;
                            }
                        }
                        
                        if ($updated) {
                            DB::table('listings')
                                ->where('id', $listing->id)
                                ->update(['images' => json_encode($images)]);
                        }
                    }
                }

                // Update video paths
                if (!empty($listing->video_path)) {
                    $videoPath = $listing->video_path;
                    if (strpos($videoPath, 'uploads/videos/') === 0) {
                        $videoPath = str_replace('uploads/videos/', 'storage/videos/', $videoPath);
                        DB::table('listings')
                            ->where('id', $listing->id)
                            ->update(['video_path' => $videoPath]);
                    }
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('listings')) {
            return;
        }

        // Revert paths back to uploads/
        DB::table('listings')->orderBy('id')->chunk(100, function ($listings) {
            foreach ($listings as $listing) {
                if (!empty($listing->images)) {
                    $images = json_decode($listing->images, true);
                    if (is_array($images)) {
                        $updated = false;
                        foreach ($images as &$image) {
                            if (strpos($image, 'storage/listings/') === 0) {
                                $image = str_replace('storage/listings/', 'uploads/listings/', $image);
                                $updated = true;
                            }
                            if (strpos($image, 'storage/videos/') === 0) {
                                $image = str_replace('storage/videos/', 'uploads/videos/', $image);
                                $updated = true;
                            }
                        }
                        
                        if ($updated) {
                            DB::table('listings')
                                ->where('id', $listing->id)
                                ->update(['images' => json_encode($images)]);
                        }
                    }
                }

                // Update video paths
                if (!empty($listing->video_path)) {
                    $videoPath = $listing->video_path;
                    if (strpos($videoPath, 'storage/videos/') === 0) {
                        $videoPath = str_replace('storage/videos/', 'uploads/videos/', $videoPath);
                        DB::table('listings')
                            ->where('id', $listing->id)
                            ->update(['video_path' => $videoPath]);
                    }
                }
            }
        });
    }
};
