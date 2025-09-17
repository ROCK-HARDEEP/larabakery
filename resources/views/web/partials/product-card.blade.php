<div class="skc-product-card" data-product-id="{{ $product->id }}" data-variants="{{ json_encode($product->active_variants->map(function($variant) {
    return [
        'id' => $variant->id,
        'variant_type' => $variant->variant_type,
        'variant_value' => $variant->variant_value,
        'price' => $variant->price,
        'stock_quantity' => $variant->stock_quantity
    ];
})) }}">
    @if($product->dynamic_tag && $product->dynamic_tag !== 'popular')
        <span class="skc-product-badge {{ $product->dynamic_tag }}">{{ $product->dynamic_tag_label }}</span>
    @endif

    <a href="{{ route('product.show', $product->slug) }}" style="text-decoration: none; color: inherit;" class="skc-product-link">
        <div class="skc-product-image-wrapper">
            @if($product->image_url && $product->image_url !== asset('img/placeholder-product.jpg'))
                <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="skc-product-image">
            @else
                <div class="skc-product-image-placeholder">
                    <i class="fas fa-cake-candles"></i>
                    <span>No Image</span>
                </div>
            @endif
        </div>

        <div class="skc-product-details">
            <div class="skc-product-category">{{ $product->category->name ?? 'Traditional' }}</div>

            <!-- Product Rating -->
            <div class="skc-product-rating">
                @if($product->display_rating > 0)
                    <div class="skc-product-stars">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= $product->display_rating)
                                <i class="fas fa-star" style="color: #ffc107;"></i>
                            @elseif($i - $product->display_rating < 1)
                                <i class="fas fa-star-half-alt" style="color: #ffc107;"></i>
                            @else
                                <i class="far fa-star" style="color: #ddd;"></i>
                            @endif
                        @endfor
                    </div>
                    <span class="skc-product-rating-count">({{ $product->reviews_count }})</span>
                @else
                    <div style="height: 18px; display: flex; align-items: center; color: #999; font-size: 11px;">
                        No reviews yet
                    </div>
                @endif
            </div>

            <h3 class="skc-product-name">{{ $product->name }}</h3>

            <!-- Variant-based Pricing -->
            <div class="skc-product-price-section">
                <div class="skc-product-price">
                    @if($product->has_variable_pricing)
                        <span style="font-size: 14px; color: #666; margin-right: 4px;">From</span>
                    @endif
                    <span class="skc-product-price-currency">₹</span>{{ number_format($product->min_price, 0) }}
                    @if($product->has_variable_pricing)
                        <span style="font-size: 14px; color: #666; margin-left: 4px;">onwards</span>
                    @endif
                </div>
            </div>
        </div>
    </a>

    <div class="skc-product-footer" style="padding: 0 16px 16px 16px;">
        @if($product->active_variants->count() > 0)
            <button onclick="event.stopPropagation(); openVariantModal({{ $product->id }}, '{{ addslashes($product->name) }}', '{{ $product->image_url }}', '{{ $product->display_price }}')"
                    class="skc-add-cart-btn"
                    style="background: #ffc107; color: #333; border: none; padding: 12px 20px; border-radius: 6px; font-weight: 600; cursor: pointer; transition: all 0.2s; width: 100%; display: inline-block; text-align: center; text-decoration: none;">
                <i class="fas fa-shopping-cart" style="margin-right: 8px;"></i>
                Add to Cart
            </button>
        @else
            <a href="{{ route('product.show', $product->slug) }}" class="skc-add-cart-btn" style="background: #666; color: white; border: none; padding: 12px 20px; border-radius: 6px; font-weight: 600; cursor: pointer; transition: all 0.2s; width: 100%; display: inline-block; text-align: center; text-decoration: none;">
                View Product
            </a>
        @endif
    </div>
</div>