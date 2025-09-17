<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Category;

class UpdateProductDetailsSeeder extends Seeder
{
    public function run()
    {
        // Get all products and update them with detailed information
        $products = Product::all();
        
        foreach ($products as $product) {
            // Update product details based on category
            $this->updateProductDetails($product);
            
            // Create variants for the product
            $this->createProductVariants($product);
        }
    }
    
    private function updateProductDetails($product)
    {
        $categoryName = strtolower($product->category->name ?? '');
        
        // Common nutritional info template
        $nutritionalInfo = [
            'calories' => '250-350 kcal',
            'protein' => '5-8g',
            'carbohydrates' => '35-45g',
            'fat' => '10-15g',
            'fiber' => '2-3g',
            'sugar' => '15-25g',
            'sodium' => '200-300mg'
        ];
        
        // Update based on category
        if (str_contains($categoryName, 'bread') || str_contains($categoryName, 'loaf')) {
            $product->update([
                'full_description' => 'Our artisanal ' . $product->name . ' is freshly baked daily using traditional methods and premium ingredients. Each loaf is carefully crafted to achieve the perfect texture and flavor, with a golden crust and soft, fluffy interior.',
                'ingredients' => 'Wheat flour, water, yeast, salt, sugar, butter, milk powder, eggs, improver (E300, E472e), preservative (E282)',
                'nutritional_info' => array_merge($nutritionalInfo, ['calories' => '280 kcal']),
                'allergen_info' => 'Contains: Wheat (gluten), milk, eggs. May contain traces of nuts, sesame seeds, and soy.',
                'storage_instructions' => 'Store in a cool, dry place. Once opened, keep in an airtight container. For best freshness, consume within 3-4 days.',
                'shelf_life' => '5 days from manufacture'
            ]);
        } elseif (str_contains($categoryName, 'cake')) {
            $product->update([
                'full_description' => 'Indulge in our delicious ' . $product->name . ', made with love and the finest ingredients. Each cake is a perfect blend of moistness and flavor, decorated beautifully for any occasion.',
                'ingredients' => 'Refined wheat flour, sugar, eggs, butter, milk, baking powder, vanilla extract, cocoa powder (for chocolate variants), fresh cream, food coloring (E102, E110, E133)',
                'nutritional_info' => array_merge($nutritionalInfo, ['calories' => '380 kcal', 'sugar' => '30-35g']),
                'allergen_info' => 'Contains: Wheat (gluten), milk, eggs, soy. May contain traces of tree nuts and peanuts.',
                'storage_instructions' => 'Store in refrigerator at 2-8°C. Keep away from strong odors. Best consumed within 48 hours of purchase.',
                'shelf_life' => '3 days when refrigerated'
            ]);
        } elseif (str_contains($categoryName, 'pastry') || str_contains($categoryName, 'pastries')) {
            $product->update([
                'full_description' => 'Experience the flaky, buttery goodness of our ' . $product->name . '. Each pastry is handcrafted with layers of delicate dough and premium fillings for an unforgettable taste.',
                'ingredients' => 'Wheat flour, butter, water, salt, sugar, eggs, milk, yeast, filling ingredients vary by product',
                'nutritional_info' => array_merge($nutritionalInfo, ['calories' => '320 kcal', 'fat' => '18-22g']),
                'allergen_info' => 'Contains: Wheat (gluten), milk, eggs. May contain nuts, sesame seeds depending on variety.',
                'storage_instructions' => 'Best enjoyed fresh. Store in an airtight container at room temperature for up to 2 days.',
                'shelf_life' => '2 days from manufacture'
            ]);
        } elseif (str_contains($categoryName, 'cookie') || str_contains($categoryName, 'biscuit')) {
            $product->update([
                'full_description' => 'Enjoy our crispy and delicious ' . $product->name . ', perfect for tea time or as a sweet snack. Baked to golden perfection with a satisfying crunch.',
                'ingredients' => 'Wheat flour, sugar, butter, eggs, baking soda, salt, chocolate chips/nuts (varies by type), vanilla essence',
                'nutritional_info' => array_merge($nutritionalInfo, ['calories' => '150 kcal per serving', 'sugar' => '10-15g']),
                'allergen_info' => 'Contains: Wheat (gluten), milk, eggs. May contain nuts, chocolate, and soy.',
                'storage_instructions' => 'Store in an airtight container in a cool, dry place. Keep away from direct sunlight.',
                'shelf_life' => '15 days from manufacture'
            ]);
        } else {
            $product->update([
                'full_description' => 'Discover the delightful taste of our ' . $product->name . ', made with premium ingredients and traditional baking methods for an authentic experience.',
                'ingredients' => 'Premium wheat flour, sugar, butter, eggs, milk, natural flavoring, preservatives (as per food safety standards)',
                'nutritional_info' => $nutritionalInfo,
                'allergen_info' => 'Contains: Wheat (gluten), milk, eggs. Please check packaging for specific allergen information.',
                'storage_instructions' => 'Store as per package instructions. Keep in a cool, dry place.',
                'shelf_life' => 'Check package for best before date'
            ]);
        }
    }
    
