<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductVariant;

class ProductVariantsAndRatingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = Product::all();

        foreach ($products as $product) {
            // Initialize rating and review_count to 0 if not set (admin can set manually)
            if (is_null($product->rating)) {
                $product->rating = 0;
            }
            if (is_null($product->review_count)) {
                $product->review_count = 0;
            }
            $product->save();

            // Delete existing variants to start fresh
            $product->variants()->delete();

            // Determine variant type based on product category
            $categoryName = $product->category->name ?? '';
            $variantData = $this->getVariantDataForProduct($product, $categoryName);

            foreach ($variantData as $variant) {
                ProductVariant::create([
                    'product_id' => $product->id,
                    'variant_type' => $variant['type'],
                    'variant_value' => $variant['value'],
                    'sku' => $this->generateSku($product, $variant),
                    'price' => $variant['price'],
                    'compare_at_price' => $variant['compare_price'],
                    'stock_quantity' => $variant['stock'],
                    'stock' => $variant['stock'],
                    'is_active' => true,
                    'sort_order' => $variant['sort_order'] ?? 0,
                    'attributes_json' => $variant['attributes'] ?? [],
                ]);
            }
        }
    }

    private function getVariantDataForProduct($product, $categoryName): array
    {
        $basePrice = rand(50, 500);

        // Category-specific variants with proper hierarchical structure
        if (stripos($categoryName, 'cake') !== false) {
            return [
                // Hierarchical: Flavor - Quantity structure
                ['type' => 'Flavor', 'value' => 'Chocolate - 500g', 'price' => $basePrice, 'compare_price' => $basePrice * 1.2, 'stock' => rand(10, 30), 'sort_order' => 1, 'attributes' => ['main_variant' => 'Chocolate', 'quantity_variant' => '500g']],
                ['type' => 'Flavor', 'value' => 'Chocolate - 1kg', 'price' => $basePrice * 1.8, 'compare_price' => $basePrice * 2.2, 'stock' => rand(8, 25), 'sort_order' => 2, 'attributes' => ['main_variant' => 'Chocolate', 'quantity_variant' => '1kg']],
                ['type' => 'Flavor', 'value' => 'Chocolate - 2kg', 'price' => $basePrice * 3.5, 'compare_price' => $basePrice * 4, 'stock' => rand(5, 20), 'sort_order' => 3, 'attributes' => ['main_variant' => 'Chocolate', 'quantity_variant' => '2kg']],

                ['type' => 'Flavor', 'value' => 'Vanilla - 500g', 'price' => $basePrice, 'compare_price' => $basePrice * 1.2, 'stock' => rand(10, 30), 'sort_order' => 4, 'attributes' => ['main_variant' => 'Vanilla', 'quantity_variant' => '500g']],
                ['type' => 'Flavor', 'value' => 'Vanilla - 1kg', 'price' => $basePrice * 1.8, 'compare_price' => $basePrice * 2.2, 'stock' => rand(8, 25), 'sort_order' => 5, 'attributes' => ['main_variant' => 'Vanilla', 'quantity_variant' => '1kg']],
                ['type' => 'Flavor', 'value' => 'Vanilla - 2kg', 'price' => $basePrice * 3.5, 'compare_price' => $basePrice * 4, 'stock' => rand(5, 20), 'sort_order' => 6, 'attributes' => ['main_variant' => 'Vanilla', 'quantity_variant' => '2kg']],

                ['type' => 'Flavor', 'value' => 'Strawberry - 500g', 'price' => $basePrice * 1.15, 'compare_price' => $basePrice * 1.35, 'stock' => rand(10, 25), 'sort_order' => 7, 'attributes' => ['main_variant' => 'Strawberry', 'quantity_variant' => '500g']],
                ['type' => 'Flavor', 'value' => 'Strawberry - 1kg', 'price' => $basePrice * 2, 'compare_price' => $basePrice * 2.4, 'stock' => rand(8, 20), 'sort_order' => 8, 'attributes' => ['main_variant' => 'Strawberry', 'quantity_variant' => '1kg']],
                ['type' => 'Flavor', 'value' => 'Strawberry - 2kg', 'price' => $basePrice * 3.8, 'compare_price' => $basePrice * 4.3, 'stock' => rand(5, 15), 'sort_order' => 9, 'attributes' => ['main_variant' => 'Strawberry', 'quantity_variant' => '2kg']],
            ];
        } elseif (stripos($categoryName, 'bread') !== false || stripos($categoryName, 'loaf') !== false) {
            return [
                // Only size variants (no quantity variants to avoid duplication)
                ['type' => 'Size', 'value' => 'Regular', 'price' => $basePrice, 'compare_price' => $basePrice * 1.2, 'stock' => rand(15, 40), 'sort_order' => 1],
                ['type' => 'Size', 'value' => 'Large', 'price' => $basePrice * 1.5, 'compare_price' => $basePrice * 1.8, 'stock' => rand(10, 35), 'sort_order' => 2],
                ['type' => 'Size', 'value' => 'Extra Large', 'price' => $basePrice * 2, 'compare_price' => $basePrice * 2.4, 'stock' => rand(8, 25), 'sort_order' => 3],
            ];
        } elseif (stripos($categoryName, 'cookie') !== false || stripos($categoryName, 'biscuit') !== false) {
            return [
                // Hierarchical: Flavor - Weight structure
                ['type' => 'Flavor', 'value' => 'Chocolate Chip - 250g', 'price' => $basePrice, 'compare_price' => $basePrice * 1.2, 'stock' => rand(20, 50), 'sort_order' => 1, 'attributes' => ['main_variant' => 'Chocolate Chip', 'quantity_variant' => '250g']],
                ['type' => 'Flavor', 'value' => 'Chocolate Chip - 500g', 'price' => $basePrice * 1.9, 'compare_price' => $basePrice * 2.2, 'stock' => rand(15, 40), 'sort_order' => 2, 'attributes' => ['main_variant' => 'Chocolate Chip', 'quantity_variant' => '500g']],
                ['type' => 'Flavor', 'value' => 'Chocolate Chip - 1kg', 'price' => $basePrice * 3.5, 'compare_price' => $basePrice * 4, 'stock' => rand(10, 30), 'sort_order' => 3, 'attributes' => ['main_variant' => 'Chocolate Chip', 'quantity_variant' => '1kg']],

                ['type' => 'Flavor', 'value' => 'Oatmeal Raisin - 250g', 'price' => $basePrice * 1.1, 'compare_price' => $basePrice * 1.3, 'stock' => rand(15, 45), 'sort_order' => 4, 'attributes' => ['main_variant' => 'Oatmeal Raisin', 'quantity_variant' => '250g']],
                ['type' => 'Flavor', 'value' => 'Oatmeal Raisin - 500g', 'price' => $basePrice * 2, 'compare_price' => $basePrice * 2.3, 'stock' => rand(12, 35), 'sort_order' => 5, 'attributes' => ['main_variant' => 'Oatmeal Raisin', 'quantity_variant' => '500g']],
                ['type' => 'Flavor', 'value' => 'Oatmeal Raisin - 1kg', 'price' => $basePrice * 3.7, 'compare_price' => $basePrice * 4.2, 'stock' => rand(8, 25), 'sort_order' => 6, 'attributes' => ['main_variant' => 'Oatmeal Raisin', 'quantity_variant' => '1kg']],
            ];
        } elseif (stripos($categoryName, 'pastry') !== false || stripos($categoryName, 'pastries') !== false) {
            return [
                // Simple quantity variants
                ['type' => 'Quantity', 'value' => 'Single', 'price' => $basePrice, 'compare_price' => $basePrice * 1.2, 'stock' => rand(20, 50), 'sort_order' => 1],
                ['type' => 'Quantity', 'value' => 'Pack of 2', 'price' => $basePrice * 1.9, 'compare_price' => $basePrice * 2.3, 'stock' => rand(15, 40), 'sort_order' => 2],
                ['type' => 'Quantity', 'value' => 'Pack of 4', 'price' => $basePrice * 3.6, 'compare_price' => $basePrice * 4.2, 'stock' => rand(10, 30), 'sort_order' => 3],
                ['type' => 'Quantity', 'value' => 'Pack of 6', 'price' => $basePrice * 5.2, 'compare_price' => $basePrice * 6, 'stock' => rand(8, 25), 'sort_order' => 4],
            ];
        } elseif (stripos($categoryName, 'donut') !== false || stripos($categoryName, 'doughnut') !== false) {
            return [
                // Hierarchical: Flavor - Quantity structure
                ['type' => 'Flavor', 'value' => 'Glazed - Single', 'price' => $basePrice, 'compare_price' => null, 'stock' => rand(20, 50), 'sort_order' => 1, 'attributes' => ['main_variant' => 'Glazed', 'quantity_variant' => 'Single']],
                ['type' => 'Flavor', 'value' => 'Glazed - Pack of 6', 'price' => $basePrice * 5.5, 'compare_price' => $basePrice * 6.5, 'stock' => rand(10, 30), 'sort_order' => 2, 'attributes' => ['main_variant' => 'Glazed', 'quantity_variant' => 'Pack of 6']],
                ['type' => 'Flavor', 'value' => 'Glazed - Box of 12', 'price' => $basePrice * 10.5, 'compare_price' => $basePrice * 12.5, 'stock' => rand(5, 20), 'sort_order' => 3, 'attributes' => ['main_variant' => 'Glazed', 'quantity_variant' => 'Box of 12']],

                ['type' => 'Flavor', 'value' => 'Chocolate Frosted - Single', 'price' => $basePrice * 1.2, 'compare_price' => null, 'stock' => rand(15, 45), 'sort_order' => 4, 'attributes' => ['main_variant' => 'Chocolate Frosted', 'quantity_variant' => 'Single']],
                ['type' => 'Flavor', 'value' => 'Chocolate Frosted - Pack of 6', 'price' => $basePrice * 6.5, 'compare_price' => $basePrice * 7.5, 'stock' => rand(8, 25), 'sort_order' => 5, 'attributes' => ['main_variant' => 'Chocolate Frosted', 'quantity_variant' => 'Pack of 6']],
                ['type' => 'Flavor', 'value' => 'Chocolate Frosted - Box of 12', 'price' => $basePrice * 12, 'compare_price' => $basePrice * 14, 'stock' => rand(5, 15), 'sort_order' => 6, 'attributes' => ['main_variant' => 'Chocolate Frosted', 'quantity_variant' => 'Box of 12']],
            ];
        } elseif (stripos($categoryName, 'muffin') !== false || stripos($categoryName, 'cupcake') !== false) {
            return [
                // Hierarchical: Flavor - Quantity structure
                ['type' => 'Flavor', 'value' => 'Blueberry - Single', 'price' => $basePrice, 'compare_price' => $basePrice * 1.2, 'stock' => rand(15, 45), 'sort_order' => 1, 'attributes' => ['main_variant' => 'Blueberry', 'quantity_variant' => 'Single']],
                ['type' => 'Flavor', 'value' => 'Blueberry - Pack of 4', 'price' => $basePrice * 3.8, 'compare_price' => $basePrice * 4.4, 'stock' => rand(10, 35), 'sort_order' => 2, 'attributes' => ['main_variant' => 'Blueberry', 'quantity_variant' => 'Pack of 4']],
                ['type' => 'Flavor', 'value' => 'Blueberry - Pack of 6', 'price' => $basePrice * 5.5, 'compare_price' => $basePrice * 6.2, 'stock' => rand(8, 30), 'sort_order' => 3, 'attributes' => ['main_variant' => 'Blueberry', 'quantity_variant' => 'Pack of 6']],

                ['type' => 'Flavor', 'value' => 'Chocolate Chip - Single', 'price' => $basePrice * 1.1, 'compare_price' => $basePrice * 1.3, 'stock' => rand(15, 40), 'sort_order' => 4, 'attributes' => ['main_variant' => 'Chocolate Chip', 'quantity_variant' => 'Single']],
                ['type' => 'Flavor', 'value' => 'Chocolate Chip - Pack of 4', 'price' => $basePrice * 4.2, 'compare_price' => $basePrice * 4.8, 'stock' => rand(10, 30), 'sort_order' => 5, 'attributes' => ['main_variant' => 'Chocolate Chip', 'quantity_variant' => 'Pack of 4']],
                ['type' => 'Flavor', 'value' => 'Chocolate Chip - Pack of 6', 'price' => $basePrice * 6, 'compare_price' => $basePrice * 6.8, 'stock' => rand(8, 25), 'sort_order' => 6, 'attributes' => ['main_variant' => 'Chocolate Chip', 'quantity_variant' => 'Pack of 6']],
            ];
        } else {
            // Default simple variants for other products
            return [
                ['type' => 'Size', 'value' => 'Small', 'price' => $basePrice * 0.8, 'compare_price' => $basePrice, 'stock' => rand(15, 40), 'sort_order' => 1],
                ['type' => 'Size', 'value' => 'Medium', 'price' => $basePrice, 'compare_price' => $basePrice * 1.2, 'stock' => rand(12, 35), 'sort_order' => 2],
                ['type' => 'Size', 'value' => 'Large', 'price' => $basePrice * 1.5, 'compare_price' => $basePrice * 1.8, 'stock' => rand(10, 30), 'sort_order' => 3],
            ];
        }
    }

    private function generateSku($product, $variant): string
    {
        $productSlug = strtoupper(substr($product->slug ?? 'PROD', 0, 6));

        // For hierarchical variants, use both main variant and quantity
        if (isset($variant['attributes']['main_variant']) && isset($variant['attributes']['quantity_variant'])) {
            $mainSuffix = $this->generateSkuSuffix($variant['attributes']['main_variant']);
            $quantitySuffix = $this->generateSkuSuffix($variant['attributes']['quantity_variant']);
            $baseSku = $productSlug . '-' . $mainSuffix . '-' . $quantitySuffix;
        } else {
            // For simple variants, use just the value
            $valueSuffix = $this->generateSkuSuffix($variant['value']);
            $baseSku = $productSlug . '-' . $valueSuffix;
        }

        // Check if SKU exists and make it unique
        $sku = $baseSku;
        $counter = 1;
        while (\App\Models\ProductVariant::where('sku', $sku)->exists()) {
            $sku = $baseSku . $counter;
            $counter++;
        }

        return $sku;
    }

    private function generateSkuSuffix(string $value): string
    {
        // Remove special characters and spaces
        $value = preg_replace('/[^A-Za-z0-9]/', '', $value);

        // Common replacements for better SKU generation
        $replacements = [
            'small' => 'S',
            'medium' => 'M',
            'large' => 'L',
            'extralarge' => 'XL',
            'extrasmall' => 'XS',
            'chocolate' => 'CHO',
            'chocolatechip' => 'CHOC',
            'chocolatefrosted' => 'CHOF',
            'vanilla' => 'VAN',
            'strawberry' => 'STR',
            'blueberry' => 'BLUE',
            'oatmealraisin' => 'OATR',
            'glazed' => 'GLZ',
            'single' => 'SNG',
            'packof2' => 'P2',
            'packof4' => 'P4',
            'packof6' => 'P6',
            'boxof12' => 'B12',
            'gram' => 'G',
            'kilogram' => 'KG',
            '250g' => '250',
            '500g' => '500',
            '1kg' => '1K',
            '2kg' => '2K',
        ];

        $valueLower = strtolower($value);

        // Check if we have a predefined replacement
        foreach ($replacements as $key => $replacement) {
            if (strpos($valueLower, $key) !== false || $valueLower === $key) {
                return $replacement;
            }
        }

        // Extract numbers if present (for sizes like "500g", "1kg")
        if (preg_match('/(\d+)/', $value, $matches)) {
            return strtoupper($matches[1]);
        }

        // If no match, take first 4 characters
        $suffix = strtoupper(substr($value, 0, min(4, strlen($value))));

        // If still empty, generate a random suffix
        if (empty($suffix)) {
            $suffix = strtoupper(substr(md5($value), 0, 3));
        }

        return $suffix;
    }
}