<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductVariant;

class ProductVariantsSeeder extends Seeder
{
    public function run()
    {
        $products = Product::all();

        foreach ($products as $product) {
            // Create variants based on product category
            $variants = $this->getVariantsForProduct($product);
            
            foreach ($variants as $variant) {
                ProductVariant::create([
                    'product_id' => $product->id,
                    'variant_type' => $variant['type'],
                    'variant_value' => $variant['value'],
                    'sku' => $product->slug . '-' . strtoupper(substr($variant['value'], 0, 3)) . '-' . rand(100, 999),
                    'price' => $variant['price'],
                    'stock_quantity' => $variant['stock'],
                    'stock' => $variant['stock'], // For compatibility
                    'is_active' => true,
                    'sort_order' => $variant['order'],
                    'weight' => $variant['weight'] ?? null,
                    'dimensions' => $variant['dimensions'] ?? null,
                    'attributes_json' => [],
                ]);
            }
        }
    }

    private function getVariantsForProduct($product)
    {
        $basePrice = $product->base_price;
        $categoryName = $product->category->name ?? '';

        // Different variant types based on product categories
        if (str_contains(strtolower($categoryName), 'cake')) {
            return [
                [
                    'type' => 'Size',
                    'value' => '1 Pound',
                    'price' => $basePrice * 0.8,
                    'stock' => rand(5, 20),
                    'order' => 1,
                    'weight' => '1 lb',
                    'dimensions' => '6 inch diameter'
                ],
                [
                    'type' => 'Size', 
                    'value' => '2 Pound',
                    'price' => $basePrice,
                    'stock' => rand(3, 15),
                    'order' => 2,
                    'weight' => '2 lb',
                    'dimensions' => '8 inch diameter'
                ],
                [
                    'type' => 'Size',
                    'value' => '3 Pound',
                    'price' => $basePrice * 1.4,
                    'stock' => rand(2, 10),
                    'order' => 3,
                    'weight' => '3 lb', 
                    'dimensions' => '10 inch diameter'
                ]
            ];
        }

        if (str_contains(strtolower($categoryName), 'pastry') || str_contains(strtolower($categoryName), 'danish')) {
            return [
                [
                    'type' => 'Pack Size',
                    'value' => 'Single',
                    'price' => $basePrice * 0.7,
                    'stock' => rand(10, 30),
                    'order' => 1,
                    'weight' => '100g'
                ],
                [
                    'type' => 'Pack Size',
                    'value' => 'Pack of 4',
                    'price' => $basePrice,
                    'stock' => rand(5, 20),
                    'order' => 2,
                    'weight' => '400g'
                ],
                [
                    'type' => 'Pack Size',
                    'value' => 'Pack of 6',
                    'price' => $basePrice * 1.3,
                    'stock' => rand(3, 15),
                    'order' => 3,
                    'weight' => '600g'
                ]
            ];
        }

        if (str_contains(strtolower($categoryName), 'cookie') || str_contains(strtolower($categoryName), 'biscuit')) {
            return [
                [
                    'type' => 'Quantity',
                    'value' => '6 Pieces',
                    'price' => $basePrice * 0.6,
                    'stock' => rand(15, 40),
                    'order' => 1,
                    'weight' => '200g'
                ],
                [
                    'type' => 'Quantity',
                    'value' => '12 Pieces',
                    'price' => $basePrice,
                    'stock' => rand(8, 25),
                    'order' => 2,
                    'weight' => '400g'
                ],
                [
                    'type' => 'Quantity',
                    'value' => '24 Pieces',
                    'price' => $basePrice * 1.7,
                    'stock' => rand(4, 15),
                    'order' => 3,
                    'weight' => '800g'
                ]
            ];
        }

        if (str_contains(strtolower($categoryName), 'bread') || str_contains(strtolower($categoryName), 'loaf')) {
            return [
                [
                    'type' => 'Size',
                    'value' => 'Small Loaf',
                    'price' => $basePrice * 0.7,
                    'stock' => rand(8, 25),
                    'order' => 1,
                    'weight' => '400g',
                    'dimensions' => '20x10x8 cm'
                ],
                [
                    'type' => 'Size',
                    'value' => 'Regular Loaf',
                    'price' => $basePrice,
                    'stock' => rand(5, 20),
                    'order' => 2,
                    'weight' => '600g',
                    'dimensions' => '25x12x10 cm'
                ],
                [
                    'type' => 'Size',
                    'value' => 'Large Loaf',
                    'price' => $basePrice * 1.3,
                    'stock' => rand(3, 12),
                    'order' => 3,
                    'weight' => '800g',
                    'dimensions' => '30x14x12 cm'
                ]
            ];
        }

        if (str_contains(strtolower($categoryName), 'muffin') || str_contains(strtolower($categoryName), 'cupcake')) {
            return [
                [
                    'type' => 'Pack Size',
                    'value' => 'Single',
                    'price' => $basePrice * 0.5,
                    'stock' => rand(20, 50),
                    'order' => 1,
                    'weight' => '80g'
                ],
                [
                    'type' => 'Pack Size',
                    'value' => 'Pack of 6',
                    'price' => $basePrice,
                    'stock' => rand(10, 30),
                    'order' => 2,
                    'weight' => '480g'
                ],
                [
                    'type' => 'Pack Size',
                    'value' => 'Pack of 12',
                    'price' => $basePrice * 1.8,
                    'stock' => rand(5, 18),
                    'order' => 3,
                    'weight' => '960g'
                ]
            ];
        }

        // Default variants for other products
        return [
            [
                'type' => 'Size',
                'value' => 'Regular',
                'price' => $basePrice,
                'stock' => rand(10, 30),
                'order' => 1,
                'weight' => '250g'
            ],
            [
                'type' => 'Size',
                'value' => 'Large',
                'price' => $basePrice * 1.3,
                'stock' => rand(5, 20),
                'order' => 2,
                'weight' => '400g'
            ]
        ];
    }
}