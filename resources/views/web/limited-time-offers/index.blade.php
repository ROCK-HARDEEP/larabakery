@extends('web.layouts.app')

@section('content')
<style>
    /* LTO Page Specific Styles */
    .lto-hero-animation {
        animation: lto-hero-pulse 3s infinite ease-in-out;
    }
    
    @keyframes lto-hero-pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.02); }
    }
    
    .lto-card-blink {
        animation: lto-card-blink 2.5s infinite;
    }
    
    @keyframes lto-card-blink {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.8; }
    }
    
    .lto-badge-floating {
        animation: lto-float 3s ease-in-out infinite;
    }
    
    @keyframes lto-float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-10px); }
    }
    
    .countdown-highlight {
        animation: countdown-pulse 1s infinite;
    }
    
    @keyframes countdown-pulse {
        0%, 100% { background: #ff4757; }
        50% { background: #ff6b7a; }
    }
</style>

<!-- Hero Section with LTO Branding -->
<section class="skc-section lto-hero-animation" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 100px 0; position: relative; overflow: hidden;">
    <div class="skc-container" style="position: relative; z-index: 2;">
        <div style="text-align: center; max-width: 800px; margin: 0 auto;">
            <div class="lto-badge-floating" style="display: inline-block; background: rgba(255, 255, 255, 0.2); padding: 10px 25px; border-radius: 30px; margin-bottom: 20px; border: 2px solid rgba(255, 255, 255, 0.3);">
                <i class="fas fa-fire"></i> Limited Time Only
            </div>
            
            <h1 style="font-size: 48px; font-weight: 800; margin-bottom: 20px; text-shadow: 2px 2px 4px rgba(0,0,0,0.3);">
                Exclusive Bundle Deals
            </h1>
            
            <p style="font-size: 20px; margin-bottom: 40px; opacity: 0.9; line-height: 1.6;">
                Don't miss out on our amazing limited-time bundle offers! Save big on your favorite bakery items.
            </p>
            
            <div style="display: flex; justify-content: center; gap: 20px; flex-wrap: wrap;">
                <div style="background: rgba(255, 255, 255, 0.1); padding: 20px; border-radius: 15px; backdrop-filter: blur(10px);">
                    <div style="font-size: 32px; font-weight: 700;">{{ $bundles->total() }}</div>
                    <div style="font-size: 14px; opacity: 0.8;">Active Offers</div>
                </div>
                <div style="background: rgba(255, 255, 255, 0.1); padding: 20px; border-radius: 15px; backdrop-filter: blur(10px);">
                    <div style="font-size: 32px; font-weight: 700;">Up to 50%</div>
                    <div style="font-size: 14px; opacity: 0.8;">Savings</div>
                </div>
                <div style="background: rgba(255, 255, 255, 0.1); padding: 20px; border-radius: 15px; backdrop-filter: blur(10px);">
                    <div style="font-size: 32px; font-weight: 700;">24H</div>
                    <div style="font-size: 14px; opacity: 0.8;">Fast Delivery</div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Animated Background Elements -->
    <div style="position: absolute; top: 10%; left: 10%; width: 100px; height: 100px; background: rgba(255, 255, 255, 0.1); border-radius: 50%; animation: lto-float 4s ease-in-out infinite;"></div>
    <div style="position: absolute; top: 60%; right: 15%; width: 150px; height: 150px; background: rgba(255, 255, 255, 0.05); border-radius: 50%; animation: lto-float 3s ease-in-out infinite reverse;"></div>
</section>

<!-- Limited Time Offers Grid -->
<section class="skc-section" style="background: #f8f9fa; padding: 80px 0;">
    <div class="skc-container">
        <div style="text-align: center; margin-bottom: 60px;">
            <h2 style="font-size: 36px; font-weight: 700; color: #333; margin-bottom: 15px;">
                <i class="fas fa-bolt" style="color: #ff4757; margin-right: 10px;"></i>
                Limited Time Bundle Offers
            </h2>
            <p style="font-size: 18px; color: #666; max-width: 600px; margin: 0 auto;">
                Hurry up! These exclusive bundle deals won't last long. Get your favorites at unbeatable prices.
            </p>
        </div>

        @if($bundles && $bundles->count() > 0)
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(400px, 1fr)); gap: 30px; margin-bottom: 40px;">
                @foreach($bundles as $bundle)
                    <div class="lto-card-blink" style="background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 8px 32px rgba(0,0,0,0.1); transition: all 0.3s; position: relative;">
                        <!-- Urgency Badge -->
                        <div class="countdown-highlight" style="position: absolute; top: 20px; right: 20px; color: white; padding: 8px 16px; border-radius: 20px; font-size: 12px; font-weight: 700; z-index: 10;">
                            <i class="fas fa-clock"></i> Hurry Up!
                        </div>
                        
                        <!-- Bundle Image -->
                        <div style="height: 250px; background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); display: flex; align-items: center; justify-content: center; position: relative;">
                            @if($bundle->image_path)
                                <img src="{{ Storage::url($bundle->image_path) }}" alt="{{ $bundle->name }}" 
                                     style="width: 100%; height: 100%; object-fit: cover;">
                            @else
                                <span style="font-size: 80px;">🎁</span>
                            @endif
                            
                            <!-- Discount Badge -->
                            @if($bundle->savings_percentage > 0)
                                <div style="position: absolute; top: 20px; left: 20px; background: #28a745; color: white; padding: 10px 20px; border-radius: 25px; font-size: 16px; font-weight: 700;">
                                    {{ $bundle->savings_percentage }}% OFF
                                </div>
                            @endif
                        </div>
                        
                        <!-- Bundle Details -->
                        <div style="padding: 25px;">
                            <h3 style="font-size: 24px; font-weight: 700; color: #333; margin-bottom: 15px;">
                                {{ $bundle->name }}
                            </h3>
                            
                            @if($bundle->description)
                                <p style="color: #666; margin-bottom: 20px; line-height: 1.6;">
                                    {{ Str::limit($bundle->description, 100) }}
                                </p>
                            @endif
                            
                            <!-- Bundle Contents Preview -->
                            <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                                <div style="font-size: 14px; font-weight: 600; color: #333; margin-bottom: 10px;">
                                    <i class="fas fa-box"></i> Bundle Contains:
                                </div>
                                <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                                    @if($bundle->items && $bundle->items->count() > 0)
                                        @foreach($bundle->items->take(3) as $item)
                                            <span style="background: white; padding: 5px 10px; border-radius: 15px; font-size: 12px; color: #666;">
                                                {{ $item->product->name }} × {{ $item->qty }}
                                            </span>
                                        @endforeach
                                        @if($bundle->items->count() > 3)
                                            <span style="background: #007bff; color: white; padding: 5px 10px; border-radius: 15px; font-size: 12px;">
                                                +{{ $bundle->items->count() - 3 }} more
                                            </span>
                                        @endif
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Pricing -->
                            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px;">
                                <div>
                                    @if($bundle->original_price > $bundle->price)
                                        <span style="text-decoration: line-through; color: #999; font-size: 16px; margin-right: 10px;">
                                            ₹{{ number_format($bundle->original_price, 0) }}
                                        </span>
                                    @endif
                                    <span style="font-size: 28px; font-weight: 700; color: #333;">
                                        ₹{{ number_format($bundle->price, 0) }}
                                    </span>
                                </div>
                                @if($bundle->savings > 0)
                                    <div style="background: #e8f5e9; color: #2e7d32; padding: 8px 16px; border-radius: 20px; font-size: 14px; font-weight: 600;">
                                        Save ₹{{ number_format($bundle->savings, 0) }}
                                    </div>
                                @endif
                            </div>
                            
                            <!-- Action Buttons -->
                            <div style="display: flex; gap: 10px; margin-bottom: 15px;">
                                <button onclick="addBundleToCart({{ $bundle->id }})" 
                                        style="flex: 1; background: #ff4757; color: white; border: none; padding: 15px; border-radius: 10px; font-size: 16px; font-weight: 600; cursor: pointer; transition: all 0.3s;"
                                        onmouseover="this.style.background='#ff3742'; this.style.transform='translateY(-2px)'"
                                        onmouseout="this.style.background='#ff4757'; this.style.transform='translateY(0)'">
                                    <i class="fas fa-shopping-cart"></i> Add to Cart
                                </button>
                                <a href="{{ route('limited-time-offer.show', $bundle->slug) }}" 
                                   style="flex: 1; background: transparent; color: #ff4757; border: 2px solid #ff4757; padding: 15px; border-radius: 10px; font-size: 16px; font-weight: 600; cursor: pointer; transition: all 0.3s; text-decoration: none; display: flex; align-items: center; justify-content: center;"
                                   onmouseover="this.style.background='#ff4757'; this.style.color='white'; this.style.transform='translateY(-2px)'"
                                   onmouseout="this.style.background='transparent'; this.style.color='#ff4757'; this.style.transform='translateY(0)'">
                                    <i class="fas fa-eye"></i> View Details
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <!-- Pagination -->
            @if($bundles->hasPages())
                <div style="display: flex; justify-content: center;">
                    {{ $bundles->links() }}
                </div>
            @endif
        @else
            <div style="text-align: center; padding: 60px 20px;">
                <i class="fas fa-clock" style="font-size: 64px; color: #ddd; margin-bottom: 20px;"></i>
                <h3 style="font-size: 24px; color: #666; margin-bottom: 10px;">No Active Limited Time Offers</h3>
                <p style="color: #999; margin-bottom: 30px;">Check back soon for exciting bundle deals!</p>
                <a href="{{ route('products') }}" style="display: inline-block; background: #007bff; color: white; padding: 12px 30px; border-radius: 6px; text-decoration: none; font-weight: 600;">
                    Browse Products
                </a>
            </div>
        @endif
    </div>
</section>

<!-- Combo Offers Section -->
@if($combos && $combos->count() > 0)
<section class="skc-section" style="background: white; padding: 80px 0;">
    <div class="skc-container">
        <div style="text-align: center; margin-bottom: 60px;">
            <h2 style="font-size: 36px; font-weight: 700; color: #333; margin-bottom: 15px;">
                <i class="fas fa-gift" style="color: #28a745; margin-right: 10px;"></i>
                Special Combo Offers
            </h2>
            <p style="font-size: 18px; color: #666; max-width: 600px; margin: 0 auto;">
                Complete your order with our specially curated combo deals that save you more!
            </p>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 25px;">
            @foreach($combos as $combo)
                <div style="background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.08); transition: all 0.3s; border: 2px solid transparent;"
                     onmouseover="this.style.borderColor='#28a745'; this.style.transform='translateY(-5px)'"
                     onmouseout="this.style.borderColor='transparent'; this.style.transform='translateY(0)'">
                    
                    <div style="height: 200px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; position: relative;">
                        @if($combo->image_path)
                            <img src="{{ Storage::url($combo->image_path) }}" alt="{{ $combo->name }}" 
                                 style="width: 100%; height: 100%; object-fit: cover;">
                        @else
                            <span style="font-size: 60px;">🎁</span>
                        @endif
                        
                        @if($combo->discount_percentage > 0)
                            <div style="position: absolute; top: 15px; right: 15px; background: #ff4757; color: white; padding: 8px 15px; border-radius: 20px; font-size: 14px; font-weight: 700;">
                                {{ $combo->discount_percentage }}% OFF
                            </div>
                        @endif
                    </div>
                    
                    <div style="padding: 20px;">
                        <h3 style="font-size: 20px; font-weight: 600; color: #333; margin-bottom: 10px;">
                            {{ $combo->name }}
                        </h3>
                        
                        <p style="color: #666; font-size: 14px; margin-bottom: 15px; line-height: 1.5;">
                            {{ Str::limit($combo->description, 80) }}
                        </p>
                        
                        <!-- Price -->
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 15px;">
                            <div>
                                @if($combo->original_price > $combo->combo_price)
                                    <span style="text-decoration: line-through; color: #999; font-size: 14px; margin-right: 8px;">
                                        ₹{{ number_format($combo->original_price, 0) }}
                                    </span>
                                @endif
                                <span style="font-size: 22px; font-weight: 700; color: #28a745;">
                                    ₹{{ number_format($combo->combo_price, 0) }}
                                </span>
                            </div>
                        </div>
                        
                        <a href="{{ route('combo.show', $combo->slug) }}" 
                           style="display: block; width: 100%; text-align: center; background: #28a745; color: white; padding: 12px; border-radius: 8px; text-decoration: none; font-weight: 600; transition: all 0.3s;"
                           onmouseover="this.style.background='#218838'"
                           onmouseout="this.style.background='#28a745'">
                            View Combo Details
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
        
        <div style="text-align: center; margin-top: 40px;">
            <a href="{{ route('combos.index') }}" 
               style="display: inline-block; background: transparent; color: #28a745; border: 2px solid #28a745; padding: 12px 30px; border-radius: 8px; text-decoration: none; font-weight: 600; transition: all 0.3s;"
               onmouseover="this.style.background='#28a745'; this.style.color='white'"
               onmouseout="this.style.background='transparent'; this.style.color='#28a745'">
                View All Combo Offers
            </a>
        </div>
    </div>
</section>
@endif

<!-- Featured Products Section -->
@if($featuredProducts && $featuredProducts->count() > 0)
<section class="skc-section" style="background: #f8f9fa; padding: 80px 0;">
    <div class="skc-container">
        <div style="text-align: center; margin-bottom: 60px;">
            <h2 style="font-size: 36px; font-weight: 700; color: #333; margin-bottom: 15px;">
                <i class="fas fa-star" style="color: #ffc107; margin-right: 10px;"></i>
                You Might Also Like
            </h2>
            <p style="font-size: 18px; color: #666; max-width: 600px; margin: 0 auto;">
                Complete your shopping with our handpicked featured products
            </p>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 25px;">
            @foreach($featuredProducts as $product)
                <div style="background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.08); transition: all 0.3s;"
                     onmouseover="this.style.transform='translateY(-8px)'; this.style.boxShadow='0 8px 30px rgba(0,0,0,0.15)'"
                     onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 20px rgba(0,0,0,0.08)'">
                    
                    <a href="{{ route('product.show', $product->slug) }}" style="text-decoration: none; color: inherit;">
                        <div style="height: 200px; background: #f8f9fa; display: flex; align-items: center; justify-content: center;">
                            @if($product->main_image)
                                <img src="{{ Storage::url($product->main_image) }}" alt="{{ $product->name }}" 
                                     style="width: 100%; height: 100%; object-fit: cover;">
                            @else
                                <span style="font-size: 60px; opacity: 0.5;">🥖</span>
                            @endif
                        </div>
                        
                        <div style="padding: 20px;">
                            <h3 style="font-size: 18px; font-weight: 600; color: #333; margin-bottom: 8px;">
                                {{ $product->name }}
                            </h3>
                            
                            @if($product->short_description)
                                <p style="color: #666; font-size: 14px; margin-bottom: 15px; line-height: 1.4;">
                                    {{ Str::limit($product->short_description, 60) }}
                                </p>
                            @endif
                            
                            <div style="display: flex; align-items: center; justify-content: space-between;">
                                <span style="font-size: 20px; font-weight: 700; color: #333;">
                                    ₹{{ number_format($product->price, 0) }}
                                </span>
                                
                                <button onclick="quickAddToCart({{ $product->id }})"
                                        style="background: #007bff; color: white; border: none; padding: 8px 16px; border-radius: 6px; font-size: 12px; font-weight: 600; cursor: pointer; transition: all 0.3s;"
                                        onmouseover="this.style.background='#0056b3'"
                                        onmouseout="this.style.background='#007bff'">
                                    <i class="fas fa-plus"></i> Add
                                </button>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Call to Action Section -->
<section class="skc-section" style="background: linear-gradient(135deg, #ff4757 0%, #ff6b7a 100%); color: white; padding: 80px 0; text-align: center;">
    <div class="skc-container">
        <h2 style="font-size: 42px; font-weight: 800; margin-bottom: 20px;">
            Don't Wait, These Offers Won't Last!
        </h2>
        <p style="font-size: 20px; margin-bottom: 40px; opacity: 0.9;">
            Get your favorite bundles before they're gone. Limited quantities available!
        </p>
        
        <div style="display: flex; justify-content: center; gap: 20px; flex-wrap: wrap;">
            <a href="{{ route('products') }}" 
               style="background: white; color: #ff4757; padding: 16px 32px; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 18px; transition: all 0.3s;"
               onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 8px 25px rgba(0,0,0,0.2)'"
               onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                Browse All Products
            </a>
            <a href="{{ route('combos.index') }}" 
               style="background: transparent; color: white; border: 2px solid white; padding: 16px 32px; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 18px; transition: all 0.3s;"
               onmouseover="this.style.background='white'; this.style.color='#ff4757'"
               onmouseout="this.style.background='transparent'; this.style.color='white'">
                View Combo Offers
            </a>
        </div>
    </div>
</section>

<script>
// Add to cart functions
function addBundleToCart(bundleId) {
    // Add bundle to cart logic
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

function quickAddToCart(productId) {
    // Add single product to cart logic
    fetch('/cart/add', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            product_id: productId,
            quantity: 1
        })
    }).then(response => response.json())
      .then(data => {
          if (data.success) {
              showNotification('Product added to cart!', 'success');
              updateCartCount();
          } else {
              showNotification('Failed to add product to cart', 'error');
          }
      }).catch(error => {
          console.error('Error:', error);
          showNotification('An error occurred', 'error');
      });
}

function showNotification(message, type) {
    // Simple notification system
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
    // Update cart count in header
    // This would need to be implemented based on your cart system
    console.log('Updating cart count...');
}
</script>
@endsection