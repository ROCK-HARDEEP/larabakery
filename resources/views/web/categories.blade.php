@extends('web.layouts.app')

@section('title', 'All Categories - ' . config('app.name'))

@section('content')
<!-- All Categories Page -->
<section class="all-categories-page" style="padding: 80px 0; background: #f8f9fa;">
    <div class="container" style="max-width: 1400px; margin: 0 auto; padding: 0 20px;">
        <!-- Page Header -->
        <div class="page-header" style="text-align: center; margin-bottom: 60px;">
            <h1 class="page-title" style="font-size: 42px; font-weight: 700; color: #333; margin: 0 0 20px 0;">
                All Categories
            </h1>
            <p class="page-subtitle" style="font-size: 18px; color: #666; margin: 0 0 30px 0;">
                Explore our complete range of bakery categories
            </p>

            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" style="display: flex; justify-content: center;">
                <ol class="breadcrumb" style="background: none; padding: 0; margin: 0; list-style: none; display: flex; gap: 15px;">
                    <li class="breadcrumb-item">
                        <a href="{{ route('home') }}" style="color: #f27522; text-decoration: none;">Home</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page" style="color: #666;">
                        › All Categories
                    </li>
                </ol>
            </nav>
        </div>

        <!-- Categories Grid -->
        <div class="categories-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 30px; margin-bottom: 40px;">
            @foreach($categories as $category)
            <div class="category-card-wrapper" style="transition: all 0.3s ease;">
                <a href="{{ route('category.products', $category->slug) }}" class="category-card-link" style="text-decoration: none; display: block; height: 100%;">
                    <div class="category-card" style="background: white; border-radius: 16px; overflow: hidden; height: 100%; display: flex; flex-direction: column; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08); transition: all 0.3s ease; border: 2px solid transparent;">
                        <div class="category-image-wrapper" style="width: 100%; height: 200px; background: linear-gradient(135deg, #fef7f0, #fdecd8); display: flex; align-items: center; justify-content: center; position: relative; overflow: hidden;">
                            @if($category->image_url && $category->image_url !== asset('img/placeholder-category.jpg'))
                                <img src="{{ $category->image_url }}" alt="{{ $category->name }}" class="category-image" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s ease;">
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
                                        'snacks' => '🍩',
                                        'desserts' => '🍮',
                                        'pies' => '🥧',
                                        'sandwiches' => '🥪',
                                        'breakfast' => '🥞'
                                    ];
                                    $emoji = $categoryEmojis[strtolower($category->slug)] ?? '🧁';
                                @endphp
                                <div class="category-emoji-placeholder" style="font-size: 80px; opacity: 0.8;">{{ $emoji }}</div>
                            @endif
                        </div>
                        <div class="category-info" style="padding: 25px; flex: 1; display: flex; flex-direction: column;">
                            <h3 class="category-name" style="font-size: 22px; font-weight: 700; color: #333; margin: 0 0 10px 0; transition: color 0.3s ease;">{{ $category->name }}</h3>
                            <p class="category-description" style="font-size: 14px; color: #666; margin: 0 0 20px 0; flex: 1; line-height: 1.6;">
                                {{ $category->description ?? 'Delicious ' . strtolower($category->name) . ' freshly made for you' }}
                            </p>
                            <div class="category-footer" style="display: flex; justify-content: space-between; align-items: center; padding-top: 15px; border-top: 1px solid #f0f0f0;">
                                <span class="category-count" style="font-size: 14px; font-weight: 600; color: #f27522;">{{ $category->products_count ?? 0 }} Products</span>
                                <span class="category-arrow" style="font-size: 20px; color: #f27522; transition: transform 0.3s ease;">→</span>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            @endforeach
        </div>

        <!-- Back to Home Button -->
        <div class="back-to-home" style="text-align: center; margin-top: 60px;">
            <a href="{{ route('home') }}" class="back-home-btn" style="display: inline-flex; align-items: center; padding: 14px 32px; background: white; color: #333; text-decoration: none; font-size: 16px; font-weight: 600; border-radius: 30px; transition: all 0.3s ease; border: 2px solid #f27522;">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 8px;">
                    <line x1="19" y1="12" x2="5" y2="12"></line>
                    <polyline points="12 19 5 12 12 5"></polyline>
                </svg>
                Back to Home
            </a>
        </div>
    </div>
</section>

