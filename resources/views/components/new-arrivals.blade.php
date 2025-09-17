<!-- New Arrivals Section -->
<section class="skc-section new-arrivals-section" style="background: white; padding: 80px 0;">
    <div class="skc-container">
        <div class="skc-section-header" style="margin-bottom: 50px; text-align: center;">
            <h2 class="skc-section-title" style="font-size: 42px; font-weight: 700; color: var(--skc-black); margin: 0 0 15px 0;">New Arrivals</h2>
            <p class="skc-section-subtitle" style="font-size: 18px; color: var(--skc-medium-gray); margin: 0;">Fresh additions to our bakery collection</p>
        </div>

        <!-- New Products Grid - Max 5 per row, auto-adjust size -->
        <div class="new-arrivals-grid-container">
            @foreach($newArrivals as $product)
            <div class="new-arrival-grid-item">
                <div class="new-arrival-card" data-product-id="{{ $product->id }}" data-variants="{{ json_encode($product->active_variants->map(function($variant) {
    return [
        'id' => $variant->id,
        'variant_type' => $variant->variant_type,
        'variant_value' => $variant->variant_value,
        'price' => $variant->price,
        'stock_quantity' => $variant->stock_quantity
    ];
})) }}">
                    {{-- Always show New badge for this section --}}
                    <span class="product-badge new">New</span>

                    <a href="{{ route('product.show', $product->slug) }}" class="product-image-link">
                        <div class="product-image-wrapper">
                            @if($product->image_url && $product->image_url !== asset('img/placeholder-product.jpg'))
                                <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="product-image" loading="lazy">
                            @else
                                <div class="product-image-placeholder">
                                    <i class="fas fa-cake-candles"></i>
                                    <span>No Image</span>
                                </div>
                            @endif

                            <!-- Detail View Overlay -->
                            <div class="product-overlay">
                                <button type="button" class="detail-view-btn" onclick="event.stopPropagation(); window.location.href='{{ route('product.show', $product->slug) }}'">
                                    <i class="fas fa-eye"></i> Detail View
                                </button>
                            </div>
                        </div>
                    </a>

                    <div class="product-details">
                        <a href="{{ route('product.show', $product->slug) }}" class="product-name-link">
                            <h3 class="product-name">{{ $product->name }}</h3>
                        </a>

                        <p class="product-description">{{ Str::limit($product->description, 60) }}</p>

                        <div class="product-rating">
                            @if($product->display_rating > 0)
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $product->display_rating)
                                        <i class="fas fa-star" style="color: #ffc107;"></i>
                                    @elseif($i - $product->display_rating < 1)
                                        <i class="fas fa-star-half-alt" style="color: #ffc107;"></i>
                                    @else
                                        <i class="far fa-star" style="color: #ddd;"></i>
                                    @endif
                                @endfor
                                <span class="rating-count">({{ $product->reviews_count }})</span>
                            @else
                                <div style="height: 20px; display: flex; align-items: center; color: #999; font-size: 12px;">
                                    No reviews yet
                                </div>
                            @endif
                        </div>

                        <div class="product-price-section">
                            <div class="price-wrapper">
                                @if($product->has_variable_pricing)
                                    <span style="font-size: 14px; color: #666; margin-right: 4px;">From</span>
                                @endif
                                <span class="final-price">₹{{ number_format($product->min_price, 0) }}</span>
                                @if($product->has_variable_pricing)
                                    <span style="font-size: 14px; color: #666; margin-left: 4px;">onwards</span>
                                @endif
                            </div>

                            <!-- Updated to use variant modal -->
                            @if($product->active_variants->count() > 0)
                                <button type="button" class="add-to-cart-btn" onclick="event.stopPropagation(); openVariantModal({{ $product->id }}, '{{ addslashes($product->name) }}', '{{ $product->image_url }}', '{{ $product->display_price }}')">
                                    <i class="fas fa-shopping-cart"></i>
                                    <span>Add to Cart</span>
                                </button>
                            @else
                                <a href="{{ route('product.show', $product->slug) }}" class="add-to-cart-btn" style="text-decoration: none; color: inherit;">
                                    <i class="fas fa-eye"></i>
                                    <span>View Product</span>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        @if($newArrivals->count() === 0)
        <!-- No New Arrivals Message -->
        <div style="text-align: center; padding: 60px 20px; color: var(--skc-medium-gray);">
            <i class="fas fa-box-open" style="font-size: 64px; opacity: 0.3; margin-bottom: 20px;"></i>
            <h3 style="font-size: 24px; margin-bottom: 10px;">No New Arrivals Yet</h3>
            <p>Check back soon for our latest bakery creations!</p>
        </div>
        @endif
    </div>
