<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CouponController extends Controller
{
    /**
     * Apply a coupon code to the cart
     */
    public function apply(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:255',
        ]);

        $code = strtoupper($request->code);
        $coupon = Coupon::where('code', $code)->first();

        if (!$coupon) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid coupon code',
            ], 404);
        }

        // Get cart data
        $cart = Session::get('cart', []);
        $orderAmount = $this->calculateCartTotal($cart);
        $products = $this->getCartProducts($cart);
        $user = Auth::user();

        // Validate coupon
        $validation = $coupon->isValidForUser($user, $orderAmount, $products);

        if (!$validation['valid']) {
            return response()->json([
                'success' => false,
                'message' => $validation['message'],
            ], 400);
        }

        // Calculate discount
        $discount = $coupon->calculateDiscount($orderAmount, $products);

        // Store coupon in session
        Session::put('applied_coupon', [
            'id' => $coupon->id,
            'code' => $coupon->code,
            'discount' => $discount,
            'type' => $coupon->type,
            'discount_type' => $coupon->discount_type,
            'discount_value' => $coupon->discount_value,
        ]);

        return response()->json([
            'success' => true,
            'message' => $validation['message'],
            'coupon' => [
                'code' => $coupon->code,
                'discount' => $discount,
                'discount_label' => $coupon->getDiscountLabel(),
                'type' => $coupon->getTypeLabel(),
            ],
            'cart' => [
                'subtotal' => $orderAmount,
                'discount' => $discount,
                'total' => $orderAmount - $discount,
            ],
        ]);
    }

    /**
     * Remove applied coupon from cart
     */
    public function remove()
    {
        Session::forget('applied_coupon');

        $cart = Session::get('cart', []);
        $orderAmount = $this->calculateCartTotal($cart);

        return response()->json([
            'success' => true,
            'message' => 'Coupon removed successfully',
            'cart' => [
                'subtotal' => $orderAmount,
                'discount' => 0,
                'total' => $orderAmount,
            ],
        ]);
    }

    /**
     * Validate a coupon code without applying it
     */
    public function validate(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:255',
        ]);

        $code = strtoupper($request->code);
        $coupon = Coupon::where('code', $code)->first();

        if (!$coupon) {
            return response()->json([
                'valid' => false,
                'message' => 'Invalid coupon code',
            ]);
        }

        // Get cart data
        $cart = Session::get('cart', []);
        $orderAmount = $this->calculateCartTotal($cart);
        $products = $this->getCartProducts($cart);
        $user = Auth::user();

        // Validate coupon
        $validation = $coupon->isValidForUser($user, $orderAmount, $products);

        if (!$validation['valid']) {
            return response()->json([
                'valid' => false,
                'message' => $validation['message'],
            ]);
        }

        // Calculate potential discount
        $discount = $coupon->calculateDiscount($orderAmount, $products);

        return response()->json([
            'valid' => true,
            'message' => $validation['message'],
            'discount' => $discount,
            'discount_label' => $coupon->getDiscountLabel(),
        ]);
    }

    /**
     * Get available coupons for the current user
     */
    public function available()
    {
        $user = Auth::user();
        $cart = Session::get('cart', []);
        $orderAmount = $this->calculateCartTotal($cart);
        $products = $this->getCartProducts($cart);

        $coupons = Coupon::active()->get();
        $availableCoupons = [];

        foreach ($coupons as $coupon) {
            $validation = $coupon->isValidForUser($user, $orderAmount, $products);
            
            if ($validation['valid']) {
                $availableCoupons[] = [
                    'code' => $coupon->code,
                    'description' => $coupon->description,
                    'discount_label' => $coupon->getDiscountLabel(),
                    'type' => $coupon->getTypeLabel(),
                    'expires_at' => $coupon->expires_at ? $coupon->expires_at->format('M d, Y') : null,
                ];
            }
        }

        return response()->json([
            'success' => true,
            'coupons' => $availableCoupons,
        ]);
    }

    /**
     * Calculate cart total
     */
    private function calculateCartTotal($cart)
    {
        $total = 0;
        
        foreach ($cart as $item) {
            $price = $item['price'] ?? 0;
            $quantity = $item['quantity'] ?? 1;
            $total += $price * $quantity;
        }
        
        return $total;
    }

    /**
     * Get products from cart
     */
    private function getCartProducts($cart)
    {
        $products = [];
        
        foreach ($cart as $item) {
            $products[] = [
                'id' => $item['product_id'] ?? $item['id'],
                'price' => $item['price'] ?? 0,
                'quantity' => $item['quantity'] ?? 1,
            ];
        }
        
        return $products;
    }
}