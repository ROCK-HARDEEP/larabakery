<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\Product;
use Carbon\Carbon;

class SampleOrderSeeder extends Seeder
{
    public function run()
    {
        $users = User::all();
        $products = Product::where('is_active', true)->get();
        
        if ($users->isEmpty() || $products->isEmpty()) {
            $this->command->info('No users or products found. Skipping order seeding.');
            return;
        }
        
        // Create orders for the last 30 days
        for ($i = 0; $i < 50; $i++) {
            $user = $users->random();
            $orderDate = Carbon::now()->subDays(rand(0, 30));
            
            $subtotal = 0;
            $items = [];
            
            // Random number of items per order
            $itemCount = rand(1, 5);
            for ($j = 0; $j < $itemCount; $j++) {
                $product = $products->random();
                $quantity = rand(1, 3);
                $price = $product->price;
                $subtotal += $price * $quantity;
                
                $items[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                    'price' => $price,
                ];
            }
            
            $tax = $subtotal * 0.18; // 18% GST
            $shipping = $subtotal > 500 ? 0 : 50;
            $total = $subtotal + $tax + $shipping;
            
            $statuses = ['placed', 'processing', 'completed', 'cancelled'];
            $status = $statuses[array_rand($statuses)];
            
            // Create order
            $order = Order::create([
                'user_id' => $user->id,
                'status' => $status,
                'payment_mode' => rand(0, 1) ? 'cod' : 'online',
                'payment_status' => $status === 'completed' ? 'paid' : 'pending',
                'currency' => 'INR',
                'subtotal' => $subtotal,
                'tax' => $tax,
                'discount' => 0,
                'shipping_fee' => $shipping,
                'total' => $total,
                'created_at' => $orderDate,
                'updated_at' => $orderDate,
            ]);
            
            // Create order items
            foreach ($items as $item) {
                $lineSubtotal = $item['price'] * $item['quantity'];
                $lineTax = $lineSubtotal * 0.18;
                
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product']->id,
                    'name_snapshot' => $item['product']->name,
                    'sku_snapshot' => $item['product']->sku ?? 'SKU-' . $item['product']->id,
                    'price' => $item['price'],
                    'qty' => $item['quantity'],
                    'line_subtotal' => $lineSubtotal,
                    'line_tax' => $lineTax,
                    'line_total' => $lineSubtotal + $lineTax,
                    'created_at' => $orderDate,
                    'updated_at' => $orderDate,
                ]);
            }
        }
        
        $this->command->info('Sample orders created successfully!');
    }
}