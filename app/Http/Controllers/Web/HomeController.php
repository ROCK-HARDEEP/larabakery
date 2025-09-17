<?php

namespace App\Http\Controllers\Web;


use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\HeroSlide;
use App\Models\Bundle;
use App\Models\ComboOffer;
use App\Models\HomePageCategory;
use App\Models\PopularProduct;
use App\Models\WhyChooseUs;
use App\Models\Blog;
use App\Models\Testimonial;
use App\Models\HomepageFaq;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    public function index()
    {
        // Load hero slides with caching
        $heroSlides = Cache::remember('home.hero_slides', 300, function () {
            return HeroSlide::where('is_active', true)
                ->with(['category', 'product'])
                ->orderBy('sort_order')
                ->get();
        });

        // Load home page categories with caching
        $homePageCategories = Cache::remember('home.page_categories', 300, function () {
            return HomePageCategory::where('is_active', true)
                ->with(['category' => function($query) {
                    $query->withCount(['products as products_count' => function($q){ 
                        $q->where('is_active', true); 
                    }]);
                }])
                ->orderBy('sort_order')
                ->get();
        });

        // Fallback to regular categories if no home page categories are set
        if ($homePageCategories->isEmpty()) {
            $categories = Category::where('is_active', true)
                ->withCount(['products as products_count' => function($q){ $q->where('is_active', true); }])
                ->orderBy('position')
                ->take(6)
                ->get();
        } else {
            $categories = $homePageCategories->pluck('category');
        }

        // Load popular products with caching
        $popularProducts = Cache::remember('home.popular_products', 300, function () {
            return PopularProduct::where('is_active', true)
                ->with(['product' => function($query) {
                    $query->where('is_active', true)
                        ->with('category');
                }])
                ->orderBy('sort_order')
                ->get();
        });

        // Get the actual products from the relationship
        $popular = $popularProducts->pluck('product')->filter()->take(8);
        
        // If no popular products are set, fallback to latest products
        if ($popular->isEmpty()) {
            $popular = Product::where('is_active', true)->latest()->take(8)->get();
        }
        
        // Active bundles with caching (shorter cache time for time-sensitive offers)
        $bundles = Cache::remember('home.bundles', 60, function () {
            return Bundle::query()
                ->with(['items.product'])
                ->withCount('items')
                ->where('is_active', true)
                ->whereHas('items')
                ->where(function($q){
                    $now = now();
                    $q->whereNull('starts_at')->orWhere('starts_at', '<=', $now);
                })
                ->where(function($q){
                    $now = now();
                    $q->whereNull('ends_at')->orWhere('ends_at', '>=', $now);
                })
                ->latest()
                ->take(4)
                ->get();
        });
        
        // Active combo offers with caching (first 3 by display order)
        $combos = Cache::remember('home.combos', 60, function () {
            return ComboOffer::active()
                ->with(['products'])
                ->orderBy('display_order', 'asc')
                ->orderBy('created_at', 'desc')
                ->take(3)
                ->get();
        });
        
        // Load Why Choose Us items with caching
        $whyChooseUs = Cache::remember('home.why_choose_us', 3600, function () {
            return WhyChooseUs::where('is_active', true)
                ->orderBy('sort_order')
                ->get();
        });
        
        // Load blogs with caching
        $blogs = Cache::remember('home.blogs', 300, function () {
            return Blog::active()
                ->published()
                ->ordered()
                ->take(6)
                ->get();
        });
        
        // Load testimonials with caching
        $testimonials = Cache::remember('home.testimonials', 600, function () {
            return Testimonial::active()
                ->ordered()
                ->take(12)
                ->get();
        });
        
        // Load homepage FAQs
        $homepageFaqs = Cache::remember('home.faqs', 300, function () {
            return HomepageFaq::where('is_active', true)
                ->ordered()
                ->get();
        });

        // Load new arrivals (5 most recent products with the "new" tag)
        $newArrivals = Cache::remember('home.new_arrivals', 300, function () {
            return Product::where('is_active', true)
                ->with('category')
                ->new() // Use the new scope we defined
                ->get();
        });

        // Get total count of active categories to determine if "View All" button should show
        $totalCategories = Cache::remember('home.total_categories_count', 600, function () {
            return Category::where('is_active', true)->count();
        });

        return view('web.home', compact('heroSlides', 'categories', 'popular', 'bundles', 'combos', 'whyChooseUs', 'blogs', 'testimonials', 'homepageFaqs', 'newArrivals', 'totalCategories'));
    }
}