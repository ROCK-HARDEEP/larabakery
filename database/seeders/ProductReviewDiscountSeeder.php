<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductReviewDiscountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = Product::all();
        
        foreach ($products as $product) {
            // Add random rating between 3.5 and 5.0
            $rating = round(rand(35, 50) / 10, 1);
            
            // Add random review count between 5 and 150
            $reviewCount = rand(5, 150);
            
            // Randomly enable discount for some products (30% chance)
            $hasDiscount = rand(1, 10) <= 3;
            
            if ($hasDiscount) {
                $discountPercentage = rand(10, 40); // 10% to 40% discount
                $discountPrice = $product->base_price * (1 - ($discountPercentage / 100));
                
                // Set discount dates (active for next 30 days)
                $startDate = now();
                $endDate = now()->addDays(30);
            } else {
                $discountPercentage = null;
                $discountPrice = null;
                $startDate = null;
                $endDate = null;
            }
            
            $product->update([
                'rating' => $rating,
                'review_count' => $reviewCount,
                'has_discount' => $hasDiscount,
                'discount_percentage' => $discountPercentage,
                'discount_price' => $discountPrice,
                'discount_start_date' => $startDate,
                'discount_end_date' => $endDate,
            ]);
        }
        
        $this->command->info('Product review and discount data seeded successfully!');
    }
}
