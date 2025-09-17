@extends('web.layouts.app')

@section('content')
<style>
    /* Bundle Detail Specific Styles */
    .bundle-image-gallery {
        position: relative;
        overflow: hidden;
    }
    
    .bundle-countdown-urgent {
        animation: urgent-pulse 1.5s infinite;
    }
    
    @keyframes urgent-pulse {
        0%, 100% { background: #8B0000; transform: scale(1); }
        50% { background: #B22222; transform: scale(1.05); }
    }
    
    .product-item-hover {
        transition: all 0.3s ease;
    }
    
    .product-item-hover:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }
    
    .bundle-specs {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    }
    
    .savings-highlight {
        animation: savings-glow 2s infinite ease-in-out;
    }
    
    @keyframes savings-glow {
        0%, 100% { box-shadow: 0 0 20px rgba(40, 167, 69, 0.3); }
        50% { box-shadow: 0 0 30px rgba(40, 167, 69, 0.6); }
    }
</style>

<!-- Breadcrumb -->
<section style="background: #f8f9fa; padding: 20px 0;">
    <div class="skc-container">
        <nav style="font-size: 14px; color: #666;">
            <a href="{{ route('home') }}" style="color: #007bff; text-decoration: none;">Home</a>
            <span style="margin: 0 8px;">/</span>
            <a href="{{ route('limited-time-offers') }}" style="color: #007bff; text-decoration: none;">Limited Time Offers</a>
            <span style="margin: 0 8px;">/</span>
            <span style="color: #333;">{{ $bundle->name }}</span>
        </nav>
    </div>
</section>

<!-- Bundle Detail Header -->
<section class="skc-section" style="background: linear-gradient(135deg, #8B0000 0%, #B22222 100%); color: white; padding: 60px 0; position: relative;">
    <div class="skc-container">
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 30px;">
            <div style="flex: 1; min-width: 300px;">
                <div class="bundle-countdown-urgent" style="display: inline-block; padding: 10px 20px; border-radius: 25px; font-size: 14px; font-weight: 700; margin-bottom: 20px;">
                    <i class="fas fa-fire"></i> LIMITED TIME ONLY
                </div>
                
                <h1 style="font-size: 42px; font-weight: 800; margin-bottom: 15px; line-height: 1.2;">
                    {{ $bundle->name }}
                </h1>
                
                @if($bundle->description)
                    <p style="font-size: 18px; opacity: 0.9; margin-bottom: 25px; line-height: 1.6;">
                        {{ $bundle->description }}
                    </p>
                @endif
                
                <!-- Pricing -->
                <div style="display: flex; align-items: center; gap: 20px; margin-bottom: 20px;">
                    @if($bundle->original_price > $bundle->price)
                        <span style="font-size: 24px; text-decoration: line-through; opacity: 0.7;">
                            ₹{{ number_format($bundle->original_price, 0) }}
                        </span>
                    @endif
                    <span style="font-size: 36px; font-weight: 700;">
                        ₹{{ number_format($bundle->price, 0) }}
                    </span>
                    @if($bundle->savings_percentage > 0)
                        <div class="savings-highlight" style="background: #28a745; padding: 10px 20px; border-radius: 25px; font-size: 16px; font-weight: 700;">
                            SAVE {{ $bundle->savings_percentage }}%
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Countdown Timer -->
            @if($bundle->ends_at)
                <div style="background: rgba(255, 255, 255, 0.1); padding: 30px; border-radius: 15px; backdrop-filter: blur(10px); text-align: center;">
                    <div style="font-size: 16px; font-weight: 600; margin-bottom: 15px;">OFFER ENDS IN</div>
                    <div id="countdown-timer" style="display: flex; gap: 10px; font-family: 'Courier New', monospace;">
                        <div style="background: rgba(255, 255, 255, 0.2); padding: 15px; border-radius: 10px; min-width: 60px;">
                            <div id="days" style="font-size: 24px; font-weight: 700;">00</div>
                            <div style="font-size: 12px; opacity: 0.8;">DAYS</div>
                        </div>
                        <div style="background: rgba(255, 255, 255, 0.2); padding: 15px; border-radius: 10px; min-width: 60px;">
                            <div id="hours" style="font-size: 24px; font-weight: 700;">00</div>
                            <div style="font-size: 12px; opacity: 0.8;">HRS</div>
                        </div>
                        <div style="background: rgba(255, 255, 255, 0.2); padding: 15px; border-radius: 10px; min-width: 60px;">
                            <div id="minutes" style="font-size: 24px; font-weight: 700;">00</div>
                            <div style="font-size: 12px; opacity: 0.8;">MIN</div>
                        </div>
                        <div style="background: rgba(255, 255, 255, 0.2); padding: 15px; border-radius: 10px; min-width: 60px;">
                            <div id="seconds" style="font-size: 24px; font-weight: 700;">00</div>
                            <div style="font-size: 12px; opacity: 0.8;">SEC</div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</section>

<!-- Bundle Details Section -->
<section class="skc-section" style="background: white; padding: 80px 0;">
    <div class="skc-container">
        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 60px;">
            <!-- Left Column - Main Content -->
            <div>
                <!-- Bundle Image -->
                <div class="bundle-image-gallery" style="margin-bottom: 40px;">
                    <div style="height: 400px; background: #f8f9fa; border-radius: 15px; display: flex; align-items: center; justify-content: center; position: relative; overflow: hidden;">
                        @if($bundle->image_path)
                            <img src="{{ Storage::url($bundle->image_path) }}" alt="{{ $bundle->name }}" 
                                 style="width: 100%; height: 100%; object-fit: cover; border-radius: 15px;">
                        @else
                            <div style="text-align: center;">
                                <span style="font-size: 120px; opacity: 0.3;">🎁</span>
                                <div style="font-size: 20px; color: #666; margin-top: 20px;">{{ $bundle->name }}</div>
                            </div>
                        @endif
                        
                        <!-- Bundle Badge -->
                        <div style="position: absolute; top: 20px; left: 20px; background: #8B0000; color: white; padding: 12px 20px; border-radius: 25px; font-size: 14px; font-weight: 700;">
                            <i class="fas fa-gift"></i> Bundle Deal
                        </div>
                        
                        @if($bundle->savings > 0)
                            <div style="position: absolute; top: 20px; right: 20px; background: #28a745; color: white; padding: 12px 20px; border-radius: 25px; font-size: 14px; font-weight: 700;">
                                Save ₹{{ number_format($bundle->savings, 0) }}
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Dynamic Bundle Description -->
                <div style="margin-bottom: 40px;">
                    <h2 style="font-size: 28px; font-weight: 700; color: #333; margin-bottom: 20px;">
                        About This Bundle Offer
                    </h2>
                    
                    <div style="background: #f8f9fa; padding: 30px; border-radius: 15px; margin-bottom: 30px;">
                        @if($bundle->description)
                            <p style="font-size: 16px; line-height: 1.8; color: #555; margin-bottom: 20px;">
                                {{ $bundle->description }}
                            </p>
                        @endif
                        
                        <!-- Dynamic Coverage Description -->
                        <div style="background: white; padding: 25px; border-radius: 12px; border-left: 5px solid #8B0000;">
                            <h3 style="font-size: 20px; font-weight: 600; color: #333; margin-bottom: 15px;">
                                <i class="fas fa-star" style="color: #8B0000; margin-right: 10px;"></i>
                                What's Covered in This Offer
                            </h3>
                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <i class="fas fa-check-circle" style="color: #28a745; font-size: 16px;"></i>
                                    <span style="font-size: 14px; color: #555;">{{ $bundle->items_count }} Premium Items</span>
                                </div>
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <i class="fas fa-check-circle" style="color: #28a745; font-size: 16px;"></i>
                                    <span style="font-size: 14px; color: #555;">{{ $bundle->savings_percentage }}% Discount Applied</span>
                                </div>
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <i class="fas fa-check-circle" style="color: #28a745; font-size: 16px;"></i>
                                    <span style="font-size: 14px; color: #555;">Free Packaging</span>
                                </div>
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <i class="fas fa-check-circle" style="color: #28a745; font-size: 16px;"></i>
                                    <span style="font-size: 14px; color: #555;">Same Day Delivery</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Products in Bundle -->
                <div style="margin-bottom: 40px;">
                    <h2 style="font-size: 28px; font-weight: 700; color: #333; margin-bottom: 20px;">
                        Products Included in This Bundle
                    </h2>
                    
                    @if($bundle->items && $bundle->items->count() > 0)
                        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
                            @foreach($bundle->items as $item)
                                <div class="product-item-hover" style="background: white; border: 2px solid #f0f0f0; border-radius: 12px; padding: 20px; transition: all 0.3s;">
                                    <div style="display: flex; gap: 15px;">
                                        <!-- Product Image -->
                                        <div style="width: 100px; height: 100px; background: #f8f9fa; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; overflow: hidden;">
                                            @php
                                                $productImage = null;

                                                // Check for various image fields
                                                if ($item->product->image_path && Storage::exists($item->product->image_path)) {
                                                    $productImage = Storage::url($item->product->image_path);
                                                } elseif ($item->product->main_image && Storage::exists($item->product->main_image)) {
                                                    $productImage = Storage::url($item->product->main_image);
                                                } elseif ($item->product->images_path && is_array($item->product->images_path) && count($item->product->images_path) > 0) {
                                                    $productImage = asset('storage/' . $item->product->images_path[0]);
                                                } elseif ($item->product->first_image) {
                                                    $productImage = asset('storage/' . $item->product->first_image);
                                                }
                                            @endphp

                                            @if($productImage)
                                                <img src="{{ $productImage }}" alt="{{ $item->product->name }}"
                                                     style="width: 100%; height: 100%; object-fit: cover; border-radius: 10px;">
                                            @else
                                                <!-- Fallback with dark red gradient -->
                                                <div style="width: 100%; height: 100%; background: linear-gradient(135deg, #8B0000 0%, #B22222 100%); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 24px;">
                                                    {{ strtoupper(substr($item->product->name, 0, 1)) }}
                                                </div>
                                            @endif
                                        </div>
                                        
                                        <!-- Product Details -->
                                        <div style="flex: 1;">
                                            <h3 style="font-size: 16px; font-weight: 600; color: #333; margin-bottom: 5px;">
                                                {{ $item->product->name }}
                                            </h3>
                                            
                                            @if($item->product->category)
                                                <div style="font-size: 12px; color: #666; margin-bottom: 8px;">
                                                    <i class="fas fa-tag"></i> {{ $item->product->category->name }}
                                                </div>
                                            @endif
                                            
                                            @if($item->product->short_description)
                                                <p style="font-size: 13px; color: #777; margin-bottom: 10px; line-height: 1.4;">
                                                    {{ Str::limit($item->product->short_description, 80) }}
                                                </p>
                                            @endif
                                            
                                            <div style="display: flex; justify-content: between; align-items: center;">
                                                <div>
                                                    <span style="font-size: 14px; font-weight: 600; color: #333;">
                                                        Qty: {{ $item->qty }}
                                                    </span>
                                                    <span style="font-size: 14px; color: #666; margin-left: 15px;">
                                                        Unit: ₹{{ number_format($item->product->price, 0) }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <!-- Total Value Breakdown -->
                        <div style="background: linear-gradient(135deg, #e3f2fd 0%, #f3e5f5 100%); padding: 25px; border-radius: 15px; margin-top: 30px;">
                            <h3 style="font-size: 20px; font-weight: 600; color: #333; margin-bottom: 20px;">
                                <i class="fas fa-calculator" style="color: #007bff; margin-right: 10px;"></i>
                                Value Breakdown
                            </h3>
                            
                            <div style="font-size: 16px;">
                                @foreach($bundle->items as $item)
                                    <div style="display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid rgba(255, 255, 255, 0.3);">
                                        <span style="flex: 1;">{{ $item->product->name }} × {{ $item->qty }}</span>
                                        <span style="font-weight: 600; margin-left: 20px;">₹{{ number_format($item->product->price * $item->qty, 0) }}</span>
                                    </div>
                                @endforeach
                                
                                <div style="background: rgba(255, 255, 255, 0.5); padding: 15px; border-radius: 10px; margin-top: 15px;">
                                    <div style="display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 16px;">
                                        <span>Individual Price Total:</span>
                                        <span style="font-weight: 600;">₹{{ number_format($bundle->original_price, 0) }}</span>
                                    </div>
                                    <div style="display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 16px; color: #28a745;">
                                        <span>Bundle Discount:</span>
                                        <span style="font-weight: 600;">-₹{{ number_format($bundle->savings, 0) }}</span>
                                    </div>
                                    <div style="display: flex; justify-content: space-between; font-size: 20px; font-weight: 700; color: #8B0000; border-top: 2px solid rgba(255, 255, 255, 0.5); padding-top: 15px;">
                                        <span>Bundle Price:</span>
                                        <span>₹{{ number_format($bundle->price, 0) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div style="text-align: center; padding: 40px; background: #f8f9fa; border-radius: 15px;">
                            <i class="fas fa-box-open" style="font-size: 48px; color: #ccc; margin-bottom: 20px;"></i>
                            <p style="color: #666; font-size: 16px;">No items found in this bundle</p>
                        </div>
                    @endif
                </div>

                <!-- Bundle Specifications -->
                <div class="bundle-specs" style="padding: 30px; border-radius: 15px; margin-bottom: 40px;">
                    <h2 style="font-size: 28px; font-weight: 700; color: #333; margin-bottom: 20px;">
                        Bundle Specifications
                    </h2>
                    
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
                        <div style="background: white; padding: 20px; border-radius: 10px;">
                            <h4 style="font-size: 16px; font-weight: 600; color: #333; margin-bottom: 10px;">
                                <i class="fas fa-clock" style="color: #8B0000; margin-right: 8px;"></i>
                                Validity Period
                            </h4>
                            <p style="color: #666; font-size: 14px;">
                                @if($bundle->starts_at && $bundle->ends_at)
                                    {{ $bundle->starts_at->format('M d, Y') }} to {{ $bundle->ends_at->format('M d, Y') }}
                                @elseif($bundle->ends_at)
                                    Valid until {{ $bundle->ends_at->format('M d, Y') }}
                                @else
                                    No expiration date
                                @endif
                            </p>
                        </div>
                        
                        <div style="background: white; padding: 20px; border-radius: 10px;">
                            <h4 style="font-size: 16px; font-weight: 600; color: #333; margin-bottom: 10px;">
                                <i class="fas fa-weight-hanging" style="color: #28a745; margin-right: 8px;"></i>
                                Total Weight
                            </h4>
                            <p style="color: #666; font-size: 14px;">
                                @php
                                    $totalWeight = $bundle->items->sum(function($item) {
                                        return ($item->product->weight ?? 0.5) * $item->qty;
                                    });
                                @endphp
                                Approx. {{ number_format($totalWeight, 1) }} kg
                            </p>
                        </div>
                        
                        <div style="background: white; padding: 20px; border-radius: 10px;">
                            <h4 style="font-size: 16px; font-weight: 600; color: #333; margin-bottom: 10px;">
                                <i class="fas fa-truck" style="color: #007bff; margin-right: 8px;"></i>
                                Delivery
                            </h4>
                            <p style="color: #666; font-size: 14px;">
                                Same day delivery available<br>
                                Free shipping on this bundle
                            </p>
                        </div>
                        
                        <div style="background: white; padding: 20px; border-radius: 10px;">
                            <h4 style="font-size: 16px; font-weight: 600; color: #333; margin-bottom: 10px;">
                                <i class="fas fa-shield-alt" style="color: #6f42c1; margin-right: 8px;"></i>
                                Quality Guarantee
                            </h4>
                            <p style="color: #666; font-size: 14px;">
                                Fresh products guaranteed<br>
                                24-hour quality promise
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Right Column - Sidebar -->
            <div>
                <!-- Purchase Card -->
                <div style="background: white; border: 2px solid #8B0000; border-radius: 15px; padding: 30px; margin-bottom: 30px; position: sticky; top: 100px;">
                    <div style="text-align: center; margin-bottom: 25px;">
                        <div style="background: #8B0000; color: white; padding: 10px 20px; border-radius: 25px; font-size: 14px; font-weight: 700; margin-bottom: 15px;">
                            <i class="fas fa-fire"></i> LIMITED TIME OFFER
                        </div>
                        
                        <div style="font-size: 36px; font-weight: 700; color: #333; margin-bottom: 10px;">
                            ₹{{ number_format($bundle->price, 0) }}
                        </div>
                        
                        @if($bundle->savings > 0)
                            <div style="font-size: 16px; color: #666; margin-bottom: 5px;">
                                <span style="text-decoration: line-through;">₹{{ number_format($bundle->original_price, 0) }}</span>
                            </div>
                            <div style="color: #28a745; font-size: 16px; font-weight: 600;">
                                You save ₹{{ number_format($bundle->savings, 0) }}!
                            </div>
                        @endif
                    </div>
                    
                    <button onclick="addBundleToCart({{ $bundle->id }})"
                            style="width: 100%; background: #8B0000; color: white; border: none; padding: 18px; border-radius: 12px; font-size: 18px; font-weight: 700; cursor: pointer; transition: all 0.3s; margin-bottom: 15px;"
                            onmouseover="this.style.background='#6B0000'; this.style.transform='translateY(-2px)'"
                            onmouseout="this.style.background='#8B0000'; this.style.transform='translateY(0)'">
                        <i class="fas fa-shopping-cart"></i> Add Bundle to Cart
                    </button>
                    
                    <!-- Benefits -->
                    <div style="border-top: 1px solid #f0f0f0; padding-top: 20px;">
                        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                            <i class="fas fa-shipping-fast" style="color: #28a745; font-size: 16px;"></i>
                            <span style="font-size: 14px; color: #666;">Free same-day delivery</span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                            <i class="fas fa-undo" style="color: #007bff; font-size: 16px;"></i>
                            <span style="font-size: 14px; color: #666;">Easy returns within 24 hours</span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <i class="fas fa-certificate" style="color: #ffc107; font-size: 16px;"></i>
                            <span style="font-size: 14px; color: #666;">Quality guaranteed</span>
                        </div>
                    </div>
                </div>
                
                <!-- Share Bundle -->
                <div style="background: #f8f9fa; padding: 25px; border-radius: 15px; margin-bottom: 30px;">
                    <h3 style="font-size: 18px; font-weight: 600; color: #333; margin-bottom: 15px;">
                        Share This Deal
                    </h3>
                    <div style="display: flex; gap: 10px;">
                        <a href="#" onclick="shareOnFacebook()" style="background: #3b5998; color: white; padding: 10px; border-radius: 8px; text-decoration: none; flex: 1; text-align: center;">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" onclick="shareOnTwitter()" style="background: #1da1f2; color: white; padding: 10px; border-radius: 8px; text-decoration: none; flex: 1; text-align: center;">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" onclick="shareOnWhatsApp()" style="background: #25d366; color: white; padding: 10px; border-radius: 8px; text-decoration: none; flex: 1; text-align: center;">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                        <a href="#" onclick="copyLink()" style="background: #666; color: white; padding: 10px; border-radius: 8px; text-decoration: none; flex: 1; text-align: center;">
                            <i class="fas fa-link"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Related Bundles -->
@if($relatedBundles && $relatedBundles->count() > 0)
<section class="skc-section" style="background: #f8f9fa; padding: 80px 0;">
    <div class="skc-container">
        <div style="text-align: center; margin-bottom: 60px;">
            <h2 style="font-size: 36px; font-weight: 700; color: #333; margin-bottom: 15px;">
                Other Limited Time Offers
            </h2>
            <p style="font-size: 18px; color: #666;">
                Don't miss out on these other amazing bundle deals!
            </p>
        </div>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 25px;">
            @foreach($relatedBundles as $relatedBundle)
                <div style="background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.08); transition: all 0.3s;"
                     onmouseover="this.style.transform='translateY(-8px)'; this.style.boxShadow='0 8px 30px rgba(0,0,0,0.15)'"
                     onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 20px rgba(0,0,0,0.08)'">
                    
                    <a href="{{ route('limited-time-offer.show', $relatedBundle->slug) }}" style="text-decoration: none; color: inherit;">
                        <div style="height: 200px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; position: relative;">
                            @if($relatedBundle->image_path)
                                <img src="{{ Storage::url($relatedBundle->image_path) }}" alt="{{ $relatedBundle->name }}" 
                                     style="width: 100%; height: 100%; object-fit: cover;">
                            @else
                                <span style="font-size: 60px;">🎁</span>
                            @endif
                            
                            @if($relatedBundle->savings_percentage > 0)
                                <div style="position: absolute; top: 15px; right: 15px; background: #8B0000; color: white; padding: 8px 15px; border-radius: 20px; font-size: 14px; font-weight: 700;">
                                    {{ $relatedBundle->savings_percentage }}% OFF
                                </div>
                            @endif
                        </div>
                        
                        <div style="padding: 20px;">
                            <h3 style="font-size: 18px; font-weight: 600; color: #333; margin-bottom: 10px;">
                                {{ $relatedBundle->name }}
                            </h3>
                            
                            @if($relatedBundle->description)
                                <p style="color: #666; font-size: 14px; margin-bottom: 15px; line-height: 1.4;">
                                    {{ Str::limit($relatedBundle->description, 80) }}
                                </p>
                            @endif
                            
                            <div style="display: flex; align-items: center; justify-content: space-between;">
                                <div>
                                    @if($relatedBundle->original_price > $relatedBundle->price)
                                        <span style="text-decoration: line-through; color: #999; font-size: 14px; margin-right: 8px;">
                                            ₹{{ number_format($relatedBundle->original_price, 0) }}
                                        </span>
                                    @endif
                                    <span style="font-size: 20px; font-weight: 700; color: #333;">
                                        ₹{{ number_format($relatedBundle->price, 0) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Combo Offers Cross-sell -->
@if($combos && $combos->count() > 0)
<section class="skc-section" style="background: white; padding: 80px 0;">
    <div class="skc-container">
        <div style="text-align: center; margin-bottom: 60px;">
            <h2 style="font-size: 36px; font-weight: 700; color: #333; margin-bottom: 15px;">
                <i class="fas fa-plus-circle" style="color: #28a745; margin-right: 10px;"></i>
                Add Combo Offers to Your Order
            </h2>
            <p style="font-size: 18px; color: #666;">
                Complete your bundle with these special combo deals
            </p>
        </div>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 30px;">
            @foreach($combos as $combo)
                <div style="background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.08); transition: all 0.3s;"
                     onmouseover="this.style.transform='translateY(-5px)'"
                     onmouseout="this.style.transform='translateY(0)'">
                    
                    <a href="{{ route('combo.show', $combo->slug) }}" style="text-decoration: none;">
                        <div style="position: relative; height: 250px; background: linear-gradient(135deg, #f69d1c 0%, #ff8c00 100%); display: flex; align-items: center; justify-content: center;">
                            @if($combo->image_path)
                                <img src="{{ Storage::url($combo->image_path) }}" alt="{{ $combo->name }}" 
                                     style="width: 100%; height: 100%; object-fit: cover;">
                            @else
                                <div style="text-align: center; color: white;">
                                    <i class="fas fa-gift" style="font-size: 64px; margin-bottom: 15px;"></i>
                                    <p style="font-weight: 600; font-size: 20px;">Combo Deal</p>
                                </div>
                            @endif
                            
                            @if($combo->discount_percentage > 0)
                                <div style="position: absolute; top: 20px; right: 20px; background: #8B0000; color: white; padding: 8px 16px; border-radius: 25px; font-weight: 700; font-size: 16px; box-shadow: 0 4px 8px rgba(0,0,0,0.2);">
                                    {{ $combo->discount_percentage }}% OFF
                                </div>
                            @endif
                            
                            @if($combo->ends_at)
                                <div style="position: absolute; bottom: 20px; left: 20px; background: rgba(0,0,0,0.7); color: white; padding: 6px 12px; border-radius: 6px; font-size: 13px;">
                                    <i class="fas fa-clock"></i> Valid until {{ $combo->ends_at->format('M d, Y') }}
                                </div>
                            @endif
                        </div>
                        
                        <div style="padding: 25px;">
                            <h3 style="font-size: 22px; font-weight: 700; color: var(--skc-black); margin: 0 0 12px 0;">{{ $combo->name }}</h3>
                            <p style="color: var(--skc-medium-gray); font-size: 15px; margin: 0 0 20px 0; line-height: 1.6;">{{ $combo->description }}</p>
                            
                            <!-- Products in combo -->
                            <div style="background: #f8f9fa; border-radius: 8px; padding: 15px; margin-bottom: 20px;">
                                <p style="font-size: 14px; font-weight: 600; color: var(--skc-black); margin: 0 0 10px 0;">
                                    <i class="fas fa-box" style="margin-right: 8px; color: var(--skc-orange);"></i>
                                    Items in this combo:
                                </p>
                                <ul style="margin: 0; padding-left: 20px;">
                                    @foreach($combo->products->take(3) as $product)
                                        <li style="color: var(--skc-medium-gray); font-size: 14px; margin-bottom: 5px;">{{ $product->name }}</li>
                                    @endforeach
                                    @if($combo->products->count() > 3)
                                        <li style="color: var(--skc-orange); font-size: 14px; font-weight: 600;">+{{ $combo->products->count() - 3 }} more items</li>
                                    @endif
                                </ul>
                            </div>
                            
                            <!-- Pricing -->
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                                <div>
                                    @if($combo->original_price > $combo->combo_price)
                                        <span style="text-decoration: line-through; color: #999; font-size: 16px;">₹{{ number_format($combo->original_price, 0) }}</span>
                                    @endif
                                    <span style="font-size: 28px; font-weight: 700; color: var(--skc-orange); margin-left: 10px;">₹{{ number_format($combo->combo_price, 0) }}</span>
                                </div>
                                @if($combo->savings > 0)
                                    <span style="background: #e8f5e9; color: #2e7d32; padding: 6px 14px; border-radius: 6px; font-size: 14px; font-weight: 600;">
                                        Save ₹{{ number_format($combo->savings, 0) }}
                                    </span>
                                @endif
                            </div>
                            
                            <!-- View Details Button -->
                            <button style="width: 100%; padding: 14px; background: var(--skc-black); color: white; border: none; border-radius: 8px; font-weight: 600; font-size: 16px; cursor: pointer; transition: all 0.3s;">
                                View Details
                            </button>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<script>
// Countdown Timer
@if($bundle->ends_at)
function startCountdown() {
    const endDate = new Date('{{ $bundle->ends_at->toISOString() }}').getTime();
    
    const timer = setInterval(function() {
        const now = new Date().getTime();
        const timeLeft = endDate - now;
        
        if (timeLeft < 0) {
            clearInterval(timer);
            document.getElementById('countdown-timer').innerHTML = '<div style="color: #8B0000; font-size: 18px; font-weight: 700;">OFFER EXPIRED</div>';
            return;
        }
        
        const days = Math.floor(timeLeft / (1000 * 60 * 60 * 24));
        const hours = Math.floor((timeLeft % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);
        
        document.getElementById('days').textContent = String(days).padStart(2, '0');
        document.getElementById('hours').textContent = String(hours).padStart(2, '0');
        document.getElementById('minutes').textContent = String(minutes).padStart(2, '0');
        document.getElementById('seconds').textContent = String(seconds).padStart(2, '0');
    }, 1000);
}

document.addEventListener('DOMContentLoaded', function() {
    startCountdown();
});
@endif

// Add to cart function
function addBundleToCart(bundleId) {
    fetch('/cart/add-bundle/' + bundleId, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    }).then(response => response.json())
      .then(data => {
          if (data.success) {
              showNotification('Bundle added to cart successfully!', 'success');
              updateCartCount();
          } else {
              showNotification('Failed to add bundle to cart', 'error');
          }
      }).catch(error => {
          console.error('Error:', error);
          showNotification('An error occurred', 'error');
      });
}

// Social sharing functions
function shareOnFacebook() {
    const url = encodeURIComponent(window.location.href);
    const title = encodeURIComponent('{{ $bundle->name }} - Limited Time Offer');
    window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}`, '_blank');
}

function shareOnTwitter() {
    const url = encodeURIComponent(window.location.href);
    const text = encodeURIComponent('Check out this amazing bundle deal: {{ $bundle->name }}');
    window.open(`https://twitter.com/intent/tweet?url=${url}&text=${text}`, '_blank');
}

function shareOnWhatsApp() {
    const url = encodeURIComponent(window.location.href);
    const text = encodeURIComponent('Check out this limited time bundle offer: {{ $bundle->name }} - Save {{ $bundle->savings_percentage }}%!');
    window.open(`https://wa.me/?text=${text} ${url}`, '_blank');
}

function copyLink() {
    navigator.clipboard.writeText(window.location.href).then(function() {
        showNotification('Link copied to clipboard!', 'success');
    });
}

function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${type === 'success' ? '#28a745' : '#dc3545'};
        color: white;
        padding: 15px 20px;
        border-radius: 5px;
        z-index: 10000;
        font-weight: 600;
    `;
    notification.textContent = message;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

function updateCartCount() {
    // Update cart count logic here
    console.log('Updating cart count...');
}
</script>
@endsection