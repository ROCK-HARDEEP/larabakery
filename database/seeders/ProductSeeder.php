<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductAddon;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure categories exist
        $freshBreads = Category::firstOrCreate(['slug' => 'fresh-breads'], ['name' => 'Fresh Breads', 'position' => 1]);
        $celebrationCakes = Category::firstOrCreate(['slug' => 'celebration-cakes'], ['name' => 'Celebration Cakes', 'position' => 2]);
        $gourmetPastries = Category::firstOrCreate(['slug' => 'gourmet-pastries'], ['name' => 'Gourmet Pastries', 'position' => 3]);
        $artisanCookies = Category::firstOrCreate(['slug' => 'artisan-cookies'], ['name' => 'Artisan Cookies', 'position' => 4]);
        $sweetTreats = Category::firstOrCreate(['slug' => 'sweet-treats'], ['name' => 'Sweet Treats', 'position' => 5]);

        $products = [
            // Fresh Breads Category
            [
                'category' => $freshBreads->id,
                'name' => 'Artisan Sourdough Bread',
                'hsn' => '1905', 'tax' => 5.00, 'price' => 120.00,
                'desc' => 'Traditional sourdough bread made with our 100-year-old starter. Perfect crust with a tangy, complex flavor and chewy interior.',
                'images' => ['sourdough-bread-1.jpg', 'sourdough-bread-2.jpg'],
                'stock' => 25,
                'variants' => [
                    ['sku' => 'SOUR-500', 'price' => 120.00, 'attrs' => ['size' => '500g']],
                    ['sku' => 'SOUR-1000', 'price' => 220.00, 'attrs' => ['size' => '1kg']],
                ],
            ],
            [
                'category' => $freshBreads->id,
                'name' => 'Whole Wheat Multigrain Loaf',
                'hsn' => '1905', 'tax' => 5.00, 'price' => 95.00,
                'desc' => 'Nutritious whole wheat bread packed with seeds and grains. Rich in fiber and perfect for healthy sandwiches.',
                'images' => ['multigrain-bread-1.jpg', 'multigrain-bread-2.jpg'],
                'stock' => 30,
                'variants' => [
                    ['sku' => 'WG-500', 'price' => 95.00, 'attrs' => ['size' => '500g']],
                ],
            ],
            [
                'category' => $freshBreads->id,
                'name' => 'Classic French Baguette',
                'hsn' => '1905', 'tax' => 5.00, 'price' => 75.00,
                'desc' => 'Authentic French baguette with a crisp golden crust and soft, airy interior. Perfect for sandwiches or with butter.',
                'images' => ['baguette-1.jpg', 'baguette-2.jpg'],
                'stock' => 40,
                'variants' => [
                    ['sku' => 'BAG-250', 'price' => 75.00, 'attrs' => ['size' => '250g']],
                ],
            ],

            // Celebration Cakes Category
            [
                'category' => $celebrationCakes->id,
                'name' => 'Chocolate Truffle Celebration Cake',
                'hsn' => '1905', 'tax' => 5.00, 'price' => 850.00,
                'desc' => 'Luxurious chocolate cake layered with dark chocolate ganache and chocolate truffle filling. Decorated with chocolate shavings and gold leaf.',
                'images' => ['chocolate-truffle-cake-1.jpg', 'chocolate-truffle-cake-2.jpg'],
                'stock' => 15,
                'variants' => [
                    ['sku' => 'CTC-500', 'price' => 450.00, 'attrs' => ['size' => '500g']],
                    ['sku' => 'CTC-1000', 'price' => 850.00, 'attrs' => ['size' => '1kg']],
                    ['sku' => 'CTC-1500', 'price' => 1200.00, 'attrs' => ['size' => '1.5kg']],
                ],
                'addons' => [
                    ['name' => 'Custom Message', 'price' => 50.00],
                    ['name' => 'Extra Decorations', 'price' => 100.00],
                ],
            ],
            [
                'category' => $celebrationCakes->id,
                'name' => 'Vanilla Bean Wedding Cake',
                'hsn' => '1905', 'tax' => 5.00, 'price' => 1200.00,
                'desc' => 'Elegant vanilla bean cake with vanilla buttercream and fresh fruit filling. Perfect for weddings and special celebrations.',
                'images' => ['vanilla-wedding-cake-1.jpg', 'vanilla-wedding-cake-2.jpg'],
                'stock' => 10,
                'variants' => [
                    ['sku' => 'VWC-1000', 'price' => 1200.00, 'attrs' => ['size' => '1kg']],
                    ['sku' => 'VWC-2000', 'price' => 2200.00, 'attrs' => ['size' => '2kg']],
                ],
                'addons' => [
                    ['name' => 'Custom Design', 'price' => 200.00],
                    ['name' => 'Fresh Flowers', 'price' => 150.00],
                ],
            ],
            [
                'category' => $celebrationCakes->id,
                'name' => 'Red Velvet Cream Cheese Cake',
                'hsn' => '1905', 'tax' => 5.00, 'price' => 750.00,
                'desc' => 'Classic red velvet cake with cream cheese frosting. Moist, flavorful cake with a beautiful red color and smooth frosting.',
                'images' => ['red-velvet-cake-1.jpg', 'red-velvet-cake-2.jpg'],
                'stock' => 20,
                'variants' => [
                    ['sku' => 'RVC-500', 'price' => 400.00, 'attrs' => ['size' => '500g']],
                    ['sku' => 'RVC-1000', 'price' => 750.00, 'attrs' => ['size' => '1kg']],
                ],
            ],

            // Gourmet Pastries Category
            [
                'category' => $gourmetPastries->id,
                'name' => 'Butter Croissant',
                'hsn' => '1905', 'tax' => 5.00, 'price' => 65.00,
                'desc' => 'Flaky, buttery croissant made with premium French butter. Perfect golden color with layers that melt in your mouth.',
                'images' => ['butter-croissant-1.jpg', 'butter-croissant-2.jpg'],
                'stock' => 50,
                'variants' => [
                    ['sku' => 'CRO-80', 'price' => 65.00, 'attrs' => ['size' => '80g']],
                ],
            ],
            [
                'category' => $gourmetPastries->id,
                'name' => 'Chocolate Éclair',
                'hsn' => '1905', 'tax' => 5.00, 'price' => 85.00,
                'desc' => 'Classic French éclair filled with rich chocolate cream and topped with dark chocolate glaze. Elegant and indulgent.',
                'images' => ['chocolate-eclair-1.jpg', 'chocolate-eclair-2.jpg'],
                'stock' => 35,
                'variants' => [
                    ['sku' => 'ECL-100', 'price' => 85.00, 'attrs' => ['size' => '100g']],
                ],
            ],
            [
                'category' => $gourmetPastries->id,
                'name' => 'Apple Danish Pastry',
                'hsn' => '1905', 'tax' => 5.00, 'price' => 75.00,
                'desc' => 'Flaky Danish pastry filled with spiced apple compote and topped with vanilla glaze. Perfect breakfast or afternoon treat.',
                'images' => ['apple-danish-1.jpg', 'apple-danish-2.jpg'],
                'stock' => 40,
                'variants' => [
                    ['sku' => 'DAN-90', 'price' => 75.00, 'attrs' => ['size' => '90g']],
                ],
            ],

            // Artisan Cookies Category
            [
                'category' => $artisanCookies->id,
                'name' => 'Double Chocolate Chip Cookies',
                'hsn' => '1905', 'tax' => 5.00, 'price' => 45.00,
                'desc' => 'Rich chocolate cookies loaded with dark chocolate chips. Crispy edges with a soft, chewy center. Perfect for chocolate lovers.',
                'images' => ['double-chocolate-cookies-1.jpg', 'double-chocolate-cookies-2.jpg'],
                'stock' => 60,
                'variants' => [
                    ['sku' => 'DCC-100', 'price' => 45.00, 'attrs' => ['size' => '100g (4 cookies)']],
                    ['sku' => 'DCC-250', 'price' => 100.00, 'attrs' => ['size' => '250g (10 cookies)']],
                ],
            ],
            [
                'category' => $artisanCookies->id,
                'name' => 'Oatmeal Raisin Cookies',
                'hsn' => '1905', 'tax' => 5.00, 'price' => 40.00,
                'desc' => 'Classic oatmeal cookies with plump raisins and warm spices. Hearty texture with a hint of cinnamon and nutmeg.',
                'images' => ['oatmeal-raisin-cookies-1.jpg', 'oatmeal-raisin-cookies-2.jpg'],
                'stock' => 55,
                'variants' => [
                    ['sku' => 'ORC-100', 'price' => 40.00, 'attrs' => ['size' => '100g (4 cookies)']],
                    ['sku' => 'ORC-250', 'price' => 90.00, 'attrs' => ['size' => '250g (10 cookies)']],
                ],
            ],
            [
                'category' => $artisanCookies->id,
                'name' => 'Butter Shortbread Cookies',
                'hsn' => '1905', 'tax' => 5.00, 'price' => 50.00,
                'desc' => 'Traditional Scottish shortbread made with premium butter. Delicate, crumbly texture with a rich, buttery flavor.',
                'images' => ['shortbread-cookies-1.jpg', 'shortbread-cookies-2.jpg'],
                'stock' => 45,
                'variants' => [
                    ['sku' => 'SBC-100', 'price' => 50.00, 'attrs' => ['size' => '100g (6 cookies)']],
                    ['sku' => 'SBC-250', 'price' => 110.00, 'attrs' => ['size' => '250g (15 cookies)']],
                ],
            ],

            // Sweet Treats Category
            [
                'category' => $sweetTreats->id,
                'name' => 'Blueberry Muffins',
                'hsn' => '1905', 'tax' => 5.00, 'price' => 55.00,
                'desc' => 'Moist muffins bursting with fresh blueberries. Light texture with a sweet crumb topping. Perfect for breakfast or brunch.',
                'images' => ['blueberry-muffins-1.jpg', 'blueberry-muffins-2.jpg'],
                'stock' => 40,
                'variants' => [
                    ['sku' => 'BBM-120', 'price' => 55.00, 'attrs' => ['size' => '120g (1 muffin)']],
                    ['sku' => 'BBM-360', 'price' => 150.00, 'attrs' => ['size' => '360g (3 muffins)']],
                ],
            ],
            [
                'category' => $sweetTreats->id,
                'name' => 'Chocolate Brownies',
                'hsn' => '1905', 'tax' => 5.00, 'price' => 60.00,
                'desc' => 'Fudgy chocolate brownies with a crackly top. Rich chocolate flavor with chocolate chips throughout. Irresistibly decadent.',
                'images' => ['chocolate-brownies-1.jpg', 'chocolate-brownies-2.jpg'],
                'stock' => 35,
                'variants' => [
                    ['sku' => 'CBR-100', 'price' => 60.00, 'attrs' => ['size' => '100g (2 brownies)']],
                    ['sku' => 'CBR-250', 'price' => 140.00, 'attrs' => ['size' => '250g (5 brownies)']],
                ],
            ],
            [
                'category' => $sweetTreats->id,
                'name' => 'Vanilla Cupcakes',
                'hsn' => '1905', 'tax' => 5.00, 'price' => 70.00,
                'desc' => 'Light and fluffy vanilla cupcakes with vanilla buttercream frosting. Decorated with sprinkles and perfect for parties.',
                'images' => ['vanilla-cupcakes-1.jpg', 'vanilla-cupcakes-2.jpg'],
                'stock' => 30,
                'variants' => [
                    ['sku' => 'VCC-100', 'price' => 70.00, 'attrs' => ['size' => '100g (1 cupcake)']],
                    ['sku' => 'VCC-300', 'price' => 180.00, 'attrs' => ['size' => '300g (3 cupcakes)']],
                ],
                'addons' => [
                    ['name' => 'Custom Frosting Color', 'price' => 20.00],
                    ['name' => 'Extra Decorations', 'price' => 30.00],
                ],
            ],
        ];

        foreach ($products as $product) {
            $p = Product::firstOrCreate(
                ['slug' => Str::slug($product['name'])],
                [
                    'category_id' => $product['category'],
                    'name' => $product['name'],
                    'hsn_code' => $product['hsn'],
                    'tax_rate' => $product['tax'],
                    'base_price' => $product['price'],
                    'description' => $product['desc'],
                    'stock' => $product['stock'],
                    'images_path' => $product['images'],
                    'is_active' => true,
                ]
            );

            // Create variants
            if (!empty($product['variants'])) {
                foreach ($product['variants'] as $variant) {
                ProductVariant::firstOrCreate(
                        ['sku' => $variant['sku']],
                    [
                        'product_id' => $p->id,
                            'price' => $variant['price'],
                            'stock' => $product['stock'],
                            'attributes_json' => $variant['attrs'],
                        'is_active' => true,
                    ]
                );
                }
            }

            // Create addons
            if (!empty($product['addons'])) {
                foreach ($product['addons'] as $addon) {
                    ProductAddon::firstOrCreate(
                        ['product_id' => $p->id, 'name' => $addon['name']],
                        ['price' => $addon['price'], 'is_active' => true]
                    );
                }
            }
        }

        $this->command->info('15 bakery products created successfully across 5 categories!');
    }
}
