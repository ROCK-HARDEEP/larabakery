@extends('web.layouts.app')

@section('title', 'My Wishlist')

@section('content')
<section class="skc-section" style="padding-top: 20px; background: #f8f8f8; min-height: 100vh;">
    <!-- Breadcrumb -->
    <div style="background: white; border-bottom: 1px solid var(--skc-border); margin-bottom: 30px;">
        <div class="skc-container" style="padding: 15px 20px;">
            <nav style="display: flex; align-items: center; gap: 10px; font-size: 14px;">
                <a href="{{ route('home') }}" style="color: var(--skc-medium-gray); text-decoration: none; transition: color 0.2s;">Home</a>
                <span style="color: #ccc;">/</span>
                <span style="color: var(--skc-black); font-weight: 600;">My Wishlist</span>
            </nav>
        </div>
    </div>

    <div class="skc-container">
        <div style="background: white; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,0.08); overflow: hidden; padding: 40px;">
            
            <!-- Header -->
            <div style="margin-bottom: 40px; text-align: center;">
                <h1 style="font-size: 36px; font-weight: 700; color: var(--skc-black); margin: 0 0 10px 0;">
                    <i class="fas fa-heart" style="color: #e74c3c; margin-right: 15px;"></i>
                    My Wishlist
                </h1>
                <p style="color: var(--skc-medium-gray); font-size: 16px;">Your favorite items saved for later</p>
            </div>

            @if($wishlists && $wishlists->count() > 0)
                <!-- Wishlist Items -->
                <div id="wishlist-items" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 30px;">
                    @foreach($wishlists as $wishlist)
                        <div class="wishlist-item" data-product-id="{{ $wishlist->product->id }}" style="background: #fafafa; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.05); transition: all 0.3s ease;">
                            <!-- Product Image -->
                            <div style="position: relative; aspect-ratio: 1/1; background: white;">
                                @php
                                    $images = $wishlist->product->images_path ?? [];
                                    $hasImages = !empty($images) && is_array($images);
                                @endphp
                                
                                @if($hasImages)
                                    <img src="{{ asset('storage/' . $images[0]) }}" 
                                         alt="{{ $wishlist->product->name }}" 
                                         style="width: 100%; height: 100%; object-fit: cover;">
                                @else
                                    <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background: #f5f5f5;">
                                        <span style="color: #999; font-size: 14px;">No Image</span>
                                    </div>
                                @endif

                                <!-- Remove Button -->
                                <button onclick="removeFromWishlist({{ $wishlist->product->id }})" 
                                        style="position: absolute; top: 15px; right: 15px; width: 35px; height: 35px; border-radius: 50%; background: rgba(231, 76, 60, 0.9); border: none; color: white; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.2s; z-index: 2;">
                                    <i class="fas fa-times" style="font-size: 14px;"></i>
                                </button>

                                <!-- Product Badge -->
                                @if($wishlist->product->has_discount && $wishlist->product->discount_percentage)
                                    <div style="position: absolute; top: 15px; left: 15px; background: #4caf50; color: white; padding: 5px 10px; border-radius: 15px; font-size: 12px; font-weight: 600;">
                                        -{{ number_format($wishlist->product->discount_percentage) }}%
                                    </div>
                                @endif
                            </div>

                            <!-- Product Details -->
                            <div style="padding: 20px;">
                                <h3 style="font-size: 18px; font-weight: 600; color: var(--skc-black); margin: 0 0 10px 0; line-height: 1.3;">
                                    <a href="{{ route('product.show', $wishlist->product->slug) }}" style="color: inherit; text-decoration: none;">
                                        {{ $wishlist->product->name }}
                                    </a>
                                </h3>
                                
                                <div style="margin-bottom: 15px;">
                                    @if($wishlist->product->has_discount && $wishlist->product->discount_price)
                                        <span style="font-size: 20px; font-weight: 700; color: var(--skc-orange);">₹{{ number_format($wishlist->product->discount_price, 2) }}</span>
                                        <span style="font-size: 14px; color: #999; text-decoration: line-through; margin-left: 8px;">₹{{ number_format($wishlist->product->base_price, 2) }}</span>
                                    @else
                                        <span style="font-size: 20px; font-weight: 700; color: var(--skc-orange);">₹{{ number_format($wishlist->product->base_price, 2) }}</span>
                                    @endif
                                </div>

                                <!-- Stock Status -->
                                @if($wishlist->product->stock > 0)
                                    <div style="margin-bottom: 15px; color: #4caf50; font-size: 14px; font-weight: 500;">
                                        <i class="fas fa-check-circle" style="margin-right: 5px;"></i>
                                        In Stock ({{ $wishlist->product->stock }} available)
                                    </div>
                                @else
                                    <div style="margin-bottom: 15px; color: #e74c3c; font-size: 14px; font-weight: 500;">
                                        <i class="fas fa-times-circle" style="margin-right: 5px;"></i>
                                        Out of Stock
                                    </div>
                                @endif

                                <!-- Action Buttons -->
                                <div style="display: flex; gap: 10px;">
                                    @if($wishlist->product->stock > 0)
                                        <button onclick="addToCart({{ $wishlist->product->id }})" 
                                                style="flex: 1; background: var(--skc-orange); color: white; border: none; padding: 12px; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.2s;">
                                            <i class="fas fa-shopping-cart" style="margin-right: 8px;"></i>
                                            Add to Cart
                                        </button>
                                    @else
                                        <button disabled 
                                                style="flex: 1; background: #ccc; color: #666; border: none; padding: 12px; border-radius: 8px; font-weight: 600; cursor: not-allowed;">
                                            <i class="fas fa-ban" style="margin-right: 8px;"></i>
                                            Out of Stock
                                        </button>
                                    @endif
                                    
                                    <a href="{{ route('product.show', $wishlist->product->slug) }}" 
                                       style="background: var(--skc-black); color: white; border: none; padding: 12px 20px; border-radius: 8px; text-decoration: none; display: flex; align-items: center; justify-content: center; transition: all 0.2s;">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Clear All Wishlist -->
                <div style="text-align: center; margin-top: 40px; padding-top: 30px; border-top: 1px solid #eee;">
                    <button onclick="clearWishlist()" 
                            style="background: none; border: 2px solid #e74c3c; color: #e74c3c; padding: 12px 30px; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.2s;">
                        <i class="fas fa-trash" style="margin-right: 8px;"></i>
                        Clear All Wishlist
                    </button>
                </div>

            @else
                <!-- Empty Wishlist -->
                <div style="text-align: center; padding: 60px 20px;">
                    <div style="font-size: 80px; color: #e0e0e0; margin-bottom: 20px;">
                        <i class="far fa-heart"></i>
                    </div>
                    <h2 style="font-size: 28px; font-weight: 600; color: var(--skc-black); margin: 0 0 15px 0;">Your Wishlist is Empty</h2>
                    <p style="color: var(--skc-medium-gray); font-size: 16px; margin-bottom: 30px;">Start adding your favorite products to see them here!</p>
                    <a href="{{ route('products') }}" 
                       style="display: inline-block; background: var(--skc-orange); color: white; padding: 15px 30px; border-radius: 8px; text-decoration: none; font-weight: 600; transition: all 0.2s;">
                        <i class="fas fa-shopping-bag" style="margin-right: 10px;"></i>
                        Start Shopping
                    </a>
                </div>
            @endif
        </div>
    </div>
