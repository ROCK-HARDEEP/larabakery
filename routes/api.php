<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductReviewController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// API Routes for Bakery Shop
Route::prefix('v1')->group(function () {
    // Public API routes
    Route::get('/health', function () {
        return response()->json(['status' => 'ok', 'message' => 'Bakery Shop API is running']);
    });
    
    // Product review routes
    Route::get('/products/{product}/reviews', [ProductReviewController::class, 'show']);
    Route::post('/products/{product}/reviews', [ProductReviewController::class, 'store']);
    
    // Protected API routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/profile', function (Request $request) {
            return $request->user();
        });
    });
});
