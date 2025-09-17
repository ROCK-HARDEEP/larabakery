<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Bundle;
use App\Models\ComboOffer;
use App\Models\Product;
use Illuminate\Http\Request;

class LimitedTimeOfferController extends Controller
{
    public function index()
    {
        // Get active bundles (Limited Time Offers)
        $bundles = Bundle::query()
            ->with(['items.product'])
            ->withCount('items')
            ->where('is_active', true)
            ->whereHas('items')
            ->where(function($q){
                $now = now();
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', $now);
            })
            ->where(function($q){
                $now = now();
                $q->whereNull('ends_at')->orWhere('ends_at', '>=', $now);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        // Get active combo offers (first 3 by display order for section)
        $combos = ComboOffer::active()
            ->with(['products'])
            ->orderBy('display_order', 'asc')
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();

        // Get popular products from PopularProduct model or latest products
        $featuredProducts = [];
        try {
            $popularProducts = \App\Models\PopularProduct::where('is_active', true)
                ->with(['product' => function($query) {
                    $query->where('is_active', true);
                }])
                ->orderBy('sort_order')
                ->take(8)
                ->get();
            
            $featuredProducts = $popularProducts->pluck('product')->filter()->values();
        } catch (\Exception $e) {
            // Fallback if PopularProduct doesn't exist
        }
        
        // If no popular products, get latest products
        if (empty($featuredProducts) || $featuredProducts->isEmpty()) {
            $featuredProducts = Product::where('is_active', true)
                ->latest()
                ->take(8)
                ->get();
        }

        return view('web.limited-time-offers.index', compact('bundles', 'combos', 'featuredProducts'));
    }

    public function show($slug)
    {
        // Get the specific bundle by slug
        $bundle = Bundle::where('slug', $slug)
            ->with(['items.product.category'])
            ->withCount('items')
            ->firstOrFail();
            
        // Check if bundle is currently active
        if (!$bundle->is_active) {
            abort(404);
        }
        
        // Check time-based availability
        $now = now();
        if ($bundle->starts_at && $now->lt($bundle->starts_at)) {
            abort(404, 'This offer has not started yet.');
        }
        if ($bundle->ends_at && $now->gt($bundle->ends_at)) {
            abort(404, 'This offer has expired.');
        }

        // Get related bundles
        $relatedBundles = Bundle::where('id', '!=', $bundle->id)
            ->where('is_active', true)
            ->where(function($q) use ($now) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', $now);
            })
            ->where(function($q) use ($now) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>=', $now);
            })
            ->with(['items.product'])
            ->take(4)
            ->get();

        // Get combo offers for cross-selling
        $combos = ComboOffer::active()
            ->with(['products'])
            ->orderBy('display_order', 'asc')
            ->take(3)
            ->get();

        return view('web.limited-time-offers.show', compact('bundle', 'relatedBundles', 'combos'));
    }
}