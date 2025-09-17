<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProductReviewController extends Controller
{
    /**
     * Add a review to a product
     */
    public function store(Request $request, Product $product): JsonResponse
    {
        $request->validate([
            'rating' => 'required|numeric|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        try {
            // Add the review to the product
            $product->addReview($request->rating);
            
            return response()->json([
                'success' => true,
                'message' => 'Review added successfully',
                'data' => [
                    'new_rating' => $product->fresh()->rating,
                    'new_review_count' => $product->fresh()->review_count,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add review',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get product review statistics
     */
    public function show(Product $product): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'rating' => $product->rating,
                'review_count' => $product->review_count,
                'formatted_rating' => $product->formatted_rating,
                'stars' => $product->stars,
            ]
        ]);
    }
}
