<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;

class SampleProductSeeder extends Seeder
{
    public function run()
    {
        $categories = Category::where('is_active', true)->pluck('id')->toArray();
        
        if (empty($categories)) {
            $category = Category::create([
                'name' => 'Cakes',
                'slug' => 'cakes',
                'is_active' => true,
                'position' => 1
            ]);
            $categories = [$category->id];
        }

        $products = [
            [
                'name' => 'Chocolate Cake',
                'slug' => 'chocolate-cake',
                'base_price' => 450,
                'stock' => 25,
                'description' => 'Rich and moist chocolate cake with chocolate ganache',
                'images_path' => ['products/chocolate-cake.jpg'],
                'is_active' => true
            ],
            [
                'name' => 'Red Velvet Cake',
                'slug' => 'red-velvet-cake',
                'base_price' => 550,
                'stock' => 20,
                'description' => 'Classic red velvet cake with cream cheese frosting',
                'images_path' => ['products/red-velvet.jpg'],
                'is_active' => true
            ],
            [
                'name' => 'Croissant',
                'slug' => 'croissant',
                'base_price' => 75,
                'stock' => 50,
                'description' => 'Buttery, flaky French croissant',
                'images_path' => ['products/croissant.jpg'],
                'is_active' => true
            ],
            [
                'name' => 'Blueberry Muffin',
                'slug' => 'blueberry-muffin',
                'base_price' => 60,
                'stock' => 40,
                'description' => 'Fresh blueberry muffin with vanilla glaze',
                'images_path' => ['products/blueberry-muffin.jpg'],
                'is_active' => true
            ],
            [
                'name' => 'Sourdough Bread',
                'slug' => 'sourdough-bread',
                'base_price' => 120,
                'stock' => 30,
                'description' => 'Artisanal sourdough bread with perfect crust',
                'images_path' => ['products/sourdough.jpg'],
                'is_active' => true
            ],
            [
                'name' => 'Cinnamon Roll',
                'slug' => 'cinnamon-roll',
                'base_price' => 85,
                'stock' => 35,
                'description' => 'Sweet cinnamon roll with cream cheese icing',
                'images_path' => ['products/cinnamon-roll.jpg'],
                'is_active' => true
            ]
        ];

        foreach ($products as $product) {
            $product['category_id'] = $categories[array_rand($categories)];
            $product['tax_rate'] = 5;
            $product['hsn_code'] = '19059090';
            
            Product::updateOrCreate(
                ['slug' => $product['slug']],
                $product
            );
        }

        $this->command->info('Sample products created successfully!');
    }
}