@extends('web.layouts.app')

@section('content')
<div class="bg-gray-50 min-h-screen">
    <!-- Breadcrumb -->
    <div class="bg-white border-b">
        <div class="skc-container py-4">
            <nav class="flex items-center space-x-2 text-sm">
                <a href="{{ route('home') }}" class="text-gray-600 hover:text-orange-600">Home</a>
                <span class="text-gray-400">/</span>
                <span class="text-gray-600">{{ $product->category->name }}</span>
                <span class="text-gray-400">/</span>
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
                        <div class="relative aspect-square lg:h-full bg-gray-100">
                            <img id="mainImage" 
                                 src="{{ asset('storage/' . $productImages[0]) }}" 
                                 alt="{{ $product->name }}" 
                                 class="w-full h-full object-cover">
                        </div>
                    @else
                        <div class="aspect-square lg:h-full bg-gray-200 flex items-center justify-center">
                            <p class="text-gray-500">No Image Available</p>
                        </div>
                    @endif
                </div>

                <!-- Product Details -->
                <div class="p-8 lg:p-12 space-y-8">
                    <!-- Product Header -->
                    <div class="space-y-4">
                        <h1 class="text-3xl lg:text-4xl font-bold text-gray-900">{{ $product->name }}</h1>
                        
                        <!-- Pricing -->
                        <div class="border-l-4 border-orange-500 pl-4 bg-orange-50 rounded-r-lg py-4">
                            @if($product->has_discount && $product->discount_price)
                                <div class="flex items-center gap-3">
                                    <span class="text-4xl font-bold text-orange-600">₹{{ number_format($product->discount_price, 2) }}</span>
                                    <span class="text-xl text-gray-500 line-through">₹{{ number_format($product->base_price, 2) }}</span>
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
                    </div>

                    <!-- Product Variants -->
                    @if($product->variants && $product->variants->count() > 0)
                    <div class="space-y-6 border-t pt-8">
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
                                                {{ $variant->stock_quantity > 0 ? $variant->stock_quantity . ' in stock' : 'Out of stock' }}
                                            </div>
                                        </div>
                                    </div>
                                </label>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif
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
                        Description
                    </button>
                    <button onclick="showTab('ingredients')" 
                            class="tab-button flex-1 py-4 px-6 text-center font-semibold text-lg border-b-3 border-transparent text-gray-600 hover:bg-gray-50 transition-all">
                        Ingredients
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
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($related as $relatedProduct)
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden group hover:shadow-2xl transition-all duration-300">
                <a href="{{ route('product.show', $relatedProduct->slug) }}" class="block">
                    <div class="relative h-48 overflow-hidden">
                        @if($relatedProduct->first_image)
                            <img src="{{ asset('storage/' . $relatedProduct->first_image) }}" 
                                 alt="{{ $relatedProduct->name }}" 
                                 class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                                <span class="text-gray-400">No Image</span>
                            </div>
                        @endif
                    </div>
                    <div class="p-6">
                        <h3 class="font-bold text-gray-900 mb-3 text-lg">{{ $relatedProduct->name }}</h3>
                        <div class="text-xl font-bold text-orange-600">₹{{ number_format($relatedProduct->base_price, 2) }}</div>
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
    const hasVariants = {{ ($product->variants && $product->variants->count() > 0) ? 'true' : 'false' }};
    
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
        const current = parseInt(input.value);
        const max = parseInt(input.max);
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
        
        if (hasVariants && !selectedVariant) {
            alert('Please select a variant');
            return;
        }
        
        alert('Product added to cart! (Quantity: ' + quantity + ')');
    }
</script>
@endpush
@endsection