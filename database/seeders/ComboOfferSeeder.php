<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ComboOffer;
use App\Models\Product;
use Illuminate\Support\Str;

class ComboOfferSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get some products for combo offers
        $products = Product::where('is_active', true)->take(20)->get();
        
        if ($products->count() < 4) {
            $this->command->warn('Not enough products to create combo offers. Please seed products first.');
            return;
        }

        $combos = [
            [
                'name' => 'Birthday Party Combo',
                'description' => 'Perfect for birthday celebrations! Includes a delicious cake, pastries, and cookies for your special day.',
                'original_price' => 1499.00,
                'combo_price' => 999.00,
                'max_quantity' => 5,
                'products' => [
                    ['quantity' => 1, 'unit_price' => 699],  // Birthday cake
                    ['quantity' => 6, 'unit_price' => 50],   // Pastries
                    ['quantity' => 12, 'unit_price' => 25],  // Cookies
                    ['quantity' => 1, 'unit_price' => 200],  // Special bread
                ]
            ],
            [
                'name' => 'Weekend Breakfast Bundle',
                'description' => 'Start your weekend right with fresh breads, croissants, muffins and spreads for the whole family.',
                'original_price' => 799.00,
                'combo_price' => 549.00,
                'max_quantity' => 10,
                'products' => [
                    ['quantity' => 2, 'unit_price' => 150],  // Bread loaves
                    ['quantity' => 4, 'unit_price' => 80],   // Croissants
                    ['quantity' => 6, 'unit_price' => 60],   // Muffins
                    ['quantity' => 2, 'unit_price' => 120],  // Jam/spreads
                ]
            ],
            [
                'name' => 'Office Meeting Pack',
                'description' => 'Ideal for office meetings and corporate events. Assorted pastries, sandwiches, and beverages.',
                'original_price' => 1899.00,
                'combo_price' => 1399.00,
                'max_quantity' => 3,
                'products' => [
                    ['quantity' => 10, 'unit_price' => 80],  // Sandwiches
                    ['quantity' => 10, 'unit_price' => 60],  // Pastries
                    ['quantity' => 5, 'unit_price' => 40],   // Donuts
                    ['quantity' => 20, 'unit_price' => 15],  // Cookies
                ]
            ],
            [
                'name' => 'Tea Time Special',
                'description' => 'Perfect accompaniment for your evening tea. Includes biscuits, cookies, and savory snacks.',
                'original_price' => 599.00,
                'combo_price' => 399.00,
                'max_quantity' => 15,
                'products' => [
                    ['quantity' => 2, 'unit_price' => 120],  // Biscuit packs
                    ['quantity' => 2, 'unit_price' => 100],  // Cookie boxes
                    ['quantity' => 3, 'unit_price' => 80],   // Savory items
                ]
            ],
            [
                'name' => 'Festive Celebration Box',
                'description' => 'Celebrate festivals with this special combo of traditional sweets, cakes, and bakery delights.',
                'original_price' => 2499.00,
                'combo_price' => 1799.00,
                'max_quantity' => 5,
                'products' => [
                    ['quantity' => 1, 'unit_price' => 899],  // Premium cake
                    ['quantity' => 1, 'unit_price' => 400],  // Sweet box
                    ['quantity' => 8, 'unit_price' => 75],   // Pastries
                    ['quantity' => 2, 'unit_price' => 250],  // Special breads
                    ['quantity' => 1, 'unit_price' => 100],  // Cookies pack
                ]
            ],
            [
                'name' => 'Healthy Morning Combo',
                'description' => 'Start your day healthy with whole wheat breads, multigrain cookies, and sugar-free options.',
                'original_price' => 899.00,
                'combo_price' => 649.00,
                'max_quantity' => 8,
                'products' => [
                    ['quantity' => 2, 'unit_price' => 180],  // Whole wheat bread
                    ['quantity' => 2, 'unit_price' => 150],  // Multigrain items
                    ['quantity' => 3, 'unit_price' => 90],   // Sugar-free cookies
                ]
            ],
            [
                'name' => 'Kids Party Package',
                'description' => 'Make your kids party special with colorful cupcakes, cookies, and fun-shaped pastries.',
                'original_price' => 1299.00,
                'combo_price' => 899.00,
                'max_quantity' => 6,
                'products' => [
                    ['quantity' => 12, 'unit_price' => 40],  // Cupcakes
                    ['quantity' => 20, 'unit_price' => 20],  // Cookies
                    ['quantity' => 8, 'unit_price' => 50],   // Fun pastries
                    ['quantity' => 1, 'unit_price' => 19],   // Party cake slice
                ]
            ],
            [
                'name' => 'Romantic Date Night',
                'description' => 'Set the mood with heart-shaped pastries, chocolate cake, and premium cookies for two.',
                'original_price' => 999.00,
                'combo_price' => 749.00,
                'max_quantity' => 10,
                'products' => [
                    ['quantity' => 2, 'unit_price' => 200],  // Cake slices
                    ['quantity' => 4, 'unit_price' => 100],  // Heart pastries
                    ['quantity' => 6, 'unit_price' => 50],   // Premium cookies
                ]
            ]
        ];

        foreach ($combos as $index => $comboData) {
            $combo = ComboOffer::create([
                'name' => $comboData['name'],
                'slug' => Str::slug($comboData['name']),
                'description' => $comboData['description'],
                'original_price' => $comboData['original_price'],
                'combo_price' => $comboData['combo_price'],
                'discount_percentage' => round((($comboData['original_price'] - $comboData['combo_price']) / $comboData['original_price']) * 100, 2),
                'max_quantity' => $comboData['max_quantity'],
                'sold_quantity' => rand(5, 50),
                'is_active' => true,
                'display_order' => $index,
                'starts_at' => now()->subDays(rand(1, 10)),
                'ends_at' => now()->addDays(rand(15, 60)),
            ]);

            // Attach products to combo
            foreach ($comboData['products'] as $i => $productData) {
                if (isset($products[$i])) {
                    $combo->products()->attach($products[$i]->id, [
                        'quantity' => $productData['quantity'],
                        'unit_price' => $productData['unit_price'],
                    ]);
                }
            }
        }

        $this->command->info('Combo offers seeded successfully!');
    }
}