<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

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
            'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:2048',
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
                // Store in 'public/listings' directory
                $path = $image->store('listings', 'public');
                // Create a full URL or relative path that can be used with asset() or storage link
                $imagePaths[] = '/storage/' . $path;
            }
        }

        $videoPath = null;
        if ($canUploadVideo && $request->hasFile('video_file')) {
            $path = $request->file('video_file')->store('listings/videos', 'public');
            $videoPath = '/storage/' . $path;
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
        if ($listing->user_id !== auth()->id()) {
            abort(403);
        }
        return view('listings.edit', compact('listing'));
    }

    public function update(Request $request, Listing $listing)
    {
        if ($listing->user_id !== auth()->id()) {
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
            'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:2048',
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
        $removeImages = $validated['remove_images'] ?? [];
        $removeImages = array_values(array_intersect($existingImages, $removeImages));
        $keptImages = array_values(array_diff($existingImages, $removeImages));

        foreach ($removeImages as $path) {
            $relative = ltrim(str_replace('/storage/', '', (string) $path), '/');
            if ($relative !== '') {
                Storage::disk('public')->delete($relative);
            }
        }

        $newImagePaths = [];
        if ($request->hasFile('images')) {
            $images = $request->file('images');
            if ($maxImages === 1) {
                $keptImages = [];
                $images = array_slice($images, 0, 1);
            }

            foreach ($images as $image) {
                $path = $image->store('listings', 'public');
                $newImagePaths[] = '/storage/' . $path;
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
                if (str_starts_with($existingVideo, '/storage/')) {
                    $relative = ltrim(str_replace('/storage/', '', $existingVideo), '/');
                    if ($relative !== '') {
                        Storage::disk('public')->delete($relative);
                    }
                }

                $videoPath = null;
            } elseif ($request->hasFile('video_file')) {
                if (str_starts_with($existingVideo, '/storage/')) {
                    $relative = ltrim(str_replace('/storage/', '', $existingVideo), '/');
                    if ($relative !== '') {
                        Storage::disk('public')->delete($relative);
                    }
                }

                $path = $request->file('video_file')->store('listings/videos', 'public');
                $videoPath = '/storage/' . $path;
            } elseif (!empty($validated['video_url'])) {
                $videoPath = $validated['video_url'];
            } else {
                $videoPath = $listing->video_path;
            }
        }

        $updateData = $validated;
        unset($updateData['images'], $updateData['remove_images'], $updateData['video_file'], $updateData['video_url'], $updateData['remove_video']);
        $updateData['images'] = $finalImages;
        $updateData['video_path'] = $canUploadVideo ? $videoPath : null;
        $updateData['agent_id'] = $validated['agent_id'] ?? null;

        $listing->update($updateData);

        if (Auth::user()->isDealer()) {
            return redirect()->route('dealer.dashboard')->with('success', 'Listing updated successfully.');
        }

        return redirect()->route('dashboard')->with('success', 'Listing updated successfully.');
    }

    public function destroy(Listing $listing)
    {
        if ($listing->user_id !== auth()->id()) {
            abort(403);
        }

        $listing->delete();

        if (Auth::user()->isDealer()) {
            return redirect()->route('dealer.dashboard')->with('success', 'Listing deleted successfully.');
        }

        return redirect()->route('dashboard')->with('success', 'Listing deleted successfully.');
    }
}
