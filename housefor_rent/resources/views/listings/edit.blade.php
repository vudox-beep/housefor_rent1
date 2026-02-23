@extends('layouts.dealer')

@section('title', 'Edit Listing')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Edit Property Details</h3>
        </div>

        <form method="POST" action="{{ route('listings.update', $listing->public_id) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- Title & Location -->
            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                <div class="form-group">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Property Title</label>
                    <input type="text" name="title" value="{{ old('title', $listing->title) }}" required 
                           style="width: 100%; padding: 0.8rem; border: 1px solid var(--border-color); border-radius: var(--radius-md);"
                           placeholder="e.g. Luxury Villa in Kabulonga">
                    @error('title') <span style="color: red; font-size: 0.8rem;">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Location</label>
                    <input type="text" id="location-autocomplete" name="location" value="{{ old('location', $listing->location) }}" required 
                           style="width: 100%; padding: 0.8rem; border: 1px solid var(--border-color); border-radius: var(--radius-md);"
                           placeholder="Start typing a city...">
                    <div style="margin-top: 0.75rem;">
                        <label style="display: block; margin-bottom: 0.35rem; font-weight: 600; font-size: 0.9rem;">Country</label>
                        <select id="location-country" name="country_select" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: white;">
                            <option value="">All countries</option>
                            <option value="zm" {{ strtolower(old('country', $listing->country ?? '')) === 'zm' ? 'selected' : '' }}>Zambia</option>
                            <option value="mw" {{ strtolower(old('country', $listing->country ?? '')) === 'mw' ? 'selected' : '' }}>Malawi</option>
                            <option value="za" {{ strtolower(old('country', $listing->country ?? '')) === 'za' ? 'selected' : '' }}>South Africa</option>
                            <option value="us" {{ strtolower(old('country', $listing->country ?? '')) === 'us' ? 'selected' : '' }}>United States</option>
                            <option value="gb" {{ strtolower(old('country', $listing->country ?? '')) === 'gb' ? 'selected' : '' }}>United Kingdom</option>
                        </select>
                    </div>

                    @php
                        $agents = auth()->user()->agents ?? collect();
                    @endphp
                    @if($agents->count() > 0)
                        <div style="margin-top: 0.75rem;">
                            <label style="display: block; margin-bottom: 0.35rem; font-weight: 600; font-size: 0.9rem;">Assign Agent (Optional)</label>
                            <select name="agent_id" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: white;">
                                <option value="">No specific agent (Show me)</option>
                                @foreach($agents as $agent)
                                    <option value="{{ $agent->id }}" {{ (old('agent_id', $listing->agent_id) == $agent->id) ? 'selected' : '' }}>{{ $agent->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <input type="hidden" name="city" id="location-city" value="{{ old('city', $listing->city) }}">
                    <input type="hidden" name="country" id="location-country-iso" value="{{ old('country', $listing->country) }}">
                    <input type="hidden" name="latitude" id="location-lat" value="{{ old('latitude', $listing->latitude) }}">
                    <input type="hidden" name="longitude" id="location-lng" value="{{ old('longitude', $listing->longitude) }}">
                    @error('location') <span style="color: red; font-size: 0.8rem;">{{ $message }}</span> @enderror
                    @error('city') <span style="color: red; font-size: 0.8rem;">{{ $message }}</span> @enderror
                    @error('country') <span style="color: red; font-size: 0.8rem;">{{ $message }}</span> @enderror
                    @error('latitude') <span style="color: red; font-size: 0.8rem;">{{ $message }}</span> @enderror
                    @error('longitude') <span style="color: red; font-size: 0.8rem;">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Description -->
            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Description</label>
                <textarea name="description" rows="5" required
                          style="width: 100%; padding: 0.8rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); font-family: inherit;"
                          placeholder="Describe the key features of the property...">{{ old('description', $listing->description) }}</textarea>
                @error('description') <span style="color: red; font-size: 0.8rem;">{{ $message }}</span> @enderror
            </div>

            <!-- Price & Type -->
            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                <div class="form-group">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Listing Type</label>
                    <select name="type" style="width: 100%; padding: 0.8rem; border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                        <option value="rent" {{ $listing->type == 'rent' ? 'selected' : '' }}>For Rent</option>
                        <option value="buy" {{ $listing->type == 'buy' ? 'selected' : '' }}>For Sale</option>
                    </select>
                </div>

                <div class="form-group">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Category</label>
                    <select name="category" style="width: 100%; padding: 0.8rem; border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                        <option value="house" {{ $listing->category == 'house' ? 'selected' : '' }}>House</option>
                        <option value="apartment" {{ $listing->category == 'apartment' ? 'selected' : '' }}>Apartment</option>
                        <option value="boarding_house" {{ $listing->category == 'boarding_house' ? 'selected' : '' }}>Boarding House</option>
                        <option value="office" {{ $listing->category == 'office' ? 'selected' : '' }}>Office Space</option>
                        <option value="restaurant" {{ $listing->category == 'restaurant' ? 'selected' : '' }}>Restaurant/Bar</option>
                        <option value="land" {{ $listing->category == 'land' ? 'selected' : '' }}>Land</option>
                    </select>
                </div>

                <div class="form-group">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Price</label>
                    <div style="display: flex; gap: 0.5rem;">
                        <select name="currency" style="width: 80px; padding: 0.8rem; border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                            <option value="ZMW" {{ $listing->currency == 'ZMW' ? 'selected' : '' }}>ZMW</option>
                            <option value="USD" {{ $listing->currency == 'USD' ? 'selected' : '' }}>USD</option>
                        </select>
                        <input type="number" name="price" value="{{ old('price', $listing->price) }}" required step="0.01"
                               style="flex: 1; padding: 0.8rem; border: 1px solid var(--border-color); border-radius: var(--radius-md);"
                               placeholder="0.00">
                    </div>
                </div>
            </div>

            <!-- Features -->
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.5rem; margin-bottom: 1.5rem;">
                <div class="form-group">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Bedrooms</label>
                    <input type="number" name="bedrooms" value="{{ old('bedrooms', $listing->bedrooms) }}" 
                           style="width: 100%; padding: 0.8rem; border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                </div>
                <div class="form-group">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Bathrooms</label>
                    <input type="number" name="bathrooms" value="{{ old('bathrooms', $listing->bathrooms) }}" 
                           style="width: 100%; padding: 0.8rem; border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                </div>
                <div class="form-group">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Area (sqm)</label>
                    <input type="number" name="area" value="{{ old('area', $listing->area) }}" 
                           style="width: 100%; padding: 0.8rem; border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                </div>
                <div class="form-group">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Year Built</label>
                    <input type="number" name="year_built" value="{{ old('year_built', $listing->year_built) }}" 
                           style="width: 100%; padding: 0.8rem; border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                </div>
                <div class="form-group">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Previous Renters</label>
                    <input type="number" name="previous_renters" value="{{ old('previous_renters', $listing->previous_renters) }}" 
                           style="width: 100%; padding: 0.8rem; border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                </div>
                <div class="form-group">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Condition</label>
                    <select name="condition" style="width: 100%; padding: 0.8rem; border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                        <option value="New" {{ $listing->condition == 'New' ? 'selected' : '' }}>New</option>
                        <option value="Excellent" {{ $listing->condition == 'Excellent' ? 'selected' : '' }}>Excellent</option>
                        <option value="Good" {{ $listing->condition == 'Good' ? 'selected' : '' }}>Good</option>
                        <option value="Fair" {{ $listing->condition == 'Fair' ? 'selected' : '' }}>Fair</option>
                        <option value="Needs Renovation" {{ $listing->condition == 'Needs Renovation' ? 'selected' : '' }}>Needs Renovation</option>
                    </select>
                </div>
            </div>

            <!-- Media -->
            <div class="form-group" style="margin-bottom: 2rem;">
                <label style="display: block; margin-bottom: 0.75rem; font-weight: 800;">Listing Media</label>

                @if(Auth::user()->hasActiveTrial() && !Auth::user()->isGold())
                    <div style="background-color: #DCFCE7; color: #166534; padding: 0.8rem; border-radius: var(--radius-md); margin-bottom: 1rem; font-size: 0.9rem;">
                        <strong>Free Trial Active:</strong> Upload up to 20 photos and add a video until {{ Auth::user()->trial_expires_at->format('M d, Y') }}.
                    </div>
                @elseif(!Auth::user()->isGold())
                    <div style="background-color: #FEF3C7; color: #92400E; padding: 0.8rem; border-radius: var(--radius-md); margin-bottom: 1rem; font-size: 0.9rem;">
                        <strong>Basic Plan:</strong> 1 photo only, no video. <a href="{{ route('dealer.subscription') }}" style="text-decoration: underline; color: #B45309;">Upgrade to Gold</a>.
                    </div>
                @endif

                @php
                    $images = is_array($listing->images) ? $listing->images : [];
                @endphp

                @if(count($images) > 0)
                    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.75rem; margin-bottom: 1rem;">
                        @foreach($images as $img)
                            <div style="border: 1px solid var(--border-color); border-radius: var(--radius-md); overflow: hidden; background: white;">
                                <div style="aspect-ratio: 4 / 3; background: #f3f4f6;">
                                    <img src="{{ $img }}" alt="Listing image" style="width: 100%; height: 100%; object-fit: cover;">
                                </div>
                                <label style="display: flex; align-items: center; gap: 0.5rem; padding: 0.65rem; font-size: 0.85rem; color: var(--muted-text);">
                                    <input type="checkbox" name="remove_images[]" value="{{ $img }}">
                                    Remove
                                </label>
                            </div>
                        @endforeach
                    </div>
                @endif

                <div style="border: 2px dashed var(--border-color); padding: 1.25rem; border-radius: var(--radius-lg); text-align: center; background-color: var(--light-bg);">
                    <p style="margin-bottom: 0.75rem; color: var(--muted-text); font-weight: 600;">Upload new photos {{ Auth::user()->maxListingImages() > 1 ? '(Max ' . Auth::user()->maxListingImages() . ' total)' : '(Replaces current photo)' }}</p>
                    <input type="file" name="images[]" {{ Auth::user()->maxListingImages() > 1 ? 'multiple' : '' }} accept="image/*"
                           style="display: block; margin: 0 auto;">
                    <p style="font-size: 0.8rem; color: var(--muted-text); margin-top: 0.5rem;">Supported formats: JPG, PNG, WEBP</p>
                </div>
                @error('images') <span style="color: red; font-size: 0.8rem;">{{ $message }}</span> @enderror
                @error('images.*') <span style="color: red; font-size: 0.8rem;">{{ $message }}</span> @enderror
            </div>

            @if(Auth::user()->canUploadVideo())
                <div class="form-group" style="margin-bottom: 2rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Property Video File (Optional)</label>
                    <input type="file" name="video_file" accept="video/mp4,video/webm,video/quicktime"
                           style="width: 100%; padding: 0.8rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: white;">
                    @error('video_file') <span style="color: red; font-size: 0.8rem;">{{ $message }}</span> @enderror

                    @php
                        $videoIsUrl = is_string($listing->video_path) && preg_match('/^https?:\/\//i', $listing->video_path);
                    @endphp
                    @if($listing->video_path)
                        <div style="margin-top: 0.75rem; color: var(--muted-text); font-size: 0.9rem;">
                            Current: {{ $videoIsUrl ? 'Video link' : 'Uploaded video' }}
                        </div>
                        <label style="display: flex; align-items: center; gap: 0.5rem; margin-top: 0.75rem; color: var(--muted-text); font-size: 0.9rem;">
                            <input type="checkbox" name="remove_video" value="1">
                            Remove current video
                        </label>
                    @endif

                    <div style="height: 0.75rem;"></div>

                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Property Video URL (Optional)</label>
                    <input type="url" name="video_url" value="{{ old('video_url', $videoIsUrl ? $listing->video_path : '') }}"
                           style="width: 100%; padding: 0.8rem; border: 1px solid var(--border-color); border-radius: var(--radius-md);"
                           placeholder="https://youtube.com/watch?v=...">
                    @error('video_url') <span style="color: red; font-size: 0.8rem;">{{ $message }}</span> @enderror
                </div>
            @endif

            <div style="text-align: right;">
                <button type="submit" 
                        style="background-color: var(--primary-color); color: white; padding: 1rem 2rem; border: none; border-radius: var(--radius-md); font-weight: 700; cursor: pointer; font-size: 1rem;">
                    Update Listing
                </button>
            </div>
        </form>
    </div>

    @if(config('services.google_maps.key'))
        <script src="https://maps.googleapis.com/maps/api/js?key={{ urlencode(config('services.google_maps.key')) }}&libraries=places&callback=initListingLocation" async defer></script>
        <script>
            function initListingLocation() {
                const input = document.getElementById('location-autocomplete');
                const countrySelect = document.getElementById('location-country');
                const cityField = document.getElementById('location-city');
                const countryIsoField = document.getElementById('location-country-iso');
                const latField = document.getElementById('location-lat');
                const lngField = document.getElementById('location-lng');

                if (!input || !window.google?.maps?.places) return;

                const autocomplete = new google.maps.places.Autocomplete(input, {
                    types: ['(cities)'],
                    fields: ['name', 'geometry', 'address_components'],
                });

                function setRestriction() {
                    const country = (countrySelect?.value || '').trim();
                    if (country) {
                        autocomplete.setComponentRestrictions({ country: [country] });
                    } else {
                        autocomplete.setComponentRestrictions({});
                    }
                    countryIsoField.value = country;
                }

                setRestriction();
                if (countrySelect) {
                    countrySelect.addEventListener('change', function () {
                        setRestriction();
                        input.value = '';
                        cityField.value = '';
                        latField.value = '';
                        lngField.value = '';
                        input.focus();
                    });
                }

                autocomplete.addListener('place_changed', function () {
                    const place = autocomplete.getPlace();
                    if (!place || !place.geometry || !place.geometry.location) return;

                    const lat = place.geometry.location.lat();
                    const lng = place.geometry.location.lng();

                    let countryIso = '';
                    let city = '';
                    if (Array.isArray(place.address_components)) {
                        for (const comp of place.address_components) {
                            if (!countryIso && comp.types.includes('country')) {
                                countryIso = (comp.short_name || '').toLowerCase();
                            }
                            if (!city && comp.types.includes('locality')) {
                                city = comp.long_name || '';
                            }
                            if (!city && comp.types.includes('administrative_area_level_1')) {
                                city = comp.long_name || '';
                            }
                        }
                    }

                    // Auto-select country if matches
                    if (countrySelect && countryIso) {
                        const option = countrySelect.querySelector(`option[value="${countryIso}"]`);
                        if (option) {
                            countrySelect.value = countryIso;
                        }
                    }

                    input.value = place.name || input.value;
                    cityField.value = city || place.name || '';
                    countryIsoField.value = countryIso;
                    latField.value = String(lat);
                    lngField.value = String(lng);
                });
            }
        </script>
    @endif
@endsection
