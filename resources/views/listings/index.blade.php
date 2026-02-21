<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Listings - {{ config('app.name', 'Laravel') }}</title>
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
        <!-- Custom CSS -->
        <link rel="stylesheet" href="{{ asset('css/base.css') }}">
        <link rel="stylesheet" href="{{ asset('css/layout.css') }}">
        <link rel="stylesheet" href="{{ asset('css/components.css') }}">
        <link rel="stylesheet" href="{{ asset('css/home.css') }}">
        <style>
            .page-header {
                background-color: var(--primary-brown);
                color: var(--white);
                padding: 3rem 0;
                text-align: center;
            }
            .page-title {
                color: var(--white);
                font-size: 2.5rem;
            }

            /* Custom Search Form Styles for this page */
            .search-box .search-form {
                display: grid;
                grid-template-columns: repeat(4, 1fr);
                gap: 1.5rem;
                align-items: end;
                background: var(--white);
                padding: 2rem;
                border-radius: var(--radius-lg);
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
                border: 1px solid var(--border-color);
                max-width: 1440px;
                margin: 0 auto;
            }
            .search-box .search-form > .form-group {
                min-width: 0;
            }
            .search-box .search-form input,
            .search-box .search-form select {
                width: 100%;
                box-sizing: border-box;
            }
            .search-box .btn-search {
                width: 100%;
                justify-content: center;
                height: 42px; /* Align with inputs */
            }
            .search-box .btn-near {
                background: transparent;
                border: 2px solid var(--border-color);
                color: var(--dark-text);
            }
            .search-box .btn-near:hover {
                border-color: var(--dark-text);
                background: var(--bg-gray);
            }

            /* Responsive */
            @media (max-width: 992px) {
                .search-box .search-form {
                    grid-template-columns: repeat(2, 1fr);
                }
            }

            @media (max-width: 576px) {
                .search-box .search-form {
                    grid-template-columns: 1fr;
                }
            }
        </style>
    </head>
    <body>
        <!-- Navigation -->
        <nav class="navbar">
            <div class="container">
                <div class="logo">
                    <div class="logo-icon">
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    </div>
                    <a href="{{ route('home') }}">HouseForRent</a>
                </div>
                
                <div class="menu-toggle" onclick="toggleMenu()">
                    <span class="bar"></span>
                    <span class="bar"></span>
                    <span class="bar"></span>
                </div>

                <div class="nav-links">
                    <a href="{{ route('listings.index') }}" class="nav-link">Buy</a>
                    <a href="{{ route('listings.index') }}" class="nav-link">Rent</a>
                    <a href="{{ route('register') }}" class="nav-link">Sell</a>
                    <a href="#" class="nav-link">Agents</a>
                </div>

                <div class="auth-links">
                    @auth
                        @php
                            $dashboardUrl = auth()->user()->isAdmin()
                                ? route('admin.dashboard')
                                : (auth()->user()->isDealer() ? route('dealer.dashboard') : route('dashboard'));
                        @endphp
                        <a href="{{ $dashboardUrl }}" class="btn-primary">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="btn-login">Sign in</a>
                        <a href="{{ route('register') }}" class="btn-primary">Post Property</a>
                    @endauth
                </div>
            </div>
        </nav>

        <div class="page-header">
            <div class="container">
                <h1 class="page-title">Property Listings</h1>
                <p>Find your perfect match</p>
            </div>
        </div>

        <div class="listings-section">
            <div class="container">
                <!-- Search Form -->
                <div class="search-box" style="margin-bottom: 3rem; margin-top: -5rem; position: relative; z-index: 10;">
                    <form action="{{ route('listings.index') }}" method="GET" class="search-form">
                        <div class="form-group">
                            <label>City</label>
                            <input type="text" id="filter-city" name="search" value="{{ request('search') }}" placeholder="Start typing a city..." class="search-input">
                            <input type="hidden" id="filter-lat" name="lat" value="{{ request('lat') }}">
                            <input type="hidden" id="filter-lng" name="lng" value="{{ request('lng') }}">
                        </div>
                        <div class="form-group">
                            <label>Country</label>
                            <select id="filter-country" name="country" class="search-select">
                                <option value="">All countries</option>
                                <option value="zm" {{ request('country') === 'zm' ? 'selected' : '' }}>Zambia</option>
                                <option value="mw" {{ request('country') === 'mw' ? 'selected' : '' }}>Malawi</option>
                                <option value="za" {{ request('country') === 'za' ? 'selected' : '' }}>South Africa</option>
                                <option value="us" {{ request('country') === 'us' ? 'selected' : '' }}>United States</option>
                                <option value="gb" {{ request('country') === 'gb' ? 'selected' : '' }}>United Kingdom</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Type</label>
                            <select name="type" class="search-select">
                                <option value="">Any Type</option>
                                <option value="rent" {{ request('type') == 'rent' ? 'selected' : '' }}>For Rent</option>
                                <option value="buy" {{ request('type') == 'buy' ? 'selected' : '' }}>For Sale</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Category</label>
                            <select name="category" class="search-select">
                                <option value="">Any Category</option>
                                <option value="house" {{ request('category') == 'house' ? 'selected' : '' }}>House</option>
                                <option value="apartment" {{ request('category') == 'apartment' ? 'selected' : '' }}>Apartment</option>
                                <option value="boarding_house" {{ request('category') == 'boarding_house' ? 'selected' : '' }}>Boarding House</option>
                                <option value="office" {{ request('category') == 'office' ? 'selected' : '' }}>Office Space</option>
                                <option value="restaurant" {{ request('category') == 'restaurant' ? 'selected' : '' }}>Restaurant</option>
                                <option value="land" {{ request('category') == 'land' ? 'selected' : '' }}>Land</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Near (km)</label>
                            <select name="radius" class="search-select">
                                @php $radius = (int) request('radius', 25); @endphp
                                <option value="5" {{ $radius === 5 ? 'selected' : '' }}>5 km</option>
                                <option value="10" {{ $radius === 10 ? 'selected' : '' }}>10 km</option>
                                <option value="25" {{ $radius === 25 ? 'selected' : '' }}>25 km</option>
                                <option value="50" {{ $radius === 50 ? 'selected' : '' }}>50 km</option>
                                <option value="100" {{ $radius === 100 ? 'selected' : '' }}>100 km</option>
                            </select>
                        </div>
                        <div class="form-group" style="flex: 1.5;">
                            <label>Price Range</label>
                            <div style="display: flex; gap: 0.5rem;">
                                <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="Min" class="search-input" style="width: 100%;">
                                <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="Max" class="search-input" style="width: 100%;">
                            </div>
                        </div>
                        <button type="button" id="nearMeBtn" class="btn-search btn-near">Near Me</button>
                        <button type="submit" class="btn-search">Filter</button>
                    </form>
                </div>

                @if($listings->count() > 0)
                    <div class="listings-grid">
                        @foreach($listings as $listing)
                            <a href="{{ route('listings.show', $listing->public_id) }}" class="listing-card">
                                <div class="listing-image-container">
                                    @if($listing->images && is_array($listing->images) && count($listing->images) > 0)
                                        <img src="{{ asset($listing->images[0]) }}" alt="{{ $listing->title }}" class="listing-image" onerror="this.src='https://images.unsplash.com/photo-1564013799919-ab600027ffc6?q=80&w=1000&auto=format&fit=crop'">
                                    @else
                                        @php
                                            $imgUrl = 'https://images.unsplash.com/photo-1564013799919-ab600027ffc6?q=80&w=1000&auto=format&fit=crop';
                                            if($listing->id % 3 == 0) $imgUrl = 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?q=80&w=1000&auto=format&fit=crop';
                                            if($listing->id % 3 == 1) $imgUrl = 'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?q=80&w=1000&auto=format&fit=crop';
                                            if($listing->category == 'restaurant') $imgUrl = 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?q=80&w=1000&auto=format&fit=crop';
                                        @endphp
                                        <img src="{{ $imgUrl }}" alt="{{ $listing->title }}" class="listing-image">
                                    @endif
                                    
                                    <span class="listing-badge">
                                        {{ strtoupper($listing->type) }}
                                    </span>
                                    
                                    <span class="listing-price-tag">
                                        {{ $listing->currency }} {{ number_format($listing->price) }}
                                    </span>
                                </div>
                                
                                <div class="listing-content">
                                    <h3 class="listing-title">{{ $listing->title }}</h3>
                                    <p class="listing-address">
                                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                        {{ $listing->location }}
                                    </p>
                                    
                                    <div class="listing-features">
                                        @if($listing->category == 'house')
                                            <div class="feature-item">
                                                <svg class="feature-icon" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                                                {{ $listing->bedrooms ?? 0 }}
                                            </div>
                                            <div class="feature-item">
                                                <svg class="feature-icon" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                                {{ $listing->bathrooms ?? 0 }}
                                            </div>
                                            <div class="feature-item">
                                                <svg class="feature-icon" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path></svg>
                                                {{ $listing->area ?? 'N/A' }}
                                            </div>
                                        @elseif($listing->category == 'restaurant')
                                            <div class="feature-item">
                                                <svg class="feature-icon" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                                {{ $listing->cuisine ?? 'Fine Dining' }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                    
                    <div style="margin-top: 3rem; display: flex; justify-content: center;">
                        <!-- Mock Pagination -->
                        {{ $listings->links('pagination::simple-default') }}
                    </div>
                @else
                    <div class="empty-state">
                        <p style="color: var(--muted-text); font-size: 1.125rem;">No listings match your search.</p>
                        <a href="{{ route('listings.index') }}" class="cta-link">Clear Filters</a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Footer -->
        <footer class="footer">
            <div class="container">
                <div class="footer-content">
                    <div class="footer-brand">
                        <h3>HouseForRent</h3>
                        <p>Your trusted platform for finding the best properties.</p>
                    </div>
                    <div class="footer-links">
                        <h4>Contact</h4>
                        <ul>
                            <li><a href="#">support@houseforrent.com</a></li>
                        </ul>
                    </div>
                </div>
                <div class="footer-bottom">
                    <p>&copy; {{ date('Y') }} HouseForRent. All rights reserved.</p>
                </div>
            </div>
        </footer>
        <script>
            function toggleMenu() {
                const navbar = document.querySelector('.navbar');
                navbar.classList.toggle('active');
            }
        </script>
        @if(config('services.google_maps.key'))
            <script src="https://maps.googleapis.com/maps/api/js?key={{ urlencode(config('services.google_maps.key')) }}&libraries=places&callback=initFilterCity" async defer></script>
            <script>
                function initFilterCity() {
                    const input = document.getElementById('filter-city');
                    const countrySelect = document.getElementById('filter-country');
                    const latField = document.getElementById('filter-lat');
                    const lngField = document.getElementById('filter-lng');
                    const nearBtn = document.getElementById('nearMeBtn');

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
                    }

                    setRestriction();
                    if (countrySelect) {
                        countrySelect.addEventListener('change', function () {
                            setRestriction();
                            input.value = '';
                            latField.value = '';
                            lngField.value = '';
                            input.focus();
                        });
                    }

                    autocomplete.addListener('place_changed', function () {
                        const place = autocomplete.getPlace();
                        if (!place || !place.geometry || !place.geometry.location) return;
                        input.value = place.name || input.value;
                        latField.value = String(place.geometry.location.lat());
                        lngField.value = String(place.geometry.location.lng());
                    });

                    input.addEventListener('input', function () {
                        if (input.value.trim() === '') {
                            latField.value = '';
                            lngField.value = '';
                        }
                    });

                    if (nearBtn) {
                        nearBtn.addEventListener('click', function () {
                            if (!navigator.geolocation) {
                                alert('Geolocation is not supported in this browser.');
                                return;
                            }

                            const form = nearBtn.closest('form');
                            const originalText = nearBtn.textContent;
                            nearBtn.disabled = true;
                            nearBtn.textContent = 'Detecting...';

                            navigator.geolocation.getCurrentPosition(function (pos) {
                                const lat = pos.coords.latitude;
                                const lng = pos.coords.longitude;
                                latField.value = String(lat);
                                lngField.value = String(lng);

                                // Detect address for display purposes
                                const geocoder = new google.maps.Geocoder();
                                geocoder.geocode({ location: { lat, lng } }, function (results, status) {
                                    let country = '';
                                    let city = '';
                                    if (status === 'OK' && results[0]) {
                                        for (const comp of results[0].address_components) {
                                            if (comp.types.includes('country')) {
                                                country = (comp.short_name || '').toLowerCase();
                                            }
                                            if (!city && comp.types.includes('locality')) {
                                                city = comp.long_name;
                                            }
                                            if (!city && comp.types.includes('administrative_area_level_1')) {
                                                city = comp.long_name;
                                            }
                                        }
                                    }

                                    if (countrySelect && country) {
                                        const option = countrySelect.querySelector(`option[value="${country}"]`);
                                        if (option) {
                                            countrySelect.value = country;
                                            setRestriction(); // Update restrictions based on detected country
                                        }
                                    }
                                    
                                    if (city) {
                                        input.value = city;
                                    }

                                    nearBtn.disabled = false;
                                    nearBtn.textContent = originalText;
                                    if (form) form.submit();
                                });
                            }, function (err) {
                                console.error(err);
                                nearBtn.disabled = false;
                                nearBtn.textContent = originalText;
                                alert('Unable to detect your location. Please allow location access.');
                            }, {
                                enableHighAccuracy: true,
                                timeout: 10000,
                                maximumAge: 0,
                            });
                        });
                    }
                }
            </script>
        @endif
    </body>
</html>
