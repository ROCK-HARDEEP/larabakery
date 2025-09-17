<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\HeroSlide;
use App\Models\Category;
use App\Models\Product;

class HeroSlideSeeder extends Seeder
{
    public function run(): void
    {
        // Get categories and products for routing
        $freshBreads = Category::where('slug', 'fresh-breads')->first();
        $celebrationCakes = Category::where('slug', 'celebration-cakes')->first();
        $gourmetPastries = Category::where('slug', 'gourmet-pastries')->first();
        $artisanCookies = Category::where('slug', 'artisan-cookies')->first();
        $sweetTreats = Category::where('slug', 'sweet-treats')->first();

        $chocolateCake = Product::where('slug', 'chocolate-truffle-celebration-cake')->first();
        $sourdoughBread = Product::where('slug', 'artisan-sourdough-bread')->first();
        $croissant = Product::where('slug', 'butter-croissant')->first();

        $slides = [
            [
                'title' => 'Fresh Artisan Breads',
                'subtitle' => 'Baked daily with love and tradition',
                'image_path' => 'hero/fresh-breads-hero.jpg',
                'button_label' => 'Shop Breads',
                'category_id' => $freshBreads ? $freshBreads->id : null,
                'product_id' => null,
                'title_color' => '#ffffff',
                'subtitle_color' => '#f3f4f6',
                'title_size' => 64,
                'subtitle_size' => 24,
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'title' => 'Celebration Cakes',
                'subtitle' => 'Make every moment special with our custom cakes',
                'image_path' => 'hero/celebration-cakes-hero.jpg',
                'button_label' => 'Order Cake',
                'category_id' => $celebrationCakes ? $celebrationCakes->id : null,
                'product_id' => $chocolateCake ? $chocolateCake->id : null,
                'title_color' => '#ffffff',
                'subtitle_color' => '#fef3c7',
                'title_size' => 60,
                'subtitle_size' => 22,
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'title' => 'Gourmet Pastries',
                'subtitle' => 'French-inspired delicacies for the connoisseur',
                'image_path' => 'hero/gourmet-pastries-hero.jpg',
                'button_label' => 'Explore Pastries',
                'category_id' => $gourmetPastries ? $gourmetPastries->id : null,
                'product_id' => $croissant ? $croissant->id : null,
                'title_color' => '#ffffff',
                'subtitle_color' => '#e0e7ff',
                'title_size' => 58,
                'subtitle_size' => 20,
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'title' => 'Artisan Cookies',
                'subtitle' => 'Handcrafted with premium ingredients',
                'image_path' => 'hero/artisan-cookies-hero.jpg',
                'button_label' => 'Shop Cookies',
                'category_id' => $artisanCookies ? $artisanCookies->id : null,
                'product_id' => null,
                'title_color' => '#ffffff',
                'subtitle_color' => '#fef3c7',
                'title_size' => 56,
                'subtitle_size' => 20,
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'title' => 'Sweet Treats',
                'subtitle' => 'Indulge in our delightful desserts',
                'image_path' => 'hero/sweet-treats-hero.jpg',
                'button_label' => 'Discover Treats',
                'category_id' => $sweetTreats ? $sweetTreats->id : null,
                'product_id' => null,
                'title_color' => '#ffffff',
                'subtitle_color' => '#fce7f3',
                'title_size' => 54,
                'subtitle_size' => 20,
                'is_active' => true,
                'sort_order' => 5,
            ],
        ];

        foreach ($slides as $slide) {
            HeroSlide::create($slide);
        }

        $this->command->info('5 hero slides created successfully with product routing!');
    }
}
