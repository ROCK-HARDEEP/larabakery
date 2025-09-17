@extends('web.layouts.app')

@section('content')
<div class="skc-mobile-search-page">
    <!-- Search Header -->
    <div class="skc-search-header">
        <div class="skc-search-bar">
            <form action="{{ route('products') }}" method="GET" class="skc-search-form-mobile">
                <div class="skc-search-input-wrapper">
                    <i class="fas fa-search skc-search-icon"></i>
                    <input 
                        type="search" 
                        name="q" 
                        class="skc-search-input-mobile" 
                        placeholder="Search for products..." 
                        value="{{ $searchQuery }}"
                        autocomplete="off"
                        required
                    >
                    @if($searchQuery)
                        <button type="button" class="skc-clear-search" onclick="clearSearch()">
                            <i class="fas fa-times"></i>
                        </button>
                    @endif
                </div>
                <button type="submit" class="skc-search-submit-mobile">
                    <i class="fas fa-arrow-right"></i>
                </button>
            </form>
        </div>
    </div>

    <!-- Search Content -->
    <div class="skc-search-content">
        <!-- Recent Searches -->
        @if(!empty($recentSearches))
        <div class="skc-search-section">
            <div class="skc-section-header">
                <h3>Recent Searches</h3>
                <button onclick="clearRecentSearches()" class="skc-clear-btn">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
            <div class="skc-search-tags">
                @foreach($recentSearches as $search)
                <a href="{{ route('products', ['q' => $search]) }}" class="skc-search-tag">
                    <i class="fas fa-history"></i>
                    {{ $search }}
                </a>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Popular Searches -->
        <div class="skc-search-section">
            <h3>Popular Searches</h3>
            <div class="skc-search-tags">
                @foreach($popularSearches as $search)
                <a href="{{ route('products', ['q' => $search]) }}" class="skc-search-tag">
                    <i class="fas fa-fire"></i>
                    {{ $search }}
                </a>
                @endforeach
            </div>
        </div>

        <!-- Categories -->
        <div class="skc-search-section">
            <h3>Shop by Category</h3>
            <div class="skc-category-grid">
                @foreach($categories as $category)
                <a href="{{ route('products', ['category' => $category->slug]) }}" class="skc-category-card-mobile">
                    <div class="skc-category-icon-mobile">
                        @if($category->image_url && $category->image_url !== asset('img/placeholder-category.jpg'))
                            <img src="{{ $category->image_url }}" alt="{{ $category->name }}" class="skc-category-image-mobile">
                        @else
                            @php
                                $categoryEmojis = [
                                    'cakes' => '🎂',
                                    'pastries' => '🥐',
                                    'bread' => '🍞',
                                    'cookies' => '🍪',
                                    'cupcakes' => '🧁',
                                    'seasonal' => '🍰',
                                    'beverages' => '☕',
                                    'snacks' => '🍩'
                                ];
                                $emoji = $categoryEmojis[strtolower($category->slug)] ?? '🧁';
                            @endphp
                            <span class="skc-category-emoji">{{ $emoji }}</span>
                        @endif
                    </div>
                    <span class="skc-category-name-mobile">{{ $category->name }}</span>
                </a>
                @endforeach
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="skc-search-section">
            <h3>Quick Actions</h3>
            <div class="skc-quick-actions">
                <a href="{{ route('products') }}" class="skc-quick-action">
                    <i class="fas fa-th-large"></i>
                    <span>Browse All Products</span>
                </a>
                <a href="{{ route('products', ['sort' => 'price_asc']) }}" class="skc-quick-action">
                    <i class="fas fa-sort-amount-down"></i>
                    <span>Lowest Price</span>
                </a>
                <a href="{{ route('products', ['in_stock' => 1]) }}" class="skc-quick-action">
                    <i class="fas fa-check-circle"></i>
                    <span>In Stock Only</span>
                </a>
                <a href="{{ route('products', ['sort' => 'newest']) }}" class="skc-quick-action">
                    <i class="fas fa-clock"></i>
                    <span>New Arrivals</span>
                </a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-focus search input
    const searchInput = document.querySelector('.skc-search-input-mobile');
    if (searchInput) {
        searchInput.focus();
    }

    // Save search to recent searches
    const searchForm = document.querySelector('.skc-search-form-mobile');
    searchForm.addEventListener('submit', function(e) {
        const query = searchInput.value.trim();
        if (query) {
            saveRecentSearch(query);
        }
    });
});

function clearSearch() {
    const searchInput = document.querySelector('.skc-search-input-mobile');
    searchInput.value = '';
    searchInput.focus();
}

function saveRecentSearch(query) {
    let recentSearches = JSON.parse(localStorage.getItem('recentSearches') || '[]');
    
    // Remove if already exists
    recentSearches = recentSearches.filter(item => item !== query);
    
    // Add to beginning
    recentSearches.unshift(query);
    
    // Keep only last 10 searches
    recentSearches = recentSearches.slice(0, 10);
    
    localStorage.setItem('recentSearches', JSON.stringify(recentSearches));
}

function clearRecentSearches() {
    localStorage.removeItem('recentSearches');
    location.reload();
}

// Handle search suggestions (optional)
const searchInput = document.querySelector('.skc-search-input-mobile');
if (searchInput) {
    let searchTimeout;
    
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const query = this.value.trim();
        
        if (query.length >= 2) {
            searchTimeout = setTimeout(() => {
                // You can implement AJAX search suggestions here
                console.log('Searching for:', query);
            }, 300);
        }
    });
}
</script>
@endpush
@endsection
