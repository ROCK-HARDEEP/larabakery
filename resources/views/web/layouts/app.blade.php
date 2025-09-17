<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Bakery Shop') }} - Fresh Baked Goods & Delicious Treats</title>
    <meta name="description" content="Fresh baked breads, cakes, pastries and more. Quality ingredients, traditional recipes, made with love daily.">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Google Fonts - Jost -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
    
    <!-- SKC Exact Theme CSS -->
    <link rel="stylesheet" href="{{ asset('css/skc-exact-theme.css') }}">
    
    <!-- Custom Sections CSS -->
    <link rel="stylesheet" href="{{ asset('css/custom-sections.css') }}">
    
    <!-- Frontend Fixes CSS -->
    <link rel="stylesheet" href="{{ asset('css/frontend-fixes.css') }}">
    
    <!-- Blogs & Testimonials CSS -->
    <link rel="stylesheet" href="{{ asset('css/blogs-testimonials.css') }}">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    {{-- Dynamic Styles --}}
    @php
        $announcementBgColor = $headerFooterSettings->announcement_bar_bg_color ?: '#f69d1c';
        $announcementTextColor = $headerFooterSettings->announcement_bar_text_color ?: '#000000';
        $footerBgColor = $headerFooterSettings->footer_background_color ?: '#1a1a1a';
        $footerTextColor = $headerFooterSettings->footer_text_color ?: '#ffffff';
    @endphp
    @include('web.layouts.app-styles')
    
    @stack('styles')
