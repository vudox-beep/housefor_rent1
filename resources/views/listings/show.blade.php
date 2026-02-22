<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $listing->title }} - {{ config('app.name', 'Laravel') }}</title>
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
        <!-- Custom CSS -->
        <link rel="stylesheet" href="{{ asset('css/base.css') }}">
        <link rel="stylesheet" href="{{ asset('css/layout.css') }}">
        <link rel="stylesheet" href="{{ asset('css/components.css') }}">
        <link rel="stylesheet" href="{{ asset('css/show.css') }}">
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

        <div class="container" style="padding-top: 3rem; padding-bottom: 5rem;">
            <!-- Header -->
            <div class="listing-header">
                <h1 class="listing-title-large">{{ $listing->title }}</h1>
                <div class="listing-location-large">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    {{ $listing->location }}
                </div>
            </div>

            <!-- Gallery -->
            @php
                if (isset($listing->images) && is_array($listing->images) && count($listing->images) > 0) {
                    $displayImages = array_map(function($img) { return asset($img); }, $listing->images);
                } else {
                    $displayImages = [];
                }
            @endphp

            @if(isset($listing->images) && is_array($listing->images) && count($listing->images) >= 3)
                <div class="gallery-grid">
                    <div class="gallery-main photo-card">
                        <img src="{{ $displayImages[0] }}" class="gallery-img" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                    <div class="gallery-sub photo-card">
                        <img src="{{ $displayImages[1] }}" class="gallery-img" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                    <div class="gallery-sub photo-card">
                        <img src="{{ $displayImages[2] }}" class="gallery-img" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                </div>
            @elseif(isset($listing->images) && is_array($listing->images) && count($listing->images) > 0)
                <div class="gallery-grid" style="grid-template-columns: 1fr;">
                    <div class="gallery-main photo-card">
                        <img src="{{ $displayImages[0] ?? '' }}" class="gallery-img" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                </div>
            @else
                <div class="gallery-grid" style="grid-template-columns: 1fr; min-height: 400px; background: var(--light-bg); border-radius: var(--radius-md);"></div>
            @endif

            <div class="gallery-carousel">
                @foreach($displayImages as $img)
                    <div class="gallery-carousel-item photo-card">
                        <img src="{{ $img }}" class="gallery-carousel-img" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                @endforeach
            </div>

                <!-- Video Section (Gold Feature) -->
                @if($listing->video_path)
                    <div class="listing-section" style="margin-top: 2rem;">
                        <h3>Property Video Tour</h3>
                        @php
                            $video = (string) $listing->video_path;
                            $isLocalVideo = preg_match('/^uploads\//i', $video) === 1;
                            $isDirectVideo = preg_match('/\.(mp4|webm|ogg)(\?.*)?$/i', $video) === 1;
                            $isYouTubeShort = preg_match('/^https?:\/\/youtu\.be\//i', $video) === 1;
                            $youtubeEmbed = $video;
                            if ($isYouTubeShort) {
                                $youtubeEmbed = preg_replace('/^https?:\/\/youtu\.be\//i', 'https://www.youtube.com/embed/', $video);
                            } else {
                                $youtubeEmbed = str_replace('watch?v=', 'embed/', $video);
                            }
                        @endphp

                        @if($isLocalVideo || $isDirectVideo)
                            <video controls style="width: 100%; border-radius: var(--radius-md); background: #000;">
                                <source src="{{ asset($video) }}">
                            </video>
                        @else
                            <div style="position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; max-width: 100%; border-radius: var(--radius-md);">
                                <iframe src="{{ $youtubeEmbed }}"
                                        style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: 0;"
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                        allowfullscreen>
                                </iframe>
                            </div>
                        @endif
                    </div>
                @endif

                <div class="listing-container">
                <!-- Main Details -->
                <div class="listing-details">
                    <div class="listing-section">
                        <h3>Property Overview</h3>
                        <div class="features-grid">
                            <div class="feature-box">
                                <span class="feature-value">{{ $listing->bedrooms ?? 0 }}</span>
                                <span class="feature-label">Bedrooms</span>
                            </div>
                            <div class="feature-box">
                                <span class="feature-value">{{ $listing->bathrooms ?? 0 }}</span>
                                <span class="feature-label">Bathrooms</span>
                            </div>
                            <div class="feature-box">
                                <span class="feature-value">{{ $listing->area ?? 'N/A' }}</span>
                                <span class="feature-label">Area</span>
                            </div>
                            <div class="feature-box">
                                <span class="feature-value">{{ $listing->condition ?? 'N/A' }}</span>
                                <span class="feature-label">Condition</span>
                            </div>
                            <div class="feature-box">
                                <span class="feature-value">{{ $listing->year_built ?? 'N/A' }}</span>
                                <span class="feature-label">Year Built</span>
                            </div>
                            <div class="feature-box">
                                <span class="feature-value">{{ $listing->previous_renters ?? 0 }}</span>
                                <span class="feature-label">Previous Renters</span>
                            </div>
                        </div>
                    </div>

                    <div class="listing-section">
                        <h3>Description</h3>
                        <p style="color: var(--muted-text); line-height: 1.8;">
                            {{ $listing->description }}
                        </p>
                    </div>

                @if(config('services.google_maps.key') && ($listing->latitude || $listing->location))
                    <div class="listing-section">
                        <h3>Location</h3>
                        <div id="listing-map" style="width: 100%; height: 360px; border-radius: var(--radius-md); border: 1px solid var(--border-color); background-color: #f3f4f6;"></div>
                    </div>
                @endif
                </div>

                <!-- Sidebar / Owner Info -->
                <div class="listing-sidebar">
                    <div class="owner-card">
                        <span class="price-display">
                            {{ $listing->currency }} {{ number_format($listing->price) }}
                            <span style="font-size: 1rem; color: var(--muted-text); font-weight: normal;">/ month</span>
                        </span>

                        <div class="owner-header">
                            @if($listing->agent)
                                <!-- Agent Info -->
                                @if($listing->agent->photo_path)
                                    <img src="{{ asset($listing->agent->photo_path) }}" alt="{{ $listing->agent->name }}" class="owner-avatar">
                                @else
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($listing->agent->name) }}&background=random" alt="{{ $listing->agent->name }}" class="owner-avatar">
                                @endif
                                <div class="owner-info">
                                    <h4>{{ $listing->agent->name }}</h4>
                                    <span class="owner-role">Agent</span>
                                </div>
                            @else
                                <!-- Owner Info -->
                                <img src="{{ $listing->user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($listing->user->name) . '&background=random' }}" alt="{{ $listing->user->name }}" class="owner-avatar">
                                <div class="owner-info">
                                    <h4>{{ $listing->user->name }}</h4>
                                    <span class="owner-role">{{ ucfirst($listing->user->role) }}</span>
                                </div>
                            @endif
                        </div>

                        @php
                            $contactPhone = $listing->agent ? $listing->agent->phone : $listing->user->phone;
                            $contactName = $listing->agent ? $listing->agent->name : $listing->user->name;
                        @endphp

                        <a href="https://wa.me/{{ str_replace(['+', ' '], '', $contactPhone) }}" target="_blank" class="contact-btn btn-whatsapp">
                            <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                            WhatsApp {{ $listing->agent ? 'Agent' : 'Owner' }}
                        </a>

                        <button class="contact-btn btn-outline" onclick="copyLink()" style="justify-content: center;">
                            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path></svg>
                            Share Link
                        </button>
                        
                        <button class="contact-btn btn-chat">
                            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                            Start Live Chat
                        </button>

                        <a href="tel:{{ $contactPhone }}" class="contact-btn btn-outline" style="text-align: center; display: block;">
                            Show Phone Number
                        </a>
                        
                        <!-- Report Button -->
                        <div style="margin-top: 1rem; text-align: center;">
                            <button onclick="document.getElementById('report-form-container').style.display = 'block'" 
                                    style="background: none; border: none; color: #DC2626; text-decoration: underline; cursor: pointer; font-size: 0.9rem;">
                                Report this Listing
                            </button>
                        </div>

                        <!-- Report Form (Hidden by default) -->
                        <div id="report-form-container" style="display: none; margin-top: 1.5rem; border-top: 1px solid var(--border-color); padding-top: 1.5rem;">
                            <h4 style="margin-bottom: 1rem; color: #DC2626;">Report Listing</h4>
                            <form action="{{ route('reports.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="reportable_id" value="{{ $listing->id }}">
                                <input type="hidden" name="reportable_type" value="App\Models\Listing">
                                
                                <div style="margin-bottom: 0.8rem;">
                                    <textarea name="reason" rows="3" placeholder="Why are you reporting this listing? (e.g., Fake, Spam, Sold)" required
                                              style="width: 100%; padding: 0.6rem; border: 1px solid #ddd; border-radius: 4px; font-family: inherit;"></textarea>
                                </div>
                                <div style="display: flex; gap: 0.5rem;">
                                    <button type="button" onclick="document.getElementById('report-form-container').style.display = 'none'" 
                                            style="flex: 1; padding: 0.6rem; background-color: #eee; border: none; border-radius: 4px; cursor: pointer;">Cancel</button>
                                    <button type="submit" style="flex: 1; padding: 0.6rem; background-color: #DC2626; color: white; border: none; border-radius: 4px; cursor: pointer;">Submit Report</button>
                                </div>
                            </form>
                        </div>
                        
                        <!-- Contact Form -->
                        <div style="margin-top: 1.5rem; border-top: 1px solid var(--border-color); padding-top: 1.5rem;">
                            <h4 style="margin-bottom: 1rem;">Send Message</h4>
                            <form action="{{ route('leads.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="listing_id" value="{{ $listing->id }}">
                                
                                <div style="margin-bottom: 0.8rem;">
                                    <input type="text" name="name" placeholder="Your Name" required
                                           value="{{ auth()->user()->name ?? '' }}"
                                           style="width: 100%; padding: 0.6rem; border: 1px solid #ddd; border-radius: 4px;">
                                </div>
                                <div style="margin-bottom: 0.8rem;">
                                    <input type="email" name="email" placeholder="Your Email" required
                                           value="{{ auth()->user()->email ?? '' }}"
                                           style="width: 100%; padding: 0.6rem; border: 1px solid #ddd; border-radius: 4px;">
                                </div>
                                <div style="margin-bottom: 0.8rem;">
                                    <input type="tel" name="phone" placeholder="Your Phone" required
                                           value="{{ auth()->user()->phone ?? '' }}"
                                           style="width: 100%; padding: 0.6rem; border: 1px solid #ddd; border-radius: 4px;">
                                </div>
                                <div style="margin-bottom: 0.8rem;">
                                    <textarea name="message" rows="3" placeholder="I'm interested in this property..." required
                                              style="width: 100%; padding: 0.6rem; border: 1px solid #ddd; border-radius: 4px; font-family: inherit;"></textarea>
                                </div>
                                <button type="submit" class="contact-btn btn-primary" style="width: 100%; justify-content: center;">
                                    Send Message
                                </button>
                            </form>
                            @if(session('success'))
                                <div style="background-color: #DEF7EC; color: #03543F; padding: 0.8rem; border-radius: 4px; margin-top: 1rem; font-size: 0.9rem;">
                                    {{ session('success') }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="image-lightbox" class="image-lightbox" aria-hidden="true">
            <button type="button" id="image-lightbox-close" class="image-lightbox-close" aria-label="Close">×</button>
            <img id="image-lightbox-img" alt="Listing photo">
        </div>

        <!-- Footer -->
        <footer class="footer">
            <div class="container">
                <p>&copy; {{ date('Y') }} HouseForRent. All rights reserved.</p>
            </div>
        </footer>
        <script>
            function toggleMenu() {
                const navbar = document.querySelector('.navbar');
                navbar.classList.toggle('active');
            }

            function copyLink() {
                const url = window.location.href;
                navigator.clipboard.writeText(url).then(() => {
                    alert('Link copied to clipboard!');
                }).catch(err => {
                    console.error('Failed to copy: ', err);
                });
            }

            (function () {
                const lightbox = document.getElementById('image-lightbox');
                const lightboxImg = document.getElementById('image-lightbox-img');
                const closeBtn = document.getElementById('image-lightbox-close');

                function openLightbox(src) {
                    if (!src) return;
                    lightboxImg.src = src;
                    lightbox.classList.add('open');
                    lightbox.setAttribute('aria-hidden', 'false');
                    document.body.style.overflow = 'hidden';
                }

                function closeLightbox() {
                    lightbox.classList.remove('open');
                    lightbox.setAttribute('aria-hidden', 'true');
                    lightboxImg.src = '';
                    document.body.style.overflow = '';
                }

                document.addEventListener('click', function (e) {
                    const target = e.target;
                    if (!(target instanceof Element)) return;

                    if (target.matches('.gallery-img, .gallery-carousel-img')) {
                        openLightbox(target.getAttribute('src'));
                    }
                });

                lightbox.addEventListener('click', function (e) {
                    if (e.target === lightbox) closeLightbox();
                });

                closeBtn.addEventListener('click', closeLightbox);

                document.addEventListener('keydown', function (e) {
                    if (e.key === 'Escape' && lightbox.classList.contains('open')) {
                        closeLightbox();
                    }
                });
            })();

            function initListingMap() {
                const el = document.getElementById('listing-map');
                if (!el || !window.google?.maps) return;

                const lat = {{ $listing->latitude ?? 'null' }};
                const lng = {{ $listing->longitude ?? 'null' }};
                const locationName = "{!! addslashes($listing->location) !!}";

                const mapOptions = {
                    zoom: 13,
                    mapTypeControl: false,
                    streetViewControl: false,
                    fullscreenControl: true,
                };

                if (lat !== null && lng !== null) {
                    const center = { lat: lat, lng: lng };
                    const map = new google.maps.Map(el, { ...mapOptions, center });
                    new google.maps.Marker({ position: center, map });
                } else if (locationName) {
                    const geocoder = new google.maps.Geocoder();
                    geocoder.geocode({ address: locationName }, function(results, status) {
                        if (status === 'OK' && results[0]) {
                            const map = new google.maps.Map(el, { ...mapOptions, center: results[0].geometry.location });
                            new google.maps.Marker({
                                map: map,
                                position: results[0].geometry.location
                            });
                        } else {
                            el.innerHTML = `
                                <div style="height: 100%; display: flex; align-items: center; justify-content: center; color: #6b7280; flex-direction: column; gap: 0.5rem;">
                                    <svg width="32" height="32" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                    <span>Map location not found</span>
                                </div>
                            `;
                        }
                    });
                }
            }
        </script>
        @if(config('services.google_maps.key') && ($listing->latitude || $listing->location))
            <script src="https://maps.googleapis.com/maps/api/js?key={{ urlencode(config('services.google_maps.key')) }}&callback=initListingMap" async defer></script>
        @endif
    </body>
</html>
