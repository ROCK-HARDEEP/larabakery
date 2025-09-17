@extends('web.layouts.app')

@section('content')
    <!-- Custom Sections Before Hero -->
    @include('partials.custom-sections', ['page' => 'home', 'position' => 'before_hero'])
    
    <!-- Hero Section with Slider -->
    <section class="skc-hero-section" style="position: relative;">
        <div class="skc-hero-slider">
            @if($heroSlides && $heroSlides->count() > 0)
                @foreach($heroSlides as $index => $slide)
                    <div class="skc-hero-slide {{ $index === 0 ? 'active' : '' }}" data-slide="{{ $index }}">
                        @php
                            // Use uploaded image or fallback to Unsplash
                            $heroImage = $slide->image_url ?? 'https://images.unsplash.com/photo-1486427944299-bb1a5e99bd69?w=1600';
                        @endphp
                        <img src="{{ $heroImage }}" alt="{{ $slide->title }}" class="skc-hero-image">
                        <div class="skc-hero-content">
                            <h1 class="skc-hero-title">{{ $slide->title ?? 'Welcome to Our Bakery' }}</h1>
                            <p class="skc-hero-subtitle">{{ $slide->subtitle ?? 'Fresh Baked Goods Made Daily with Love and Premium Ingredients' }}</p>
                            @if($slide->button_label)
                                @php
                                    $href = $slide->product
                                        ? route('product.show', $slide->product->slug)
                                        : ($slide->category
                                            ? route('products', ['category' => $slide->category->slug])
                                            : route('products'));
                                @endphp
                                <a href="{{ $href }}" class="skc-hero-btn">{{ $slide->button_label }}</a>
                            @else
                                <a href="{{ route('products') }}" class="skc-hero-btn">Shop Now</a>
                            @endif
                        </div>
                    </div>
                @endforeach
            @else
                <div class="skc-hero-slide active">
                    <img src="https://images.unsplash.com/photo-1511081692775-05d0f180a065?w=1600" alt="Bakery Shop" class="skc-hero-image">
                    <div class="skc-hero-content">
                        <h1 class="skc-hero-title">Welcome to Our Bakery</h1>
                        <p class="skc-hero-subtitle">Fresh Baked Goods Made Daily with Love and Premium Ingredients</p>
                        <a href="{{ route('products') }}" class="skc-hero-btn">Shop Now</a>
                    </div>
                </div>
            @endif
        </div>
        
        <!-- Hero Slider Dots -->
        @if($heroSlides && $heroSlides->count() > 1)
        <div class="hero-dots" style="position: absolute; bottom: 30px; left: 50%; transform: translateX(-50%); display: flex; gap: 12px; z-index: 10;">
            @foreach($heroSlides as $index => $slide)
                <button class="hero-dot {{ $index === 0 ? 'active' : '' }}" 
                        onclick="goToSlide({{ $index }})" 
                        data-slide="{{ $index }}">
                </button>
            @endforeach
        </div>
        <style>
            .hero-dot { width: 12px; height: 12px; border-radius: 50%; border: 2px solid white; background: transparent; cursor: pointer; transition: all 0.3s; opacity: 0.6; }
            .hero-dot.active { background: white; opacity: 1; }
        </style>
        @endif
    </section>
    
    <!-- Custom Sections After Hero -->
    @include('partials.custom-sections', ['page' => 'home', 'position' => 'after_hero'])

    <!-- Custom Sections Before Limited Time Offer -->
    @include('partials.custom-sections', ['page' => 'home', 'position' => 'before_limited_time_offer'])

    <!-- Limited Time Offer Section - New Design -->
    @if($bundles && $bundles->count() > 0)
    <section class="skc-section" style="background: #f8f9fa; padding: 80px 0;">
        <div class="skc-container">
            <div class="skc-section-header" style="text-align: center;">
                <h2 class="skc-section-title" style="font-size: 48px; font-weight: 800; color: #000000; text-transform: uppercase; letter-spacing: 2px; text-shadow: 2px 2px 4px rgba(0,0,0,0.1);">Limited Time Offer</h2>
                <p style="font-size: 20px; color: #666; margin-top: 10px;">Special Bundle Deals</p>
            </div>
            
            <!-- Featured Bundle Card - New Design -->
            @if($bundles->first())
                @php
                    $featuredBundle = $bundles->first();
                @endphp
                <div style="background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 8px 32px rgba(0,0,0,0.1); margin-top: 40px; max-width: 1400px; margin-left: auto; margin-right: auto;">
                    <div style="display: flex; min-height: 400px;" class="limited-offer-card">
                    <style>
                        /* Zoom in/out animation for LTO images */
                        .lto-image-zoom {
                            animation: lto-zoom 3s infinite ease-in-out;
                        }
                        
                        @keyframes lto-zoom {
                            0% { transform: scale(1); }
                            50% { transform: scale(1.05); }
                            100% { transform: scale(1); }
                        }
                        
                        /* Title blinking animation */
                        @keyframes title-blink {
                            0%, 100% {
                                opacity: 1;
                                transform: scale(1);
                            }
                            50% {
                                opacity: 0.85;
                                transform: scale(1.02);
                            }
                        }

                        /* Badge blinking animation */
                        @keyframes badge-blink {
                            0%, 100% {
                                transform: scale(1);
                                box-shadow: 0 4px 15px rgba(139, 0, 0, 0.4);
                            }
                            50% {
                                transform: scale(1.08);
                                box-shadow: 0 6px 25px rgba(139, 0, 0, 0.6);
                            }
                        }

                        /* Fire icon pulse animation */
                        @keyframes fire-pulse {
                            0%, 100% {
                                transform: scale(1);
                            }
                            50% {
                                transform: scale(1.2);
                            }
                        }
                        
                        @keyframes lto-blink {
                            0% {
                                transform: scale(1);
                                opacity: 1;
                                box-shadow: 0 4px 15px rgba(139, 0, 0, 0.5);
                            }
                            50% {
                                transform: scale(1.05);
                                opacity: 0.85;
                                box-shadow: 0 6px 20px rgba(139, 0, 0, 0.7);
                            }
                            100% {
                                transform: scale(1);
                                opacity: 1;
                                box-shadow: 0 4px 15px rgba(139, 0, 0, 0.5);
                            }
                        }
                        
                        @media (max-width: 768px) {
                            .limited-offer-card {
                                flex-direction: column !important;
                                min-height: auto !important;
                            }
                            .limited-offer-card > div:first-child {
                                flex: none !important;
                                min-height: 250px !important;
                            }
                            .limited-offer-card > div:last-child {
                                flex: none !important;
                                padding: 20px !important;
                            }
                            .countdown-timer {
                                justify-content: center !important;
                                margin-top: 10px !important;
                            }
                            .delivery-info {
                                flex-direction: column !important;
                                gap: 15px !important;
                            }
                        }
                    </style>
                        <!-- Left Section - Product Image -->
                        <div style="flex: 1.4; position: relative; background: #f8f9fa; display: flex; align-items: center; justify-content: center; padding: 40px;">
                            
                            @if($featuredBundle->image_path)
                                <img src="{{ Storage::url($featuredBundle->image_path) }}" alt="{{ $featuredBundle->name }}"
                                     class="lto-image-zoom"
                                     style="width: 100%; height: 100%; max-width: 450px; max-height: 450px; object-fit: cover; border-radius: 12px; border: 2px solid #8B0000; box-shadow: 0 0 20px rgba(139, 0, 0, 0.3);">
                            @else
                                <div class="lto-image-zoom" style="width: 300px; height: 300px; background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; position: relative; border: 2px solid #8B0000; box-shadow: 0 0 20px rgba(139, 0, 0, 0.3);">
                                    <span style="font-size: 120px;">🎁</span>
                                </div>
                            @endif
                            
                            <!-- Discount Badge -->
                            @if($featuredBundle->savings_percentage > 0)
                            <div style="position: absolute; top: 20px; right: 20px; background: #28a745; color: white; padding: 8px 16px; border-radius: 20px; font-size: 14px; font-weight: 600; z-index: 9;">
                                {{ $featuredBundle->savings_percentage }}% OFF
                            </div>
                            @endif
                        </div>
                        
                        <!-- Right Section - Product Details -->
                        <div style="flex: 1.6; padding: 40px; display: flex; flex-direction: column; justify-content: space-between;">
                            <!-- Top Banner -->
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px;">
                                <div class="lto-top-badge" style="background: linear-gradient(45deg, #8B0000, #B22222); color: white; padding: 12px 24px; border-radius: 30px; font-size: 18px; font-weight: 700; display: flex; align-items: center; gap: 8px; text-transform: uppercase; letter-spacing: 1px; box-shadow: 0 4px 15px rgba(139, 0, 0, 0.4); animation: badge-blink 1.5s infinite;">
                                    <i class="fas fa-fire" style="font-size: 20px; animation: fire-pulse 1s infinite;"></i>
                                    Limited Time Offer
                                </div>
                                
                                <!-- Countdown Timer -->
                                <div class="countdown-timer" style="display: flex; gap: 8px; font-family: 'Courier New', monospace; font-size: 20px; font-weight: 700; color: #333;">
                                    <div id="days-container" style="background: #f8f9fa; padding: 8px 12px; border-radius: 8px; min-width: 50px; text-align: center; display: none;">
                                        <div style="font-size: 16px; line-height: 1.2;">
                                            <span id="days">00</span>
                                            <div style="font-size: 10px; color: #666;">DAYS</div>
                                        </div>
                                    </div>
                                    <div style="background: #f8f9fa; padding: 8px 12px; border-radius: 8px; min-width: 50px; text-align: center;">
                                        <div style="font-size: 16px; line-height: 1.2;">
                                            <span id="hours">02</span>
                                            <div style="font-size: 10px; color: #666;">HRS</div>
                                        </div>
                                    </div>
                                    <div style="background: #f8f9fa; padding: 8px 12px; border-radius: 8px; min-width: 50px; text-align: center;">
                                        <div style="font-size: 16px; line-height: 1.2;">
                                            <span id="minutes">15</span>
                                            <div style="font-size: 10px; color: #666;">MIN</div>
                                        </div>
                                    </div>
                                    <div style="background: #f8f9fa; padding: 8px 12px; border-radius: 8px; min-width: 50px; text-align: center;">
                                        <div style="font-size: 16px; line-height: 1.2;">
                                            <span id="seconds">34</span>
                                            <div style="font-size: 10px; color: #666;">SEC</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Product Title -->
                            <h3 style="font-size: 32px; font-weight: 700; color: #1a1a1a; margin-bottom: 15px; line-height: 1.2;">
                                {{ $featuredBundle->name }}
                            </h3>
                            
                            <!-- Product Description -->
                            @if($featuredBundle->description)
                            <p style="color: #666; font-size: 16px; margin-bottom: 25px; line-height: 1.5;">
                                {{ $featuredBundle->description }}
                            </p>
                            @endif
                            
                            <!-- Bundle Contents -->
                            <div style="margin-bottom: 25px;">
                                <h4 style="font-size: 16px; font-weight: 600; color: #333; margin-bottom: 15px;">Bundle contents</h4>
                                <div style="display: flex; flex-direction: column; gap: 12px;">
                                    @if($featuredBundle->items && $featuredBundle->items->count() > 0)
                                        @foreach($featuredBundle->items as $item)
                                            @php
                                                $productName = strtolower($item->product->name);
                                                $icon = '🥐'; // default
                                                if (str_contains($productName, 'croissant')) $icon = '🥐';
                                                elseif (str_contains($productName, 'muffin')) $icon = '🧁';
                                                elseif (str_contains($productName, 'cookie')) $icon = '🍪';
                                                elseif (str_contains($productName, 'cake')) $icon = '🎂';
                                                elseif (str_contains($productName, 'bread')) $icon = '🍞';
                                            @endphp
                                            <div style="display: flex; align-items: center; gap: 12px;">
                                                <div style="width: 24px; height: 24px; background: #e3f2fd; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                                    <span style="font-size: 14px;">{{ $icon }}</span>
                                                </div>
                                                <span style="font-size: 14px; color: #333; font-weight: 500;">
                                                    {{ $item->product->name }} × {{ $item->qty }}
                                                </span>
                                            </div>
                                        @endforeach
                                    @else
                                        <div style="text-align: center; color: #666; font-style: italic;">
                                            No items in this bundle
                                        </div>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Pricing Section -->
                            <div style="margin-bottom: 25px;">
                                <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 10px;">
                                    @if($featuredBundle->original_price > $featuredBundle->price)
                                    <span style="font-size: 18px; color: #999; text-decoration: line-through;">
                                        ₹{{ number_format($featuredBundle->original_price, 0) }}
                                    </span>
                                    @endif
                                    <span style="font-size: 36px; font-weight: 700; color: #333;">
                                        ₹{{ number_format($featuredBundle->price, 0) }}
                                    </span>
                                    @if($featuredBundle->savings_percentage > 0)
                                    <span style="color: #f69d1c; font-size: 16px; font-weight: 600;">
                                        {{ $featuredBundle->savings_percentage }}% OFF
                                    </span>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Action Buttons Section -->
                            <div class="action-buttons-section" style="margin-bottom: 20px; display: flex; gap: 15px;">
                                <button onclick="addBundleToCart({{ $featuredBundle->id }})"
                                        class="btn-primary-cta"
                                        style="background: linear-gradient(135deg, #f69d1c, #ff8c00); color: white; padding: 16px 32px; border: none; border-radius: 12px; font-size: 16px; font-weight: 600; cursor: pointer; transition: all 0.3s; display: flex; align-items: center; gap: 8px; justify-content: center; flex: 1;">
                                    <i class="fas fa-shopping-cart"></i>
                                    Add to Cart
                                </button>
                                <a href="{{ route('limited-time-offer.show', $featuredBundle->slug) }}"
                                   class="btn-outline-primary"
                                   style="background: transparent; color: #f69d1c; padding: 16px 32px; border: 2px solid #f69d1c; border-radius: 12px; font-size: 16px; font-weight: 600; cursor: pointer; transition: all 0.3s; display: flex; align-items: center; gap: 8px; justify-content: center; text-decoration: none; flex: 1;">
                                    <i class="fas fa-eye"></i>
                                    View Full Details
                                </a>
                            </div>
                            <style>
                                .btn-primary-cta:hover { background: linear-gradient(135deg, #e68a00, #f69d1c) !important; transform: translateY(-2px); box-shadow: 0 6px 20px rgba(246, 157, 28, 0.4); }
                                .btn-outline-primary:hover { background: linear-gradient(135deg, #f69d1c, #ff8c00) !important; color: white !important; transform: translateY(-2px); }

                                /* Animation for small SAVE badges */
                                @keyframes save-blink {
                                    0% { opacity: 1; transform: scale(1); }
                                    50% { opacity: 0.85; transform: scale(1.05); }
                                    100% { opacity: 1; transform: scale(1); }
                                }

                                /* Hover effect for bundle cards buttons */
                                .btn-primary-cta:hover {
                                    background: linear-gradient(135deg, #e68a00, #f69d1c) !important;
                                    box-shadow: 0 6px 20px rgba(246, 157, 28, 0.4);
                                }
                            </style>
                            
                            <!-- Delivery and Payment Information -->
                            <div class="delivery-info" style="display: flex; gap: 30px; padding-top: 20px; border-top: 1px solid #f0f0f0;">
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <i class="fas fa-check-circle" style="color: #28a745; font-size: 16px;"></i>
                                    <span style="font-size: 14px; color: #666;">Free delivery</span>
                                </div>
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <i class="fas fa-shield-alt" style="color: #17a2b8; font-size: 16px;"></i>
                                    <span style="font-size: 14px; color: #666;">Secure payment</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            
            <!-- Additional Bundles Grid -->
            @if($bundles->count() > 1)
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 30px; margin-top: 50px;">
                @foreach($bundles->skip(1) as $bundle)
                    <div style="background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.08); transition: transform 0.3s, box-shadow 0.3s; position: relative;" 
                         onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 8px 25px rgba(0,0,0,0.12)'" 
                         onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(0,0,0,0.08)'">
                        
                        @if($bundle->savings_percentage > 0)
                        <div style="position: absolute; top: 15px; right: 15px; background: linear-gradient(45deg, #8B0000, #B22222); color: white; padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; z-index: 10; animation: save-blink 1.5s infinite;">
                            SAVE {{ $bundle->savings_percentage }}%
                        </div>
                        @endif
                        
                        <!-- Bundle Image -->
                        <div style="height: 200px; background: #f8f8f8; position: relative; overflow: hidden;">
                            @if($bundle->image_path)
                                <img src="{{ Storage::url($bundle->image_path) }}" alt="{{ $bundle->name }}" 
                                     style="width: 100%; height: 100%; object-fit: cover;">
                            @else
                                <div style="display: flex; align-items: center; justify-content: center; height: 100%; background: linear-gradient(135deg, #f69d1c 0%, #ff8c00 100%);">
                                    <span style="font-size: 48px;">🎁</span>
                                </div>
                            @endif
                        </div>
                        
                        <!-- Bundle Content -->
                        <div style="padding: 20px;">
                            <h3 style="font-size: 20px; font-weight: 600; color: #1a1a1a; margin-bottom: 10px;">{{ $bundle->name }}</h3>
                            
                            @if($bundle->description)
                            <p style="color: #666; font-size: 14px; margin-bottom: 15px; line-height: 1.5;">{{ $bundle->description }}</p>
                            @endif
                            
                            <!-- Bundle Items -->
                            <div style="margin-bottom: 20px; padding: 15px; background: #f9f9f9; border-radius: 8px;">
                                <div style="font-size: 12px; font-weight: 600; color: #888; text-transform: uppercase; margin-bottom: 10px;">This bundle includes:</div>
                                @if($bundle->items && $bundle->items->count() > 0)
                                    @foreach($bundle->items as $item)
                                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px;">
                                            <span style="font-size: 14px; color: #333;">
                                                • {{ $item->product->name }}
                                            </span>
                                            <span style="font-size: 13px; color: #666; font-weight: 500;">
                                                x{{ $item->qty }}
                                            </span>
                                        </div>
                                    @endforeach
                                @else
                                    <div style="text-align: center; color: #666; font-style: italic;">
                                        No items in this bundle
                                    </div>
                                @endif
                            </div>
                            
                            <!-- Pricing -->
                            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px;">
                                <div>
                                    @if($bundle->original_price > $bundle->price)
                                    <div style="font-size: 14px; color: #999; text-decoration: line-through;">₹{{ number_format($bundle->original_price, 2) }}</div>
                                    @endif
                                    <div style="font-size: 24px; font-weight: 700; color: #f69d1c;">₹{{ number_format($bundle->price, 2) }}</div>
                                </div>
                                @if($bundle->savings > 0)
                                <div style="text-align: right;">
                                    <div style="font-size: 12px; color: #666;">You Save</div>
                                    <div style="font-size: 16px; font-weight: 600; color: #4caf50;">₹{{ number_format($bundle->savings, 2) }}</div>
                                </div>
                                @endif
                            </div>
                            
                            <!-- Timer if bundle has end date -->
                            @if($bundle->ends_at)
                            <div style="margin-bottom: 15px; padding: 10px; background: #fff3cd; border-radius: 6px; text-align: center;">
                                <div style="font-size: 12px; color: #856404;">
                                    <i class="fas fa-clock"></i> Offer ends {{ $bundle->ends_at->diffForHumans() }}
                                </div>
                            </div>
                            @endif
                            
                            <!-- Add to Cart Button -->
                            <button onclick="addBundleToCart({{ $bundle->id }})"
                                    class="btn-primary-cta"
                                    style="width: 100%; padding: 12px; background: linear-gradient(135deg, #f69d1c, #ff8c00); color: white; border: none; border-radius: 8px; font-size: 16px; font-weight: 600; cursor: pointer; transition: all 0.3s;">
                                <i class="fas fa-shopping-cart"></i> Add Bundle to Cart
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
            @endif
            
            <script>
            function addBundleToCart(bundleId) {
                fetch(`/cart/add-bundle/${bundleId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.showToast('Bundle added to cart successfully!', 'success');
                        if (data.cart_count) {
                            window.updateCartCount(data.cart_count);
                        }
                    } else {
                        window.showToast(data.message || 'Failed to add bundle to cart', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    window.showToast('An error occurred. Please try again.', 'error');
                });
            }

            // Countdown Timer Functionality
            const featuredBundleEndsAt = @json(optional($featuredBundle->ends_at ?? null)->toISOString() ?? null);
            function startCountdown() {
                if (featuredBundleEndsAt) {
                    const endTime = new Date(featuredBundleEndsAt);
                    function updateTimer() {
                        const now = new Date();
                        const totalSeconds = Math.floor((endTime - now) / 1000);
                        if (totalSeconds <= 0) {
                            const timer = document.querySelector('.countdown-timer');
                            if (timer) timer.innerHTML = '<div style="color: #dc3545; font-weight: bold;">OFFER EXPIRED</div>';
                            const addToCartBtn = document.querySelector('button[onclick*="addBundleToCart"]');
                            if (addToCartBtn) {
                                addToCartBtn.disabled = true;
                                addToCartBtn.innerHTML = '<i class="fas fa-clock"></i> Offer Ended';
                                addToCartBtn.style.background = '#6c757d';
                                addToCartBtn.style.cursor = 'not-allowed';
                            }
                            return;
                        }
                        const days = Math.floor(totalSeconds / (3600 * 24));
                        const hours = Math.floor((totalSeconds % (3600 * 24)) / 3600);
                        const minutes = Math.floor((totalSeconds % 3600) / 60);
                        const seconds = totalSeconds % 60;
                        const daysElement = document.getElementById('days');
                        const daysContainer = document.getElementById('days-container');
                        const hoursElement = document.getElementById('hours');
                        const minutesElement = document.getElementById('minutes');
                        const secondsElement = document.getElementById('seconds');
                        if (days > 0) {
                            if (daysContainer) daysContainer.style.display = 'block';
                            if (daysElement) daysElement.textContent = days.toString().padStart(2, '0');
                        } else {
                            if (daysContainer) daysContainer.style.display = 'none';
                        }
                        if (hoursElement) hoursElement.textContent = hours.toString().padStart(2, '0');
                        if (minutesElement) minutesElement.textContent = minutes.toString().padStart(2, '0');
                        if (secondsElement) secondsElement.textContent = seconds.toString().padStart(2, '0');
                    }
                    updateTimer();
                    setInterval(updateTimer, 1000);
                } else {
                    const timerElement = document.querySelector('.countdown-timer');
                    if (timerElement) {
                        timerElement.style.display = 'none';
                    }
                }
            }

            // Start countdown when page loads
            document.addEventListener('DOMContentLoaded', function() {
                startCountdown();
            });
            </script>
        </div>
    </section>
    @endif
    
    <!-- Custom Sections After Limited Time Offer -->
    @include('partials.custom-sections', ['page' => 'home', 'position' => 'after_limited_time_offer'])
    
    <!-- Custom Sections Before Categories -->
    @include('partials.custom-sections', ['page' => 'home', 'position' => 'before_categories'])

    <!-- Categories Section -->
    @if(isset($categories) && $categories->count() > 0)
        @include('components.shop-by-category', ['categories' => $categories, 'totalCategories' => $totalCategories])
    @endif

    <!-- New Arrivals Section -->
    @if(isset($newArrivals) && $newArrivals->count() > 0)
        @include('components.new-arrivals', ['newArrivals' => $newArrivals])
    @endif

    <!-- Custom Sections After Categories -->
    @include('partials.custom-sections', ['page' => 'home', 'position' => 'after_categories'])
    
    <!-- Custom Sections Before Popular Products -->
    @include('partials.custom-sections', ['page' => 'home', 'position' => 'before_popular_products'])

    {{-- Combo Offers Section moved to after Popular Products --}}
    {{-- @if(isset($combos) && $combos->count() > 0)
    <section class="skc-section" style="background: #f8f9fa; padding: 60px 0;">
        <div class="skc-container">
            <div class="skc-section-header">
                <h2 class="skc-section-title">Combo Offers</h2>
                <p class="skc-section-subtitle">Save more with our special combo deals</p>
            </div>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 30px; margin-bottom: 40px;">
                @foreach($combos as $combo)
                    <div style="background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.08); transition: all 0.3s;">
                        <a href="{{ route('combo.show', $combo->slug) }}" style="text-decoration: none;">
                            <div style="position: relative; height: 200px; background: linear-gradient(135deg, #f69d1c 0%, #ff8c00 100%); display: flex; align-items: center; justify-content: center;">
                                @if($combo->image_path)
                                    <img src="{{ Storage::url($combo->image_path) }}" alt="{{ $combo->name }}" 
                                         style="width: 100%; height: 100%; object-fit: cover;">
                                @else
                                    <div style="text-align: center; color: white;">
                                        <i class="fas fa-gift" style="font-size: 48px; margin-bottom: 10px;"></i>
                                        <p style="font-weight: 600;">Combo Deal</p>
                                    </div>
                                @endif
                                
                                @if($combo->discount_percentage > 0)
                                    <div style="position: absolute; top: 15px; right: 15px; background: #ff4444; color: white; padding: 5px 12px; border-radius: 20px; font-weight: 600; font-size: 14px;">
                                        {{ $combo->discount_percentage }}% OFF
                                    </div>
                                @endif
                            </div>
                            
                            <div style="padding: 20px;">
                                <h3 style="font-size: 20px; font-weight: 700; color: var(--skc-black); margin: 0 0 10px 0;">{{ $combo->name }}</h3>
                                <p style="color: var(--skc-medium-gray); font-size: 14px; margin: 0 0 15px 0; line-height: 1.5;">{{ Str::limit($combo->description, 80) }}</p>
                                
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                                    <div>
                                        @if($combo->original_price > $combo->combo_price)
                                            <span style="text-decoration: line-through; color: #999; font-size: 14px;">₹{{ number_format($combo->original_price, 0) }}</span>
                                        @endif
                                        <span style="font-size: 24px; font-weight: 700; color: var(--skc-orange); margin-left: 8px;">₹{{ number_format($combo->combo_price, 0) }}</span>
                                    </div>
                                    <span style="background: #e8f5e9; color: #2e7d32; padding: 4px 10px; border-radius: 4px; font-size: 12px; font-weight: 600;">
                                        Save ₹{{ number_format($combo->savings, 0) }}
                                    </span>
                                </div>
                                
                                <div style="border-top: 1px solid #f0f0f0; padding-top: 15px; margin-top: 15px;">
                                    <p style="font-size: 13px; color: var(--skc-medium-gray); margin: 0;">
                                        <i class="fas fa-box" style="margin-right: 5px;"></i>
                                        {{ $combo->products->count() }} Items in this combo
                                    </p>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
            
            <div style="text-align: center; margin-top: 40px;">
                <a href="{{ route('combos.index') }}" class="view-all-combos-btn" style="display: inline-flex; align-items: center; padding: 14px 32px; background: linear-gradient(135deg, #1a1a1a, #000000); color: white; text-decoration: none; font-size: 16px; font-weight: 600; border-radius: 30px; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3); position: relative; overflow: hidden;">
                    View All Combos
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-left: 8px; vertical-align: middle;">
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                        <polyline points="12 5 19 12 12 19"></polyline>
                    </svg>
                </a>
            </div>
        </div>
    </section>
    @endif
    --}}

    <!-- Featured Products -->
    @if(isset($popular) && $popular->count() > 0)
        @include('components.popular-products', ['popular' => $popular])
    @endif
    {{--
    <section class="skc-section" style="background: var(--skc-light-gray);">
        <div class="skc-container">
            <div class="skc-section-header">
                <h2 class="skc-section-title">Popular Products</h2>
                <p class="skc-section-subtitle">Customer favorites from our kitchen</p>
            </div>
            
            <div class="skc-products-grid">
                @foreach($popular as $product)
                    <div class="skc-product-card">
                        @if($loop->first)
                            <span class="skc-product-badge">Bestseller</span>
                        @elseif($loop->iteration <= 3)
                            <span class="skc-product-badge">Popular</span>
                        @endif
                        
                        <a href="{{ route('product.show', $product->slug) }}" style="text-decoration: none; color: inherit;">
                            <div class="skc-product-image-wrapper">
                                @php
                                    // Use uploaded product image or fallback to placeholder based on product name
                                    $imageUrl = $product->image_url;
                                    
                                    if (!$imageUrl || $imageUrl === asset('img/placeholder-product.jpg')) {
                                        // Fallback to smart placeholders based on product name
                                        $placeholders = [
                                            'cake' => 'https://images.unsplash.com/photo-1578985545062-69928b1d9587?w=400',
                                            'cookie' => 'https://images.unsplash.com/photo-1558961363-fa8fdf82db35?w=400',
                                            'bread' => 'https://images.unsplash.com/photo-1509440159596-0249088772ff?w=400',
                                            'muffin' => 'https://images.unsplash.com/photo-1607958996333-41aef7caefaa?w=400',
                                            'croissant' => 'https://images.unsplash.com/photo-1555507036-ab1f4038808a?w=400',
                                            'waffle' => 'https://images.unsplash.com/photo-1562376552-0d160a2f238d?w=400',
                                            'roll' => 'https://images.unsplash.com/photo-1609501676725-7186f017a4b7?w=400',
                                            'velvet' => 'https://images.unsplash.com/photo-1588195538326-c5b1e9f80a1b?w=400',
                                            'chocolate' => 'https://images.unsplash.com/photo-1606890737304-57a1ca8a5b62?w=400',
                                            'cinnamon' => 'https://images.unsplash.com/photo-1609501676725-7186f017a4b7?w=400',
                                            'sourdough' => 'https://images.unsplash.com/photo-1549931319-a545dcf3bc73?w=400',
                                            'blueberry' => 'https://images.unsplash.com/photo-1607958996333-41aef7caefaa?w=400',
                                            'default' => 'https://images.unsplash.com/photo-1486427944299-bb1a5e99bd69?w=400'
                                        ];
                                        
                                        $productNameLower = strtolower($product->name);
                                        $imageUrl = $placeholders['default'];
                                        
                                        foreach($placeholders as $key => $url) {
                                            if(str_contains($productNameLower, $key)) {
                                                $imageUrl = $url;
                                                break;
                                            }
                                        }
                                    }
                                @endphp
                                <img src="{{ $imageUrl }}" alt="{{ $product->name }}" class="skc-product-image">
                            </div>
                        </a>
                        
                        <div class="skc-product-details">
                            <div class="skc-product-category">{{ $product->category->name ?? 'Traditional' }}</div>
                            
                            <!-- Product Rating -->
                            <div class="skc-product-rating">
                                <div class="skc-product-stars">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= $product->rating)
                                            <i class="fas fa-star" style="color: #ffc107;"></i>
                                        @elseif($i - $product->rating < 1)
                                            <i class="fas fa-star-half-alt" style="color: #ffc107;"></i>
                                        @else
                                            <i class="far fa-star" style="color: #ddd;"></i>
                                        @endif
                                    @endfor
                                </div>
                                <span class="skc-product-rating-count">({{ $product->review_count }})</span>
                            </div>
                            
                            <h3 class="skc-product-name">{{ $product->name }}</h3>
                            
                            <div class="skc-product-price-section">
                                @if($product->has_discount && $product->isDiscountActive())
                                    <span class="skc-product-original-price">₹{{ number_format($product->base_price, 0) }}</span>
                                    <div class="skc-product-price">
                                        <span class="skc-product-price-currency">₹</span>{{ number_format($product->discount_price, 0) }}
                                    </div>
                                    <span class="skc-product-discount">{{ number_format($product->discount_percentage, 0) }}% OFF</span>
                                @else
                                    <div class="skc-product-price">
                                        <span class="skc-product-price-currency">₹</span>{{ number_format($product->base_price, 0) }}
                                    </div>
                                @endif
                            </div>
                            
                            <div class="skc-product-footer">
                                <a href="{{ route('product.show', $product->slug) }}" class="skc-add-cart-btn" style="background: #ffc107; color: #333; border: none; padding: 12px 20px; border-radius: 6px; font-weight: 600; cursor: pointer; transition: all 0.2s; width: 100%; display: inline-block; text-align: center; text-decoration: none;">
                                    View Product
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <div class="skc-text-center skc-mt-40">
                <a href="{{ route('products') }}" class="skc-hero-btn" style="background: var(--skc-black); color: white;">View All Products</a>
            </div>
        </div>
    </section>
    --}}
    
    <!-- Custom Sections After Popular Products -->
    @include('partials.custom-sections', ['page' => 'home', 'position' => 'after_popular_products'])
    
    <!-- Combo Offers Section -->
    @if(isset($combos) && $combos->count() > 0)
    <section class="skc-section" style="background: #f8f9fa; padding: 60px 0;">
        <div class="skc-container">
            <div class="skc-section-header">
                <h2 class="skc-section-title">Combo Offers</h2>
                <p class="skc-section-subtitle">Save more with our special combo deals</p>
            </div>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 30px; margin-bottom: 40px;">
                @foreach($combos as $combo)
                    <div style="background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.08); transition: all 0.3s;">
                        <a href="{{ route('combo.show', $combo->slug) }}" style="text-decoration: none;">
                            <div style="position: relative; height: 200px; background: linear-gradient(135deg, #f69d1c 0%, #ff8c00 100%); display: flex; align-items: center; justify-content: center;">
                                @if($combo->image_path)
                                    <img src="{{ Storage::url($combo->image_path) }}" alt="{{ $combo->name }}" 
                                         style="width: 100%; height: 100%; object-fit: cover;">
                                @else
                                    <div style="text-align: center; color: white;">
                                        <i class="fas fa-gift" style="font-size: 48px; margin-bottom: 10px;"></i>
                                        <p style="font-weight: 600;">Combo Deal</p>
                                    </div>
                                @endif
                                
                                @if($combo->discount_percentage > 0)
                                    <div style="position: absolute; top: 15px; right: 15px; background: #ff4444; color: white; padding: 5px 12px; border-radius: 20px; font-weight: 600; font-size: 14px;">
                                        {{ $combo->discount_percentage }}% OFF
                                    </div>
                                @endif
                            </div>
                            
                            <div style="padding: 20px;">
                                <h3 style="font-size: 20px; font-weight: 700; color: var(--skc-black); margin: 0 0 10px 0;">{{ $combo->name }}</h3>
                                <p style="color: var(--skc-medium-gray); font-size: 14px; margin: 0 0 15px 0; line-height: 1.5;">{{ Str::limit($combo->description, 80) }}</p>
                                
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                                    <div>
                                        @if($combo->original_price > $combo->combo_price)
                                            <span style="text-decoration: line-through; color: #999; font-size: 14px;">₹{{ number_format($combo->original_price, 0) }}</span>
                                        @endif
                                        <span style="font-size: 24px; font-weight: 700; color: var(--skc-orange); margin-left: 8px;">₹{{ number_format($combo->combo_price, 0) }}</span>
                                    </div>
                                    <span style="background: #e8f5e9; color: #2e7d32; padding: 4px 10px; border-radius: 4px; font-size: 12px; font-weight: 600;">
                                        Save ₹{{ number_format($combo->savings, 0) }}
                                    </span>
                                </div>
                                
                                <div style="border-top: 1px solid #f0f0f0; padding-top: 15px; margin-top: 15px;">
                                    <p style="font-size: 13px; color: var(--skc-medium-gray); margin: 0;">
                                        <i class="fas fa-box" style="margin-right: 5px;"></i>
                                        {{ $combo->products->count() }} Items in this combo
                                    </p>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
            
            <div style="text-align: center; margin-top: 40px;">
                <a href="{{ route('combos.index') }}" class="view-all-combos-btn" style="display: inline-flex; align-items: center; padding: 14px 32px; background: linear-gradient(135deg, #1a1a1a, #000000); color: white; text-decoration: none; font-size: 16px; font-weight: 600; border-radius: 30px; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3); position: relative; overflow: hidden;">
                    View All Combos
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-left: 8px; vertical-align: middle;">
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                        <polyline points="12 5 19 12 12 19"></polyline>
                    </svg>
                </a>
            </div>
        </div>
    </section>
    @endif
    
    <!-- Custom Sections Before Why Choose Us -->
    @include('partials.custom-sections', ['page' => 'home', 'position' => 'before_why_choose_us'])

    <!-- Features Section -->
    <section class="skc-features skc-section">
        <div class="skc-container">
            <div class="skc-section-header">
                <h2 class="skc-section-title">Why Choose Us</h2>
                <p class="skc-section-subtitle">Join the Good Food Revolution</p>
            </div>
            
            @if(isset($whyChooseUs) && $whyChooseUs->count() > 0)
                <!-- Always use Carousel for responsive behavior -->
                <div class="swiper features-swiper">
                    <div class="swiper-wrapper">
                        @foreach($whyChooseUs as $feature)
                        <div class="swiper-slide">
                            <div class="skc-feature-box">
                                <div class="skc-feature-icon">
                                    @if($feature->image)
                                        <img src="{{ Storage::url($feature->image) }}" alt="{{ $feature->title }}" style="width: 60px; height: 60px; object-fit: cover; border-radius: 50%;">
                                    @elseif($feature->icon)
                                        @php
                                            // Parse icon class - support for Font Awesome and Heroicons
                                            $iconClass = $feature->icon;
                                            if (str_starts_with($iconClass, 'heroicon-')) {
                                                // For heroicons, we'll use Font Awesome fallbacks
                                                $iconMap = [
                                                    'heroicon-o-star' => 'fas fa-star',
                                                    'heroicon-o-shield-check' => 'fas fa-shield-alt',
                                                    'heroicon-o-heart' => 'fas fa-heart',
                                                    'heroicon-o-globe' => 'fas fa-globe',
                                                    'heroicon-o-truck' => 'fas fa-truck',
                                                    'heroicon-o-clock' => 'fas fa-clock',
                                                ];
                                                $iconClass = $iconMap[$iconClass] ?? 'fas fa-check-circle';
                                            }
                                        @endphp
                                        <i class="{{ $iconClass }}"></i>
                                    @else
                                        <i class="fas fa-check-circle"></i>
                                    @endif
                                </div>
                                <h3 class="skc-feature-title">{{ $feature->title }}</h3>
                                <p class="skc-feature-desc">{{ $feature->description }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            @else
                {{-- Default fallback if no items are configured --}}
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 30px;">
                    <div class="skc-feature-box">
                        <div class="skc-feature-icon">
                            <i class="fas fa-ban"></i>
                        </div>
                        <h3 class="skc-feature-title">No Palm Oil</h3>
                        <p class="skc-feature-desc">All our products are made without palm oil, promoting better health and sustainability.</p>
                    </div>
                    
                    <div class="skc-feature-box">
                        <div class="skc-feature-icon">
                            <i class="fas fa-leaf"></i>
                        </div>
                        <h3 class="skc-feature-title">No Preservatives</h3>
                        <p class="skc-feature-desc">Fresh ingredients, traditional recipes, and no artificial preservatives ever.</p>
                    </div>
                    
                    <div class="skc-feature-box">
                        <div class="skc-feature-icon">
                            <i class="fas fa-heart"></i>
                        </div>
                        <h3 class="skc-feature-title">Organic Recipes</h3>
                        <p class="skc-feature-desc">Authentic South Indian recipes passed down through generations.</p>
                    </div>
                    
                    <div class="skc-feature-box">
                        <div class="skc-feature-icon">
                            <i class="fas fa-globe"></i>
                        </div>
                        <h3 class="skc-feature-title">Global Delivery</h3>
                        <p class="skc-feature-desc">We deliver to 32+ countries, bringing Indian flavors worldwide.</p>
                    </div>
                </div>
            @endif
        </div>
    </section>



    @push('scripts')
    <script>
        // Hero Slider
        document.addEventListener('DOMContentLoaded', function() {
            const slides = document.querySelectorAll('.skc-hero-slide');
            if (slides.length > 1) {
                let currentSlide = 0;
                
                function showSlide(index) {
                    slides.forEach(slide => slide.classList.remove('active'));
                    slides[index].classList.add('active');
                }
                
                function nextSlide() {
                    currentSlide = (currentSlide + 1) % slides.length;
                    showSlide(currentSlide);
                }
                
                // Auto-advance slides every 5 seconds
                setInterval(nextSlide, 5000);
            }
        });

        // Helper Functions
        function showToast(message, type = 'info') {
            // Create toast element
            const toast = document.createElement('div');
            toast.className = `skc-toast skc-toast-${type}`;
            toast.innerHTML = `
                <div class="skc-toast-content">
                    <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
                    <span>${message}</span>
                </div>
            `;
            
            // Add to page
            document.body.appendChild(toast);
            
            // Show toast
            setTimeout(() => toast.classList.add('show'), 100);
            
            // Remove after 4 seconds
            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => document.body.removeChild(toast), 300);
            }, 4000);
        }
        
        function updateCartCount(count) {
            // Update cart count in header
            const cartCountElement = document.getElementById('cart-count');
            if (cartCountElement) {
                if (count > 0) {
                    cartCountElement.textContent = count;
                    cartCountElement.style.display = 'inline';
                } else {
                    cartCountElement.style.display = 'none';
                }
            }
            
            // Update cart count in mobile bottom nav
            const mobileCartCount = document.querySelector('.skc-bottom-cart-count');
            if (mobileCartCount) {
                if (count > 0) {
                    mobileCartCount.textContent = count;
                    mobileCartCount.style.display = 'inline';
                } else {
                    mobileCartCount.style.display = 'none';
                }
            }
        }

        // Add to Cart
        function addToCart(productId) {
            // Convert to number if it's a string
            productId = parseInt(productId);
            
            // Get the button that was clicked
            const button = event.target.closest('.skc-add-cart-btn');
            const originalText = button.innerHTML;
            
            // Show loading state
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';
            button.disabled = true;
            
            fetch('{{ route("cart.add") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    product_id: productId,
                    qty: 1
                })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Show success state
                    button.innerHTML = '<i class="fas fa-check"></i> Added';
                    button.style.background = '#28a745';
                    button.style.color = 'white';
                    
                    // Update cart count
                    if (data.cart_count !== undefined) {
                        updateCartCount(data.cart_count);
                    }
                    
                    // Show success message
                    showToast(data.message || 'Product added to cart!', 'success');
                    
                    // Reset button after 2 seconds
                    setTimeout(() => {
                        button.innerHTML = originalText;
                        button.style.background = '';
                        button.style.color = '';
                        button.disabled = false;
                    }, 2000);
                } else {
                    // Show error state
                    button.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Error';
                    button.style.background = '#dc3545';
                    button.style.color = 'white';
                    
                    showToast(data.message || 'Error adding to cart', 'error');
                    
                    // Reset button after 3 seconds
                    setTimeout(() => {
                        button.innerHTML = originalText;
                        button.style.background = '';
                        button.style.color = '';
                        button.disabled = false;
                    }, 3000);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                
                // Show error state
                button.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Error';
                button.style.background = '#dc3545';
                button.style.color = 'white';
                
                showToast('Network error. Please try again.', 'error');
                
                // Reset button after 3 seconds
                setTimeout(() => {
                    button.innerHTML = originalText;
                    button.style.background = '';
                    button.style.color = '';
                    button.disabled = false;
                }, 3000);
            });
        }



        // Fade in animation on scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('skc-fade-in');
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);

        // Observe all sections
        document.querySelectorAll('.skc-section').forEach(section => {
            observer.observe(section);
        });

        // Initialize Carousels with Continuous Non-Stop Auto-Scrolling
        // Categories Carousel
        if (document.querySelector('.categories-swiper')) {
            new Swiper('.categories-swiper', {
                slidesPerView: 'auto',
                spaceBetween: 20,
                loop: true,
                loopedSlides: 10, // More slides for smoother looping
                centeredSlides: false,
                allowTouchMove: false, // Disable touch/mouse interaction
                simulateTouch: false, // Disable touch simulation
                grabCursor: false, // No grab cursor
                autoplay: {
                    delay: 0, // No delay - continuous movement
                    disableOnInteraction: false, // Never stop
                    pauseOnMouseEnter: false, // Don't pause on hover
                    stopOnLastSlide: false,
                    waitForTransition: false
                },
                speed: 15000, // Slower for super smooth movement
                freeMode: {
                    enabled: false // Disable free mode for consistent speed
                },
                resistance: false,
                resistanceRatio: 0,
                // No pagination
                breakpoints: {
                    320: { 
                        slidesPerView: 2.5, 
                        spaceBetween: 12,
                        speed: 20000 
                    },
                    768: { 
                        slidesPerView: 4.5, 
                        spaceBetween: 18,
                        speed: 18000 
                    },
                    1024: { 
                        slidesPerView: 6.5, 
                        spaceBetween: 25,
                        speed: 15000 
                    }
                }
            });
        }

        // Features (Why Choose Us) Carousel
        if (document.querySelector('.features-swiper')) {
            new Swiper('.features-swiper', {
                slidesPerView: 'auto',
                spaceBetween: 20,
                loop: true,
                loopedSlides: 8, // More slides for smoother looping
                centeredSlides: false,
                allowTouchMove: false, // Disable touch/mouse interaction
                simulateTouch: false, // Disable touch simulation
                grabCursor: false, // No grab cursor
                autoplay: {
                    delay: 0, // No delay - continuous movement
                    disableOnInteraction: false, // Never stop
                    pauseOnMouseEnter: false, // Don't pause on hover
                    reverseDirection: true, // Opposite direction
                    stopOnLastSlide: false,
                    waitForTransition: false
                },
                speed: 18000, // Slower for super smooth movement
                freeMode: {
                    enabled: false // Disable free mode for consistent speed
                },
                resistance: false,
                resistanceRatio: 0,
                // No pagination
                breakpoints: {
                    320: { 
                        slidesPerView: 1.5, 
                        spaceBetween: 12,
                        speed: 22000 
                    },
                    768: { 
                        slidesPerView: 2.8, 
                        spaceBetween: 18,
                        speed: 20000 
                    },
                    1024: { 
                        slidesPerView: 4.5, 
                        spaceBetween: 25,
                        speed: 18000 
                    }
                }
            });
        }
        
        // Add CSS for perfect smooth rendering
        const style = document.createElement('style');
        style.textContent = `
            /* Smooth linear animation */
            .swiper-wrapper {
                transition-timing-function: linear !important;
                -webkit-transition-timing-function: linear !important;
            }
            
            /* Disable all interactions */
            .categories-swiper,
            .features-swiper {
                pointer-events: none !important;
                user-select: none !important;
                -webkit-user-select: none !important;
            }
            
            /* Allow clicks on links inside slides */
            .categories-swiper .skc-category-card,
            .features-swiper .skc-feature-box {
                pointer-events: auto !important;
                cursor: pointer !important;
            }
            
            /* Remove any hover effects on the carousel container */
            .categories-swiper:hover .swiper-wrapper,
            .features-swiper:hover .swiper-wrapper {
                transition-timing-function: linear !important;
            }
            
            /* Hardware acceleration for smooth scrolling */
            .categories-swiper .swiper-slide,
            .features-swiper .swiper-slide {
                will-change: transform;
                -webkit-backface-visibility: hidden;
                backface-visibility: hidden;
                -webkit-transform: translateZ(0);
                transform: translateZ(0);
                -webkit-font-smoothing: antialiased;
            }
            
            /* Hide pagination dots */
            .swiper-pagination {
                display: none !important;
            }
            
            /* Ensure smooth infinite scroll */
            .swiper-wrapper {
                -webkit-transform-style: preserve-3d;
                transform-style: preserve-3d;
            }
            
            /* Mobile optimizations */
            @media (max-width: 768px) {
                .swiper-wrapper {
                    -webkit-transform: translate3d(0, 0, 0);
                    transform: translate3d(0, 0, 0);
                }
                
                .categories-swiper .swiper-slide,
                .features-swiper .swiper-slide {
                    -webkit-transform: translate3d(0, 0, 0) scale(1);
                    transform: translate3d(0, 0, 0) scale(1);
                }
            }
            
            /* Prevent any cursor changes */
            .swiper-container,
            .swiper-wrapper,
            .swiper-slide {
                cursor: default !important;
            }
        `;
        document.head.appendChild(style);
    </script>
    @endpush
    
    <!-- Custom Sections After Why Choose Us -->
    @include('partials.custom-sections', ['page' => 'home', 'position' => 'after_why_choose_us'])

    <!-- FAQ Section -->
    @if(isset($homepageFaqs) && $homepageFaqs->count() > 0)
        @include('components.faq-accordion', ['faqs' => $homepageFaqs])
    @endif

    {{-- Blogs Section (Hidden - uncomment to show)
    @if(isset($blogs) && $blogs->count() > 0)
    <section class="skc-blogs skc-section">
        <div class="skc-container">
            <div class="skc-section-header">
                <h2 class="skc-section-title">Our Blogs</h2>
                <p class="skc-section-subtitle">Stories, recipes, and news from our bakery</p>
            </div>
            
            <div class="skc-blog-grid">
                @foreach($blogs->take(3) as $blog)
                <article class="skc-blog-card">
                    @if($blog->featured_image)
                        <div class="skc-blog-image">
                            <img src="{{ asset('storage/' . $blog->featured_image) }}" alt="{{ $blog->title }}">
                            <div class="skc-blog-overlay">
                                <a href="{{ route('blog.show', $blog->slug) }}" class="skc-blog-read-btn">Read More</a>
                            </div>
                        </div>
                    @endif
                    
                    <div class="skc-blog-content">
                        <div class="skc-blog-meta">
                            <span class="skc-blog-author">{{ $blog->author }}</span>
                            <span class="skc-blog-date">{{ $blog->formatted_published_date }}</span>
                        </div>
                        
                        <h3 class="skc-blog-title">
                            <a href="{{ route('blog.show', $blog->slug) }}">{{ $blog->title }}</a>
                        </h3>
                        
                        <p class="skc-blog-excerpt">{{ $blog->excerpt }}</p>
                        
                        @if($blog->tags && count($blog->tags) > 0)
                            <div class="skc-blog-tags">
                                @foreach(array_slice($blog->tags, 0, 3) as $tag)
                                    <span class="skc-blog-tag">{{ $tag }}</span>
                                @endforeach
                            </div>
                        @endif
                        
                        <div class="skc-blog-footer">
                            <span class="skc-blog-reading-time">{{ $blog->reading_time }} min read</span>
                            <a href="{{ route('blog.show', $blog->slug) }}" class="skc-blog-link">Read Article <i class="fas fa-arrow-right"></i></a>
                        </div>
                    </div>
                </article>
                @endforeach
            </div>
            
            @if($blogs->count() > 3)
                <div class="skc-section-footer">
                    <a href="{{ route('blogs') }}" class="skc-btn-outline">View All Blogs</a>
                </div>
            @endif
        </div>
    </section>
    @endif
    --}}

    {{-- Testimonials Section (Hidden - uncomment to show)
    @if(isset($testimonials) && $testimonials->count() > 0)
    <section class="skc-testimonials skc-section">
        <div class="skc-container">
            <div class="skc-section-header">
                <h2 class="skc-section-title">What Our Customers Say</h2>
                <p class="skc-section-subtitle">Real feedback from real customers</p>
            </div>
            
            <div class="swiper testimonials-swiper">
                <div class="swiper-wrapper">
                    @foreach($testimonials as $testimonial)
                    <div class="swiper-slide">
                        <div class="skc-testimonial-card">
                            <div class="skc-testimonial-rating">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $testimonial->rating)
                                        <i class="fas fa-star"></i>
                                    @else
                                        <i class="far fa-star"></i>
                                    @endif
                                @endfor
                            </div>
                            
                            <blockquote class="skc-testimonial-text">
                                "{{ $testimonial->review }}"
                            </blockquote>
                            
                            <div class="skc-testimonial-author">
                                @if($testimonial->customer_image)
                                    <img src="{{ asset('storage/' . $testimonial->customer_image) }}" alt="{{ $testimonial->customer_name }}" class="skc-testimonial-avatar">
                                @else
                                    <div class="skc-testimonial-avatar-placeholder">
                                        {{ substr($testimonial->customer_name, 0, 1) }}
                                    </div>
                                @endif
                                
                                <div class="skc-testimonial-info">
                                    <h4 class="skc-testimonial-name">{{ $testimonial->customer_name }}</h4>
                                    @if($testimonial->customer_position || $testimonial->customer_company)
                                        <p class="skc-testimonial-position">
                                            @if($testimonial->customer_position && $testimonial->customer_company)
                                                {{ $testimonial->customer_position }}, {{ $testimonial->customer_company }}
                                            @elseif($testimonial->customer_position)
                                                {{ $testimonial->customer_position }}
                                            @else
                                                {{ $testimonial->customer_company }}
                                            @endif
                                        </p>
                                    @endif
                                    @if($testimonial->location)
                                        <p class="skc-testimonial-location">{{ $testimonial->location }}</p>
                                    @endif
                                </div>
                            </div>
                            
                            @if($testimonial->product_reviewed)
                                <div class="skc-testimonial-product">
                                    <small>About: {{ $testimonial->product_reviewed }}</small>
                                </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
                
                <!-- Navigation -->
                <div class="swiper-button-next"></div>
                <div class="swiper-button-prev"></div>
                <div class="swiper-pagination"></div>
            </div>
        </div>
    </section>
    @endif
    --}}

    @push('scripts')
    <script>
        // Hero Slider Auto-play with dots
        document.addEventListener('DOMContentLoaded', function() {
            let currentSlide = 0;
            let heroSlideInterval;
            const heroSlides = document.querySelectorAll('.skc-hero-slide');
            const heroDots = document.querySelectorAll('.hero-dot');
            
            window.goToSlide = function(slideIndex) {
                // Clear the interval to prevent conflicts
                if (heroSlideInterval) {
                    clearInterval(heroSlideInterval);
                }
                
                // Remove active class from all slides and dots
                heroSlides.forEach((slide, index) => {
                    slide.classList.remove('active');
                    if (heroDots[index]) {
                        heroDots[index].classList.remove('active');
                        heroDots[index].style.background = 'transparent';
                        heroDots[index].style.opacity = '0.6';
                    }
                });
                
                // Add active class to current slide and dot
                if (heroSlides[slideIndex]) {
                    heroSlides[slideIndex].classList.add('active');
                }
                if (heroDots[slideIndex]) {
                    heroDots[slideIndex].classList.add('active');
                    heroDots[slideIndex].style.background = 'white';
                    heroDots[slideIndex].style.opacity = '1';
                }
                
                currentSlide = slideIndex;
                
                // Restart the interval
                heroSlideInterval = setInterval(nextSlide, 5000);
            }
            
            function nextSlide() {
                const nextIndex = (currentSlide + 1) % heroSlides.length;
                goToSlide(nextIndex);
            }
            
            // Initialize first slide
            if (heroSlides.length > 0) {
                goToSlide(0);
            }
            
            // Auto-play hero slider without hover pause
            if (heroSlides.length > 1) {
                // Continuous auto-play without any hover interruption
                // No pause on hover - slider keeps running continuously
            }
        });
        
        // Testimonials Carousel
        if (document.querySelector('.testimonials-swiper')) {
            new Swiper('.testimonials-swiper', {
                slidesPerView: 1,
                spaceBetween: 30,
                loop: true,
                autoplay: {
                    delay: 5000,
                    disableOnInteraction: false,
                },
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true,
                },
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },
                breakpoints: {
                    768: {
                        slidesPerView: 2,
                    },
                    1024: {
                        slidesPerView: 3,
                    },
                }
            });
        }
    </script>
    @endpush

    {{-- Include Quick Add Modal (Commented out - not needed on home page) --}}
    {{-- @include('web.partials.product-quick-add') --}}

    <style>
    /* View All Combos Button Hover Effects */
    .view-all-combos-btn {
        position: relative;
        overflow: hidden;
    }

    .view-all-combos-btn:hover {
        background: linear-gradient(135deg, #2a2a2a, #1a1a1a) !important;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.4) !important;
        color: white !important;
    }

    .view-all-combos-btn::before {
        content: "";
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transition: left 0.5s ease;
    }

    .view-all-combos-btn:hover::before {
        left: 100%;
    }

    /* Responsive */
    @media (max-width: 767px) {
        .view-all-combos-btn {
            padding: 12px 24px !important;
            font-size: 14px !important;
        }
    }
    </style>

@endsection