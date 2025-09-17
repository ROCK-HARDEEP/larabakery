@extends('web.layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Page Header -->
    <div class="text-center mb-12">
        <h1 class="text-4xl font-serif font-bold text-gray-800 mb-4">All Products</h1>
        <p class="text-xl text-gray-600">Browse our full catalog of fresh bakery items</p>
    </div>

    <div class="flex gap-8">
        <!-- Filter Sidebar Toggle Button (Mobile) -->
        <button 
            id="filterToggle" 
            class="lg:hidden fixed left-4 top-1/2 transform -translate-y-1/2 z-50 bg-bakery-500 text-white p-3 rounded-r-lg shadow-lg hover:bg-bakery-600 transition-all duration-300"
            onclick="toggleFilterSidebar()"
        >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
            </svg>
        </button>

        <!-- Filter Sidebar -->
        <aside 
            id="filterSidebar" 
            class="fixed lg:relative lg:block left-0 top-0 h-full lg:h-auto w-80 lg:w-64 bg-white shadow-2xl lg:shadow-none lg:bg-transparent z-40 transform -translate-x-full lg:translate-x-0 transition-transform duration-300"
        >
            <div class="lg:sticky lg:top-4 p-6 lg:p-0">
                <!-- Close button (Mobile) -->
                <button 
                    class="lg:hidden absolute top-4 right-4 text-gray-500 hover:text-gray-700"
                    onclick="toggleFilterSidebar()"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>

                <h2 class="text-xl font-bold mb-6 text-gray-800">Filters</h2>

                <form method="GET" action="{{ route('products') }}" id="filterForm">
                    <!-- Categories -->
                    <div class="mb-6">
                        <h3 class="font-semibold mb-3 text-gray-700 flex items-center justify-between cursor-pointer" onclick="toggleSection('categories')">
                            <span>Categories</span>
                            <svg class="w-4 h-4 transform transition-transform" id="categories-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </h3>
                        <div id="categories-section" class="space-y-2">
                            <label class="flex items-center cursor-pointer hover:text-bakery-600 transition-colors">
                                <input type="radio" name="category" value="" class="mr-2 text-bakery-500 focus:ring-bakery-500" {{ !$activeCategory ? 'checked' : '' }}>
                                <span>All Categories</span>
                            </label>
                            @foreach($categories as $cat)
                                <label class="flex items-center cursor-pointer hover:text-bakery-600 transition-colors">
                                    <input type="radio" name="category" value="{{ $cat->slug }}" class="mr-2 text-bakery-500 focus:ring-bakery-500" {{ ($activeCategory==$cat->slug||$activeCategory==$cat->id)?'checked':'' }}>
                                    <span>{{ $cat->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Price Range -->
                    <div class="mb-6">
                        <h3 class="font-semibold mb-3 text-gray-700 flex items-center justify-between cursor-pointer" onclick="toggleSection('price')">
                            <span>Price Range</span>
                            <svg class="w-4 h-4 transform transition-transform" id="price-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </h3>
                        <div id="price-section" class="space-y-3">
                            <div>
                                <label class="text-sm text-gray-600">Min Price</label>
                                <input type="number" step="0.01" name="min" value="{{ $min }}" 
                                    class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-bakery-500 focus:border-transparent"
                                    placeholder="₹0">
                            </div>
                            <div>
                                <label class="text-sm text-gray-600">Max Price</label>
                                <input type="number" step="0.01" name="max" value="{{ $max }}" 
                                    class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-bakery-500 focus:border-transparent"
                                    placeholder="₹1000">
                            </div>
                            <div class="pt-2">
                                <div class="flex justify-between text-sm text-gray-500">
                                    <span>₹0</span>
                                    <span>₹1000+</span>
                                </div>
                                <input type="range" min="0" max="1000" class="w-full mt-1">
                            </div>
                        </div>
                    </div>

                    <!-- Sort Options -->
                    <div class="mb-6">
                        <h3 class="font-semibold mb-3 text-gray-700 flex items-center justify-between cursor-pointer" onclick="toggleSection('sort')">
                            <span>Sort By</span>
                            <svg class="w-4 h-4 transform transition-transform" id="sort-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </h3>
                        <div id="sort-section" class="space-y-2">
                            <label class="flex items-center cursor-pointer hover:text-bakery-600 transition-colors">
                                <input type="radio" name="sort" value="" class="mr-2 text-bakery-500 focus:ring-bakery-500" {{ $sort==''?'checked':'' }}>
                                <span>Newest First</span>
                            </label>
                            <label class="flex items-center cursor-pointer hover:text-bakery-600 transition-colors">
                                <input type="radio" name="sort" value="price_asc" class="mr-2 text-bakery-500 focus:ring-bakery-500" {{ $sort=='price_asc'?'checked':'' }}>
                                <span>Price: Low to High</span>
                            </label>
                            <label class="flex items-center cursor-pointer hover:text-bakery-600 transition-colors">
                                <input type="radio" name="sort" value="price_desc" class="mr-2 text-bakery-500 focus:ring-bakery-500" {{ $sort=='price_desc'?'checked':'' }}>
                                <span>Price: High to Low</span>
                            </label>
                            <label class="flex items-center cursor-pointer hover:text-bakery-600 transition-colors">
                                <input type="radio" name="sort" value="newest" class="mr-2 text-bakery-500 focus:ring-bakery-500" {{ $sort=='newest'?'checked':'' }}>
                                <span>Recently Added</span>
                            </label>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col gap-2 mt-8">
                        <button type="submit" class="w-full px-4 py-3 bg-bakery-500 text-white rounded-lg hover:bg-bakery-600 transition-colors font-semibold">
                            Apply Filters
                        </button>
                        <a href="{{ route('products') }}" class="w-full px-4 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors text-center">
                            Reset All
                        </a>
                    </div>
                </form>
            </div>
        </aside>

        <!-- Products Grid -->
        <div class="flex-1">
            <!-- Active Filters Bar -->
            @if($activeCategory || $min || $max || $sort)
            <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Active Filters:</span>
                    <div class="flex flex-wrap gap-2">
                        @if($activeCategory)
                            <span class="px-3 py-1 bg-bakery-100 text-bakery-700 rounded-full text-sm">
                                Category: {{ ucfirst($activeCategory) }}
                                <a href="{{ route('products', array_merge(request()->except('category'))) }}" class="ml-2 text-bakery-500 hover:text-bakery-700">×</a>
                            </span>
                        @endif
                        @if($min)
                            <span class="px-3 py-1 bg-bakery-100 text-bakery-700 rounded-full text-sm">
                                Min: ₹{{ $min }}
                                <a href="{{ route('products', array_merge(request()->except('min'))) }}" class="ml-2 text-bakery-500 hover:text-bakery-700">×</a>
                            </span>
                        @endif
                        @if($max)
                            <span class="px-3 py-1 bg-bakery-100 text-bakery-700 rounded-full text-sm">
                                Max: ₹{{ $max }}
                                <a href="{{ route('products', array_merge(request()->except('max'))) }}" class="ml-2 text-bakery-500 hover:text-bakery-700">×</a>
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            <!-- Products Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($products as $product)
                    <div class="group bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-100 hover:border-bakery-200 overflow-hidden">
                        <div class="relative overflow-hidden">
                            @php($first = $product->first_image ?? null)
                            @if($first)
                                <img src="{{ asset('storage/' . $first) }}" alt="{{ $product->name }}" class="w-full h-48 object-cover group-hover:scale-110 transition-transform duration-300"/>
                            @else
                                <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                                    <i class="fas fa-image text-4xl text-gray-400"></i>
                                </div>
                            @endif
                            <!-- Quick View Badge -->
                            <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                <button class="bg-white p-2 rounded-full shadow-lg hover:bg-bakery-50">
                                    <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <div class="p-5">
                            <h3 class="font-semibold text-lg text-gray-800 mb-2 group-hover:text-bakery-600 transition-colors">
                                <a href="{{ route('product.show', $product->slug) }}">{{ $product->name }}</a>
                            </h3>
                            <div class="flex items-center justify-between mb-3">
                                <div class="text-2xl font-bold text-bakery-600">₹{{ number_format($product->base_price, 2) }}</div>
                                @if($product->original_price > $product->base_price)
                                <div class="text-sm text-gray-400 line-through">₹{{ number_format($product->original_price, 2) }}</div>
                                @endif
                            </div>
                            <div class="flex items-center gap-2">
                                <select id="qty-list-{{ $product->id }}" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-gray-800 text-sm">
                                    @for($i=1;$i<=10;$i++)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                                @php($stock = (int)($product->total_stock ?? 0))
                                @if($stock <= 0)
                                    <button class="flex-1 px-4 py-2 rounded-lg bg-gray-100 text-gray-400 cursor-not-allowed text-sm" disabled>
                                        Out of stock
                                    </button>
                                @else
                                    <button class="flex-1 bg-bakery-500 text-white px-4 py-2 rounded-lg hover:bg-bakery-600 transition-colors text-sm font-semibold quick-add-btn" data-pid="{{ $product->id }}" data-select="#qty-list-{{ $product->id }}">
                                        Add to Cart
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-12">
                @include('web.components.custom-pagination', ['paginator' => $products, 'elements' => $products->links()->elements])
            </div>
        </div>
    </div>
</div>

<!-- Overlay for mobile -->
<div id="filterOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-30 hidden lg:hidden" onclick="toggleFilterSidebar()"></div>
@endsection

@push('scripts')
<script>
function toggleFilterSidebar() {
    const sidebar = document.getElementById('filterSidebar');
    const overlay = document.getElementById('filterOverlay');
    sidebar.classList.toggle('-translate-x-full');
    overlay.classList.toggle('hidden');
}

function toggleSection(section) {
    const sectionEl = document.getElementById(section + '-section');
    const arrow = document.getElementById(section + '-arrow');
    sectionEl.classList.toggle('hidden');
    arrow.classList.toggle('rotate-180');
}

document.addEventListener('DOMContentLoaded', function(){
    // Quick add to cart
    document.querySelectorAll('.quick-add-btn').forEach(function(btn){
        btn.addEventListener('click', function(e){
            e.preventDefault();
            var pid = parseInt(btn.getAttribute('data-pid'), 10);
            var select = document.querySelector(btn.getAttribute('data-select'));
            var qty = select ? parseInt(select.value, 10) || 1 : 1;
            if (window.BakeryShop && BakeryShop.quickAddToCart){
                BakeryShop.quickAddToCart(pid, qty, btn);
            }
        });
    });

    // Auto submit form on filter change
    document.querySelectorAll('#filterForm input[type="radio"]').forEach(function(input){
        input.addEventListener('change', function(){
            document.getElementById('filterForm').submit();
        });
    });
});
</script>
@endpush

@push('styles')
<style>
.rotate-180 {
    transform: rotate(180deg);
}
</style>
@endpush