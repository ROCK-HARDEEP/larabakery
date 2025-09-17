<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function show(string $slug)
    {
        $product = Product::where('slug', $slug)
            ->where('is_active', true)
            ->with(['category', 'variants' => function($query) {
                $query->where('is_active', true)
                    ->orderBy('sort_order')
                    ->orderBy('price');
            }, 'addons', 'faqs' => function($query) {
                $query->where('is_active', true)
                    ->orderBy('order_index');
            }])
            ->firstOrFail();

        // Get related products from same category
        $related = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->take(4)
            ->get();

        // Get recently viewed products (random for now)
        $recentlyViewed = Product::where('is_active', true)
            ->where('id', '!=', $product->id)
            ->inRandomOrder()
            ->take(4)
            ->get();

        return view('web.product', compact('product', 'related', 'recentlyViewed'));
    }
    
    public function quickView($id)
    {
        $product = Product::where('id', $id)
            ->where('is_active', true)
            ->with(['variants' => function($query) {
                $query->where('is_active', true)
                    ->orderBy('sort_order')
                    ->orderBy('price');
            }])
            ->firstOrFail();

        return response()->json([
            'product' => $product,
            'variants' => $product->variants
        ]);
    }
}