<!-- Popular Products Section -->
<section class="skc-section popular-products-section" style="background: white; padding: 80px 0;">
    <div class="skc-container">
        <div class="skc-section-header" style="margin-bottom: 50px; text-align: center;">
            <h2 class="skc-section-title" style="font-size: 42px; font-weight: 700; color: var(--skc-black); margin: 0 0 15px 0;">Popular Products</h2>
            <p class="skc-section-subtitle" style="font-size: 18px; color: var(--skc-medium-gray); margin: 0;">Customer favorites from our kitchen</p>
        </div>

        <!-- Products Grid -->
        <div class="products-grid">
            @foreach($popular as $product)
            <div class="product-item" data-product-id="{{ $product->id }}" data-variants="{{ json_encode($product->active_variants->map(function($variant) {
                return [
                    'id' => $variant->id,
                    'variant_type' => $variant->variant_type,
                    'variant_value' => $variant->variant_value,
                    'price' => $variant->price,
                    'stock_quantity' => $variant->stock_quantity
                ];
            })) }}">
                <div class="product-card">
                    {{-- Remove popular badges from popular products section --}}

                    <a href="{{ route('product.show', $product->slug) }}" class="product-link">
                        <div class="product-image">
                            @if($product->image_url && $product->image_url !== asset('img/placeholder-product.jpg'))
                                <img src="{{ $product->image_url }}" alt="{{ $product->name }}" loading="lazy">
                            @else
                                <div class="product-placeholder">
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

                        <div class="product-info">
                            <h3 class="product-name">{{ $product->name }}</h3>

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
                                    <button type="button" class="add-to-cart-btn js-open-variant" data-product-id="{{ $product->id }}" data-product-name="{{ e($product->name) }}" data-image-url="{{ e($product->image_url) }}" data-display-price="{{ e($product->display_price) }}">
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
                    </a>
                </div>
            </div>
            @endforeach
        </div>

        <!-- View All Products Button -->
        <div style="text-align: center; margin-top: 50px;">
            <a href="{{ route('products') }}" class="view-all-btn">
                View All Products
                <i class="fas fa-arrow-right" style="margin-left: 8px;"></i>
            </a>
        </div>
    </div>
</section>

<style>
/* Products Grid - CSS Grid matching New Arrivals with Auto-Sizing */
.products-grid {
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
    .products-grid {
        grid-template-columns: repeat(5, 1fr);
        max-width: 1400px;
    }
}

@media (max-width: 1399px) and (min-width: 1200px) {
    .products-grid {
        grid-template-columns: repeat(4, 1fr);
    }
}

@media (max-width: 1199px) and (min-width: 900px) {
    .products-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}

@media (max-width: 899px) and (min-width: 600px) {
    .products-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 599px) {
    .products-grid {
        grid-template-columns: 1fr;
        gap: 20px;
    }
}

/* Auto-alignment logic for different numbers of items - Same as New Arrivals */
.products-grid:has(.product-item:nth-child(1):nth-last-child(1)) {
    /* 1 item - center it */
    justify-items: center;
    max-width: 280px;
}

.products-grid:has(.product-item:nth-child(2):nth-last-child(1)) {
    /* 2 items - center them */
    max-width: 600px;
}

.products-grid:has(.product-item:nth-child(3):nth-last-child(1)) {
    /* 3 items - center them */
    max-width: 900px;
}

.products-grid:has(.product-item:nth-child(4):nth-last-child(1)) {
    /* 4 items - center them */
    max-width: 1200px;
}

/* Product Item Container */
.product-item {
    transition: all 0.3s ease;
}

/* Product Card */
.product-card {
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

.product-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 35px rgba(0,0,0,0.15);
    border-color: var(--skc-orange);
}

