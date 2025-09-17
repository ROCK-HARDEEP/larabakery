<!-- Shop by Category Section -->
<section class="skc-section shop-by-category-section" style="background: white; padding: 60px 0;">
    <div class="skc-container">
        <div class="skc-section-header" style="margin-bottom: 40px; text-align: center;">
            <h2 class="skc-section-title" style="font-size: 36px; font-weight: 700; color: var(--skc-black); margin: 0 0 10px 0;">Shop by Category</h2>
            <p class="skc-section-subtitle" style="font-size: 16px; color: var(--skc-medium-gray); margin: 0;">Discover our freshly baked delights</p>
        </div>
        
        <!-- Category Grid - Max 8 per row, auto-adjust size -->
        <div class="category-grid-container">
            @foreach($categories as $category)
            <div class="category-grid-item">
                <a href="{{ route('category.products', $category->slug) }}" class="category-card-link">
                    <div class="category-card">
                        <div class="category-icon-wrapper">
                            @if($category->image_url && $category->image_url !== asset('img/placeholder-category.jpg'))
                                <img src="{{ $category->image_url }}" alt="{{ $category->name }}" class="category-image">
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
                                <span class="category-emoji">{{ $emoji }}</span>
                            @endif
                        </div>
                        <h3 class="category-name">{{ $category->name }}</h3>
                        <span class="category-count">{{ $category->products_count ?? 0 }} Items</span>
                    </div>
                </a>
            </div>
            @endforeach
        </div>

        <!-- View All Categories Button - Only show if there are more categories than displayed -->
        @if(isset($totalCategories) && $totalCategories > $categories->count())
        <div class="view-all-categories-container" style="text-align: center; margin-top: 40px;">
            <a href="{{ route('categories.all') }}" class="view-all-categories-btn">
                View All Categories
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-left: 8px; vertical-align: middle;">
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                    <polyline points="12 5 19 12 12 19"></polyline>
                </svg>
            </a>
        </div>
        @endif
    </div>
</section>

<style>
/* Category Grid Container */
.category-grid-container {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    width: 100%;
    justify-content: center;
}

/* Category Grid Item */
.category-grid-item {
    min-width: 120px;
    transition: all 0.3s ease;
    flex: 1 1 auto;
}

/* Adjust for different screen sizes and item counts */
@media (min-width: 1200px) {
    /* Default behavior - allow items to grow and shrink */
    .category-grid-container {
        justify-content: space-between;
    }

    .category-grid-container::after {
        content: "";
        flex: auto;
    }

    /* For 1-8 items: all in one row */
    .category-grid-container:has(.category-grid-item:nth-child(1)):not(:has(.category-grid-item:nth-child(9))) .category-grid-item {
        flex: 0 1 calc(12.5% - 18px);
        max-width: 150px;
    }

    /* For exactly 9 items: 5 in first row, 4 in second with auto-fill */
    .category-grid-container:has(.category-grid-item:nth-child(9)):not(:has(.category-grid-item:nth-child(10))) .category-grid-item:nth-child(-n+5) {
        flex: 0 1 calc(20% - 16px);
        max-width: calc(20% - 16px);
    }

    .category-grid-container:has(.category-grid-item:nth-child(9)):not(:has(.category-grid-item:nth-child(10))) .category-grid-item:nth-child(n+6) {
        flex: 1 1 calc(25% - 15px);
        max-width: calc(25% - 15px);
    }

    /* For exactly 10 items: 5-5 distribution */
    .category-grid-container:has(.category-grid-item:nth-child(10)):not(:has(.category-grid-item:nth-child(11))) .category-grid-item {
        flex: 0 1 calc(20% - 16px);
        max-width: calc(20% - 16px);
    }

    /* For 11-12 items: 6 per row */
    .category-grid-container:has(.category-grid-item:nth-child(11)):not(:has(.category-grid-item:nth-child(13))) .category-grid-item {
        flex: 0 1 calc(16.66% - 17px);
        max-width: calc(16.66% - 17px);
    }

    /* For 13-14 items: 7 per row */
    .category-grid-container:has(.category-grid-item:nth-child(13)):not(:has(.category-grid-item:nth-child(15))) .category-grid-item {
        flex: 0 1 calc(14.28% - 17px);
        max-width: calc(14.28% - 17px);
    }

    /* For 15-16 items: 8 per row */
    .category-grid-container:has(.category-grid-item:nth-child(15)):not(:has(.category-grid-item:nth-child(17))) .category-grid-item {
        flex: 0 1 calc(12.5% - 18px);
        max-width: calc(12.5% - 18px);
    }

    /* For 17+ items: max 8 per row */
    .category-grid-container:has(.category-grid-item:nth-child(17)) .category-grid-item {
        flex: 0 1 calc(12.5% - 18px);
        max-width: calc(12.5% - 18px);
    }
}

