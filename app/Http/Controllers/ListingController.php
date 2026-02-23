<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ListingController extends Controller
{
    public function home()
    {
        // Get featured listings or just latest
        $listings = Listing::where('status', 'active')->latest()->take(6)->get();
        $recentListings = Listing::where('status', 'active')->latest()->paginate(12);
        
        return view('welcome', compact('listings', 'recentListings'));
    }

    public function index(Request $request)
    {
        $query = Listing::where('status', 'active');

        $searchTerm = trim((string) $request->query('search', ''));
        $country = strtolower(trim((string) $request->query('country', '')));
        $lat = $request->query('lat');
        $lng = $request->query('lng');
        $radius = (int) $request->query('radius', 25);

        if ($radius <= 0) {
            $radius = 25;
        }
        if ($radius > 200) {
            $radius = 200;
        }

        if ($country !== '') {
            $query->where('country', $country);
        }

        if ($searchTerm !== '') {
            $query->where(function($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                  ->orWhere('location', 'like', "%{$searchTerm}%")
                  ->orWhere('city', 'like', "%{$searchTerm}%");
            });
        }

        if ($lat !== null && $lng !== null && is_numeric($lat) && is_numeric($lng)) {
            $latNum = (float) $lat;
            $lngNum = (float) $lng;

            if ($latNum >= -90 && $latNum <= 90 && $lngNum >= -180 && $lngNum <= 180) {
                $query->whereNotNull('latitude')->whereNotNull('longitude')
                    ->select('listings.*')
                    ->selectRaw(
                        '(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance',
                        [$latNum, $lngNum, $latNum]
                    )
                    ->having('distance', '<=', $radius)
                    ->orderBy('distance');
            }
        }

        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }

        if ($request->has('category') && $request->category) {
            $query->where('category', $request->category);
        }

        if ($request->has('min_price') && $request->min_price) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->has('max_price') && $request->max_price) {
            $query->where('price', '<=', $request->max_price);
        }

        $listings = $query->latest()->paginate(10);

        return view('listings.index', compact('listings'));
    }

    public function show(Listing $listing)
    {
        $listing->load('user');
        
        // Increment views
        $listing->increment('views');

        return view('listings.show', compact('listing'));
    }

    public function create()
    {
        return view('listings.create');
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $maxImages = $user->maxListingImages();
        $canUploadVideo = $user->canUploadVideo();

        // Rules based on Subscription Plan
        if (is_array($request->file('images')) && count($request->file('images')) > $maxImages) {
            return back()->withErrors([
                'images' => 'Your account allows a maximum of ' . $maxImages . ' images per listing.',
            ])->withInput();
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|in:rent,buy',
            'category' => 'required|string',
            'price' => 'required|numeric',
            'currency' => 'required|in:ZMW,USD',
            'location' => 'required|string',
            'city' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:10',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'bedrooms' => 'nullable|integer',
            'bathrooms' => 'nullable|integer',
            'area' => 'nullable|numeric',
            'year_built' => 'nullable|integer',
            'previous_renters' => 'nullable|integer',
            'condition' => 'nullable|string',
            'images' => 'required|array|min:1|max:' . $maxImages,
            'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:6144',
            'video_url' => $canUploadVideo ? 'nullable|url' : 'nullable|string|in:',
            'video_file' => $canUploadVideo ? 'nullable|file|mimes:mp4,mov,webm|max:51200' : 'nullable',
            'agent_id' => 'nullable|exists:agents,id',
        ]);

        // Verify agent belongs to user if set
        if (!empty($validated['agent_id'])) {
            $agent = \App\Models\Agent::where('id', $validated['agent_id'])
                ->where('user_id', $user->id)
                ->first();
            
            if (!$agent) {
                return back()->withErrors(['agent_id' => 'Invalid agent selected.'])->withInput();
            }
        }

        if (!$canUploadVideo && $request->hasFile('video_file')) {
            return back()->withErrors([
                'video_file' => 'Your account does not allow video uploads.',
            ])->withInput();
        }

        $imagePaths = [];
        if ($request->hasFile('images')) {
            $images = $request->file('images');
            // Force limit to 1 for basic users if they somehow bypassed first check
            if ($maxImages === 1) {
                $images = array_slice($images, 0, 1);
            }
            
            foreach ($images as $image) {
                try {
                    $disk = $this->getStorageDisk();
                    
                    $path = $image->store('properties', ['disk' => $disk]);
                    
                    // For local disk, prefix with 'storage/' for asset() compatibility
                    if ($disk === 'public') {
                        $path = 'storage/' . $path;
                    }
                    
                    $imagePaths[] = $path;
                } catch (\Exception $e) {
                    Log::error('Image upload failed: ' . $e->getMessage());
                    return back()->withErrors([
                        'images' => 'Failed to upload image: ' . $e->getMessage()
                    ])->withInput();
                }
            }
        }

        $videoPath = null;
        if ($canUploadVideo && $request->hasFile('video_file')) {
            try {
                $videoFile = $request->file('video_file');
                $disk = $this->getStorageDisk();
                
                // Store video on configured disk
                $path = $videoFile->store('videos', ['disk' => $disk]);
                
                // For local disk, prefix with 'storage/' for asset() compatibility
                if ($disk === 'public') {
                    $path = 'storage/' . $path;
                }
                
                $videoPath = $path;
            } catch (\Exception $e) {
                Log::error('Video upload failed: ' . $e->getMessage());
                return back()->withErrors([
                    'video_file' => 'Failed to upload video: ' . $e->getMessage()
                ])->withInput();
            }
        } elseif ($canUploadVideo && !empty($validated['video_url'])) {
            $videoPath = $validated['video_url'];
        }

        $listing = $request->user()->listings()->create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'type' => $validated['type'],
            'category' => $validated['category'],
            'price' => $validated['price'],
            'currency' => $validated['currency'],
            'location' => $validated['location'],
            'city' => $validated['city'] ?? null,
            'country' => $validated['country'] ?? null,
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'bedrooms' => $validated['bedrooms'] ?? 0,
            'bathrooms' => $validated['bathrooms'] ?? 0,
            'area' => $validated['area'] ?? 0,
            'year_built' => $validated['year_built'] ?? null,
            'previous_renters' => $validated['previous_renters'] ?? 0,
            'condition' => $validated['condition'] ?? 'Good',
            'images' => $imagePaths,
            'video_path' => $canUploadVideo ? $videoPath : null,
            'agent_id' => $validated['agent_id'] ?? null,
            'status' => 'active',
            'views' => 0,
        ]);

        if (Auth::user()->isDealer()) {
            return redirect()->route('dealer.dashboard')->with('success', 'Listing posted successfully!');
        } elseif (Auth::user()->isAdmin()) {
            return redirect()->route('admin.dashboard')->with('success', 'Listing posted successfully!');
        }

        return redirect()->route('dashboard')->with('success', 'Listing posted successfully!');
    }

    public function edit(Listing $listing)
    {
        if ($listing->user_id !== Auth::id()) {
            abort(403);
        }
        return view('listings.edit', compact('listing'));
    }

    public function update(Request $request, Listing $listing)
    {
        if ($listing->user_id !== Auth::id()) {
            abort(403);
        }

        $user = Auth::user();
        $maxImages = $user->maxListingImages();
        $canUploadVideo = $user->canUploadVideo();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|in:rent,buy',
            'category' => 'required|string',
            'price' => 'required|numeric',
            'currency' => 'required|in:ZMW,USD',
            'location' => 'required|string',
            'city' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:10',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'bedrooms' => 'nullable|integer',
            'bathrooms' => 'nullable|integer',
            'area' => 'nullable|numeric',
            'year_built' => 'nullable|integer',
            'previous_renters' => 'nullable|integer',
            'condition' => 'nullable|string',
            'images' => 'nullable|array|max:' . $maxImages,
            'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:6144',
            'remove_images' => 'nullable|array',
            'remove_images.*' => 'string',
            'remove_video' => 'nullable|boolean',
            'video_url' => $canUploadVideo ? 'nullable|url' : 'nullable|string|in:',
            'video_file' => $canUploadVideo ? 'nullable|file|mimes:mp4,mov,webm|max:51200' : 'nullable',
            'agent_id' => 'nullable|exists:agents,id',
        ]);

        if (!empty($validated['agent_id'])) {
            $agent = \App\Models\Agent::where('id', $validated['agent_id'])
                ->where('user_id', $user->id)
                ->first();
            
            if (!$agent) {
                return back()->withErrors(['agent_id' => 'Invalid agent selected.'])->withInput();
            }
        }

        if (!$canUploadVideo && $request->hasFile('video_file')) {
            return back()->withErrors([
                'video_file' => 'Your account does not allow video uploads.',
            ])->withInput();
        }

        $existingImages = is_array($listing->images) ? $listing->images : [];
        
        // Handle removal logic robustly
        $removeImages = $validated['remove_images'] ?? [];
        $keptImages = [];

        foreach ($existingImages as $existing) {
            // Check if existing image is in removal list (loose comparison for robustness)
            $shouldRemove = false;
            foreach ($removeImages as $remove) {
                // 1. Direct equality check (most reliable if frontend sends exact path)
                if ($remove == $existing) {
                    $shouldRemove = true;
                    break;
                }

                // Decode HTML entities in the removal path (in case &amp; was passed)
                $decodedRemove = html_entity_decode($remove);
                
                // Compare raw paths
                if ($decodedRemove == $existing) {
                    $shouldRemove = true;
                    break;
                }
                
                // Compare with signed URL stripping (in case full URL was passed)
                $cleanRemove = explode('?', $decodedRemove)[0];
                
                // Decode URL-encoded characters (e.g. %20 -> space)
                $cleanRemove = urldecode($cleanRemove);
                
                // Handle temporaryUrl domain mismatch (if different R2 endpoint used)
                // Extract just the path component if it's a URL
                if (filter_var($cleanRemove, FILTER_VALIDATE_URL)) {
                    $urlPath = parse_url($cleanRemove, PHP_URL_PATH);
                    // Remove leading slash
                    $urlPath = ltrim($urlPath ?? '', '/');
                    if (str_ends_with($urlPath, $existing)) {
                        $shouldRemove = true;
                        break;
                    }
                }

                // Remove leading slash from both for comparison
                $normalizedCleanRemove = ltrim($cleanRemove, '/');
                $normalizedExisting = ltrim($existing, '/');
                
                if (str_contains($normalizedCleanRemove, $normalizedExisting)) {
                    $shouldRemove = true;
                    break;
                }
            }

            if (!$shouldRemove) {
                $keptImages[] = $existing;
            } else {
                // If it IS in the removal list, delete it
                try {
                    $disk = $this->getStorageDisk();
                    
                    if ($disk === 'uploads') {
                        // For cloud storage, try multiple path variations to ensure deletion
                        $candidates = $this->cloudDeleteCandidates($existing);
                        
                        // Also try to delete the exact path from remove list if it's a relative path
                        // This handles cases where $existing matches the DB but maybe file is stored differently?
                        // No, $existing IS the path stored in DB.
                        
                        // Let's add more candidates based on the REMOVE value itself
                        // This is crucial because sometimes the frontend sends a full URL that might differ slightly
                        // from what we derive from $existing
                        
                        foreach ($removeImages as $remove) {
                             $decodedRemove = html_entity_decode($remove);
                             $cleanRemove = explode('?', $decodedRemove)[0];
                             $cleanRemove = urldecode($cleanRemove);
                             
                             if (filter_var($cleanRemove, FILTER_VALIDATE_URL)) {
                                 $urlPath = parse_url($cleanRemove, PHP_URL_PATH);
                                 $urlPath = ltrim($urlPath ?? '', '/');
                                 if (str_ends_with($urlPath, $existing)) {
                                     // This removal request matches this existing image
                                     // Add this specific path as candidate too
                                     $candidates[] = $urlPath;
                                 }
                             }
                        }
                        
                        foreach ($candidates as $candidate) {
                            if (Storage::disk($disk)->exists($candidate)) {
                                Storage::disk($disk)->delete($candidate);
                            }
                        }
                    } elseif ($disk === 'public') {
                        // Local storage fallback
                        $storagePath = str_replace('storage/', 'app/public/', $existing);
                        if (file_exists(storage_path($storagePath))) {
                            unlink(storage_path($storagePath));
                        }
                    }
                } catch (\Exception $e) {
                    Log::warning('Failed to delete image: ' . $e->getMessage());
                }
            }
        }
        
        // Update the listing with kept images
        $listing->images = array_values($keptImages);
        // Explicitly set the attribute on the model, just to be sure
        $listing->setAttribute('images', array_values($keptImages));
        
        // Ensure changes are persisted. 
        // We do this by forcing the updateData to contain the new images array.
        // This ensures that when $listing->update($updateData) is called later, it uses our filtered array.
        $validated['images'] = array_values($keptImages);

        $newImagePaths = [];
        if ($request->hasFile('images')) {
            $images = $request->file('images');
            if ($maxImages === 1) {
                // If max images is 1, and we are uploading a new one, we should remove the old one(s)
                // The current logic clears $keptImages, which means the old image path is removed from DB
                // BUT we should also physically delete it if it wasn't already in remove_images
                
                foreach ($keptImages as $kept) {
                     try {
                        $disk = $this->getStorageDisk();
                        if ($disk === 'uploads') {
                             $candidates = $this->cloudDeleteCandidates($kept);
                             foreach ($candidates as $candidate) {
                                if (Storage::disk($disk)->exists($candidate)) {
                                    Storage::disk($disk)->delete($candidate);
                                }
                            }
                        } elseif ($disk === 'public') {
                             $storagePath = str_replace('storage/', 'app/public/', $kept);
                             if (file_exists(storage_path($storagePath))) {
                                unlink(storage_path($storagePath));
                            }
                        }
                     } catch (\Exception $e) {
                        Log::warning('Failed to delete old image for single-image replacement: ' . $e->getMessage());
                     }
                }
                
                $keptImages = [];
                $images = array_slice($images, 0, 1);
            }

            foreach ($images as $image) {
                try {
                    $disk = $this->getStorageDisk();
                    
                    $path = $image->store('properties', ['disk' => $disk]);
                    
                    // For local disk, prefix with 'storage/' for asset() compatibility
                    if ($disk === 'public') {
                        $path = 'storage/' . $path;
                    }
                    
                    $newImagePaths[] = $path;
                } catch (\Exception $e) {
                    Log::error('Image upload failed in update: ' . $e->getMessage());
                    return back()->withErrors([
                        'images' => 'Failed to upload image: ' . $e->getMessage()
                    ])->withInput();
                }
            }
        }

        $finalImages = $maxImages === 1 ? $newImagePaths : array_values(array_merge($keptImages, $newImagePaths));

        if (count($finalImages) < 1) {
            return back()->withErrors(['images' => 'Please keep at least 1 image for your listing.'])->withInput();
        }

        if (count($finalImages) > $maxImages) {
            return back()->withErrors([
                'images' => 'Your account allows a maximum of ' . $maxImages . ' images per listing.',
            ])->withInput();
        }

        $videoPath = null;
        if ($canUploadVideo) {
            $removeVideo = (bool) ($validated['remove_video'] ?? false);

            $existingVideo = (string) ($listing->video_path ?? '');
            if ($removeVideo) {
                try {
                    if ($existingVideo !== '') {
                        if ($disk === 'public') {
                            $storagePath = str_replace('storage/', 'app/public/', $existingVideo);
                            if (file_exists(storage_path($storagePath))) {
                                unlink(storage_path($storagePath));
                            }
                        } else {
                            foreach ($this->cloudDeleteCandidates($existingVideo) as $key) {
                                Storage::disk($disk)->delete($key);
                            }
                        }
                    }
                } catch (\Exception $e) {
                    Log::warning('Failed to delete video: ' . $e->getMessage());
                }

                $videoPath = null;
            } elseif ($request->hasFile('video_file')) {
                try {
                    if ($existingVideo !== '') {
                        if ($disk === 'public') {
                            $storagePath = str_replace('storage/', 'app/public/', $existingVideo);
                            if (file_exists(storage_path($storagePath))) {
                                unlink(storage_path($storagePath));
                            }
                        } else {
                            foreach ($this->cloudDeleteCandidates($existingVideo) as $key) {
                                Storage::disk($disk)->delete($key);
                            }
                        }
                    }

                    $videoFile = $request->file('video_file');
                    $path = $videoFile->store('videos', ['disk' => $disk]);
                    
                    // For local disk, prefix with 'storage/'
                    if ($disk === 'public') {
                        $path = 'storage/' . $path;
                    }
                    
                    $videoPath = $path;
                } catch (\Exception $e) {
                    Log::error('Video upload failed in update: ' . $e->getMessage());
                    return back()->withErrors([
                        'video_file' => 'Failed to upload video: ' . $e->getMessage()
                    ])->withInput();
                }
            } elseif (!empty($validated['video_url'])) {
                $videoPath = $validated['video_url'];
            } else {
                $videoPath = $listing->video_path;
            }
        }

        $updateData = $validated;
        unset($updateData['images'], $updateData['remove_images'], $updateData['video_file'], $updateData['video_url'], $updateData['remove_video']);
        // Re-assign images from our calculated array. 
        // This is crucial because $validated['images'] is filtered out above, 
        // and we need to ensure the final list (kept + new) is saved.
        $updateData['images'] = array_values($finalImages); 
        $updateData['video_path'] = $videoPath; 
        $updateData['agent_id'] = $validated['agent_id'] ?? null;

        $listing->update($updateData);

        if (Auth::user()->isDealer()) {
            return redirect()->route('dealer.dashboard')->with('success', 'Listing updated successfully.');
        }

        return redirect()->route('dashboard')->with('success', 'Listing updated successfully.');
    }

    public function destroy(Listing $listing)
    {
        if ($listing->user_id !== Auth::id()) {
            abort(403);
        }

        $listing->delete();

        if (Auth::user()->isDealer()) {
            return redirect()->route('dealer.dashboard')->with('success', 'Listing deleted successfully.');
        }

        return redirect()->route('dashboard')->with('success', 'Listing deleted successfully.');
    }

    /**
     * Determine which storage disk to use based on configuration
     * For Laravel Cloud: uses the 'uploads' disk when connected
     * For local development: falls back to 'public' if cloud disk not available
     */
    private function getStorageDisk()
    {
        // NEVER use local storage - always use cloud storage (Cloudflare R2/Laravel Cloud)
        // This ensures images persist and never disappear
        
        $isProduction = env('APP_ENV') === 'production';
        $appUrl = env('APP_URL', '');
        
        // On production (Laravel Cloud), cloud storage MUST work
        if ($isProduction && str_contains($appUrl, '.laravel.cloud')) {
            try {
                // Use the 'uploads' disk (Laravel Cloud Object Storage)
                // Laravel Cloud automatically configures S3-compatible storage
                return 'uploads';
            } catch (\Exception $e) {
                // Production requires cloud storage - throw error if it fails
                throw new \Exception('Cloud storage required for production: ' . $e->getMessage());
            }
        }
        
        // For local development, still prefer cloud storage if configured
        $cloudConfigured = env('AWS_ACCESS_KEY_ID') && env('AWS_SECRET_ACCESS_KEY') && env('AWS_BUCKET');
        
        if ($cloudConfigured || env('FORCE_CLOUD_STORAGE', false)) {
            try {
                // Use the 'uploads' disk (Cloudflare R2)
                // Don't check existence - just use the disk directly
                return 'uploads';
            } catch (\Exception $e) {
                // If cloud fails locally, throw error instead of using local storage
                throw new \Exception('Cloud storage configuration required: ' . $e->getMessage());
            }
        }
        
        // NEVER fall back to local storage - require cloud configuration
        throw new \Exception('Cloud storage not configured. Please set up Cloudflare R2/Laravel Cloud storage.');
    }

    private function cloudDeleteCandidates($path): array
    {
        $value = trim((string) $path);
        if ($value === '') {
            return [];
        }

        // Remove query parameters if present
        $value = explode('?', $value, 2)[0];

        $candidates = [$value];

        // If it's a full URL, parse path
        if (preg_match('/^https?:\\/\\//i', $value)) {
            $parsedPath = parse_url($value, PHP_URL_PATH);
            if (is_string($parsedPath) && $parsedPath !== '') {
                $candidates[] = ltrim($parsedPath, '/');
            }
        }

        $bucket = (string) env('AWS_BUCKET', '');
        
        $keys = [];
        foreach ($candidates as $candidate) {
            // Normalize path
            $candidate = ltrim((string) $candidate, '/');
            
            // 1. Try removing bucket name prefix if present
            if ($bucket !== '' && str_starts_with($candidate, $bucket . '/')) {
                $keys[] = substr($candidate, strlen($bucket) + 1);
            }

            // 2. Always include the candidate itself
            $keys[] = $candidate;

            // 3. Try removing storage/ prefix
            if (str_starts_with($candidate, 'storage/')) {
                $keys[] = substr($candidate, strlen('storage/'));
            }
            
            // 4. Try removing uploads/ prefix
            if (str_starts_with($candidate, 'uploads/')) {
                $keys[] = substr($candidate, strlen('uploads/'));
            }
        }

        return array_values(array_unique(array_filter($keys, fn ($k) => is_string($k) && $k !== '')));
    }

}