/* Product Badge */
.product-badge {
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

.product-badge.new {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    color: white;
    box-shadow: 0 2px 10px rgba(0, 123, 255, 0.3);
}

.product-badge.popular {
    background: #007bff;
    color: white;
}

/* Product Link */
.product-link {
    text-decoration: none;
    color: inherit;
    flex: 1;
    display: flex;
    flex-direction: column;
}

/* Product Image */
.product-image {
    height: 240px;
    overflow: hidden;
    background: #f8f9fa;
    position: relative;
}

.product-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.product-card:hover .product-image img {
    transform: scale(1.1);
}

/* Product Overlay - Same as New Arrivals */
.product-overlay {
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

.product-card:hover .product-overlay {
    opacity: 1;
}

.detail-view-btn {
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

.detail-view-btn:hover {
    background: var(--skc-orange);
    color: white;
    transform: scale(1.05);
}

.product-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: #999;
    gap: 8px;
}

.product-placeholder i {
    font-size: 40px;
    opacity: 0.5;
}

/* Product Info */
.product-info {
    padding: 20px;
    flex: 1;
    display: flex;
    flex-direction: column;
}

.product-name {
    font-size: 16px;
    font-weight: 600;
    color: var(--skc-black);
    margin: 0 0 8px 0;
    line-height: 1.3;
    transition: color 0.3s ease;
}

.product-name:hover {
    color: var(--skc-orange);
}

.product-description {
    font-size: 13px;
    color: var(--skc-medium-gray);
    line-height: 1.5;
    margin: 0 0 12px 0;
    flex: 1;
}

.product-rating {
    display: flex;
    align-items: center;
    gap: 3px;
    margin-bottom: 15px;
}

.product-rating i {
    font-size: 12px;
    color: #ffc107;
}

.rating-count {
    font-size: 12px;
    color: var(--skc-medium-gray);
    margin-left: 5px;
}

/* Product Price Section */
.product-price-section {
    display: flex;
    flex-direction: column;
    gap: 12px;
    margin-top: auto;
}

.price-wrapper {
    display: flex;
    align-items: center;
    gap: 8px;
}

.original-price {
    text-decoration: line-through;
    color: #999;
    font-size: 14px;
}

.final-price {
    font-size: 20px;
    font-weight: 700;
    color: var(--skc-orange);
}


.add-to-cart-btn {
    width: 100%;
    padding: 12px 20px;
    background: var(--skc-orange);
    color: white;
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
    text-decoration: none;
    z-index: 10;
    position: relative;
}

.add-to-cart-btn:hover {
    background: var(--skc-black);
    transform: scale(1.02);
}

.add-to-cart-btn.view-btn {
    background: #666;
}

.add-to-cart-btn.view-btn:hover {
    background: var(--skc-black);
}

.add-to-cart-btn i {
    font-size: 14px;
}

/* View All Button */
.view-all-btn {
    display: inline-flex;
    align-items: center;
    padding: 12px 24px;
    background: #333;
    color: white;
    text-decoration: none;
    border-radius: 6px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.view-all-btn:hover {
    background: #f69d1c;
    color: white;
}

/* Responsive Design */
@media (max-width: 768px) {
    .product-image {
        height: 200px;
    }

    .product-info {
        padding: 15px;
    }

    .product-name {
        font-size: 14px;
    }

    .product-description {
        font-size: 12px;
    }

    .final-price {
        font-size: 18px;
    }

    .add-to-cart-btn {
        padding: 10px 15px;
        font-size: 13px;
    }
}

@media (max-width: 479px) {
    .products-grid {
        gap: 15px;
    }
}

/* Animation for items */
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

.product-item {
    animation: fadeInUp 0.5s ease-out forwards;
    opacity: 0;
}

.product-item:nth-child(1) { animation-delay: 0.1s; }
.product-item:nth-child(2) { animation-delay: 0.15s; }
.product-item:nth-child(3) { animation-delay: 0.2s; }
.product-item:nth-child(4) { animation-delay: 0.25s; }
.product-item:nth-child(5) { animation-delay: 0.3s; }
</style>

<script>
// Popular products now use the variant modal system from variant-selector-modal.blade.php
console.log('Popular products component loaded - using variant modal system');

// CSS Grid with :has() selectors handles auto-alignment automatically
// No JavaScript needed for alignment - CSS does it all!
console.log('Popular products using CSS Grid auto-alignment like New Arrivals');

// Delegate click to open variant modal using data attributes
document.addEventListener('click', function (event) {
    var target = event.target;
    if (!(target instanceof Element)) { return; }

    var button = target.closest('.js-open-variant');
    if (!button) { return; }

    event.preventDefault();
    event.stopPropagation();

    var productId = parseInt(button.getAttribute('data-product-id') || '0', 10);
    var productName = button.getAttribute('data-product-name') || '';
    var imageUrl = button.getAttribute('data-image-url') || '';
    var displayPrice = button.getAttribute('data-display-price') || '';

    if (typeof openVariantModal === 'function') {
        openVariantModal(productId, productName, imageUrl, displayPrice);
    }
});
</script>