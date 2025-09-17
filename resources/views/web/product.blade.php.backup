@extends('web.layouts.app')

@section('content')
<div class="bg-gray-50 min-h-screen">
    <!-- Breadcrumb -->
    <div class="bg-white border-b">
        <div class="skc-container py-4">
            <nav class="flex items-center space-x-2 text-sm">
                <a href="{{ route('home') }}" class="flex items-center text-gray-600 hover:text-orange-600 transition-colors">
                    <i class="fas fa-home mr-1"></i>
                    Home
                </a>
                <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
                <a href="{{ route('products', ['category' => $product->category->slug]) }}" class="text-gray-600 hover:text-orange-600 transition-colors">
                    {{ $product->category->name }}
                </a>
                <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
                <span class="text-gray-900 font-medium">{{ $product->name }}</span>
            </nav>
        </div>
    </div>

    <!-- Main Product Section -->
    <div class="skc-container py-8">
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-0">
                <!-- Product Images -->
                <div class="relative lg:h-full">
                    @php($productImages = $product->images_path ?? [])
                    @if(!empty($productImages) && is_array($productImages))
                        <!-- Main Image -->
                        <div class="relative aspect-square lg:h-full bg-gray-100">
                            <img id="mainImage" 
                                 src="{{ asset('storage/' . $productImages[0]) }}" 
                                 alt="{{ $product->name }}" 
                                 class="w-full h-full object-cover">
                            
                            <!-- Wishlist Button -->
                            <button class="absolute top-4 right-4 w-10 h-10 bg-white/90 backdrop-blur-sm rounded-full shadow-lg flex items-center justify-center text-gray-600 hover:bg-orange-500 hover:text-white transition-all duration-300">
                                <i class="fas fa-heart"></i>
                            </button>
                            
                            <!-- Discount Badge -->
                            @if($product->has_discount && $product->discount_percentage)
                            <div class="absolute top-4 left-4 bg-red-500 text-white px-3 py-1 rounded-full text-sm font-bold">
                                -{{ number_format($product->discount_percentage) }}%
                            </div>
                            @endif
                        </div>
                        
                        <!-- Thumbnail Images -->
                        @if(count($productImages) > 1)
                        <div class="absolute bottom-4 left-4 right-4">
                            <div class="flex gap-2 overflow-x-auto scrollbar-hide">
                                @foreach($productImages as $index => $image)
                                <button onclick="changeMainImage('{{ asset('storage/' . $image) }}')" 
                                        class="flex-shrink-0 w-16 h-16 rounded-lg overflow-hidden border-2 border-white/50 hover:border-orange-500 transition-all duration-300 shadow-lg">
                                    <img src="{{ asset('storage/' . $image) }}" 
                                         alt="Thumbnail {{ $index + 1 }}" 
                                         class="w-full h-full object-cover">
                                </button>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    @else
                        <!-- Default Image Placeholder -->
                        <div class="aspect-square lg:h-full bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center">
                            <div class="text-center">
                                <i class="fas fa-image text-6xl text-gray-400 mb-4"></i>
                                <p class="text-gray-500 text-lg">No Image Available</p>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Product Details -->
                <div class="p-8 lg:p-12 space-y-8">
                    <!-- Product Header -->
                    <div class="space-y-4">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <h1 class="text-3xl lg:text-4xl font-bold text-gray-900 leading-tight mb-3">{{ $product->name }}</h1>
                                <div class="flex items-center gap-4 mb-4">
                                    <div class="flex items-center gap-1">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= $product->rating)
                                                <i class="fas fa-star text-yellow-400 text-sm"></i>
                                            @else
                                                <i class="far fa-star text-gray-300 text-sm"></i>
                                            @endif
                                        @endfor
                                        <span class="ml-2 text-sm text-gray-600">({{ $product->review_count }} reviews)</span>
                                    </div>
                                    @if($product->variants && $product->variants->first())
                                    <div class="text-sm text-gray-500">
                                        SKU: <span class="font-medium">{{ $product->variants->first()->sku }}</span>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <!-- Pricing -->
                        <div class="border-l-4 border-orange-500 pl-4 bg-orange-50 rounded-r-lg py-4">
                            @if($product->has_discount && $product->discount_price)
                                <div class="flex items-center gap-3">
                                    <span class="text-4xl font-bold text-orange-600">₹{{ number_format($product->discount_price, 2) }}</span>
                                    <span class="text-xl text-gray-500 line-through">₹{{ number_format($product->base_price, 2) }}</span>
                                    <div class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-sm font-medium">
                                        Save ₹{{ number_format($product->base_price - $product->discount_price, 2) }}
                                    </div>
                                </div>
                            @else
                                <span class="text-4xl font-bold text-orange-600">₹{{ number_format($product->base_price, 2) }}</span>
                            @endif
                        </div>

                        <!-- Description -->
                        @if($product->description)
                        <div class="prose prose-gray max-w-none">
                            <p class="text-gray-700 text-lg leading-relaxed">{{ $product->description }}</p>
                        </div>
                        @endif
                    </div>

                    <!-- Product Variants -->
                    @if($product->variants && $product->variants->count() > 0)
                    <div class="space-y-6">
                        <h3 class="text-xl font-semibold text-gray-900 mb-4">Choose Your Option:</h3>
                        
                        @php
                            $variantsByType = $product->variants->groupBy('variant_type');
                        @endphp
                        
                        @foreach($variantsByType as $type => $variants)
                        <div class="space-y-3">
                            <h4 class="text-lg font-medium text-gray-800">{{ $type ?? 'Options' }}:</h4>
                            <div class="grid grid-cols-2 lg:grid-cols-3 gap-3">
                                @foreach($variants as $variant)
                                <label class="variant-option cursor-pointer">
                                    <input type="radio" 
                                           name="variant" 
                                           value="{{ $variant->id }}" 
                                           data-price="{{ $variant->price }}" 
                                           data-stock="{{ $variant->stock_quantity }}" 
                                           data-sku="{{ $variant->sku }}"
                                           class="sr-only variant-radio">
                                    <div class="border-2 border-gray-200 rounded-xl p-4 hover:border-orange-300 transition-all duration-300 bg-white hover:bg-orange-50 hover:shadow-md">
                                        <div class="text-center">
                                            <div class="font-semibold text-gray-900 mb-1">{{ $variant->variant_value }}</div>
                                            <div class="text-lg font-bold text-orange-600">₹{{ number_format($variant->price, 2) }}</div>
                                            <div class="text-xs text-gray-500 mt-1">
                                                @if($variant->stock_quantity > 0)
                                                    {{ $variant->stock_quantity }} in stock
                                                @else
                                                    Out of stock
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </label>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                        
                        <!-- Selected Variant Info -->
                        <div id="variantInfo" class="hidden bg-gray-50 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600">Selected: <span id="selectedVariant" class="font-medium"></span></p>
                                    <p class="text-sm text-gray-600">SKU: <span id="selectedSKU" class="font-medium"></span></p>
                                </div>
                                <div class="text-right">
                                    <p class="text-lg font-bold text-orange-600" id="selectedPrice"></p>
                                    <p class="text-sm text-gray-600" id="selectedStock"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Quantity and Add to Cart -->
                    <div class="space-y-6 border-t pt-8">
                        <div class="flex items-end gap-6">
                            <!-- Quantity Selector -->
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">Quantity:</label>
                                <div class="flex items-center bg-gray-50 border border-gray-300 rounded-xl">
                                    <button onclick="decrementQuantity()" 
                                            class="w-12 h-12 flex items-center justify-center text-gray-600 hover:bg-gray-200 rounded-l-xl transition-colors">
                                        <i class="fas fa-minus text-sm"></i>
                                    </button>
                                    <input type="number" 
                                           id="quantity" 
                                           name="quantity" 
                                           value="1" 
                                           min="1" 
                                           max="10" 
                                           class="w-16 text-center bg-transparent border-0 focus:ring-0 font-semibold">
                                    <button onclick="incrementQuantity()" 
                                            class="w-12 h-12 flex items-center justify-center text-gray-600 hover:bg-gray-200 rounded-r-xl transition-colors">
                                        <i class="fas fa-plus text-sm"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Add to Cart Button -->
                            <div class="flex-1">
                                <button onclick="addToCart()" 
                                        class="w-full bg-gradient-to-r from-orange-500 to-orange-600 text-white py-4 px-8 rounded-xl font-bold text-lg hover:from-orange-600 hover:to-orange-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 flex items-center justify-center gap-3">
                                    <i class="fas fa-shopping-cart text-lg"></i>
                                    Add to Cart
                                </button>
                            </div>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="grid grid-cols-2 gap-4">
                            <button class="border-2 border-gray-300 py-3 px-6 rounded-xl font-semibold hover:bg-gray-50 hover:border-gray-400 transition-all duration-300 flex items-center justify-center gap-2">
                                <i class="fas fa-heart text-red-500"></i>
                                Wishlist
                            </button>
                            <button class="border-2 border-gray-300 py-3 px-6 rounded-xl font-semibold hover:bg-gray-50 hover:border-gray-400 transition-all duration-300 flex items-center justify-center gap-2">
                                <i class="fas fa-share-alt text-blue-500"></i>
                                Share
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    
    <!-- Product Information Tabs -->
    <div class="skc-container py-8">
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="border-b border-gray-100">
                <nav class="flex">
                    <button onclick="showTab('description')" 
                            class="tab-button active flex-1 py-4 px-6 text-center font-semibold text-lg border-b-3 border-orange-500 text-orange-600 bg-orange-50">
                        <i class="fas fa-file-text mr-2"></i>Description
                    </button>
                    <button onclick="showTab('ingredients')" 
                            class="tab-button flex-1 py-4 px-6 text-center font-semibold text-lg border-b-3 border-transparent text-gray-600 hover:bg-gray-50 transition-all">
                        <i class="fas fa-list-ul mr-2"></i>Ingredients
                    </button>
                    <button onclick="showTab('nutrition')" 
                            class="tab-button flex-1 py-4 px-6 text-center font-semibold text-lg border-b-3 border-transparent text-gray-600 hover:bg-gray-50 transition-all">
                        <i class="fas fa-chart-bar mr-2"></i>Nutrition
                    </button>
                    <button onclick="showTab('storage')" 
                            class="tab-button flex-1 py-4 px-6 text-center font-semibold text-lg border-b-3 border-transparent text-gray-600 hover:bg-gray-50 transition-all">
                        <i class="fas fa-archive mr-2"></i>Storage
                    </button>
                </nav>
            </div>
            
            <div class="p-8">
                <div id="description-tab" class="tab-content">
                    <div class="prose prose-lg max-w-none">
                        <h3 class="text-2xl font-bold text-gray-900 mb-4">Product Description</h3>
                        <div class="text-gray-700 leading-relaxed space-y-4">
                            <p>{{ $product->full_description ?? $product->description ?? 'Detailed product description will be updated soon.' }}</p>
                        </div>
                    </div>
                </div>
                
                <div id="ingredients-tab" class="tab-content hidden">
                    <div class="prose prose-lg max-w-none">
                        <h3 class="text-2xl font-bold text-gray-900 mb-4">Ingredients</h3>
                        <div class="text-gray-700 leading-relaxed">
                            <p class="mb-4">{{ $product->ingredients ?? 'Ingredients information will be updated soon.' }}</p>
                        </div>
                        @if($product->allergen_info)
                        <div class="mt-6 p-6 bg-amber-50 border-l-4 border-amber-500 rounded-r-lg">
                            <div class="flex items-start">
                                <i class="fas fa-exclamation-triangle text-amber-500 text-lg mr-3 mt-1"></i>
                                <div>
                                    <h4 class="font-bold text-amber-800 mb-2">Allergen Information</h4>
                                    <p class="text-amber-700">{{ $product->allergen_info }}</p>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                
                <div id="nutrition-tab" class="tab-content hidden">
                    <div class="prose prose-lg max-w-none">
                        <h3 class="text-2xl font-bold text-gray-900 mb-6">Nutritional Information</h3>
                        @if($product->nutritional_info && is_array($product->nutritional_info))
                        <div class="bg-gray-50 rounded-lg p-6">
                            <table class="w-full">
                                <thead>
                                    <tr class="border-b-2 border-gray-200">
                                        <th class="text-left py-3 font-bold text-gray-900">Nutrient</th>
                                        <th class="text-right py-3 font-bold text-gray-900">Per 100g</th>
                                    </tr>
                                </thead>
                                <tbody class="space-y-2">
                                    @foreach($product->nutritional_info as $nutrient => $value)
                                    <tr class="border-b border-gray-200 hover:bg-gray-100">
                                        <td class="py-3 font-medium text-gray-800">{{ ucfirst($nutrient) }}</td>
                                        <td class="text-right py-3 text-gray-700 font-semibold">{{ $value }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <div class="text-center py-8">
                            <i class="fas fa-chart-bar text-4xl text-gray-400 mb-4"></i>
                            <p class="text-gray-600 text-lg">Nutritional information will be updated soon.</p>
                        </div>
                        @endif
                    </div>
                </div>
                
                <div id="storage-tab" class="tab-content hidden">
                    <div class="prose prose-lg max-w-none">
                        <h3 class="text-2xl font-bold text-gray-900 mb-6">Storage Instructions</h3>
                        <div class="space-y-6">
                            <div class="flex items-start space-x-4 p-4 bg-blue-50 rounded-lg">
                                <i class="fas fa-temperature-low text-blue-600 text-xl mt-1"></i>
                                <div>
                                    <h4 class="font-semibold text-blue-900 mb-2">Storage Guidelines</h4>
                                    <p class="text-blue-800">{{ $product->storage_instructions ?? 'Store in a cool, dry place away from direct sunlight.' }}</p>
                                </div>
                            </div>
                            
                            @if($product->shelf_life)
                            <div class="flex items-start space-x-4 p-4 bg-green-50 rounded-lg">
                                <i class="fas fa-calendar-alt text-green-600 text-xl mt-1"></i>
                                <div>
                                    <h4 class="font-semibold text-green-900 mb-2">Shelf Life</h4>
                                    <p class="text-green-800">{{ $product->shelf_life }} days from the date of manufacture</p>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Related Products -->
    @if($related && $related->count() > 0)
    <div class="skc-container py-8">
        <div class="text-center mb-8">
            <h2 class="text-3xl font-bold text-gray-900 mb-2">You Might Also Like</h2>
            <p class="text-gray-600">Discover more delicious options from our bakery</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($related as $relatedProduct)
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden group hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                <a href="{{ route('product.show', $relatedProduct->slug) }}" class="block">
                    <div class="relative h-48 overflow-hidden">
                        @if($relatedProduct->first_image)
                            <img src="{{ asset('storage/' . $relatedProduct->first_image) }}" 
                                 alt="{{ $relatedProduct->name }}" 
                                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                        @else
                            <div class="w-full h-full bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center">
                                <i class="fas fa-image text-4xl text-gray-400"></i>
                            </div>
                        @endif
                        @if($relatedProduct->has_discount && $relatedProduct->discount_percentage)
                        <div class="absolute top-3 left-3 bg-red-500 text-white px-3 py-1 rounded-full text-xs font-bold">
                            -{{ number_format($relatedProduct->discount_percentage) }}%
                        </div>
                        @endif
                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition-all duration-300"></div>
                    </div>
                    <div class="p-6">
                        <h3 class="font-bold text-gray-900 mb-3 text-lg leading-tight group-hover:text-orange-600 transition-colors">{{ $relatedProduct->name }}</h3>
                        <div class="flex items-center justify-between">
                            @if($relatedProduct->has_discount && $relatedProduct->discount_price)
                                <div class="space-y-1">
                                    <div class="text-xl font-bold text-orange-600">₹{{ number_format($relatedProduct->discount_price, 2) }}</div>
                                    <div class="text-sm text-gray-500 line-through">₹{{ number_format($relatedProduct->base_price, 2) }}</div>
                                </div>
                            @else
                                <div class="text-xl font-bold text-orange-600">₹{{ number_format($relatedProduct->base_price, 2) }}</div>
                            @endif
                            <button class="bg-orange-100 hover:bg-orange-500 text-orange-600 hover:text-white p-2 rounded-full transition-all duration-300 opacity-0 group-hover:opacity-100">
                                <i class="fas fa-shopping-cart text-sm"></i>
                            </button>
                        </div>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
    let selectedVariant = null;
    let basePrice = {{ $product->base_price }};
    
    function changeMainImage(imageSrc) {
        document.getElementById('mainImage').src = imageSrc;
    }
    
    function showTab(tabName) {
        // Hide all tabs
        document.querySelectorAll('.tab-content').forEach(tab => {
            tab.classList.add('hidden');
        });
        
        // Remove active class from all buttons
        document.querySelectorAll('.tab-button').forEach(btn => {
            btn.classList.remove('border-orange-500', 'text-orange-600', 'bg-orange-50', 'active');
            btn.classList.add('border-transparent', 'text-gray-600');
        });
        
        // Show selected tab
        document.getElementById(tabName + '-tab').classList.remove('hidden');
        
        // Add active class to clicked button
        event.target.classList.add('border-orange-500', 'text-orange-600', 'bg-orange-50', 'active');
        event.target.classList.remove('border-transparent', 'text-gray-600');
    }
    
    function incrementQuantity() {
        const input = document.getElementById('quantity');
        const max = parseInt(input.max);
        const current = parseInt(input.value);
        if (current < max) {
            input.value = current + 1;
        }
    }
    
    function decrementQuantity() {
        const input = document.getElementById('quantity');
        const current = parseInt(input.value);
        if (current > 1) {
            input.value = current - 1;
        }
    }
    
    function addToCart() {
        const quantity = document.getElementById('quantity').value;
        const productId = {{ $product->id }};
        
        @if($product->variants && $product->variants->count() > 0)
        if (!selectedVariant) {
            alert('Please select a variant');
            return;
        }
        @endif
        
        // Add to cart logic here
        fetch('/cart/add', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                product_id: productId,
                variant_id: selectedVariant,
                quantity: quantity
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message or update cart count
                alert('Product added to cart!');
            }
        });
    }
    
    // Initialize variant selection
    document.addEventListener('DOMContentLoaded', function() {
        // Handle variant selection
        document.querySelectorAll('.variant-radio').forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.checked) {
                    selectedVariant = this.value;
                    const price = this.getAttribute('data-price');
                    const stock = this.getAttribute('data-stock');
                    const sku = this.getAttribute('data-sku');
                    const variantValue = this.closest('.variant-option').querySelector('.font-semibold').textContent;
                    
                    // Update variant selection UI
                    document.querySelectorAll('.variant-option div').forEach(div => {
                        div.classList.remove('border-orange-500', 'bg-orange-100');
                        div.classList.add('border-gray-200', 'bg-white');
                    });
                    
                    // Highlight selected variant
                    const selectedDiv = this.nextElementSibling;
                    selectedDiv.classList.add('border-orange-500', 'bg-orange-100');
                    selectedDiv.classList.remove('border-gray-200', 'bg-white');
                    
                    // Show variant info
                    const variantInfo = document.getElementById('variantInfo');
                    if (variantInfo) {
                        variantInfo.classList.remove('hidden');
                        document.getElementById('selectedVariant').textContent = variantValue;
                        document.getElementById('selectedSKU').textContent = sku;
                        document.getElementById('selectedStock').textContent = stock + ' units';
                        document.getElementById('selectedPrice').textContent = '₹' + parseFloat(price).toFixed(2);
                    }
                    
                    // Update max quantity based on stock
                    document.getElementById('quantity').max = Math.min(stock, 10);
                }
            });
        });
        
        // Initialize first tab as active (already done in HTML)
    });
</script>
@endpush
@endsection