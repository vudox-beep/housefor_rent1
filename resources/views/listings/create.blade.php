@extends('layouts.dealer')

@section('title', 'Create New Listing')

@section('content')
    @php
        $isBasic = !Auth::user()->isGold() && !Auth::user()->hasActiveTrial();
        $isGold = Auth::user()->isGold();
        $isTrial = Auth::user()->hasActiveTrial();
    @endphp

    @if($isBasic)
        <div style="margin-bottom: 1.5rem; background: rgba(239, 68, 68, 0.1); border: 2px solid rgba(239, 68, 68, 0.3); color: #B91C1C; padding: 1rem; border-radius: var(--radius-md); font-weight: 600;">
            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem;">
                <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2m1 15h-2v-2h2v2m0-4h-2V7h2v6z"/></svg>
                <span>Basic Account - Limited to 1 Image Per Listing</span>
            </div>
            <p style="margin-bottom: 0.75rem; font-size: 0.95rem; font-weight: 500;">Upgrade to Gold for unlimited images + video uploads</p>
            <a href="{{ route('dealer.subscription') }}" style="display: inline-block; background: #DC2626; color: white; padding: 0.5rem 1rem; border-radius: 0.5rem; text-decoration: none; font-weight: 600; font-size: 0.9rem;">
                Upgrade Now
            </a>
        </div>
    @elseif($isTrial)
        <div style="margin-bottom: 1.5rem; background: rgba(217, 119, 6, 0.12); border: 2px solid rgba(217, 119, 6, 0.3); color: var(--dark-text); padding: 1rem; border-radius: var(--radius-md); font-weight: 600;">
            <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24" style="display: inline-block; margin-right: 0.5rem;"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2m0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8m3.5-9c.83 0 1.5-.67 1.5-1.5S16.33 8 15.5 8 14 8.67 14 9.5s.67 1.5 1.5 1.5m-7 0c.83 0 1.5-.67 1.5-1.5S9.33 8 8.5 8 7 8.67 7 9.5 7.67 11 8.5 11m3.5 6.5c2.33 0 4.31-1.46 5.11-3.5H6.89c.8 2.04 2.78 3.5 5.11 3.5z"/></svg>
            Free Trial Account - Upload up to 20 images + 1 video per listing
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Property Details</h3>
        </div>

        <form method="POST" action="{{ route('listings.store') }}" enctype="multipart/form-data">
            @csrf

            <!-- Title & Location -->
            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                <div class="form-group">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Property Title</label>
                    <input type="text" name="title" value="{{ old('title') }}" required 
                           style="width: 100%; padding: 0.8rem; border: 1px solid var(--border-color); border-radius: var(--radius-md);"
                           placeholder="e.g. Luxury Villa in Kabulonga">
                    @error('title') <span style="color: red; font-size: 0.8rem;">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Location</label>
                    <input type="text" id="location-autocomplete" name="location" value="{{ old('location') }}" required 
                           style="width: 100%; padding: 0.8rem; border: 1px solid var(--border-color); border-radius: var(--radius-md);"
                           placeholder="Start typing a city...">
                    <div style="margin-top: 0.75rem;">
                        <label style="display: block; margin-bottom: 0.35rem; font-weight: 600; font-size: 0.9rem;">Country</label>
                        <select id="location-country" name="country_select" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: white;">
                            <option value="">All countries</option>
                            <option value="zm">Zambia</option>
                            <option value="mw">Malawi</option>
                            <option value="za">South Africa</option>
                            <option value="us">United States</option>
                            <option value="gb">United Kingdom</option>
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
                                    <option value="{{ $agent->id }}" {{ old('agent_id') == $agent->id ? 'selected' : '' }}>{{ $agent->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <input type="hidden" name="city" id="location-city" value="{{ old('city') }}">
                    <input type="hidden" name="country" id="location-country-iso" value="{{ old('country') }}">
                    <input type="hidden" name="latitude" id="location-lat" value="{{ old('latitude') }}">
                    <input type="hidden" name="longitude" id="location-lng" value="{{ old('longitude') }}">
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
                          placeholder="Describe the key features of the property...">{{ old('description') }}</textarea>
                @error('description') <span style="color: red; font-size: 0.8rem;">{{ $message }}</span> @enderror
            </div>

            <!-- Price & Type -->
            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                <div class="form-group">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Listing Type</label>
                    <select name="type" style="width: 100%; padding: 0.8rem; border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                        <option value="rent">For Rent</option>
                        <option value="buy">For Sale</option>
                    </select>
                </div>

                <div class="form-group">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Category</label>
                    <select name="category" style="width: 100%; padding: 0.8rem; border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                        <option value="house">House</option>
                        <option value="apartment">Apartment</option>
                        <option value="boarding_house">Boarding House</option>
                        <option value="office">Office Space</option>
                        <option value="restaurant">Restaurant/Bar</option>
                        <option value="land">Land</option>
                    </select>
                </div>

                <div class="form-group">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Price</label>
                    <div style="display: flex; gap: 0.5rem;">
                        <select name="currency" style="width: 80px; padding: 0.8rem; border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                            <option value="ZMW">ZMW</option>
                            <option value="USD">USD</option>
                        </select>
                        <input type="number" name="price" value="{{ old('price') }}" required step="0.01"
                               style="flex: 1; padding: 0.8rem; border: 1px solid var(--border-color); border-radius: var(--radius-md);"
                               placeholder="0.00">
                    </div>
                </div>
            </div>

            <!-- Features -->
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.5rem; margin-bottom: 1.5rem;">
                <div class="form-group">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Bedrooms</label>
                    <input type="number" name="bedrooms" value="{{ old('bedrooms') }}" 
                           style="width: 100%; padding: 0.8rem; border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                </div>
                <div class="form-group">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Bathrooms</label>
                    <input type="number" name="bathrooms" value="{{ old('bathrooms') }}" 
                           style="width: 100%; padding: 0.8rem; border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                </div>
                <div class="form-group">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Area (sqm)</label>
                    <input type="number" name="area" value="{{ old('area') }}" 
                           style="width: 100%; padding: 0.8rem; border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                </div>
                <div class="form-group">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Year Built</label>
                    <input type="number" name="year_built" value="{{ old('year_built') }}" 
                           style="width: 100%; padding: 0.8rem; border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                </div>
                <div class="form-group">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Previous Renters</label>
                    <input type="number" name="previous_renters" value="{{ old('previous_renters') }}" 
                           style="width: 100%; padding: 0.8rem; border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                </div>
                <div class="form-group">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Condition</label>
                    <select name="condition" style="width: 100%; padding: 0.8rem; border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                        <option value="New">New</option>
                        <option value="Excellent">Excellent</option>
                        <option value="Good">Good</option>
                        <option value="Fair">Fair</option>
                        <option value="Needs Renovation">Needs Renovation</option>
                    </select>
                </div>
            </div>

            <!-- Images -->
            <div class="form-group" style="margin-bottom: 2rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Property Images</label>
                
                @if(Auth::user()->hasActiveTrial() && !Auth::user()->isGold())
                    <div style="background-color: #DCFCE7; color: #166534; padding: 0.8rem; border-radius: var(--radius-md); margin-bottom: 1rem; font-size: 0.9rem;">
                        <strong>Free Trial Active:</strong> Upload up to 20 photos and add a video until {{ Auth::user()->trial_expires_at->format('M d, Y') }}.
                    </div>
                @elseif(!Auth::user()->isGold())
                    <div style="background-color: #FEF3C7; color: #92400E; padding: 0.8rem; border-radius: var(--radius-md); margin-bottom: 1rem; font-size: 0.9rem;">
                        <strong>Basic Plan Limit:</strong> You can only upload 1 photo. <a href="{{ route('dealer.subscription') }}" style="text-decoration: underline; color: #B45309;">Upgrade to Gold</a> for more photos and video.
                    </div>
                @endif

                <div style="border: 2px dashed var(--border-color); padding: 2rem; border-radius: var(--radius-lg); text-align: center; background-color: var(--light-bg);">
                    <svg width="40" height="40" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: var(--muted-text); margin-bottom: 1rem;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    <p style="margin-bottom: 1rem; color: var(--muted-text);">Click to upload images (Max {{ Auth::user()->maxListingImages() }})</p>
                    <input type="file" name="images[]" {{ Auth::user()->maxListingImages() > 1 ? 'multiple' : '' }} accept="image/*" required
                           style="display: block; margin: 0 auto;">
                    <p style="font-size: 0.8rem; color: var(--muted-text); margin-top: 0.5rem;">Supported formats: JPG, PNG, WEBP</p>
                </div>
                @error('images') <span style="color: red; font-size: 0.8rem;">{{ $message }}</span> @enderror
                @error('images.*') <span style="color: red; font-size: 0.8rem;">{{ $message }}</span> @enderror
            </div>

            <!-- Video (Gold Only) -->
            @if(Auth::user()->canUploadVideo())
                <div class="form-group" style="margin-bottom: 2rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Property Video File (Optional)</label>
                    <input type="file" name="video_file" accept="video/mp4,video/webm,video/quicktime"
                           style="width: 100%; padding: 0.8rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: white;">
                    @error('video_file') <span style="color: red; font-size: 0.8rem;">{{ $message }}</span> @enderror

                    <div style="height: 0.75rem;"></div>

                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Property Video URL (Optional)</label>
                    <input type="url" name="video_url" value="{{ old('video_url') }}" 
                           style="width: 100%; padding: 0.8rem; border: 1px solid var(--border-color); border-radius: var(--radius-md);"
                           placeholder="https://youtube.com/watch?v=...">
                    @error('video_url') <span style="color: red; font-size: 0.8rem;">{{ $message }}</span> @enderror
                </div>
            @endif

            <div style="text-align: right;">
                <button type="submit" 
                        style="background-color: var(--primary-color); color: white; padding: 1rem 2rem; border: none; border-radius: var(--radius-md); font-weight: 700; cursor: pointer; font-size: 1rem;">
                    Post Listing
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
                        // Check if option exists, otherwise select empty or keep as is?
                        // For now we assume the dropdown has the country or we leave it if not restricted
                        const option = countrySelect.querySelector(`option[value="${countryIso}"]`);
                        if (option) {
                            countrySelect.value = countryIso;
                            // Update restriction to lock it? Or just let it be.
                            // Better to sync them.
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
