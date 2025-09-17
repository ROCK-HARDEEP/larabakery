@extends('web.layouts.app')

@section('content')
<div class="products-container">
    <!-- Page Header with Enhanced Design -->
    <div class="products-header">
        <div class="container mx-auto px-6">
            <div class="text-center mb-12 animate-fade-in">
                <h1 class="products-main-title">Our Premium Collection</h1>
                <p class="products-subtitle">Handcrafted with love, baked to perfection</p>
                <div class="products-decorative-line"></div>
            </div>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="products-main-content">
        <div class="container mx-auto px-6">
            <div class="products-layout">
                
                <!-- Enhanced Desktop Filter Sidebar -->
                <div class="desktop-filter-sidebar">
                    <div class="filter-header">
                        <h2 class="filter-title">
                            <i class="fas fa-sliders-h"></i>
                            Refine Your Search
                        </h2>
                        <div class="filter-results-count">
                            {{ $products->total() }} Products Found
                        </div>
                    </div>
                    
                    <form method="GET" action="{{ route('products') }}" class="filter-form" id="desktopFilterForm">
                        <!-- Categories Filter -->
                        <div class="filter-section active">
                            <div class="filter-section-header" onclick="toggleDesktopFilterSection(this)">
                                <h3><i class="fas fa-th-large"></i> Categories</h3>
                                <i class="fas fa-chevron-up toggle-icon"></i>
                            </div>
                            <div class="filter-section-content">
                                <div class="filter-options-grid">
                                    <label class="filter-option-card {{ !isset($activeCategory) || $activeCategory == '' ? 'active' : '' }}">
                                        <input type="radio" name="category" value="" {{ !isset($activeCategory) || $activeCategory == '' ? 'checked' : '' }}>
                                        <span class="filter-option-content">
                                            <i class="fas fa-th"></i>
                                            <span>All Categories</span>
                                        </span>
                                    </label>
                                    @foreach($categories as $cat)
                                    <label class="filter-option-card {{ ($activeCategory == $cat->slug || $activeCategory == $cat->id) ? 'active' : '' }}">
                                        <input type="radio" name="category" value="{{ $cat->slug }}" 
                                            {{ ($activeCategory == $cat->slug || $activeCategory == $cat->id) ? 'checked' : '' }}>
                                        <span class="filter-option-content">
                                            <i class="fas fa-cookie-bite"></i>
                                            <span>{{ $cat->name }}</span>
                                        </span>
                                    </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Price Range Filter -->
                        <div class="filter-section">
                            <div class="filter-section-header" onclick="toggleDesktopFilterSection(this)">
                                <h3><i class="fas fa-rupee-sign"></i> Price Range</h3>
                                <i class="fas fa-chevron-down toggle-icon"></i>
                            </div>
                            <div class="filter-section-content">
                                <div class="price-range-inputs">
                                    <div class="price-input-group">
                                        <label>Min Price</label>
                                        <input type="number" name="min" placeholder="₹0" value="{{ request('min') ?? '' }}" class="price-input">
                                    </div>
                                    <div class="price-separator">to</div>
                                    <div class="price-input-group">
                                        <label>Max Price</label>
                                        <input type="number" name="max" placeholder="₹1000" value="{{ request('max') ?? '' }}" class="price-input">
                                    </div>
                                </div>
                                
                                <div class="price-quick-select">
                                    <h4>Quick Select</h4>
                                    <div class="price-options-grid">
                                        <label class="price-option-pill">
                                            <input type="radio" name="price_range" value="0-50">
                                            <span>Under ₹50</span>
                                        </label>
                                        <label class="price-option-pill">
                                            <input type="radio" name="price_range" value="50-100">
                                            <span>₹50 - ₹100</span>
                                        </label>
                                        <label class="price-option-pill">
                                            <input type="radio" name="price_range" value="100-200">
                                            <span>₹100 - ₹200</span>
                                        </label>
                                        <label class="price-option-pill">
                                            <input type="radio" name="price_range" value="200+">
                                            <span>₹200+</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sort Options -->
                        <div class="filter-section">
                            <div class="filter-section-header" onclick="toggleDesktopFilterSection(this)">
                                <h3><i class="fas fa-sort"></i> Sort By</h3>
                                <i class="fas fa-chevron-down toggle-icon"></i>
                            </div>
                            <div class="filter-section-content">
                                <div class="sort-options">
                                    <label class="sort-option {{ request('sort') == '' ? 'active' : '' }}">
                                        <input type="radio" name="sort" value="" {{ request('sort') == '' ? 'checked' : '' }}>
                                        <span><i class="fas fa-star"></i> Featured</span>
                                    </label>
                                    <label class="sort-option {{ request('sort') == 'price_asc' ? 'active' : '' }}">
                                        <input type="radio" name="sort" value="price_asc" {{ request('sort') == 'price_asc' ? 'checked' : '' }}>
                                        <span><i class="fas fa-sort-amount-up"></i> Price: Low to High</span>
                                    </label>
                                    <label class="sort-option {{ request('sort') == 'price_desc' ? 'active' : '' }}">
                                        <input type="radio" name="sort" value="price_desc" {{ request('sort') == 'price_desc' ? 'checked' : '' }}>
                                        <span><i class="fas fa-sort-amount-down"></i> Price: High to Low</span>
                                    </label>
                                    <label class="sort-option {{ request('sort') == 'newest' ? 'active' : '' }}">
                                        <input type="radio" name="sort" value="newest" {{ request('sort') == 'newest' ? 'checked' : '' }}>
                                        <span><i class="fas fa-clock"></i> Recently Added</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Filter Actions -->
                        <div class="filter-actions">
                            <button type="submit" class="apply-filters-btn">
                                <i class="fas fa-check"></i>
                                Apply Filters
                            </button>
                            <a href="{{ route('products') }}" class="reset-filters-btn">
                                <i class="fas fa-redo"></i>
                                Reset All
                            </a>
                        </div>
                    </form>
                </div>

                <!-- Mobile Filter Toggle -->
                <div class="mobile-filter-toggle">
                    <button class="filter-toggle-btn" onclick="toggleMobileFilterSidebar()">
                        <i class="fas fa-filter"></i>
                        <span>Filters</span>
                        <span class="filter-count">{{ count(array_filter([request('category'), request('min'), request('max'), request('sort')])) }}</span>
                    </button>
                    
                    <div class="active-filters-preview">
                        @if(isset($activeCategory) && $activeCategory != '')
                            <span class="active-filter-tag">
                                Category: {{ $categories->where('slug', $activeCategory)->first()?->name ?? $activeCategory }}
                                <i class="fas fa-times" onclick="removeFilter('category')"></i>
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Enhanced Products Grid -->
                <div class="products-grid-container">
                    <div class="products-grid-header">
                        <div class="grid-view-controls">
                            <button class="view-control active" data-view="grid">
                                <i class="fas fa-th-large"></i>
                            </button>
                            <button class="view-control" data-view="list">
                                <i class="fas fa-list"></i>
                            </button>
                        </div>
                        
                        <div class="results-info">
                            Showing {{ $products->firstItem() }}-{{ $products->lastItem() }} of {{ $products->total() }} products
                        </div>
                    </div>
                    
                    <div class="products-grid" id="productsGrid">
                        @foreach($products as $product)
                            <div class="product-card-enhanced" data-product-id="{{ $product->id }}">
                                <div class="product-image-container">
                                    @php($first = $product->first_image ?? null)
                                    @if($first)
                                        <img src="{{ asset('storage/' . $first) }}" alt="{{ $product->name }}" class="product-image" loading="lazy">
                                        <img src="{{ asset('storage/' . $first) }}" alt="{{ $product->name }}" class="product-image-hover" loading="lazy">
                                    @else
                                        <div class="product-image-placeholder">
                                            <i class="fas fa-image"></i>
                                        </div>
                                    @endif
                                    
                                    <!-- Product Badges -->
                                    <div class="product-badges">
                                        @if(isset($product->discount_percentage) && $product->discount_percentage > 0)
                                            <span class="badge discount-badge">
                                                -{{ $product->discount_percentage }}%
                                            </span>
                                        @endif
                                        
                                        @php($stock = (int)($product->total_stock ?? 0))
                                        @if($stock <= 0)
                                            <span class="badge stock-badge out-of-stock">
                                                Out of Stock
                                            </span>
                                        @elseif($stock < 5)
                                            <span class="badge stock-badge low-stock">
                                                Only {{ $stock }} left
                                            </span>
                                        @endif
                                    </div>
                                    
                                    <!-- Product Actions Overlay -->
                                    <div class="product-actions-overlay">
                                        <button class="action-btn wishlist-btn" title="Add to Wishlist">
                                            <i class="far fa-heart"></i>
                                        </button>
                                        <button class="action-btn compare-btn" title="Compare">
                                            <i class="fas fa-exchange-alt"></i>
                                        </button>
                                    </div>
                                </div>
                                
                                <div class="product-content-enhanced">
                                    <div class="product-meta">
                                        <span class="product-category">{{ $product->category?->name ?? 'Bakery' }}</span>
                                        <div class="product-rating">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="fas fa-star {{ $i <= 4 ? 'filled' : '' }}"></i>
                                            @endfor
                                            <span class="rating-count">(24)</span>
                                        </div>
                                    </div>
                                    
                                    <h3 class="product-title-enhanced">
                                        <a href="{{ route('product.show', $product->slug) }}">{{ $product->name }}</a>
                                    </h3>
                                    
                                    <p class="product-description-enhanced">{{ Str::limit($product->description, 80) }}</p>
                                    
                                    <div class="product-price-enhanced">
                                        <span class="current-price">₹{{ number_format($product->base_price, 2) }}</span>
                                        @if(isset($product->original_price) && $product->original_price && $product->original_price > $product->base_price)
                                            <span class="original-price">₹{{ number_format($product->original_price, 2) }}</span>
                                            <span class="savings">Save ₹{{ number_format($product->original_price - $product->base_price, 2) }}</span>
                                        @endif
                                    </div>
                                    
                                    <div class="product-cart-section">
                                        <div class="quantity-selector">
                                            <button class="qty-btn minus" data-action="adjust-quantity" data-product-id="{{ $product->id }}" data-change="-1">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                            <input type="number" id="qty-{{ $product->id }}" value="1" min="1" max="10" class="qty-input">
                                            <button class="qty-btn plus" data-action="adjust-quantity" data-product-id="{{ $product->id }}" data-change="1">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </div>
                                        
                                        <a href="{{ route('product.show', $product->slug) }}" class="add-to-cart-btn-enhanced" style="text-decoration: none; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-eye"></i>
                                            <span>View Product</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Enhanced Pagination -->
                    <div class="products-pagination">
                        {{ $products->appends(request()->query())->links() }}
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Mobile Filter Sidebar -->
    <div class="mobile-filter-sidebar" id="mobileFilterSidebar">
        <div class="mobile-filter-header">
            <h2>
                <i class="fas fa-filter"></i>
                Filter Products
            </h2>
            <button class="close-filter-btn" onclick="toggleMobileFilterSidebar()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="mobile-filter-content">
            <!-- Mobile filter content (same as desktop but optimized for mobile) -->
            <form method="GET" action="{{ route('products') }}" class="mobile-filter-form" id="mobileFilterForm">
                <!-- Categories for Mobile -->
                <div class="mobile-filter-section">
                    <div class="mobile-filter-section-header" onclick="toggleMobileFilterSection(this)">
                        <h3><i class="fas fa-th-large"></i> Categories</h3>
                        <i class="fas fa-chevron-down toggle-icon"></i>
                    </div>
                    <div class="mobile-filter-section-content">
                        <div class="mobile-filter-options">
                            <label class="mobile-filter-option {{ !isset($activeCategory) || $activeCategory == '' ? 'active' : '' }}">
                                <input type="radio" name="category" value="" {{ !isset($activeCategory) || $activeCategory == '' ? 'checked' : '' }}>
                                <span>All Categories</span>
                            </label>
                            @foreach($categories as $cat)
                            <label class="mobile-filter-option {{ ($activeCategory == $cat->slug || $activeCategory == $cat->id) ? 'active' : '' }}">
                                <input type="radio" name="category" value="{{ $cat->slug }}" 
                                    {{ ($activeCategory == $cat->slug || $activeCategory == $cat->id) ? 'checked' : '' }}>
                                <span>{{ $cat->name }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                </div>
                
                <!-- Price Range for Mobile -->
                <div class="mobile-filter-section">
                    <div class="mobile-filter-section-header" onclick="toggleMobileFilterSection(this)">
                        <h3><i class="fas fa-rupee-sign"></i> Price Range</h3>
                        <i class="fas fa-chevron-down toggle-icon"></i>
                    </div>
                    <div class="mobile-filter-section-content">
                        <div class="mobile-price-inputs">
                            <div class="mobile-price-input-group">
                                <label class="mobile-price-label">Min Price</label>
                                <input type="number" name="min" placeholder="Min Price" value="{{ request('min') ?? '' }}">
                            </div>
                            <div class="mobile-price-input-group">
                                <label class="mobile-price-label">Max Price</label>
                                <input type="number" name="max" placeholder="Max Price" value="{{ request('max') ?? '' }}">
                            </div>
                        </div>
                        
                        <div class="mobile-price-options">
                            <label class="mobile-price-option">
                                <input type="radio" name="price_range" value="0-50">
                                <span>Under ₹50</span>
                            </label>
                            <label class="mobile-price-option">
                                <input type="radio" name="price_range" value="50-100">
                                <span>₹50 - ₹100</span>
                            </label>
                            <label class="mobile-price-option">
                                <input type="radio" name="price_range" value="100-200">
                                <span>₹100 - ₹200</span>
                            </label>
                            <label class="mobile-price-option">
                                <input type="radio" name="price_range" value="200+">
                                <span>₹200+</span>
                            </label>
                        </div>
                    </div>
                </div>
                
                <!-- Mobile Filter Actions -->
                <div class="mobile-filter-actions">
                    <button type="submit" class="mobile-apply-btn">
                        Apply Filters
                    </button>
                    <a href="{{ route('products') }}" class="mobile-reset-btn">
                        Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Mobile Filter Overlay -->
    <div class="mobile-filter-overlay" id="mobileFilterOverlay" onclick="toggleMobileFilterSidebar()"></div>

</div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/products-enhanced.css') }}">
@endpush

@push('scripts')
<script src="{{ asset('js/products-enhanced.js') }}"></script>
@endpush