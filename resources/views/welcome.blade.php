<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Laravel') }}</title>
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
        <!-- Custom CSS -->
        <link rel="stylesheet" href="{{ asset('css/base.css') }}">
        <link rel="stylesheet" href="{{ asset('css/layout.css') }}">
        <link rel="stylesheet" href="{{ asset('css/components.css') }}">
        <link rel="stylesheet" href="{{ asset('css/home.css') }}">
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

        <!-- Hero Section -->
        <div class="hero">
            <div class="container">
                <div class="hero-text">
                    <span class="hero-label">Your Trusted Partner</span>
                    <h1 class="hero-title">Find Your Dream Home with <span>Professional Care</span></h1>
                    <p class="hero-subtitle">Whether you're looking to buy, rent, or sell, we provide a seamless experience with expert guidance at every step.</p>
                    
                    <div class="hero-buttons" style="margin-top: 2rem;">
                        <a href="{{ route('listings.index') }}" class="hero-btn primary">View Listings</a>
                        <a href="#" class="hero-btn secondary">Get an Appraisal</a>
                    </div>
                </div>

                <div class="hero-image-wrapper" style="background: linear-gradient(135deg, rgba(217, 119, 6, 0.1) 0%, rgba(217, 119, 6, 0.05) 100%); display: flex; align-items: center; justify-content: center; min-height: 400px; border-radius: 12px;">
                    <svg width="100" height="100" fill="none" stroke="rgba(217, 119, 6, 0.3)" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                </div>

                <!-- Floating Search Bar -->
                <div class="search-wrapper" style="position: relative; z-index: 10;">
                    <div class="search-container">
                        <form action="{{ route('listings.index') }}" method="GET" class="search-form">
                            <div class="form-group">
                                <label>City</label>
                                <div class="input-with-icon">
                                    <div class="input-icon">
                                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    </div>
                                    <input type="text" id="home-city" name="search" placeholder="City..." class="search-input">
                                    <input type="hidden" id="home-lat" name="lat">
                                    <input type="hidden" id="home-lng" name="lng">
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Country</label>
                                <div class="input-with-icon">
                                    <div class="input-icon">
                                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7m-7 0a3 3 0 01-3-3 3 3 0 013-3m7 6a3 3 0 01-3-3 3 3 0 013-3m-3 3h3"></path></svg>
                                    </div>
                                    <select id="home-country" name="country" class="search-select">
                                        <option value="">All countries</option>
                                        <option value="zm">Zambia</option>
                                        <option value="mw">Malawi</option>
                                        <option value="za">South Africa</option>
                                        <option value="us">United States</option>
                                        <option value="gb">United Kingdom</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Type</label>
                                <div class="input-with-icon">
                                    <div class="input-icon">
                                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                                    </div>
                                    <select name="type" class="search-select">
                                        <option value="">Any Type</option>
                                        <option value="rent">For Rent</option>
                                        <option value="buy">For Sale</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label>Category</label>
                                <div class="input-with-icon">
                                    <div class="input-icon">
                                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                    </div>
                                    <select name="category" class="search-select">
                                        <option value="">Any Category</option>
                                        <option value="house">House</option>
                                        <option value="apartment">Apartment</option>
                                        <option value="boarding_house">Boarding House</option>
                                        <option value="office">Office Space</option>
                                        <option value="restaurant">Restaurant/Bar</option>
                                        <option value="land">Land</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Near (km)</label>
                                <div class="input-with-icon">
                                    <div class="input-icon">
                                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 01.553-.894L9 2m0 18l6-3m-6 3V2m6 15l5.447 2.724A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 2"></path></svg>
                                    </div>
                                    <select name="radius" class="search-select">
                                        <option value="5">5 km</option>
                                        <option value="10">10 km</option>
                                        <option value="25" selected>25 km</option>
                                        <option value="50">50 km</option>
                                        <option value="100">100 km</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label>Price Range</label>
                                <div class="input-with-icon">
                                    <div class="input-icon">
                                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    </div>
                                    <div style="display: flex; gap: 0.5rem;">
                                        <input type="number" name="min_price" placeholder="Min" class="search-input" style="width: 100%;">
                                        <input type="number" name="max_price" placeholder="Max" class="search-input" style="width: 100%;">
                                    </div>
                                </div>
                            </div>
                            
                            <button type="button" id="homeNearMeBtn" class="btn-search btn-near" style="white-space: nowrap;">
                                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                Near Me
                            </button>
                            <button type="submit" class="btn-search" style="white-space: nowrap;">
                                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                Search
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Promo Banner -->
        <div style="background: linear-gradient(135deg, rgba(217, 119, 6, 0.1) 0%, rgba(217, 119, 6, 0.05) 100%); margin: 3rem 0; padding: 2rem; border-radius: 12px; border: 1px solid rgba(217, 119, 6, 0.2);">
            <div class="container">
                <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 2rem;">
                    <div>
                        <h3 style="margin: 0 0 0.5rem 0; font-size: 1.5rem; color: var(--primary-color);">Start Listing Properties for Free</h3>
                        <p style="margin: 0; color: var(--muted-text); font-size: 1rem;">New users get a 1-month free trial to upload up to 20 images and 1 video per listing!</p>
                    </div>
                    <a href="{{ route('register') }}" class="btn-primary" style="white-space: nowrap;">Join Now</a>
                </div>
            </div>
        </div>

        <!-- Categories Section -->
        <div class="categories-section">
            <div class="container">
                <div class="section-header">
                    <div>
                        <h2 class="section-title">Explore by Category</h2>
                        <p class="section-subtitle">Find the perfect property for your lifestyle.</p>
                    </div>
                </div>
                
                <div class="category-grid">
                    <a href="{{ route('listings.index', ['category' => 'house', 'search' => 'family']) }}" class="category-card" style="background: linear-gradient(135deg, rgba(217, 119, 6, 0.15) 0%, rgba(217, 119, 6, 0.08) 100%); display: flex; align-items: center; justify-content: center; min-height: 250px; border-radius: 12px; overflow: hidden;">
                        <div class="category-overlay" style="display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center;">
                            <svg width="48" height="48" fill="none" stroke="rgba(217, 119, 6, 0.4)" viewBox="0 0 24 24" style="margin-bottom: 1rem;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                            <h3 class="category-title">Family House</h3>
                            <p class="category-subtitle">Spacious homes for growing families</p>
                        </div>
                    </a>
                    <a href="{{ route('listings.index', ['category' => 'house', 'search' => 'single']) }}" class="category-card" style="background: linear-gradient(135deg, rgba(217, 119, 6, 0.15) 0%, rgba(217, 119, 6, 0.08) 100%); display: flex; align-items: center; justify-content: center; min-height: 250px; border-radius: 12px; overflow: hidden;">
                        <div class="category-overlay" style="display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center;">
                            <svg width="48" height="48" fill="none" stroke="rgba(217, 119, 6, 0.4)" viewBox="0 0 24 24" style="margin-bottom: 1rem;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4l1 1h4l1-1h4a2 2 0 012 2v14a2 2 0 01-2 2z"></path></svg>
                            <h3 class="category-title">Single Apartment</h3>
                            <p class="category-subtitle">Modern spaces for individuals</p>
                        </div>
                    </a>
                    <a href="{{ route('listings.index', ['category' => 'restaurant']) }}" class="category-card" style="background: linear-gradient(135deg, rgba(217, 119, 6, 0.15) 0%, rgba(217, 119, 6, 0.08) 100%); display: flex; align-items: center; justify-content: center; min-height: 250px; border-radius: 12px; overflow: hidden;">
                        <div class="category-overlay" style="display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center;">
                            <svg width="48" height="48" fill="none" stroke="rgba(217, 119, 6, 0.4)" viewBox="0 0 24 24" style="margin-bottom: 1rem;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <h3 class="category-title">Restaurants</h3>
                            <p class="category-subtitle">Commercial spaces for dining</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <!-- Recent Listings -->
        <div class="listings-section">
            <div class="container">
                <div class="section-header">
                    <div>
                        <h2 class="section-title">New on the Market</h2>
                        <p class="section-subtitle">The freshest listings updated every 15 minutes.</p>
                    </div>
                    <a href="{{ route('listings.index') }}" class="view-all-link">
                        View all 
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                    </a>
                </div>
                
                @if($recentListings->count() > 0)
                    <div class="listings-grid">
                        @foreach($recentListings as $listing)
                            <a href="{{ route('listings.show', $listing->public_id) }}" class="listing-card">
                                <div class="listing-image-container" style="background: var(--light-bg); display: flex; align-items: center; justify-content: center;">
                                    @if($listing->images && is_array($listing->images) && count($listing->images) > 0)
                                        <img src="{{ asset($listing->images[0]) }}" alt="{{ $listing->title }}" class="listing-image" style="width: 100%; height: 100%; object-fit: cover;">
                                    @else
                                        <svg width="40" height="40" fill="none" stroke="var(--muted-text)" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
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
                                                {{ $listing->area ?? '2,400' }} sqft
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
                @else
                    <div class="empty-state">
                        <p style="color: var(--muted-text); font-size: 1.125rem;">No listings available yet.</p>
                        <a href="{{ route('register') }}" class="cta-link">
                            Become a dealer and post the first listing!
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Footer -->
        <footer class="footer">
            <div class="container">
                <div class="footer-content">
                    <div class="footer-brand">
                        <h3>
                            <div class="logo-icon">
                                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                            </div>
                            HouseForRent
                        </h3>
                        <p>We combine local expertise with cutting-edge technology to give you the best real estate experience possible.</p>
                    </div>
                    
                    <div class="footer-links">
                        <h4>Company</h4>
                        <ul>
                            <li><a href="#">About Us</a></li>
                            <li><a href="#">Careers</a></li>
                            <li><a href="#">Press</a></li>
                            <li><a href="{{ route('admin.dashboard') }}">Admin Demo</a></li>
                            <li><a href="{{ route('dealer.dashboard') }}">Dealer Demo</a></li>
                        </ul>
                    </div>
                    
                    <div class="footer-links">
                        <h4>Resources</h4>
                        <ul>
                            <li><a href="#">Buyers Guide</a></li>
                            <li><a href="#">Sellers Guide</a></li>
                            <li><a href="#">Rental Guide</a></li>
                            <li><a href="#">Agent Finder</a></li>
                        </ul>
                    </div>
                    
                    <div class="footer-links">
                        <h4>Contact</h4>
                        <ul>
                            <li><a href="#">Help Center</a></li>
                            <li><a href="#">Terms of Service</a></li>
                            <li><a href="#">Privacy Policy</a></li>
                            <li><a href="#">support@houseforrent.com</a></li>
                        </ul>
                    </div>
                </div>
                
                <div class="footer-bottom">
                    <p>&copy; {{ date('Y') }} HouseForRent. All rights reserved.</p>
                    <div style="display: flex; gap: 1rem;">
                        <a href="#">Twitter</a>
                        <a href="#">Facebook</a>
                        <a href="#">Instagram</a>
                        <a href="#">LinkedIn</a>
                    </div>
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
            <script src="https://maps.googleapis.com/maps/api/js?key={{ urlencode(config('services.google_maps.key')) }}&libraries=places&callback=initHomeCity" async defer></script>
            <script>
                function initHomeCity() {
                    const input = document.getElementById('home-city');
                    const countrySelect = document.getElementById('home-country');
                    const latField = document.getElementById('home-lat');
                    const lngField = document.getElementById('home-lng');
                    const nearBtn = document.getElementById('homeNearMeBtn');

                    if (!input || !window.google?.maps?.places) return;

                    const autocomplete = new google.maps.places.Autocomplete(input, {
                        types: ['(cities)'],
                        fields: ['name', 'geometry'],
                    });

                    function onCountryChange() {
                        const country = (countrySelect?.value || '').trim();
                        if (country) {
                            autocomplete.setComponentRestrictions({ country: [country] });
                        } else {
                            autocomplete.setComponentRestrictions({});
                        }
                        input.value = '';
                        latField.value = '';
                        lngField.value = '';
                        input.focus();
                    }

                    if (countrySelect) {
                        countrySelect.addEventListener('change', onCountryChange);
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
                                        // Check if option exists
                                        const option = countrySelect.querySelector(`option[value="${country}"]`);
                                        if (option) {
                                            countrySelect.value = country;
                                            onCountryChange();
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
