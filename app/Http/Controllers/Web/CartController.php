<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\CartService;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use App\Models\Bundle;
use App\Models\ComboOffer;

class CartController extends Controller
{
    public function __construct(private CartService $cart) {}

    public function view()
    {
        $raw = $this->cart->items();
        $cartItems = [];
        if (!empty($raw)) {
            $productIds = collect($raw)->pluck('product_id')->unique()->values();
            $variantIds = collect($raw)->pluck('variant_id')->filter()->unique()->values();
            $products = Product::whereIn('id', $productIds)->get()->keyBy('id');
            $variants = ProductVariant::whereIn('id', $variantIds)->get()->keyBy('id');

            foreach ($raw as $key => $it) {
                $product = $products[$it['product_id']] ?? null;
                $variant = $it['variant_id'] ? ($variants[$it['variant_id']] ?? null) : null;
                $price = (float)($it['price'] ?? ($variant->price ?? $product?->min_price ?? 0));
                $qty = (int)($it['qty'] ?? 1);
                $cartItems[] = [
                    'key' => $key,
                    'id' => $key, // for legacy blade that expects 'id'
                    'product' => $product,
                    'variant' => $variant,
                    'name' => $it['name'] ?? ($product?->name ?? 'Item'),
                    'sku' => $it['sku'] ?? ($variant->sku ?? null),
                    'price' => $price,
                    'qty' => $qty,
                    'quantity' => $qty, // for legacy blade that expects 'quantity'
                    'addons' => $it['addons'] ?? [],
                    'line_total' => $price * $qty,
                ];
            }
        }
        // Build simple totals for the view if it expects them
        $subtotal = 0.0; $totalItems = 0; $deliveryFee = 0.0; $discount = 0.0; $tax = 0.0; $total = 0.0;
        foreach ($cartItems as $ci) { $subtotal += $ci['line_total']; $totalItems += $ci['qty']; }
        $total = max(0, round($subtotal + $tax + $deliveryFee - $discount, 2));
        return view('web.cart', compact('cartItems','subtotal','totalItems','deliveryFee','discount','tax','total'));
    }