</section>

<style>
/* New Arrivals Grid Container */
.new-arrivals-grid-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 25px;
    margin-bottom: 40px;
    max-width: 1400px;
    margin-left: auto;
    margin-right: auto;
}

/* Responsive grid adjustments for consistent 5-column max layout */
@media (min-width: 1400px) {
    .new-arrivals-grid-container {
        grid-template-columns: repeat(5, 1fr);
        max-width: 1400px;
    }
}

@media (max-width: 1399px) and (min-width: 1200px) {
    .new-arrivals-grid-container {
        grid-template-columns: repeat(4, 1fr);
    }
}

@media (max-width: 1199px) and (min-width: 900px) {
    .new-arrivals-grid-container {
        grid-template-columns: repeat(3, 1fr);
    }
}

@media (max-width: 899px) and (min-width: 600px) {
    .new-arrivals-grid-container {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 599px) {
    .new-arrivals-grid-container {
        grid-template-columns: 1fr;
        gap: 20px;
    }
}

/* Auto-alignment logic for different numbers of items */
.new-arrivals-grid-container:has(.new-arrival-grid-item:nth-child(1):nth-last-child(1)) {
    /* 1 item - center it */
    justify-items: center;
    max-width: 280px;
}

.new-arrivals-grid-container:has(.new-arrival-grid-item:nth-child(2):nth-last-child(1)) {
    /* 2 items - center them */
    max-width: 600px;
}

.new-arrivals-grid-container:has(.new-arrival-grid-item:nth-child(3):nth-last-child(1)) {
    /* 3 items - center them */
    max-width: 900px;
}

.new-arrivals-grid-container:has(.new-arrival-grid-item:nth-child(4):nth-last-child(1)) {
    /* 4 items - center them */
    max-width: 1200px;
}

/* New Arrival Item */
.new-arrival-grid-item {
    transition: all 0.3s ease;
}

/* New Arrival Card */
.new-arrival-card {
    background: white;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
    height: 100%;
    display: flex;
    flex-direction: column;
    position: relative;
    border: 2px solid transparent;
}

.new-arrival-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 35px rgba(0,0,0,0.15);
    border-color: var(--skc-orange);
}

/* Product Badge - Reuse existing styles but ensure new badge stands out */
.new-arrival-card .product-badge {
    position: absolute;
    top: 15px;
    left: 15px;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    z-index: 10;
    letter-spacing: 0.5px;
}

.new-arrival-card .product-badge.new {
    background: #007bff;
    color: white;
    box-shadow: 0 2px 10px rgba(0, 123, 255, 0.3);
}

/* Discount Badge */
.new-arrival-card .product-discount-badge {
    position: absolute;
    top: 15px;
    right: 15px;
    background: var(--skc-orange);
    color: white;
    padding: 5px 10px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    z-index: 10;
}

/* Product Image */
.new-arrival-card .product-image-link {
    display: block;
    position: relative;
    overflow: hidden;
    height: 220px;
}

.new-arrival-card .product-image-wrapper {
    position: relative;
    height: 100%;
    overflow: hidden;
    background: #f8f9fa;
}

.new-arrival-card .product-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.new-arrival-card .product-image-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    background: #f8f9fa;
    color: var(--skc-medium-gray);
    gap: 10px;
}

.new-arrival-card .product-image-placeholder i {
    font-size: 48px;
    opacity: 0.5;
}

.new-arrival-card .product-image-placeholder span {
    font-size: 14px;
    font-weight: 500;
}

