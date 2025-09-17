@extends('web.layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Breadcrumb -->
    <nav class="flex mb-8" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('home') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-bakery-600">
                    <i class="fas fa-home mr-2"></i>Home
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                    <a href="{{ route('category.show', $product->category->slug) }}" class="text-sm font-medium text-gray-700 hover:text-bakery-600">
                        {{ $product->category->name }}
                    </a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                    <span class="text-sm font-medium text-gray-500">{{ $product->name }}</span>
                </div>
            </li>
        </ol>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
        <!-- Product Images -->
        <div class="space-y-4">
            <div class="relative">
                @php($productImages = $product->images_path ?? [])
                @if(!empty($productImages) && is_array($productImages))
                    <div class="w-full h-96 lg:h-[500px] rounded-2xl overflow-hidden">
                        <img id="mainImage" src="{{ asset('storage/' . $productImages[0]) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                    </div>
                    
                    <!-- Thumbnail Images -->
                    @if(count($productImages) > 1)
                    <div class="flex gap-2 mt-4 overflow-x-auto">
                        @foreach($productImages as $index => $image)
                        <button onclick="changeMainImage('{{ asset('storage/' . $image) }}')" class="flex-shrink-0 w-20 h-20 rounded-lg overflow-hidden border-2 border-transparent hover:border-bakery-500 transition-colors">
                            <img src="{{ asset('storage/' . $image) }}" alt="Thumbnail {{ $index + 1 }}" class="w-full h-full object-cover">
                        </button>
                        @endforeach
                    </div>
                    @endif
                @else
                    <div class="w-full h-96 lg:h-[500px] bg-gray-200 rounded-2xl flex items-center justify-center">
                        <i class="fas fa-image text-6xl text-gray-400"></i>
                    </div>
                @endif
                
                <!-- Wishlist Button -->
                <button class="absolute top-4 right-4 w-12 h-12 bg-white rounded-full shadow-lg flex items-center justify-center text-bakery-600 hover:bg-bakery-500 hover:text-white transition-colors">
                    <i class="fas fa-heart"></i>
                </button>
                
                @if($product->has_discount && $product->discount_percentage)
                <div class="absolute top-4 left-4 bg-red-500 text-white px-3 py-1 rounded-full text-sm font-semibold">
                    -{{ number_format($product->discount_percentage) }}%
                </div>
                @endif
            </div>
        </div>

        <!-- Product Details -->
        <div class="space-y-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $product->name }}</h1>
                <div class="flex items-center gap-4 mb-4">
                    <div class="flex items-center">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= $product->rating)
                                <i class="fas fa-star text-yellow-400"></i>
                            @else
                                <i class="far fa-star text-gray-300"></i>
                            @endif
                        @endfor
                        <span class="ml-2 text-sm text-gray-600">({{ $product->review_count }} reviews)</span>
                    </div>
                    <span class="text-sm text-gray-500">SKU: {{ $product->variants->first()->sku ?? 'N/A' }}</span>
                </div>
                
                <div class="mb-4">
                    @if($product->has_discount && $product->discount_price)
                        <div class="flex items-center gap-3">
                            <span class="text-3xl font-bold text-bakery-600">₹{{ number_format($product->discount_price, 2) }}</span>
                            <span class="text-xl text-gray-400 line-through">₹{{ number_format($product->base_price, 2) }}</span>
                        </div>
                    @else
                        <span class="text-3xl font-bold text-bakery-600">₹{{ number_format($product->base_price, 2) }}</span>
                    @endif
                </div>

                <p class="text-gray-600 mb-6">{{ $product->description }}</p>
            </div>

            <!-- Product Variants -->
            @if($product->variants && $product->variants->count() > 0)
            <div class="border-t pt-6">
                <h3 class="text-lg font-semibold mb-4">Select Variant:</h3>
                <div class="space-y-4">
                    @php
                        $variantTypes = $product->variants->groupBy('variant_type');
                    @endphp
                    
                    @foreach($variantTypes as $type => $variants)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ ucfirst($type) }}:</label>
                        <div class="flex flex-wrap gap-2">
                            @foreach($variants as $variant)
                            <button 
                                onclick="selectVariant({{ $variant->id }}, {{ $variant->price }}, '{{ $variant->sku }}', {{ $variant->stock_quantity ?? $variant->stock ?? 0 }})"
                                class="variant-option px-4 py-2 border-2 border-gray-300 rounded-lg hover:border-bakery-500 focus:border-bakery-500 focus:outline-none transition-colors"
                                data-variant-id="{{ $variant->id }}"
                                data-variant-type="{{ $type }}">
                                <span class="font-medium">{{ $variant->variant_value }}</span>
                                @if($variant->price != $product->base_price)
                                <span class="text-sm text-gray-500 ml-1">(+₹{{ number_format($variant->price - $product->base_price, 2) }})</span>
                                @endif
                            </button>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
                
                <div id="variantInfo" class="mt-4 p-4 bg-gray-50 rounded-lg hidden">
                    <div class="flex justify-between items-center">
                        <div>
                            <span class="text-sm text-gray-600">Selected SKU: </span>
                            <span id="selectedSKU" class="font-medium"></span>
                        </div>
                        <div>
                            <span class="text-sm text-gray-600">Stock: </span>
                            <span id="selectedStock" class="font-medium"></span>
                        </div>
                    </div>
                    <div class="mt-2">
                        <span class="text-sm text-gray-600">Price: </span>
                        <span id="selectedPrice" class="text-xl font-bold text-bakery-600"></span>
                    </div>
                </div>
            </div>
            @endif

            <!-- Quantity and Add to Cart -->
            <div class="border-t pt-6">
                <div class="flex items-center gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Quantity:</label>
                        <div class="flex items-center border border-gray-300 rounded-lg">
                            <button onclick="decrementQuantity()" class="px-4 py-2 text-gray-600 hover:bg-gray-100 transition-colors">
                                <i class="fas fa-minus"></i>
                            </button>
                            <input type="number" id="quantity" name="quantity" value="1" min="1" max="10" class="w-16 text-center border-0 focus:ring-0">
                            <button onclick="incrementQuantity()" class="px-4 py-2 text-gray-600 hover:bg-gray-100 transition-colors">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="flex-1">
                        <button onclick="addToCart()" class="w-full bg-bakery-600 text-white py-3 px-6 rounded-lg font-semibold hover:bg-bakery-700 transition-colors flex items-center justify-center gap-2">
                            <i class="fas fa-shopping-cart"></i>
                            Add to Cart
                        </button>
                    </div>
                </div>
                
                <div class="flex gap-4">
                    <button class="flex-1 border border-gray-300 py-3 px-6 rounded-lg font-semibold hover:bg-gray-50 transition-colors">
                        <i class="fas fa-heart mr-2"></i>
                        Add to Wishlist
                    </button>
                    <button class="flex-1 border border-gray-300 py-3 px-6 rounded-lg font-semibold hover:bg-gray-50 transition-colors">
                        <i class="fas fa-share-alt mr-2"></i>
                        Share
                    </button>
                </div>
            </div>

            <!-- Product Information Tabs -->
            <div class="border-t pt-6">
                <div class="border-b border-gray-200">
                    <nav class="-mb-px flex space-x-8">
                        <button onclick="showTab('description')" class="tab-button active py-2 px-1 border-b-2 font-medium text-sm">
                            Description
                        </button>
                        <button onclick="showTab('ingredients')" class="tab-button py-2 px-1 border-b-2 font-medium text-sm">
                            Ingredients
                        </button>
                        <button onclick="showTab('nutrition')" class="tab-button py-2 px-1 border-b-2 font-medium text-sm">
                            Nutrition
                        </button>
                        <button onclick="showTab('storage')" class="tab-button py-2 px-1 border-b-2 font-medium text-sm">
                            Storage
                        </button>
                    </nav>
                </div>
                
                <div class="mt-4">
                    <div id="description-tab" class="tab-content">
                        <h4 class="font-semibold mb-2">Product Description</h4>
                        <p class="text-gray-600">{{ $product->full_description ?? $product->description }}</p>
                    </div>
                    
                    <div id="ingredients-tab" class="tab-content hidden">
                        <h4 class="font-semibold mb-2">Ingredients</h4>
                        <p class="text-gray-600">{{ $product->ingredients ?? 'Ingredients information will be updated soon.' }}</p>
                        @if($product->allergen_info)
                        <div class="mt-4 p-3 bg-yellow-50 rounded-lg">
                            <h5 class="font-semibold text-yellow-800 mb-1">Allergen Information</h5>
                            <p class="text-sm text-yellow-700">{{ $product->allergen_info }}</p>
                        </div>
                        @endif
                    </div>
                    
                    <div id="nutrition-tab" class="tab-content hidden">
                        <h4 class="font-semibold mb-2">Nutritional Information</h4>
                        @if($product->nutritional_info)
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b">
                                    <th class="text-left py-2">Nutrient</th>
                                    <th class="text-right py-2">Per 100g</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($product->nutritional_info as $nutrient => $value)
                                <tr class="border-b">
                                    <td class="py-2">{{ ucfirst($nutrient) }}</td>
                                    <td class="text-right py-2">{{ $value }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @else
                        <p class="text-gray-600">Nutritional information will be updated soon.</p>
                        @endif
                    </div>
                    
                    <div id="storage-tab" class="tab-content hidden">
                        <h4 class="font-semibold mb-2">Storage Instructions</h4>
                        <p class="text-gray-600">{{ $product->storage_instructions ?? 'Store in a cool, dry place.' }}</p>
                        @if($product->shelf_life)
                        <p class="mt-2 text-gray-600"><strong>Shelf Life:</strong> {{ $product->shelf_life }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Related Products -->
    @if($related && $related->count() > 0)
    <div class="mt-16">
        <h2 class="text-2xl font-bold mb-8">Related Products</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($related as $relatedProduct)
            <div class="bg-white rounded-lg shadow-lg overflow-hidden group hover:shadow-xl transition-shadow">
                <a href="{{ route('product.show', $relatedProduct->slug) }}" class="block">
                    <div class="relative h-48 overflow-hidden">
                        @if($relatedProduct->first_image)
                            <img src="{{ asset('storage/' . $relatedProduct->first_image) }}" alt="{{ $relatedProduct->name }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                        @else
                            <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                                <i class="fas fa-image text-4xl text-gray-400"></i>
                            </div>
                        @endif
                        @if($relatedProduct->has_discount && $relatedProduct->discount_percentage)
                        <div class="absolute top-2 left-2 bg-red-500 text-white px-2 py-1 rounded-full text-xs font-semibold">
                            -{{ number_format($relatedProduct->discount_percentage) }}%
                        </div>
                        @endif
                    </div>
                    <div class="p-4">
                        <h3 class="font-semibold text-gray-900 mb-2">{{ $relatedProduct->name }}</h3>
                        <div class="flex items-center justify-between">
                            @if($relatedProduct->has_discount && $relatedProduct->discount_price)
                                <div>
                                    <span class="text-lg font-bold text-bakery-600">₹{{ number_format($relatedProduct->discount_price, 2) }}</span>
                                    <span class="text-sm text-gray-400 line-through ml-1">₹{{ number_format($relatedProduct->base_price, 2) }}</span>
                                </div>
                            @else
                                <span class="text-lg font-bold text-bakery-600">₹{{ number_format($relatedProduct->base_price, 2) }}</span>
                            @endif
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
    
    function selectVariant(variantId, price, sku, stock) {
        // Update selected variant
        selectedVariant = variantId;
        
        // Update UI
        document.querySelectorAll('.variant-option').forEach(btn => {
            btn.classList.remove('border-bakery-500', 'bg-bakery-50');
            if (btn.dataset.variantId == variantId) {
                btn.classList.add('border-bakery-500', 'bg-bakery-50');
            }
        });
        
        // Show variant info
        document.getElementById('variantInfo').classList.remove('hidden');
        document.getElementById('selectedSKU').textContent = sku;
        document.getElementById('selectedStock').textContent = stock + ' units';
        document.getElementById('selectedPrice').textContent = '₹' + price.toFixed(2);
        
        // Update max quantity based on stock
        document.getElementById('quantity').max = Math.min(stock, 10);
    }
    
    function showTab(tabName) {
        // Hide all tabs
        document.querySelectorAll('.tab-content').forEach(tab => {
            tab.classList.add('hidden');
        });
        
        // Remove active class from all buttons
        document.querySelectorAll('.tab-button').forEach(btn => {
            btn.classList.remove('border-bakery-600', 'text-bakery-600', 'active');
            btn.classList.add('border-transparent', 'text-gray-500');
        });
        
        // Show selected tab
        document.getElementById(tabName + '-tab').classList.remove('hidden');
        
        // Add active class to clicked button
        event.target.classList.add('border-bakery-600', 'text-bakery-600', 'active');
        event.target.classList.remove('border-transparent', 'text-gray-500');
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
    
    // Initialize first tab as active
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelector('.tab-button').classList.add('border-bakery-600', 'text-bakery-600');
        document.querySelector('.tab-button').classList.remove('border-transparent', 'text-gray-500');
    });
</script>
@endpush
@endsection