</head>
<body>
    <!-- Announcement Bar -->
    @if($headerFooterSettings->announcement_bar_enabled)
    <div class="dynamic-announcement-bar">
        <div class="skc-container">
            {{ strip_tags($headerFooterSettings->announcement_bar_text) }}
        </div>
    </div>
    @endif

    <!-- Header -->
    <header class="skc-header-wrapper">
        <div class="skc-header">
            <div class="skc-header-main">
                <!-- Logo -->
                <a href="{{ route('home') }}" class="skc-logo-link">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        @if($headerFooterSettings->header_logo)
                            <img src="{{ Storage::url($headerFooterSettings->header_logo) }}" alt="{{ $headerFooterSettings->header_brand_name }}" style="height: 50px; width: 50px; object-fit: cover; border-radius: 8px;">
                        @else
                            <img src="{{ asset('bakery-logo.jpg') }}" alt="{{ $headerFooterSettings->header_brand_name }}" style="height: 50px; width: 50px; object-fit: cover; border-radius: 8px;" onerror="this.src='data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI1MCIgaGVpZ2h0PSI1MCIgdmlld0JveD0iMCAwIDUwIDUwIj48cmVjdCB3aWR0aD0iNTAiIGhlaWdodD0iNTAiIGZpbGw9IiNmNjlkMWMiLz48dGV4dCB4PSI1MCUiIHk9IjUwJSIgZG9taW5hbnQtYmFzZWxpbmU9Im1pZGRsZSIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZm9udC1mYW1pbHk9IkFyaWFsIiBmb250LXNpemU9IjIwIiBmaWxsPSJ3aGl0ZSI+QjwvdGV4dD48L3N2Zz4='">
                        @endif
                        <div>
                            <div class="skc-logo-text">{{ $headerFooterSettings->header_brand_name }}</div>
                            <div class="skc-logo-tagline">Fresh From The Oven</div>
                        </div>
                    </div>
                </a>
                
                <!-- Navigation -->
                <nav class="skc-nav-menu">
                    <a href="{{ route('home') }}" class="skc-nav-item">Home</a>
                    <a href="{{ route('products') }}" class="skc-nav-item">Products</a>
                    @php
                        $hasActiveCombos = false;
                        try {
                            $hasActiveCombos = \App\Models\ComboOffer::active()->exists();
                        } catch (\Exception $e) {
                            // Silently fail if table doesn't exist
                        }
                    @endphp
                    @if($hasActiveCombos)
                        <a href="{{ route('combos.index') }}" class="skc-nav-item">Combo Offers</a>
                    @endif
                    <a href="{{ route('about') }}" class="skc-nav-item">About</a>
                    <a href="{{ route('contact') }}" class="skc-nav-item">Contact</a>
                </nav>
                
                <!-- Header Actions -->
                <div class="skc-header-actions">
                    <!-- Expandable Search -->
                    <div class="skc-search-container">
                        <button class="skc-search-toggle" onclick="toggleSearch()">
                            <i class="fas fa-search"></i>
                        </button>
                        <div class="skc-search-expandable" id="searchExpandable">
                            <form action="{{ route('products') }}" method="GET" class="skc-search-form" onsubmit="return validateSearch(event)">
                                <input type="search" name="q" class="skc-search-input-expanded" placeholder="Search products..." value="{{ request('q') }}" required>
                                <button type="submit" class="skc-search-submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                    
                    @auth
                        <div style="position: relative;">
                            <a href="#" class="skc-action-btn" onclick="toggleUserMenu(event)">
                                <i class="fas fa-user-circle"></i>
                            </a>
                            <div id="userMenu" style="display: none; position: absolute; right: 0; top: 100%; margin-top: 10px; background: white; border-radius: 4px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); min-width: 180px; z-index: 100;">
                                <a href="{{ route('account.index') }}" style="display: block; padding: 12px 20px; color: #333; text-decoration: none; border-bottom: 1px solid #f0f0f0;">My Account</a>
                                <a href="{{ route('account.orders') }}" style="display: block; padding: 12px 20px; color: #333; text-decoration: none; border-bottom: 1px solid #f0f0f0;">My Orders</a>
                                <a href="{{ route('wishlist.index') }}" style="display: block; padding: 12px 20px; color: #333; text-decoration: none; border-bottom: 1px solid #f0f0f0;">
                                    <i class="fas fa-heart" style="margin-right: 8px; color: #e74c3c;"></i>
                                    My Wishlist
                                </a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" style="width: 100%; text-align: left; padding: 12px 20px; background: none; border: none; cursor: pointer; color: #333;">Logout</button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="skc-action-btn">
                            <i class="fas fa-user"></i>
                        </a>
                    @endauth
                    
                    <a href="{{ route('cart.view') }}" class="skc-action-btn" style="position: relative;">
                        <i class="fas fa-shopping-bag"></i>
                        @if(session('cart_count', 0) > 0)
                            <span class="skc-cart-count" id="cart-count">{{ session('cart_count', 0) }}</span>
                        @endif
                    </a>
                    
                    <!-- Mobile Menu Toggle -->
                    <button class="skc-mobile-menu-toggle" onclick="toggleMobileMenu()">
                        <i class="fas fa-bars"></i>
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- Mobile Menu -->
    <div id="mobileMenu" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: white; z-index: 9999; overflow-y: auto;">
        <div style="padding: 20px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    @if($headerFooterSettings->header_logo)
                        <img src="{{ Storage::url($headerFooterSettings->header_logo) }}" alt="{{ $headerFooterSettings->header_brand_name }}" style="height: 40px; width: 40px; object-fit: cover; border-radius: 6px;">
                    @else
                        <img src="{{ asset('bakery-logo.jpg') }}" alt="{{ $headerFooterSettings->header_brand_name }}" style="height: 40px; width: 40px; object-fit: cover; border-radius: 6px;" onerror="this.src='data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI0MCIgaGVpZ2h0PSI0MCIgdmlld0JveD0iMCAwIDQwIDQwIj48cmVjdCB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIGZpbGw9IiNmNjlkMWMiLz48dGV4dCB4PSI1MCUiIHk9IjUwJSIgZG9taW5hbnQtYmFzZWxpbmU9Im1pZGRsZSIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZm9udC1mYW1pbHk9IkFyaWFsIiBmb250LXNpemU9IjE2IiBmaWxsPSJ3aGl0ZSI+QjwvdGV4dD48L3N2Zz4='">
                    @endif
                    <div class="skc-logo-text">{{ $headerFooterSettings->header_brand_name }}</div>
                </div>
                <button onclick="toggleMobileMenu()" style="background: none; border: none; font-size: 24px; cursor: pointer;">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <nav style="display: flex; flex-direction: column; gap: 20px;">
                <a href="{{ route('home') }}" style="color: #333; text-decoration: none; font-size: 18px; font-weight: 500;">Home</a>
                <a href="{{ route('products') }}" style="color: #333; text-decoration: none; font-size: 18px; font-weight: 500;">Products</a>
                @php
                    $hasActiveCombos = false;
                    try {
                        $hasActiveCombos = \App\Models\ComboOffer::active()->exists();
                    } catch (\Exception $e) {
                        // Silently fail if table doesn't exist
                    }
                @endphp
                @if($hasActiveCombos)
                    <a href="{{ route('combos.index') }}" style="color: #333; text-decoration: none; font-size: 18px; font-weight: 500;">Combo Offers</a>
                @endif
                <a href="{{ route('about') }}" style="color: #333; text-decoration: none; font-size: 18px; font-weight: 500;">About</a>
                <a href="{{ route('contact') }}" style="color: #333; text-decoration: none; font-size: 18px; font-weight: 500;">Contact</a>
            </nav>
        </div>
    </div>

    <!-- Main Content -->
    <main>
        <!-- Notifications -->
        @if(session('success'))
            <div class="skc-container" style="margin-top: 20px;">
                <div style="background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 15px; border-radius: 4px; display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-check-circle"></i>
                    {{ session('success') }}
                </div>
            </div>
        @endif
        
        @if(session('error'))
            <div class="skc-container" style="margin-top: 20px;">
                <div style="background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 15px; border-radius: 4px; display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-exclamation-circle"></i>
                    {{ session('error') }}
                </div>
            </div>
        @endif
        
        @if(isset($errors) && $errors->any())
            <div class="skc-container" style="margin-top: 20px;">
                <div style="background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 15px; border-radius: 4px;">
                    <ul style="margin: 0; padding-left: 20px;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif
        
        @yield('content')
    </main>

    <!-- Mobile Bottom Navigation -->
    <div class="skc-mobile-bottom-nav">
        <a href="{{ route('home') }}" class="skc-bottom-nav-item">
            <i class="fas fa-home"></i>
            <span>Home</span>
        </a>
        <a href="{{ route('search.mobile') }}" class="skc-bottom-nav-item">
            <i class="fas fa-search"></i>
            <span>Search</span>
        </a>
        @auth
            <a href="{{ route('account.orders') }}" class="skc-bottom-nav-item">
                <i class="fas fa-list-alt"></i>
                <span>Orders</span>
            </a>
        @else
            <a href="{{ route('login') }}" class="skc-bottom-nav-item">
                <i class="fas fa-list-alt"></i>
                <span>Orders</span>
            </a>
        @endauth
        <a href="{{ route('cart.view') }}" class="skc-bottom-nav-item">
            <i class="fas fa-shopping-bag"></i>
            <span>Cart</span>
            @if(session('cart_count', 0) > 0)
                <span class="skc-bottom-cart-count">{{ session('cart_count', 0) }}</span>
            @endif
        </a>
        @auth
            <a href="{{ route('account.index') }}" class="skc-bottom-nav-item">
                <i class="fas fa-user"></i>
                <span>Account</span>
            </a>
        @else
            <a href="{{ route('login') }}" class="skc-bottom-nav-item">
                <i class="fas fa-user"></i>
                <span>Account</span>
            </a>
        @endauth
    </div>

    <!-- Footer -->
    <footer class="skc-footer">
        <div class="skc-container">
            <div class="skc-footer-content">
                <!-- About Column -->
                <div class="skc-footer-column skc-footer-about">
                    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 15px;">
                        @if($headerFooterSettings->footer_logo)
                            <img src="{{ Storage::url($headerFooterSettings->footer_logo) }}" alt="{{ $headerFooterSettings->footer_brand_name }}" style="height: 45px; width: 45px; object-fit: cover; border-radius: 8px;">
                        @else
                            <img src="{{ asset('bakery-logo.jpg') }}" alt="{{ $headerFooterSettings->footer_brand_name }}" style="height: 45px; width: 45px; object-fit: cover; border-radius: 8px;" onerror="this.src='data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI0NSIgaGVpZ2h0PSI0NSIgdmlld0JveD0iMCAwIDQ1IDQ1Ij48cmVjdCB3aWR0aD0iNDUiIGhlaWdodD0iNDUiIGZpbGw9IiNmNjlkMWMiLz48dGV4dCB4PSI1MCUiIHk9IjUwJSIgZG9taW5hbnQtYmFzZWxpbmU9Im1pZGRsZSIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZm9udC1mYW1pbHk9IkFyaWFsIiBmb250LXNpemU9IjE4IiBmaWxsPSJ3aGl0ZSI+QjwvdGV4dD48L3N2Zz4='">
                        @endif
                        <h3 style="margin: 0;">{{ $headerFooterSettings->footer_brand_name }}</h3>
                    </div>
                    <p>{{ $headerFooterSettings->footer_description }}</p>
                    <div class="skc-social-links">
                        @foreach($headerFooterSettings->social_media_links as $social)
                            <a href="{{ $social['url'] }}" class="skc-social-link" target="_blank"><i class="{{ $social['icon'] }}"></i></a>
                        @endforeach
                    </div>
                </div>
                
                <!-- Quick Links -->
                <div class="skc-footer-column">
                    <h3>Quick Links</h3>
                    <ul>
                        @foreach($headerFooterSettings->quick_links as $link)
                            <li><a href="{{ $link['url'] }}">{{ $link['title'] }}</a></li>
                        @endforeach
                    </ul>
                </div>
                
                <!-- Categories -->
                <div class="skc-footer-column">
                    <h3>Categories</h3>
                    <ul>
                        @foreach($headerFooterSettings->category_links as $link)
                            <li><a href="{{ $link['url'] }}">{{ $link['title'] }}</a></li>
                        @endforeach
                    </ul>
                </div>
                
                <!-- Customer Service -->
                <div class="skc-footer-column">
                    <h3>Customer Service</h3>
                    <ul>
                        @foreach($headerFooterSettings->customer_service_links as $link)
                            <li><a href="{{ $link['url'] }}">{{ $link['title'] }}</a></li>
                        @endforeach
                    </ul>
                </div>
                
                <!-- Contact Info -->
                <div class="skc-footer-column">
                    <h3>Contact Us</h3>
                    <ul>
                        @if($headerFooterSettings->contact_phone)
                        <li class="skc-footer-contact-item">
                            <i class="fas fa-phone skc-footer-contact-icon"></i>
                            {{ $headerFooterSettings->contact_phone }}
                        </li>
                        @endif
                        @if($headerFooterSettings->contact_email)
                        <li class="skc-footer-contact-item">
                            <i class="fas fa-envelope skc-footer-contact-icon"></i>
                            {{ $headerFooterSettings->contact_email }}
                        </li>
                        @endif
                        @if($headerFooterSettings->contact_hours)
                        <li class="skc-footer-contact-item">
                            <i class="fas fa-clock skc-footer-contact-icon"></i>
                            {{ $headerFooterSettings->contact_hours }}
                        </li>
                        @endif
                        @if($headerFooterSettings->contact_address)
                        <li class="skc-footer-contact-item">
                            <i class="fas fa-map-marker-alt skc-footer-contact-icon"></i>
                            {{ $headerFooterSettings->contact_address }}
                        </li>
                        @endif
                    </ul>
                </div>
            </div>
            

            
            <!-- Footer Bottom -->
            <div class="skc-footer-bottom">
                <div class="skc-copyright" style="text-align: center; width: 100%;">
                    © {{ date('Y') }} {{ config('app.name', 'Bakery Shop') }}. All rights reserved. Made with ❤️
                </div>
            </div>
        </div>
    </footer>

    <!-- Toast Notification -->
    <div id="toast" style="position: fixed; bottom: 30px; right: 30px; background: #333; color: white; padding: 15px 20px; border-radius: 4px; display: none; align-items: center; gap: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.3); z-index: 9999; min-width: 250px;">
        <i class="fas fa-check-circle" style="color: #4caf50;"></i>
        <span id="toast-message"></span>
    </div>

    <!-- Scripts -->
    <script>
        // Dropdown Functions
        function showDropdown(id) {
            const dropdown = document.getElementById(id + '-dropdown');
            if (dropdown) {
                dropdown.classList.add('show');
            }
        }

        function hideDropdown(id) {
            const dropdown = document.getElementById(id + '-dropdown');
            if (dropdown) {
                dropdown.classList.remove('show');
            }
        }

        // User Menu Toggle
        function toggleUserMenu(event) {
            event.preventDefault();
            var menu = document.getElementById('userMenu');
            menu.style.display = menu.style.display === 'none' ? 'block' : 'none';
        }

        // Mobile Menu Toggle
        function toggleMobileMenu() {
            var menu = document.getElementById('mobileMenu');
            menu.style.display = menu.style.display === 'none' ? 'block' : 'none';
            document.body.style.overflow = menu.style.display === 'none' ? 'auto' : 'hidden';
        }

        // Expandable Search Toggle
        function toggleSearch() {
            var searchContainer = document.querySelector('.skc-search-container');
            var searchExpandable = document.getElementById('searchExpandable');
            
            searchContainer.classList.toggle('active');
            searchExpandable.classList.toggle('active');
            
            if (searchExpandable.classList.contains('active')) {
                // Focus on the search input when expanded
                setTimeout(function() {
                    searchExpandable.querySelector('.skc-search-input-expanded').focus();
                }, 300);
            }
        }

                    // Close search when clicking outside
            document.addEventListener('click', function(event) {
                var searchContainer = document.querySelector('.skc-search-container');
                var searchExpandable = document.getElementById('searchExpandable');
                
                if (searchExpandable && searchExpandable.classList.contains('active')) {
                    if (!searchContainer.contains(event.target)) {
                        searchContainer.classList.remove('active');
                        searchExpandable.classList.remove('active');
                    }
                }
            });

            // Validate search form
            function validateSearch(event) {
                var searchInput = event.target.querySelector('input[name="q"]');
                var searchTerm = searchInput.value.trim();
                
                if (!searchTerm) {
                    event.preventDefault();
                    searchInput.focus();
                    window.showToast('Please enter a search term', 'error');
                    return false;
                }
                
                return true;
            }

        // Close dropdowns when clicking outside
        document.addEventListener('click', function(event) {
            var userMenu = document.getElementById('userMenu');
            if (userMenu && !event.target.closest('.skc-action-btn') && !userMenu.contains(event.target)) {
                userMenu.style.display = 'none';
            }
        });

        // Update Cart Count
        window.updateCartCount = function(count) {
            var badge = document.getElementById('cart-count');
            if (count > 0) {
                if (!badge) {
                    var cartBtn = document.querySelector('a[href*="cart"]');
                    if (cartBtn) {
                        badge = document.createElement('span');
                        badge.className = 'skc-cart-count';
                        badge.id = 'cart-count';
                        cartBtn.appendChild(badge);
                    }
                }
                if (badge) {
                    badge.textContent = count;
                    badge.style.display = 'flex';
                }
            } else if (badge) {
                badge.style.display = 'none';
            }
        };

        // Show Toast Notification
        window.showToast = function(message, type = 'success') {
            var toast = document.getElementById('toast');
            var toastMessage = document.getElementById('toast-message');
            var icon = toast.querySelector('i');
            
            if (!toast || !toastMessage) return;
            
            toastMessage.textContent = message;
            
            // Update icon based on type
            if (type === 'error') {
                icon.className = 'fas fa-exclamation-circle';
                icon.style.color = '#f44336';
            } else {
                icon.className = 'fas fa-check-circle';
                icon.style.color = '#4caf50';
            }
            
            toast.style.display = 'flex';
            
            setTimeout(function() {
                toast.style.display = 'none';
            }, 3000);
        };

        // Smooth Scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth'
                    });
                }
            });
        });
    </script>
    
    <!-- Swiper JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script src="{{ asset('js/carousel-init.js') }}"></script>
    
    <!-- Variant Selector Modal -->
    @include('components.variant-selector-modal')

    @stack('scripts')
</body>
</html>