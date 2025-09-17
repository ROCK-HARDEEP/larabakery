<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Fresh Breads',
                'slug' => 'fresh-breads',
                'description' => 'Artisan breads baked daily with premium ingredients - sourdough, whole wheat, and specialty loaves',
                'position' => 1,
                'is_active' => true,
                'image' => 'breads-category.jpg',
            ],
            [
                'name' => 'Celebration Cakes',
                'slug' => 'celebration-cakes',
                'description' => 'Stunning cakes for birthdays, weddings, and special occasions - custom designs and flavors',
                'position' => 2,
                'is_active' => true,
                'image' => 'cakes-category.jpg',
            ],
            [
                'name' => 'Gourmet Pastries',
                'slug' => 'gourmet-pastries',
                'description' => 'French-inspired pastries including croissants, danishes, éclairs, and tarts',
                'position' => 3,
                'is_active' => true,
                'image' => 'pastries-category.jpg',
            ],
            [
                'name' => 'Artisan Cookies',
                'slug' => 'artisan-cookies',
                'description' => 'Handcrafted cookies in unique flavors - perfect for gifting and special treats',
                'position' => 4,
                'is_active' => true,
                'image' => 'cookies-category.jpg',
            ],
            [
                'name' => 'Sweet Treats',
                'slug' => 'sweet-treats',
                'description' => 'Indulgent desserts including muffins, cupcakes, brownies, and specialty sweets',
                'position' => 5,
                'is_active' => true,
                'image' => 'sweets-category.jpg',
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }

        // Add some subcategories for better organization
        $subcategories = [
            [
                'name' => 'Sourdough Breads',
                'slug' => 'sourdough-breads',
                'parent_id' => Category::where('slug', 'fresh-breads')->first()->id,
                'description' => 'Traditional sourdough breads with natural fermentation',
                'position' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Whole Grain Breads',
                'slug' => 'whole-grain-breads',
                'parent_id' => Category::where('slug', 'fresh-breads')->first()->id,
                'description' => 'Healthy whole grain and multigrain breads',
                'position' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Chocolate Cakes',
                'slug' => 'chocolate-cakes',
                'parent_id' => Category::where('slug', 'celebration-cakes')->first()->id,
                'description' => 'Rich and decadent chocolate cakes',
                'position' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Fruit Cakes',
                'slug' => 'fruit-cakes',
                'parent_id' => Category::where('slug', 'celebration-cakes')->first()->id,
                'description' => 'Traditional fruit cakes with dried fruits and nuts',
                'position' => 2,
                'is_active' => true,
            ],
        ];

        foreach ($subcategories as $subcategory) {
            Category::create($subcategory);
        }
    }
}