.new-arrival-card:hover .product-image {
    transform: scale(1.1);
}

/* Product Overlay */
.new-arrival-card .product-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.7);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.new-arrival-card:hover .product-overlay {
    opacity: 1;
}

.new-arrival-card .detail-view-btn {
    background: white;
    color: var(--skc-black);
    padding: 10px 20px;
    border: none;
    border-radius: 25px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}

.new-arrival-card .detail-view-btn:hover {
    background: var(--skc-orange);
    color: white;
    transform: scale(1.05);
}

/* Product Details */
.new-arrival-card .product-details {
    padding: 20px;
    flex: 1;
    display: flex;
    flex-direction: column;
}

.new-arrival-card .product-name-link {
    text-decoration: none;
    color: inherit;
}

.new-arrival-card .product-name {
    font-size: 16px;
    font-weight: 600;
    color: var(--skc-black);
    margin: 0 0 8px 0;
    line-height: 1.3;
    transition: color 0.3s ease;
}

.new-arrival-card .product-name:hover {
    color: var(--skc-orange);
}

.new-arrival-card .product-description {
    font-size: 13px;
    color: var(--skc-medium-gray);
    line-height: 1.5;
    margin: 0 0 12px 0;
    flex: 1;
}

/* Product Rating */
.new-arrival-card .product-rating {
    display: flex;
    align-items: center;
    gap: 3px;
    margin-bottom: 15px;
}

.new-arrival-card .product-rating i {
    font-size: 12px;
    color: #ffc107;
}

.new-arrival-card .rating-count {
    font-size: 12px;
    color: var(--skc-medium-gray);
    margin-left: 5px;
}

/* Product Price Section */
.new-arrival-card .product-price-section {
    display: flex;
    flex-direction: column;
    gap: 12px;
    margin-top: auto;
}

.new-arrival-card .price-wrapper {
    display: flex;
    align-items: center;
    gap: 8px;
}

.new-arrival-card .original-price {
    text-decoration: line-through;
    color: #999;
    font-size: 14px;
}

.new-arrival-card .final-price {
    font-size: 20px;
    font-weight: 700;
    color: var(--skc-orange);
}

/* Add to Cart Button */
.new-arrival-card .add-to-cart-btn {
    background: var(--skc-orange);
    color: white;
    padding: 12px 20px;
    border: none;
    border-radius: 25px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    width: 100%;
    z-index: 10;
    position: relative;
}

.new-arrival-card .add-to-cart-btn:hover {
    background: var(--skc-black);
    transform: scale(1.02);
}

.new-arrival-card .add-to-cart-btn i {
    font-size: 14px;
}

/* Animation for new items */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.new-arrival-grid-item {
    animation: fadeInUp 0.5s ease-out forwards;
    opacity: 0;
}

.new-arrival-grid-item:nth-child(1) { animation-delay: 0.1s; }
.new-arrival-grid-item:nth-child(2) { animation-delay: 0.15s; }
.new-arrival-grid-item:nth-child(3) { animation-delay: 0.2s; }
.new-arrival-grid-item:nth-child(4) { animation-delay: 0.25s; }
.new-arrival-grid-item:nth-child(5) { animation-delay: 0.3s; }

/* Responsive Design */
@media (max-width: 768px) {
    .new-arrival-card .product-image-link {
        height: 180px;
    }

    .new-arrival-card .product-details {
        padding: 15px;
    }

    .new-arrival-card .product-name {
        font-size: 14px;
    }

    .new-arrival-card .product-description {
        font-size: 12px;
    }

    .new-arrival-card .final-price {
        font-size: 18px;
    }

    .new-arrival-card .add-to-cart-btn {
        padding: 10px 15px;
        font-size: 13px;
    }
}

@media (max-width: 479px) {
    .new-arrivals-grid-container {
        gap: 15px;
    }
}
</style>

<script>
// New arrivals now use the variant modal system from variant-selector-modal.blade.php
// No additional JavaScript needed here as all functionality is handled by the main modal component
console.log('New arrivals component loaded - using variant modal system');
</script>