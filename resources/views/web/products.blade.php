@extends('web.layouts.app')

@section('content')
    <!-- Main Shop Section -->
    <section class="skc-section" style="padding-top: 20px;">
        <div class="skc-container">
            <!-- Header Row with Title and Filter -->
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
                <div style="flex: 1; display: flex; justify-content: flex-start;">
                    <button id="filterToggleBtn" onclick="toggleFilters()" 
                            style="display: flex; align-items: center; gap: 8px; padding: 12px 20px; background: var(--skc-black); color: white; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; transition: all 0.2s; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                        <i class="fas fa-filter"></i>
                        <span>Filters</span>
                    </button>
                </div>
                <div style="text-align: center; flex: 2;">
                    @if(isset($searchQuery) && $searchQuery)
                        <h1 style="font-size: 32px; font-weight: 700; color: black; margin: 0;">Search Results</h1>
                        <p style="font-size: 16px; color: #666; margin: 5px 0 0 0;">Found {{ $products->total() }} results for "{{ $searchQuery }}"</p>
                    @elseif(request('q') === '')
                        <h1 style="font-size: 32px; font-weight: 700; color: black; margin: 0;">Search Products</h1>
                        <p style="font-size: 16px; color: #666; margin: 5px 0 0 0;">Please enter a search term to find products</p>
                    @else
                        <h1 style="font-size: 32px; font-weight: 700; color: black; margin: 0;">Our Products</h1>
                        <p style="font-size: 16px; color: #666; margin: 5px 0 0 0;">Discover 100+ South Indian delicacies made without palm oil or preservatives</p>
                    @endif
                </div>
                <div style="flex: 1;"></div>
            </div>
            
            <div id="mainContent" style="display: grid; grid-template-columns: 1fr; gap: 40px; transition: all 0.3s;">
                <!-- Sidebar Filters -->
                <aside id="productFilters" style="background: white; border: 1px solid var(--skc-border); border-radius: 8px; padding: 30px; max-height: 80vh; overflow-y: auto; position: sticky; top: 100px; display: none;">
                    <h2 style="font-size: 22px; font-weight: 700; margin-bottom: 25px; color: var(--skc-black);">
                        <i class="fas fa-filter" style="color: var(--skc-orange); margin-right: 10px;"></i>Filters
                    </h2>
                    
                                         <!-- Categories -->
                     <div style="margin-bottom: 35px;">
                         <h3 style="font-size: 16px; font-weight: 600; margin-bottom: 15px; color: var(--skc-black); text-transform: uppercase; letter-spacing: 0.5px;">Categories</h3>
                         <div>
                             <label style="display: flex; align-items: center; margin-bottom: 12px; cursor: pointer;">
                                 <input type="radio" name="category" value="" {{ !request('category') ? 'checked' : '' }} 
                                        data-href="{{ route('products') }}"
                                        class="category-filter"
                                        style="margin-right: 10px;">
                                 <span style="color: var(--skc-medium-gray); font-size: 15px;">All Products ({{ $products->total() ?? $products->count() }})</span>
                             </label>
                             @foreach($categories as $category)
                                 <label style="display: flex; align-items: center; margin-bottom: 12px; cursor: pointer;">
                                     <input type="radio" name="category" value="{{ $category->slug }}" 
                                            {{ request('category') == $category->slug ? 'checked' : '' }}
                                            data-href="{{ route('category.products', $category->slug) }}"
                                            class="category-filter"
                                            style="margin-right: 10px;">
                                     <span style="color: var(--skc-medium-gray); font-size: 15px;">{{ $category->name }}</span>
                                 </label>
                             @endforeach
                         </div>
                     </div>

                    <!-- Price Range -->
                    <div style="margin-bottom: 35px;">
                        <h3 style="font-size: 16px; font-weight: 600; margin-bottom: 15px; color: var(--skc-black); text-transform: uppercase; letter-spacing: 0.5px;">Price Range</h3>
                        <form method="GET" action="{{ isset($activeCategory) && $activeCategory ? route('category.products', $activeCategory) : route('products') }}">
                            @if(request('category') && !isset($activeCategory))
                                <input type="hidden" name="category" value="{{ request('category') }}">
                            @endif
                            @if(request('sort'))
                                <input type="hidden" name="sort" value="{{ request('sort') }}">
                            @endif
                            <div style="margin-bottom: 15px;">
                                <input type="number" name="min_price" placeholder="Min ₹" value="{{ request('min_price') }}" 
                                       style="width: 100%; padding: 10px; border: 1px solid var(--skc-border); border-radius: 4px; font-size: 14px; margin-bottom: 10px;">
                                <input type="number" name="max_price" placeholder="Max ₹" value="{{ request('max_price') }}"
                                       style="width: 100%; padding: 10px; border: 1px solid var(--skc-border); border-radius: 4px; font-size: 14px;">
                            </div>
                            <button type="submit" style="width: 100%; padding: 12px; background: var(--skc-black); color: white; border: none; border-radius: 4px; font-weight: 600; cursor: pointer; transition: background 0.2s;">
                                Apply Price Filter
                            </button>
                        </form>
                    </div>

                    <!-- Sort By -->
                    <div>
                        <h3 style="font-size: 16px; font-weight: 600; margin-bottom: 15px; color: var(--skc-black); text-transform: uppercase; letter-spacing: 0.5px;">Sort By</h3>
                        <select onchange="window.location.href=this.value"
                                style="width: 100%; padding: 12px; border: 1px solid var(--skc-border); border-radius: 4px; font-size: 14px; background: white;">
                            @php
                                $baseRoute = isset($activeCategory) && $activeCategory ? 'category.products' : 'products';
                                $baseParams = isset($activeCategory) && $activeCategory ? [$activeCategory] : [];
                                $currentParams = request()->except('sort');
                            @endphp
                            <option value="{{ route($baseRoute, $baseParams) }}{{ http_build_query($currentParams) ? '?' . http_build_query($currentParams) : '' }}"
                                    {{ !request('sort') ? 'selected' : '' }}>Default</option>
                            <option value="{{ isset($activeCategory) && $activeCategory ? route('category.products.sorted', [$activeCategory, 'newest']) : route('products', array_merge($currentParams, ['sort' => 'newest'])) }}"
                                    {{ request('sort') == 'newest' ? 'selected' : '' }}>Latest Products</option>
                            <option value="{{ isset($activeCategory) && $activeCategory ? route('category.products.sorted', [$activeCategory, 'price_asc']) : route('products', array_merge($currentParams, ['sort' => 'price_asc'])) }}"
                                    {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                            <option value="{{ isset($activeCategory) && $activeCategory ? route('category.products.sorted', [$activeCategory, 'price_desc']) : route('products', array_merge($currentParams, ['sort' => 'price_desc'])) }}"
                                    {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
                            <option value="{{ isset($activeCategory) && $activeCategory ? route('category.products.sorted', [$activeCategory, 'name_asc']) : route('products', array_merge($currentParams, ['sort' => 'name_asc'])) }}"
                                    {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Name: A to Z</option>
                            <option value="{{ isset($activeCategory) && $activeCategory ? route('category.products.sorted', [$activeCategory, 'rating_desc']) : route('products', array_merge($currentParams, ['sort' => 'rating_desc'])) }}"
                                    {{ request('sort') == 'rating_desc' ? 'selected' : '' }}>Rating: High to Low</option>
                            <option value="{{ isset($activeCategory) && $activeCategory ? route('category.products.sorted', [$activeCategory, 'rating_asc']) : route('products', array_merge($currentParams, ['sort' => 'rating_asc'])) }}"
                                    {{ request('sort') == 'rating_asc' ? 'selected' : '' }}>Rating: Low to High</option>
                        </select>
                    </div>

                    <!-- Clear Filters -->
                    @if(request()->hasAny(['category', 'min_price', 'max_price', 'sort']))
                        <a href="{{ route('products') }}" 
                           style="display: inline-block; margin-top: 25px; color: var(--skc-orange); text-decoration: none; font-weight: 600; font-size: 14px;">
                            <i class="fas fa-times-circle"></i> Clear All Filters
                        </a>
                    @endif
                </aside>

                <!-- Products Grid -->
                <div>
                    <!-- Mobile Filter Toggle -->
                    <button onclick="toggleMobileFilters()" 
                            style="display: none; width: 100%; padding: 12px; background: var(--skc-black); color: white; border: none; border-radius: 4px; margin-bottom: 20px; font-weight: 600;">
                        <i class="fas fa-filter"></i> Show Filters
                    </button>

                    <!-- Results Header -->
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 1px solid var(--skc-border);">
                        <div>
                            <h2 style="font-size: 24px; font-weight: 600; color: var(--skc-black);">
                                @if(request('category'))
                                    {{ ucfirst(request('category')) }}
                                @else
                                    All Products
                                @endif
                            </h2>
                            <p style="color: var(--skc-medium-gray); font-size: 14px; margin-top: 5px;">
                                Showing {{ $products->firstItem() ?? 0 }}-{{ $products->lastItem() ?? 0 }} of {{ $products->total() ?? $products->count() }} products
                            </p>
                        </div>
                                                 <div style="display: flex; gap: 10px;">
                             <button id="grid3Btn" onclick="setGridView(3)" style="padding: 8px; background: var(--skc-black); color: white; border: 1px solid var(--skc-border); border-radius: 4px; cursor: pointer; transition: all 0.2s;">
                                 <i class="fas fa-th-large"></i>
                             </button>
                             <button id="grid4Btn" onclick="setGridView(4)" style="padding: 8px; background: white; color: var(--skc-black); border: 1px solid var(--skc-border); border-radius: 4px; cursor: pointer; transition: all 0.2s;">
                                 <i class="fas fa-th"></i>
                             </button>
                         </div>
                    </div>

                                         <!-- Products -->
                     <div id="productsGrid" class="skc-products-grid">
                         @if(request('q') === '')
                             <div style="grid-column: 1 / -1; text-align: center; padding: 80px 20px;">
                                 <i class="fas fa-search" style="font-size: 80px; color: #e0e0e0; margin-bottom: 20px;"></i>
                                 <h3 style="font-size: 28px; color: var(--skc-black); margin-bottom: 15px;">Enter a search term</h3>
                                 <p style="color: var(--skc-medium-gray); font-size: 16px; margin-bottom: 30px;">Please enter a product name, category, or description to search for</p>
                                 <div style="display: flex; justify-content: center; gap: 15px;">
                                     <a href="{{ route('products') }}" class="skc-hero-btn">
                                         Browse All Products
                                     </a>
                                     <button onclick="document.querySelector('.skc-search-input-expanded').focus()" class="skc-hero-btn" style="background: var(--skc-orange);">
                                         Try Search Again
                                     </button>
                                 </div>
                             </div>
                         @else
                         @forelse($products as $product)
                            @include('web.partials.product-card', ['product' => $product, 'isNew' => $loop->iteration <= 3])
                         @empty
                             <div style="grid-column: 1 / -1; text-align: center; padding: 80px 20px;">
                                 <i class="fas fa-search" style="font-size: 80px; color: #e0e0e0; margin-bottom: 20px;"></i>
                                 <h3 style="font-size: 28px; color: var(--skc-black); margin-bottom: 15px;">No products found</h3>
                                 <p style="color: var(--skc-medium-gray); font-size: 16px; margin-bottom: 30px;">Try adjusting your filters or browse all products</p>
                                 <a href="{{ route('products') }}" class="skc-hero-btn">
                                     View All Products
                                 </a>
                             </div>
                         @endforelse
                         @endif
                    </div>

                    <!-- Load More Button -->
                    @if($products->hasMorePages())
                    <div id="loadMoreContainer" style="text-align: center; padding: 40px; grid-column: 1 / -1;">
                        <button id="loadMoreBtn" onclick="loadMoreProducts()"
                                style="padding: 15px 40px; background: var(--skc-orange); color: white; border: none; border-radius: 30px; font-size: 16px; font-weight: 600; cursor: pointer; transition: all 0.3s; box-shadow: 0 4px 15px rgba(246, 157, 28, 0.3);">
                            <i class="fas fa-plus-circle" style="margin-right: 8px;"></i>
                            Load More Products
                        </button>
                    </div>
                    @endif

                    <!-- Loading Indicator -->
                    <div id="loadingIndicator" style="display: none; text-align: center; padding: 40px; grid-column: 1 / -1;">
                        <div class="spinner-border" style="width: 40px; height: 40px; border: 4px solid #f3f3f3; border-top: 4px solid var(--skc-orange); border-radius: 50%; animation: spin 1s linear infinite;"></div>
                        <p style="margin-top: 15px; color: var(--skc-medium-gray); font-size: 14px;">Loading more products...</p>
                    </div>

                    <!-- End of Products Message -->
                    <div id="endMessage" style="display: none; text-align: center; padding: 40px; grid-column: 1 / -1;">
                        <div style="width: 60px; height: 2px; background: var(--skc-orange); margin: 0 auto 20px;"></div>
                        <p style="color: var(--skc-medium-gray); font-size: 16px;">You've reached the end of our products</p>
                    </div>

                </div>
            </div>
        </div>
    </section>

    <style>
        @media (max-width: 1024px) {
            .skc-container > div {
                grid-template-columns: 1fr !important;
            }
            
            #productFilters {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                width: 320px;
                height: 100vh;
                background: white;
                z-index: 9998;
                overflow-y: auto;
                box-shadow: 5px 0 25px rgba(0,0,0,0.15);
                transform: translateX(-100%);
                transition: transform 0.3s;
            }
            
            #productFilters.active {
                display: block;
                transform: translateX(0);
            }
            
            button[onclick="toggleMobileFilters()"] {
                display: block !important;
            }
        }

        /* Product Image Placeholder Styles */
        .skc-product-image-placeholder {
            width: 100%;
            height: 250px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: #f8f9fa;
            color: var(--skc-medium-gray);
            gap: 10px;
        }

        .skc-product-image-placeholder i {
            font-size: 48px;
            opacity: 0.5;
        }

        .skc-product-image-placeholder span {
            font-size: 14px;
            font-weight: 500;
        }

        /* Load More Button Hover Effect */
        #loadMoreBtn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(246, 157, 28, 0.4) !important;
        }

        #loadMoreBtn:active {
            transform: translateY(0);
        }
    </style>

    @push('scripts')
    <script>
        // Category Filter Event Listeners
        document.addEventListener('DOMContentLoaded', function() {
            const categoryFilters = document.querySelectorAll('.category-filter');
            categoryFilters.forEach(function(filter) {
                filter.addEventListener('change', function() {
                    if (this.checked) {
                        window.location.href = this.dataset.href;
                    }
                });
            });
        });

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

        // Toggle Mobile Filters
        function toggleMobileFilters() {
            const filters = document.getElementById('productFilters');
            filters.classList.toggle('active');
        }

        // Toggle Filters
        function toggleFilters() {
            const filters = document.getElementById('productFilters');
            const mainContent = document.getElementById('mainContent');
            const toggleBtn = document.getElementById('filterToggleBtn');
            
            if (filters.style.display === 'none' || filters.style.display === '') {
                // Show filters
                filters.style.display = 'block';
                mainContent.style.gridTemplateColumns = '280px 1fr';
                toggleBtn.innerHTML = '<i class="fas fa-times"></i><span>Hide Filters</span>';
                toggleBtn.style.background = 'var(--skc-orange)';
            } else {
                // Hide filters
                filters.style.display = 'none';
                mainContent.style.gridTemplateColumns = '1fr';
                toggleBtn.innerHTML = '<i class="fas fa-filter"></i><span>Filters</span>';
                toggleBtn.style.background = 'var(--skc-black)';
            }
        }

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

        // Set Grid View
         function setGridView(columns) {
             const grid = document.getElementById('productsGrid');
             const grid3Btn = document.getElementById('grid3Btn');
             const grid4Btn = document.getElementById('grid4Btn');
             
             if (columns === 3) {
                 grid.style.gridTemplateColumns = 'repeat(auto-fill, minmax(300px, 1fr))';
                 grid3Btn.style.background = 'var(--skc-black)';
                 grid3Btn.style.color = 'white';
                 grid4Btn.style.background = 'white';
                 grid4Btn.style.color = 'var(--skc-black)';
             } else {
                 grid.style.gridTemplateColumns = 'repeat(auto-fill, minmax(260px, 1fr))';
                 grid4Btn.style.background = 'var(--skc-black)';
                 grid4Btn.style.color = 'white';
                 grid3Btn.style.background = 'white';
                 grid3Btn.style.color = 'var(--skc-black)';
             }
         }
    </script>
    @endpush
    
    <!-- Combo Offers Section -->
    @php
        try {
            $comboOffers = \App\Models\ComboOffer::active()
                ->with('products')
                ->orderBy('display_order', 'asc')
                ->orderBy('created_at', 'desc')
                ->take(3)
                ->get();
        } catch (\Exception $e) {
            $comboOffers = collect();
        }
    @endphp
    
    @if($comboOffers && $comboOffers->count() > 0)
    <section style="background: #f8f9fa; padding: 60px 0; margin-top: 60px;">
        <div class="skc-container">
            <div style="text-align: center; margin-bottom: 40px;">
                <h2 style="font-size: 32px; font-weight: 700; color: var(--skc-black); margin: 0 0 10px 0;">Special Combo Offers</h2>
                <p style="color: var(--skc-medium-gray); font-size: 16px;">Get more value with our combo deals</p>
            </div>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 30px; margin-bottom: 40px;">
                @foreach($comboOffers as $combo)
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
            
            <div style="text-align: center;">
                <a href="{{ route('combos.index') }}" style="display: inline-block; padding: 14px 40px; background: var(--skc-black); color: white; text-decoration: none; border-radius: 6px; font-weight: 600; transition: all 0.3s;">
                    View All Combos
                </a>
            </div>
        </div>
    </section>
    @endif
