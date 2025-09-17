<?php

namespace App\Http\Controllers;

use App\Models\Wishlist;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $sessionId = session()->getId();
        
        $wishlists = Wishlist::getWishlistForUser($userId, $sessionId);
        
        return view('web.wishlist', compact('wishlists'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id'
        ]);

        $productId = $request->product_id;
        $userId = Auth::id();
        $sessionId = session()->getId();

        // Check if already in wishlist
        if (Wishlist::isInWishlist($productId, $userId, $sessionId)) {
            return response()->json([
                'success' => false,
                'message' => 'Product is already in your wishlist'
            ]);
        }

        // Add to wishlist
        Wishlist::create([
            'user_id' => $userId,
            'session_id' => $userId ? null : $sessionId,
            'product_id' => $productId
        ]);

        $wishlistCount = Wishlist::getWishlistForUser($userId, $sessionId)->count();

        return response()->json([
            'success' => true,
            'message' => 'Product added to wishlist!',
            'wishlist_count' => $wishlistCount
        ]);
    }

    public function remove(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id'
        ]);

        $productId = $request->product_id;
        $userId = Auth::id();
        $sessionId = session()->getId();

        $query = Wishlist::where('product_id', $productId);
        
        if ($userId) {
            $query->where('user_id', $userId);
        } else {
            $query->where('session_id', $sessionId);
        }

        $deleted = $query->delete();

        if ($deleted) {
            $wishlistCount = Wishlist::getWishlistForUser($userId, $sessionId)->count();
            
            return response()->json([
                'success' => true,
                'message' => 'Product removed from wishlist!',
                'wishlist_count' => $wishlistCount
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Product not found in wishlist'
        ]);
    }

    public function toggle(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id'
        ]);

        $productId = $request->product_id;
        $userId = Auth::id();
        $sessionId = session()->getId();

        if (Wishlist::isInWishlist($productId, $userId, $sessionId)) {
            return $this->remove($request);
        } else {
            return $this->add($request);
        }
    }

    public function count()
    {
        $userId = Auth::id();
        $sessionId = session()->getId();
        
        $count = Wishlist::getWishlistForUser($userId, $sessionId)->count();
        
        return response()->json(['count' => $count]);
    }
}