</section>

<script>
// Remove from wishlist
function removeFromWishlist(productId) {
    fetch('/wishlist/toggle', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ product_id: productId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Remove the item from DOM
            const item = document.querySelector(`[data-product-id="${productId}"]`);
            if (item) {
                item.style.transform = 'scale(0.8)';
                item.style.opacity = '0';
                setTimeout(() => {
                    item.remove();
                    
                    // Check if wishlist is empty
                    const remainingItems = document.querySelectorAll('.wishlist-item');
                    if (remainingItems.length === 0) {
                        location.reload(); // Reload to show empty state
                    }
                }, 300);
            }
            
            // Update wishlist count in header
            if (data.wishlist_count !== undefined) {
                updateWishlistCount(data.wishlist_count);
            }
            
            showToast(data.message, 'success');
        } else {
            showToast(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('An error occurred. Please try again.', 'error');
    });
}

// Add to cart
function addToCart(productId) {
    fetch('/cart/add', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ 
            product_id: productId,
            quantity: 1
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Product added to cart!', 'success');
            if (data.cart_count) {
                updateCartCount(data.cart_count);
            }
        } else {
            showToast(data.message || 'Failed to add to cart', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('An error occurred. Please try again.', 'error');
    });
}

// Clear all wishlist
function clearWishlist() {
    if (confirm('Are you sure you want to remove all items from your wishlist?')) {
        const wishlistItems = document.querySelectorAll('.wishlist-item');
        wishlistItems.forEach(item => {
            const productId = item.getAttribute('data-product-id');
            removeFromWishlist(parseInt(productId));
        });
    }
}

// Update wishlist count in header
function updateWishlistCount(count) {
    const wishlistCountElement = document.querySelector('.wishlist-count');
    if (wishlistCountElement) {
        wishlistCountElement.textContent = count;
        wishlistCountElement.style.display = count > 0 ? 'block' : 'none';
    }
}

// Add hover effects
document.addEventListener('DOMContentLoaded', function() {
    // Wishlist item hover effects
    document.querySelectorAll('.wishlist-item').forEach(item => {
        item.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px)';
            this.style.boxShadow = '0 8px 25px rgba(0,0,0,0.15)';
        });
        
        item.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = '0 4px 12px rgba(0,0,0,0.05)';
        });
    });

    // Button hover effects
    document.querySelectorAll('button').forEach(btn => {
        if (btn.style.background === 'var(--skc-orange)' || btn.style.background.includes('orange')) {
            btn.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-1px)';
                this.style.boxShadow = '0 4px 12px rgba(246,157,28,0.3)';
            });
            btn.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
                this.style.boxShadow = 'none';
            });
        }
    });
});
</script>
@endsection