@push('scripts')
<script>
    // Infinite Scroll Implementation
    let isLoading = false;
    let currentPage = {{ $products->currentPage() }};
    let hasMorePages = {{ $products->hasMorePages() ? 'true' : 'false' }};
    const productsGrid = document.getElementById('productsGrid');
    const loadingIndicator = document.getElementById('loadingIndicator');
    const endMessage = document.getElementById('endMessage');

    // Function to get current URL parameters
    function getCurrentParams() {
        const params = new URLSearchParams(window.location.search);
        return params.toString();
    }

    // Function to load more products
    function loadMoreProducts() {
        if (isLoading || !hasMorePages) return;

        isLoading = true;

        // Hide load more button and show loading indicator
        const loadMoreContainer = document.getElementById('loadMoreContainer');
        if (loadMoreContainer) {
            loadMoreContainer.style.display = 'none';
        }
        loadingIndicator.style.display = 'block';

        const nextPage = currentPage + 1;
        const params = getCurrentParams();
        const baseUrl = @if(isset($activeCategory) && $activeCategory)
            `{{ route('category.products', $activeCategory ?? '') }}`
        @else
            `{{ route('products') }}`
        @endif;
        const url = `${baseUrl}?page=${nextPage}${params ? '&' + params : ''}`;

        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.html) {
                // Create a temporary container to hold the new products
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = data.html;

                // Append each product card to the grid
                while (tempDiv.firstChild) {
                    productsGrid.appendChild(tempDiv.firstChild);
                }

                currentPage = data.nextPage - 1;
                hasMorePages = data.hasMore;

                if (hasMorePages) {
                    // Show load more button again
                    if (loadMoreContainer) {
                        loadMoreContainer.style.display = 'block';
                    } else {
                        // Create new load more button if it doesn't exist
                        const newLoadMore = document.createElement('div');
                        newLoadMore.id = 'loadMoreContainer';
                        newLoadMore.style.cssText = 'text-align: center; padding: 40px; grid-column: 1 / -1;';
                        newLoadMore.innerHTML = `
                            <button id="loadMoreBtn" onclick="loadMoreProducts()"
                                    style="padding: 15px 40px; background: var(--skc-orange); color: white; border: none; border-radius: 30px; font-size: 16px; font-weight: 600; cursor: pointer; transition: all 0.3s; box-shadow: 0 4px 15px rgba(246, 157, 28, 0.3);">
                                <i class="fas fa-plus-circle" style="margin-right: 8px;"></i>
                                Load More Products
                            </button>
                        `;
                        productsGrid.parentNode.insertBefore(newLoadMore, loadingIndicator);
                    }
                } else {
                    endMessage.style.display = 'block';
                }
            }

            loadingIndicator.style.display = 'none';
            isLoading = false;
        })
        .catch(error => {
            console.error('Error loading more products:', error);
            loadingIndicator.style.display = 'none';
            isLoading = false;

            // Show load more button again on error
            if (loadMoreContainer) {
                loadMoreContainer.style.display = 'block';
            }

            showToast('Error loading products. Please try again.', 'error');
        });
    }

    // Removed automatic infinite scroll - now using Load More button only
    // If you want to re-enable infinite scroll, uncomment the code below:
    /*
    // Infinite scroll trigger
    function handleScroll() {
        if (!productsGrid) return;

        const scrollPosition = window.innerHeight + window.scrollY;
        const threshold = document.body.offsetHeight - 500; // Load 500px before bottom

        if (scrollPosition >= threshold) {
            loadMoreProducts();
        }
    }

    // Debounce function to improve performance
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    // Add scroll event listener with debouncing
    const debouncedScroll = debounce(handleScroll, 100);
    window.addEventListener('scroll', debouncedScroll);
    */

    // Also check on page load in case the initial content doesn't fill the viewport
    document.addEventListener('DOMContentLoaded', () => {
        setTimeout(() => {
            if (document.body.scrollHeight <= window.innerHeight && hasMorePages) {
                loadMoreProducts();
            }
        }, 500);
    });

    // Add spinner animation CSS
    const style = document.createElement('style');
    style.textContent = `
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    `;
    document.head.appendChild(style);
</script>
@endpush

@endsection