<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // Check if demo user exists, if not create one
        $demoUser = User::where('email', 'demo@bakery.com')->first();
        if (!$demoUser) {
            $demoUser = User::create([
                'name' => 'Demo User',
                'email' => 'demo@bakery.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]);
            $this->command->info('Demo user created: demo@bakery.com / password');
        }

        // Create categories if they don't exist
        $categories = [
            [
                'name' => 'Cakes',
                'slug' => 'cakes',
                'description' => 'Delicious cakes for all occasions',
                'is_active' => true,
                'position' => 1,
            ],
            [
                'name' => 'Pastries',
                'slug' => 'pastries',
                'description' => 'Fresh pastries and desserts',
                'is_active' => true,
                'position' => 2,
            ],
            [
                'name' => 'Breads',
                'slug' => 'breads',
                'description' => 'Artisanal breads and rolls',
                'is_active' => true,
                'position' => 3,
            ],
            [
                'name' => 'Cookies',
                'slug' => 'cookies',
                'description' => 'Homemade cookies and biscuits',
                'is_active' => true,
                'position' => 4,
            ],
            [
                'name' => 'Cupcakes',
                'slug' => 'cupcakes',
                'description' => 'Beautiful and tasty cupcakes',
                'is_active' => true,
                'position' => 5,
            ],
            [
                'name' => 'Special Occasions',
                'slug' => 'special-occasions',
                'description' => 'Custom cakes for celebrations',
                'is_active' => true,
                'position' => 6,
            ],
        ];

        foreach ($categories as $categoryData) {
            Category::firstOrCreate(
                ['slug' => $categoryData['slug']],
                $categoryData
            );
        }

        $this->command->info('Categories checked/created');

        // Get categories for products
        $cakesCategory = Category::where('slug', 'cakes')->first();
        $pastriesCategory = Category::where('slug', 'pastries')->first();
        $breadsCategory = Category::where('slug', 'breads')->first();
        $cookiesCategory = Category::where('slug', 'cookies')->first();
        $cupcakesCategory = Category::where('slug', 'cupcakes')->first();
        $specialCategory = Category::where('slug', 'special-occasions')->first();

        // Create products if they don't exist
        $products = [
            [
                'category_id' => $cakesCategory->id,
                'name' => 'Chocolate Truffle Cake',
                'slug' => 'chocolate-truffle-cake',
                'description' => 'Our signature chocolate truffle cake features layers of moist chocolate cake filled with rich chocolate truffle ganache. Topped with chocolate shavings and fresh berries.',
                'base_price' => 899.00,
                'hsn_code' => '1905',
                'tax_rate' => 18.00,
                'is_active' => true,
                'meta' => json_encode(['featured' => true, 'badge' => 'Best Seller']),
            ],
            [
                'category_id' => $cakesCategory->id,
                'name' => 'Vanilla Buttercream Cake',
                'slug' => 'vanilla-buttercream-cake',
                'description' => 'A timeless classic featuring fluffy vanilla cake layers with smooth vanilla buttercream frosting. Perfect for birthdays and celebrations.',
                'base_price' => 699.00,
                'hsn_code' => '1905',
                'tax_rate' => 18.00,
                'is_active' => true,
                'meta' => json_encode(['featured' => true, 'badge' => 'Popular']),
            ],
            [
                'category_id' => $pastriesCategory->id,
                'name' => 'Chocolate Éclair',
                'slug' => 'chocolate-eclair',
                'description' => 'Traditional French éclair made with choux pastry, filled with rich chocolate cream and topped with chocolate glaze.',
                'base_price' => 120.00,
                'hsn_code' => '1905',
                'tax_rate' => 18.00,
                'is_active' => true,
                'meta' => json_encode(['featured' => false]),
            ],
            [
                'category_id' => $breadsCategory->id,
                'name' => 'Sourdough Bread',
                'slug' => 'sourdough-bread',
                'description' => 'Traditional sourdough bread made with our 100-year-old starter. Features a crispy crust and tangy flavor.',
                'base_price' => 150.00,
                'hsn_code' => '1905',
                'tax_rate' => 18.00,
                'is_active' => true,
                'meta' => json_encode(['featured' => true, 'badge' => 'Artisanal']),
            ],
            [
                'category_id' => $cupcakesCategory->id,
                'name' => 'Red Velvet Cupcake',
                'slug' => 'red-velvet-cupcake',
                'description' => 'Classic red velvet cupcake with a hint of cocoa, topped with tangy cream cheese frosting.',
                'base_price' => 60.00,
                'hsn_code' => '1905',
                'tax_rate' => 18.00,
                'is_active' => true,
                'meta' => json_encode(['featured' => true, 'badge' => 'Customer Favorite']),
            ],
        ];

        foreach ($products as $productData) {
            Product::firstOrCreate(
                ['slug' => $productData['slug']],
                $productData
            );
        }

        $this->command->info('Products checked/created');
        $this->command->info('Demo data seeding completed successfully!');
    }
}