    public function add(Request $request)
    {
        // Bundle add flow
        if ($request->filled('bundle_id')) {
            $request->validate([
                'bundle_id' => 'required|integer',
                'qty' => 'nullable|integer|min:1',
                'quantity' => 'nullable|integer|min:1'
            ]);
            $bundle = Bundle::with('items.product')->findOrFail((int)$request->bundle_id);
            // Ensure bundle is active and within time window
            if (!$bundle->isCurrentlyActive()) {
                return back()->withErrors(['cart'=>'This bundle is not currently available.']);
            }
            // Basic stock check: ensure each product has required stock for requested qty
            $requestedQty = (int)($request->qty ?? $request->quantity ?? 1);
            foreach ($bundle->items as $bi) {
                $productStock = (int)($bi->product?->stock ?? 0);
                // Count already in cart for this product (only approximate for bundles)
                $inCart = 0; foreach ($this->cart->items() as $it) { if (($it['product_id'] ?? null) === $bi->product_id) { $inCart += (int)($it['qty'] ?? 0); } }
                if ($productStock - $inCart < ($bi->qty * $requestedQty)) {
                    return back()->withErrors(['cart'=>'Insufficient stock for items in this bundle.']);
                }
            }
            $this->cart->addBundle($bundle, $requestedQty);
            if ($request->expectsJson() || $request->ajax()) {
                $count = 0; foreach ($this->cart->items() as $it) { $count += (int)($it['qty'] ?? 0); }
                session(['cart_count' => $count]);
                return response()->json(['success'=>true,'cart_count'=>$count,'message'=>'Bundle added to cart']);
            }
            return redirect()->route('cart.view')->with('ok','Bundle added to cart');
        }

        $request->validate([
            'product_id' => 'required|integer',
            'variant_id' => 'nullable|integer',
            'qty' => 'nullable|integer|min:1',
            'quantity' => 'nullable|integer|min:1' // Support both parameter names
        ]);
        
        $productId = (int) $request->product_id;
        $variantId = $request->variant_id ? (int)$request->variant_id : null;
        // Support both 'qty' and 'quantity' parameters
        $requestedQty = (int)($request->qty ?? $request->quantity ?? 1);

        // Determine available stock (variant > variants sum > product stock)
        $product = Product::findOrFail($productId);
        if ($variantId) {
            $available = (int) (ProductVariant::where('id', $variantId)->value('stock') ?? 0);
        } else {
            $variantsCount = $product->variants()->count();
            if ($variantsCount > 0) {
                $available = (int) ($product->variants()->sum('stock') ?? 0);
            } else {
                $available = (int) ($product->stock ?? 0);
            }
        }

        // Current quantity of this product (and variant if provided) already in cart
        $currentInCart = 0;
        foreach ($this->cart->items() as $key => $it) {
            if ((int)$it['product_id'] === $productId) {
                if ($variantId && (int)($it['variant_id'] ?? 0) !== $variantId) continue;
                $currentInCart += (int)($it['qty'] ?? 0);
            }
        }
        $remaining = max(0, $available - $currentInCart);
        if ($remaining <= 0) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success'=>false,'message'=>'No stock available for this product.']);
            }
            return back()->withErrors(['cart'=>'No stock available for this product.']);
        }
        $qtyToAdd = min($requestedQty, $remaining);

        try {
            $this->cart->add($productId, $variantId, $qtyToAdd, $request->input('addons', []));
        } catch (\Throwable $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success'=>false,'message'=>'Unable to add item to cart.'], 500);
            }
            return back()->withErrors(['cart'=>'Unable to add item to cart.']);
        }

        // If AJAX/JSON request, return JSON so frontend buttons can update UI smoothly
        if ($request->expectsJson() || $request->ajax()) {
            $cartItems = $this->cart->items();
            $count = 0; foreach ($cartItems as $it) { $count += (int)($it['qty'] ?? 0); }
            session(['cart_count' => $count]);
            return response()->json([
                'success' => true, 
                'cart_count' => $count, 
                'added' => $qtyToAdd, 
                'remaining' => max(0, $remaining - $qtyToAdd),
                'message' => 'Product added to cart successfully!'
            ]);
        }

        return redirect()->route('cart.view')->with('ok','Added to cart');
    }

    public function update(Request $request)
    {
        // Support both legacy (item_id/change) and new (key/qty) formats
        if ($request->has(['item_id','change'])) {
            $key = (string) $request->item_id;
            $change = (int) $request->change;
            $items = $this->cart->items();
            if (!isset($items[$key])) return response()->json(['success'=>false,'message'=>'Item not found'], 404);
            
            $current = (int) ($items[$key]['qty'] ?? 1);
            $productId = (int) $items[$key]['product_id'];
            $variantId = isset($items[$key]['variant_id']) ? (int)$items[$key]['variant_id'] : null;

            // Determine stock for the product/variant
            if ($variantId) {
                $stock = (int) (ProductVariant::where('id',$variantId)->value('stock') ?? 0);
            } else {
                $stock = (int) (Product::find($productId)?->stock ?? 0);
            }

            // Qty of the same product/variant already in cart, excluding this line
            $otherQty = 0;
            foreach ($items as $k => $it) {
                if ($k === $key) continue;
                if ((int)$it['product_id'] !== $productId) continue;
                if ($variantId && (int)($it['variant_id'] ?? 0) !== $variantId) continue;
                $otherQty += (int)($it['qty'] ?? 0);
            }
            $remaining = max(0, $stock - $otherQty);
            $newQty = min(max(1, $current + $change), max(1, $remaining));
            
            if ($newQty !== $current) {
                $this->cart->update($key, $newQty);
            }
            
            // refresh cart count
            $count = 0; foreach ($this->cart->items() as $it) { $count += (int)($it['qty'] ?? 0); }
            session(['cart_count' => $count]);
            
            return response()->json([
                'success'=>true, 
                'cart_count'=>$count,
                'new_qty' => $newQty,
                'message' => 'Cart updated successfully!'
            ]);
        }
        
        $request->validate(['key'=>'required','qty'=>'required|integer|min:1']);
        // Apply cap using same logic as above
        $key = (string) $request->key;
        $qty = (int) $request->qty;
        $items = $this->cart->items();
        if (isset($items[$key])) {
            $productId = (int) $items[$key]['product_id'];
            $variantId = isset($items[$key]['variant_id']) ? (int)$items[$key]['variant_id'] : null;
            if ($variantId) {
                $stock = (int) (ProductVariant::where('id',$variantId)->value('stock') ?? 0);
            } else {
                $stock = (int) (Product::find($productId)?->stock ?? 0);
            }
            $otherQty = 0;
            foreach ($items as $k => $it) {
                if ($k === $key) continue;
                if ((int)$it['product_id'] !== $productId) continue;
                if ($variantId && (int)($it['variant_id'] ?? 0) !== $variantId) continue;
                $otherQty += (int)($it['qty'] ?? 0);
            }
            $remaining = max(0, $stock - $otherQty);
            $qty = min(max(1,$qty), max(1,$remaining));
        }
        $this->cart->update($key, $qty);
        return back();
    }

    public function remove(Request $request)
    {
        if ($request->has('item_id')) {
            $this->cart->remove((string) $request->item_id);
            // Update cart count after removal
            $cartItems = $this->cart->items();
            $count = 0; foreach ($cartItems as $it) { $count += (int)($it['qty'] ?? 0); }
            session(['cart_count' => $count]);
            return response()->json(['success'=>true, 'cart_count'=>$count, 'message'=>'Item removed from cart']);
        }
        $request->validate(['key'=>'required']);
        $this->cart->remove($request->key);
        return back();
    }
    
    public function addBundle(Bundle $bundle, Request $request)
    {
        $requestedQty = (int)($request->qty ?? $request->quantity ?? 1);
        
        // Ensure bundle is active and within time window
        if (!$bundle->isCurrentlyActive()) {
            return response()->json(['success' => false, 'message' => 'This bundle is not currently available.'], 400);
        }
        
        // Load bundle items with products
        $bundle->load('items.product');
        
        // Basic stock check: ensure each product has required stock for requested qty
        foreach ($bundle->items as $bi) {
            $productStock = (int)($bi->product?->stock ?? 0);
            // Count already in cart for this product (only approximate for bundles)
            $inCart = 0; 
            foreach ($this->cart->items() as $it) { 
                if (($it['product_id'] ?? null) === $bi->product_id) { 
                    $inCart += (int)($it['qty'] ?? 0); 
                } 
            }
            if ($productStock - $inCart < ($bi->qty * $requestedQty)) {
                return response()->json(['success' => false, 'message' => 'Insufficient stock for items in this bundle.'], 400);
            }
        }
        
        $this->cart->addBundle($bundle, $requestedQty);
        
        // Update cart count
        $count = 0; 
        foreach ($this->cart->items() as $it) { 
            $count += (int)($it['qty'] ?? 0); 
        }
        session(['cart_count' => $count]);
        
        return response()->json([
            'success' => true,
            'cart_count' => $count,
            'message' => 'Bundle added to cart successfully!'
        ]);
    }
    
    public function addCombo(ComboOffer $combo, Request $request)
    {
        $requestedQty = (int)($request->qty ?? $request->quantity ?? 1);
        
        // Ensure combo is active and within time window
        if (!$combo->isCurrentlyActive()) {
            return response()->json(['success' => false, 'message' => 'This combo offer is not currently available.'], 400);
        }
        
        // Load combo products
        $combo->load('products');
        
        // Basic stock check: ensure each product has required stock for requested qty
        foreach ($combo->products as $product) {
            $productStock = (int)($product->stock ?? 0);
            $requiredQty = ($product->pivot->quantity ?? 1) * $requestedQty;
            
            // Count already in cart for this product
            $inCart = 0; 
            foreach ($this->cart->items() as $it) { 
                if (($it['product_id'] ?? null) === $product->id) { 
                    $inCart += (int)($it['qty'] ?? 0); 
                } 
            }
            
            if ($productStock - $inCart < $requiredQty) {
                return response()->json(['success' => false, 'message' => 'Insufficient stock for items in this combo.'], 400);
            }
        }
        
        // Add combo to cart as individual items with special pricing
        foreach ($combo->products as $product) {
            $qty = ($product->pivot->quantity ?? 1) * $requestedQty;
            $unitPrice = $product->pivot->unit_price ?? $product->price;
            
            // Calculate discounted price for each item in combo
            $discountRatio = $combo->combo_price / $combo->original_price;
            $discountedPrice = round($unitPrice * $discountRatio, 2);
            
            $this->cart->add(
                $product->id,
                null,
                $qty,
                $discountedPrice,
                ['combo_id' => $combo->id, 'combo_name' => $combo->name]
            );
        }
        
        // Update cart count
        $count = 0; 
        foreach ($this->cart->items() as $it) { 
            $count += (int)($it['qty'] ?? 0); 
        }
        session(['cart_count' => $count]);
        
        return response()->json([
            'success' => true,
            'cart_count' => $count,
            'message' => 'Combo offer added to cart successfully!'
        ]);
    }
}