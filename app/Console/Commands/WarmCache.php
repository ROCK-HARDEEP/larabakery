<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use App\Models\HeroSlide;
use App\Models\HomePageCategory;
use App\Models\PopularProduct;
use App\Models\Bundle;
use App\Models\WhyChooseUs;
use App\Models\Blog;
use App\Models\Testimonial;

class WarmCache extends Command
{
    protected $signature = 'cache:warm';
    protected $description = 'Warm application cache for better performance';

    public function handle()
    {
        $this->info('Warming cache...');
        
        // Hero slides
        Cache::forget('home.hero_slides');
        Cache::remember('home.hero_slides', 300, function () {
            return HeroSlide::where('is_active', true)
                ->with(['category', 'product'])
                ->orderBy('sort_order')
                ->get();
        });
        $this->info('✓ Hero slides cached');
        
        // Home page categories
        Cache::forget('home.page_categories');
        Cache::remember('home.page_categories', 300, function () {
            return HomePageCategory::where('is_active', true)
                ->with(['category' => function($query) {
                    $query->withCount(['products as products_count' => function($q){ 
                        $q->where('is_active', true); 
                    }]);
                }])
                ->orderBy('sort_order')
                ->get();
        });
        $this->info('✓ Categories cached');
        
        // Popular products
        Cache::forget('home.popular_products');
        Cache::remember('home.popular_products', 300, function () {
            return PopularProduct::where('is_active', true)
                ->with(['product' => function($query) {
                    $query->where('is_active', true)
                        ->with('category');
                }])
                ->orderBy('sort_order')
                ->get();
        });
        $this->info('✓ Popular products cached');
        
        // Bundles
        Cache::forget('home.bundles');
        Cache::remember('home.bundles', 60, function () {
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
        $this->info('✓ Bundles cached');
        
        // Why Choose Us
        Cache::forget('home.why_choose_us');
        Cache::remember('home.why_choose_us', 3600, function () {
            return WhyChooseUs::where('is_active', true)
                ->orderBy('sort_order')
                ->get();
        });
        $this->info('✓ Why Choose Us cached');
        
        // Blogs
        Cache::forget('home.blogs');
        Cache::remember('home.blogs', 300, function () {
            return Blog::active()
                ->published()
                ->ordered()
                ->take(6)
                ->get();
        });
        $this->info('✓ Blogs cached');
        
        // Testimonials
        Cache::forget('home.testimonials');
        Cache::remember('home.testimonials', 600, function () {
            return Testimonial::active()
                ->ordered()
                ->take(12)
                ->get();
        });
        $this->info('✓ Testimonials cached');
        
        $this->info('Cache warming completed successfully!');
        
        return Command::SUCCESS;
    }
}