/* Category Card */
.category-card-link {
    text-decoration: none;
    display: block;
    height: 100%;
}

.category-card {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 20px 15px;
    text-align: center;
    transition: all 0.3s ease;
    cursor: pointer;
    height: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    border: 2px solid transparent;
    position: relative;
    overflow: hidden;
}

.category-card:hover {
    background: linear-gradient(135deg, rgba(246,157,28,0.1) 0%, rgba(246,157,28,0.05) 100%);
    border-color: var(--skc-orange);
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.12);
}

.category-card::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: var(--skc-orange);
    transform: scaleX(0);
    transition: transform 0.3s ease;
}

.category-card:hover::before {
    transform: scaleX(1);
}

/* Category Icon */
.category-icon-wrapper {
    width: 60px;
    height: 60px;
    margin: 0 auto 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: white;
    border-radius: 50%;
    transition: all 0.3s ease;
}

.category-card:hover .category-icon-wrapper {
    transform: scale(1.1);
    box-shadow: 0 4px 15px rgba(246,157,28,0.3);
}

.category-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 50%;
}

.category-emoji {
    font-size: 32px;
    line-height: 1;
}

/* Category Name */
.category-name {
    font-size: 14px;
    font-weight: 600;
    color: var(--skc-black);
    margin: 0 0 4px 0;
    transition: color 0.3s ease;
}

.category-card:hover .category-name {
    color: var(--skc-orange);
}

/* Category Count */
.category-count {
    font-size: 12px;
    color: var(--skc-medium-gray);
    font-weight: 400;
}

/* Responsive Design */
@media (max-width: 1199px) {
    .category-grid-item {
        flex: 1 1 calc(16.66% - 17px);
        max-width: 180px;
    }
}

@media (max-width: 991px) {
    .category-grid-item {
        flex: 1 1 calc(25% - 15px);
        max-width: 200px;
    }
}

@media (max-width: 767px) {
    .category-grid-item {
        flex: 1 1 calc(33.33% - 13px);
        max-width: 150px;
    }
    
    .category-icon-wrapper {
        width: 50px;
        height: 50px;
    }
    
    .category-emoji {
        font-size: 24px;
    }
    
    .category-name {
        font-size: 13px;
    }
    
    .category-count {
        font-size: 11px;
    }
}

@media (max-width: 479px) {
    .category-grid-item {
        flex: 1 1 calc(50% - 10px);
        max-width: 150px;
    }
    
    .category-card {
        padding: 15px 10px;
    }
}

/* Add smooth scroll behavior */
html {
    scroll-behavior: smooth;
}

/* View All Categories Button */
.view-all-categories-btn {
    display: inline-flex;
    align-items: center;
    padding: 14px 32px;
    background: linear-gradient(135deg, #1a1a1a, #000000);
    color: white;
    text-decoration: none;
    font-size: 16px;
    font-weight: 600;
    border-radius: 30px;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
    position: relative;
    overflow: hidden;
}

.view-all-categories-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.4);
    background: linear-gradient(135deg, #2a2a2a, #1a1a1a);
    color: white;
}

.view-all-categories-btn::before {
    content: "";
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: left 0.5s ease;
}

.view-all-categories-btn:hover::before {
    left: 100%;
}

@media (max-width: 767px) {
    .view-all-categories-btn {
        padding: 12px 24px;
        font-size: 14px;
    }
}
</style>