    private function createProductVariants($product)
    {
        $categoryName = strtolower($product->category->name ?? '');
        
        // Skip if variants already exist
        if ($product->variants()->count() > 0) {
            return;
        }
        
        // Create variants based on product type
        if (str_contains($categoryName, 'bread') || str_contains($categoryName, 'loaf')) {
            // Size variants for bread
            $variants = [
                ['type' => 'size', 'value' => 'Small (250g)', 'price_offset' => -20, 'stock' => 50],
                ['type' => 'size', 'value' => 'Regular (500g)', 'price_offset' => 0, 'stock' => 100],
                ['type' => 'size', 'value' => 'Large (750g)', 'price_offset' => 30, 'stock' => 30],
            ];
        } elseif (str_contains($categoryName, 'cake')) {
            // Flavor and size variants for cakes
            $variants = [
                ['type' => 'flavor', 'value' => 'Vanilla', 'price_offset' => 0, 'stock' => 20],
                ['type' => 'flavor', 'value' => 'Chocolate', 'price_offset' => 50, 'stock' => 25],
                ['type' => 'flavor', 'value' => 'Strawberry', 'price_offset' => 75, 'stock' => 15],
                ['type' => 'flavor', 'value' => 'Butterscotch', 'price_offset' => 60, 'stock' => 18],
            ];
        } elseif (str_contains($categoryName, 'pastry')) {
            // Flavor variants for pastries
            $variants = [
                ['type' => 'flavor', 'value' => 'Classic', 'price_offset' => 0, 'stock' => 40],
                ['type' => 'flavor', 'value' => 'Chocolate', 'price_offset' => 10, 'stock' => 35],
                ['type' => 'flavor', 'value' => 'Fruit', 'price_offset' => 15, 'stock' => 25],
            ];
        } elseif (str_contains($categoryName, 'cookie') || str_contains($categoryName, 'biscuit')) {
            // Pack size variants for cookies
            $variants = [
                ['type' => 'pack', 'value' => '100g Pack', 'price_offset' => -30, 'stock' => 100],
                ['type' => 'pack', 'value' => '250g Pack', 'price_offset' => 0, 'stock' => 80],
                ['type' => 'pack', 'value' => '500g Pack', 'price_offset' => 50, 'stock' => 50],
                ['type' => 'pack', 'value' => 'Gift Box (750g)', 'price_offset' => 100, 'stock' => 20],
            ];
        } else {
            // Default variants
            $variants = [
                ['type' => 'size', 'value' => 'Standard', 'price_offset' => 0, 'stock' => 50],
            ];
        }
        
        // Create variants for the product
        foreach ($variants as $index => $variantData) {
            $sku = strtoupper(substr($product->slug, 0, 3)) . '-' . 
                   strtoupper(substr($variantData['type'], 0, 2)) . '-' . 
                   str_pad($product->id, 3, '0', STR_PAD_LEFT) . '-' . 
                   str_pad($index + 1, 2, '0', STR_PAD_LEFT);
            
            ProductVariant::create([
                'product_id' => $product->id,
                'sku' => $sku,
                'variant_type' => $variantData['type'],
                'variant_value' => $variantData['value'],
                'price' => $product->base_price + $variantData['price_offset'],
                'compare_at_price' => $product->base_price + $variantData['price_offset'] + rand(20, 50),
                'stock_quantity' => $variantData['stock'],
                'stock' => $variantData['stock'],
                'weight' => $this->getWeight($variantData['value']),
                'is_active' => true,
                'sort_order' => $index
            ]);
        }
    }
    
    private function getWeight($variantValue)
    {
        // Extract weight from variant value if it contains weight info
        if (preg_match('/(\d+)g/', $variantValue, $matches)) {
            return $matches[1] . 'g';
        } elseif (preg_match('/(\d+)kg/', $variantValue, $matches)) {
            return $matches[1] . 'kg';
        }
        
        return null;
    }
}