
@extends('web.layouts.app')

@section('content')
    <!-- Hero Section -->
    <section class="skc-hero-section" style="height: 300px;">
        <div class="skc-hero-slider">
            <div class="skc-hero-slide active">
                <img src="https://images.unsplash.com/photo-1607082349566-187342175e2f?w=1600" alt="Shopping Cart" class="skc-hero-image">
                <div class="skc-hero-content">
                    <h1 class="skc-hero-title">Your Shopping Cart</h1>
                    <p class="skc-hero-subtitle">Review your items and proceed to checkout</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Cart Section -->
    <section class="skc-section">
        <div class="skc-container">
            @if($cartItems && count($cartItems) > 0)
                <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 40px;">
                    <!-- Cart Items -->
                    <div style="background: white; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); overflow: hidden;">
                        <div style="padding: 30px; border-bottom: 1px solid var(--skc-border);">
                            <h2 style="font-size: 28px; font-weight: 700; color: var(--skc-black);">Cart Items ({{ count($cartItems) }})</h2>
                        </div>
                        
                        <div class="cart-items-container">
                            @foreach($cartItems as $item)
                                <div class="cart-item" style="padding: 30px; border-bottom: 1px solid var(--skc-border);">
                                    <div style="display: flex; gap: 20px;">
                                        <!-- Product Image -->
                                        <div style="flex-shrink: 0;">
                                            @php($img = $item['product']?->first_image ?? null)
                                            @if($img)
                                                <img src="{{ asset('storage/' . $img) }}" alt="{{ $item['name'] }}" style="width: 100px; height: 100px; object-fit: cover; border-radius: 10px;">
                                            @else
                                                <div style="width: 100px; height: 100px; background: var(--skc-light-gray); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                                                    <i class="fas fa-image" style="font-size: 30px; color: var(--skc-medium-gray);"></i>
                                                </div>
                                            @endif
                                        </div>
                                        
                                        <!-- Product Details -->
                                        <div style="flex: 1;">
                                            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 15px;">
                                                <div>
                                                    <h3 style="font-size: 20px; font-weight: 600; color: var(--skc-black); margin-bottom: 8px;">
                                                        <a href="{{ route('product.show', $item['product']->slug) }}" style="color: inherit; text-decoration: none; transition: color 0.2s;">
                                                            {{ $item['product']->name }}
                                                        </a>
                                                    </h3>
                                                    @if(isset($item['variant']))
                                                        <p style="font-size: 14px; color: var(--skc-medium-gray); margin-bottom: 5px;">Size: {{ $item['variant']->name }}</p>
                                                    @endif
                                                    @if(isset($item['addons']) && count($item['addons']) > 0)
                                                        <div style="margin-top: 8px;">
                                                            <span style="font-size: 14px; font-weight: 600; color: var(--skc-black);">Add-ons:</span>
                                                            @foreach($item['addons'] as $addon)
                                                                <span style="display: inline-block; background: var(--skc-light-gray); color: var(--skc-black); padding: 4px 8px; border-radius: 6px; font-size: 12px; margin: 2px;">
                                                                    {{ $addon->name }}
                                                                </span>
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                </div>
                                                
                                                <!-- Price -->
                                                <div style="text-align: right;">
                                                    <div style="font-size: 24px; font-weight: 700; color: var(--skc-orange); item-price;">
                                                        ₹{{ number_format($item['price'], 2) }}
                                                    </div>
                                                    @if($item['price'] != $item['product']->base_price)
                                                        <div style="font-size: 14px; color: var(--skc-medium-gray); text-decoration: line-through;">
                                                            ₹{{ number_format($item['product']->base_price, 2) }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                            
                                            <!-- Quantity Controls -->
                                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                                <div style="display: flex; align-items: center; gap: 15px;">
                                                    <button type="button" data-key="{{ $item['id'] }}" onclick="updateCartQuantity(this.dataset.key, -1)" 
                                                            style="width: 40px; height: 40px; border: 1px solid var(--skc-border); border-radius: 8px; background: white; color: var(--skc-black); cursor: pointer; transition: all 0.2s; display: flex; align-items: center; justify-content: center;">
                                                        <i class="fas fa-minus" style="font-size: 12px;"></i>
                                                    </button>
                                                    <span style="font-size: 18px; font-weight: 600; color: var(--skc-black); min-width: 40px; text-align: center;">
                                                        {{ $item['quantity'] }}
                                                    </span>
                                                    <button type="button" data-key="{{ $item['id'] }}" onclick="updateCartQuantity(this.dataset.key, 1)" 
                                                            style="width: 40px; height: 40px; border: 1px solid var(--skc-border); border-radius: 8px; background: white; color: var(--skc-black); cursor: pointer; transition: all 0.2s; display: flex; align-items: center; justify-content: center;">
                                                        <i class="fas fa-plus" style="font-size: 12px;"></i>
                                                    </button>
                                                </div>
                                                
                                                <!-- Remove Button -->
                                                <button type="button" data-key="{{ $item['id'] }}" onclick="removeFromCart(this.dataset.key)" 
                                                        style="color: #dc2626; background: none; border: none; cursor: pointer; font-size: 14px; font-weight: 500; transition: color 0.2s; display: flex; align-items: center; gap: 8px;">
                                                    <i class="fas fa-trash"></i>Remove
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Order Summary -->
                    <div style="background: white; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); padding: 30px; height: fit-content;">
                        <h2 style="font-size: 24px; font-weight: 700; color: var(--skc-black); margin-bottom: 25px;">Order Summary</h2>
                        
                        <div style="space-y: 4;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 15px;">
                                <span style="color: var(--skc-medium-gray);">Subtotal</span>
                                <span style="font-weight: 600; color: var(--skc-black);">₹{{ number_format($subtotal, 2) }}</span>
                            </div>
                            
                            @if(isset($tax) && $tax > 0)
                                <div style="display: flex; justify-content: space-between; margin-bottom: 15px;">
                                    <span style="color: var(--skc-medium-gray);">Tax</span>
                                    <span style="font-weight: 600; color: var(--skc-black);">₹{{ number_format($tax, 2) }}</span>
                                </div>
                            @endif
                            
                            @if(isset($shipping) && $shipping > 0)
                                <div style="display: flex; justify-content: space-between; margin-bottom: 15px;">
                                    <span style="color: var(--skc-medium-gray);">Shipping</span>
                                    <span style="font-weight: 600; color: var(--skc-black);">₹{{ number_format($shipping, 2) }}</span>
                                </div>
                            @endif
                            
                            @if(isset($discount) && $discount > 0)
                                <div style="display: flex; justify-content: space-between; margin-bottom: 15px; color: #059669;">
                                    <span>Discount</span>
                                    <span style="font-weight: 600;">-₹{{ number_format($discount, 2) }}</span>
                                </div>
                            @endif
                            
                            <div style="border-top: 2px solid var(--skc-border); padding-top: 15px; margin-top: 15px;">
                                <div style="display: flex; justify-content: space-between; margin-bottom: 20px;">
                                    <span style="font-size: 18px; font-weight: 700; color: var(--skc-black);">Total</span>
                                    <span style="font-size: 24px; font-weight: 700; color: var(--skc-orange);">₹{{ number_format($total, 2) }}</span>
                                </div>
                            </div>
                            
                            <a href="{{ route('checkout.summary') }}" 
                               style="width: 100%; background: var(--skc-orange); color: white; padding: 16px 24px; border: none; border-radius: 8px; font-size: 18px; font-weight: 600; cursor: pointer; transition: all 0.2s; text-decoration: none; display: block; text-align: center; box-shadow: 0 5px 15px rgba(246, 157, 28, 0.3);">
                                Proceed to Checkout
                            </a>
                            
                            <a href="{{ route('products') }}" 
                               style="width: 100%; background: white; color: var(--skc-black); padding: 16px 24px; border: 2px solid var(--skc-border); border-radius: 8px; font-size: 16px; font-weight: 600; cursor: pointer; transition: all 0.2s; text-decoration: none; display: block; text-align: center; margin-top: 15px;">
                                Continue Shopping
                            </a>
                        </div>
                    </div>
                </div>
            @else
                <!-- Empty Cart -->
                <div style="text-align: center; padding: 80px 20px;">
                    <div style="width: 120px; height: 120px; background: var(--skc-light-gray); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 30px;">
                        <i class="fas fa-shopping-cart" style="font-size: 50px; color: var(--skc-medium-gray);"></i>
                    </div>
                    <h2 style="font-size: 32px; font-weight: 700; color: var(--skc-black); margin-bottom: 15px;">Your cart is empty</h2>
                    <p style="font-size: 18px; color: var(--skc-medium-gray); margin-bottom: 30px; line-height: 1.6;">
                        Looks like you haven't added any items to your cart yet. Start shopping to discover our delicious products!
                    </p>
                    <a href="{{ route('products') }}" class="skc-hero-btn" style="background: var(--skc-orange); color: white;">
                        Start Shopping
                    </a>
                </div>
            @endif
        </div>
    </section>

    @push('scripts')
    <script>
        // Update Cart Quantity
        function updateCartQuantity(itemKey, change) {
            fetch('/cart/update', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    item_key: itemKey,
                    change: change
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    window.showToast(data.message || 'Error updating cart', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                window.showToast('Error updating cart', 'error');
            });
        }

        // Remove from Cart
        function removeFromCart(itemKey) {
            if (confirm('Are you sure you want to remove this item from your cart?')) {
                fetch('/cart/remove', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        item_key: itemKey
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        window.showToast(data.message || 'Error removing item', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    window.showToast('Error removing item', 'error');
                });
            }
        }
    </script>
    @endpush
@endsection
