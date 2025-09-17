<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\HomepageFaq;
use App\Models\ProductFaq;
use App\Models\Product;

class FaqSeeder extends Seeder
{
    public function run(): void
    {
        // Homepage FAQs
        $homepageFaqs = [
            [
                'question' => 'What are your bakery opening hours?',
                'answer' => 'Our bakery is open Monday to Saturday from 7:00 AM to 9:00 PM, and Sunday from 8:00 AM to 8:00 PM. We are closed on major holidays.',
                'order_index' => 1,
                'is_active' => true,
            ],
            [
                'question' => 'Do you offer custom cake orders?',
                'answer' => 'Yes! We specialize in custom cakes for all occasions. Please place your order at least 48 hours in advance for standard designs and 1 week for elaborate custom designs.',
                'order_index' => 2,
                'is_active' => true,
            ],
            [
                'question' => 'When was your bakery founded?',
                'answer' => 'Our bakery was established in 1995 by master baker John Smith. We have been serving the community with fresh, handmade baked goods for over 28 years.',
                'order_index' => 3,
                'is_active' => true,
            ],
            [
                'question' => 'What ingredients do you use in your products?',
                'answer' => 'We use only the finest quality ingredients including organic flour, free-range eggs, real butter, and premium chocolate. All our products are made fresh daily without preservatives.',
                'order_index' => 4,
                'is_active' => true,
            ],
            [
                'question' => 'Do you offer gluten-free or vegan options?',
                'answer' => 'Yes! We have a dedicated gluten-free section and offer various vegan options. Please check our menu or ask our staff for specific dietary requirements.',
                'order_index' => 5,
                'is_active' => true,
            ],
            [
                'question' => 'How do you ensure freshness of your products?',
                'answer' => 'All our products are baked fresh daily in small batches. We start baking at 4 AM every morning to ensure you get the freshest items. Any unsold items are donated to local charities.',
                'order_index' => 6,
                'is_active' => true,
            ],
            [
                'question' => 'Do you offer delivery services?',
                'answer' => 'Yes, we offer delivery within a 10km radius for orders above ₹500. Same-day delivery is available for orders placed before 2 PM.',
                'order_index' => 7,
                'is_active' => true,
            ],
            [
                'question' => 'Can I place bulk orders for events?',
                'answer' => 'Absolutely! We cater to corporate events, parties, and weddings. Please contact us at least 1 week in advance for bulk orders to ensure availability.',
                'order_index' => 8,
                'is_active' => true,
            ],
        ];

        foreach ($homepageFaqs as $faq) {
            HomepageFaq::create($faq);
        }

        // Product FAQs - Add for first 5 products
        $products = Product::limit(5)->get();
        
        $productFaqTemplates = [
            [
                'question' => 'What is the shelf life of this product?',
                'answer' => 'This product stays fresh for 3-5 days when stored properly in an airtight container at room temperature.',
            ],
            [
                'question' => 'Does this contain any allergens?',
                'answer' => 'Please check the allergen information in the product details. Common allergens may include wheat, eggs, milk, and nuts.',
            ],
            [
                'question' => 'Can I customize this product?',
                'answer' => 'Yes, we offer customization options for size, flavor, and decorations. Please contact us for custom orders.',
            ],
            [
                'question' => 'What are the storage instructions?',
                'answer' => 'Store in a cool, dry place away from direct sunlight. Once opened, keep in an airtight container.',
            ],
            [
                'question' => 'Is this product suitable for vegetarians?',
                'answer' => 'Most of our products are vegetarian-friendly. Please check the ingredients list for specific dietary information.',
            ],
        ];

        foreach ($products as $product) {
            foreach ($productFaqTemplates as $index => $template) {
                ProductFaq::create([
                    'product_id' => $product->id,
                    'question' => $template['question'],
                    'answer' => $template['answer'],
                    'order_index' => $index + 1,
                    'is_active' => true,
                ]);
            }
        }

        $this->command->info('FAQ data seeded successfully!');
    }
}