<style>
/* Page Styles */
.all-categories-page {
    min-height: 100vh;
}

.skc-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 0 20px;
}

/* Breadcrumb */
.breadcrumb {
    display: flex;
    list-style: none;
    gap: 15px;
}

.breadcrumb-item:not(:last-child)::after {
    content: "›";
    margin-left: 15px;
    color: var(--skc-medium-gray);
}

/* Categories Grid */
.categories-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 30px;
    margin-bottom: 40px;
}

/* Category Card */
.category-card-wrapper {
    transition: all 0.3s ease;
}

.category-card-link {
    text-decoration: none;
    display: block;
    height: 100%;
}

.category-card {
    background: white;
    border-radius: 16px;
    overflow: hidden;
    height: 100%;
    display: flex;
    flex-direction: column;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.category-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    border-color: var(--skc-orange);
}

/* Category Image */
.category-image-wrapper {
    width: 100%;
    height: 200px;
    background: linear-gradient(135deg, #fef7f0, #fdecd8);
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    overflow: hidden;
}

.category-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.category-card:hover .category-image {
    transform: scale(1.1);
}

.category-emoji-placeholder {
    font-size: 80px;
    opacity: 0.8;
}

/* Category Info */
.category-info {
    padding: 25px;
    flex: 1;
    display: flex;
    flex-direction: column;
}

.category-name {
    font-size: 22px;
    font-weight: 700;
    color: var(--skc-black);
    margin: 0 0 10px 0;
    transition: color 0.3s ease;
}

.category-card:hover .category-name {
    color: var(--skc-orange);
}

.category-description {
    font-size: 14px;
    color: var(--skc-medium-gray);
    margin: 0 0 20px 0;
    flex: 1;
    line-height: 1.6;
}

.category-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 15px;
    border-top: 1px solid #f0f0f0;
}

.category-count {
    font-size: 14px;
    font-weight: 600;
    color: var(--skc-orange);
}

.category-arrow {
    font-size: 20px;
    color: var(--skc-orange);
    transition: transform 0.3s ease;
}

.category-card:hover .category-arrow {
    transform: translateX(5px);
}

/* Back to Home Button */
.back-home-btn {
    display: inline-flex;
    align-items: center;
    padding: 14px 32px;
    background: white;
    color: var(--skc-black);
    text-decoration: none;
    font-size: 16px;
    font-weight: 600;
    border-radius: 30px;
    transition: all 0.3s ease;
    border: 2px solid var(--skc-orange);
}

.back-home-btn:hover {
    background: var(--skc-orange);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(246, 157, 28, 0.3);
}

/* Responsive Design */
@media (max-width: 1200px) {
    .categories-grid {
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 25px;
    }
}

@media (max-width: 768px) {
    .page-title {
        font-size: 32px !important;
    }

    .page-subtitle {
        font-size: 16px !important;
    }

    .categories-grid {
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 20px;
    }

    .category-image-wrapper {
        height: 150px;
    }

    .category-emoji-placeholder {
        font-size: 60px;
    }

    .category-info {
        padding: 20px;
    }

    .category-name {
        font-size: 18px;
    }
}

@media (max-width: 480px) {
    .categories-grid {
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 15px;
    }

    .category-description {
        display: none;
    }

    .category-info {
        padding: 15px;
    }

    .back-home-btn {
        padding: 12px 24px;
        font-size: 14px;
    }
}

/* Animation */
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

.category-card-wrapper {
    animation: fadeInUp 0.5s ease-out forwards;
    opacity: 0;
}

.category-card-wrapper:nth-child(1) { animation-delay: 0.1s; }
.category-card-wrapper:nth-child(2) { animation-delay: 0.15s; }
.category-card-wrapper:nth-child(3) { animation-delay: 0.2s; }
.category-card-wrapper:nth-child(4) { animation-delay: 0.25s; }
.category-card-wrapper:nth-child(5) { animation-delay: 0.3s; }
.category-card-wrapper:nth-child(6) { animation-delay: 0.35s; }
.category-card-wrapper:nth-child(7) { animation-delay: 0.4s; }
.category-card-wrapper:nth-child(8) { animation-delay: 0.45s; }
.category-card-wrapper:nth-child(9) { animation-delay: 0.5s; }
.category-card-wrapper:nth-child(10) { animation-delay: 0.55s; }
.category-card-wrapper:nth-child(n+11) { animation-delay: 0.6s; }
</style>
@endsection