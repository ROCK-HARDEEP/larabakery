<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\CatalogController;
use App\Http\Controllers\Web\ProductController;
use App\Http\Controllers\Web\CartController;
use App\Http\Controllers\Web\CheckoutController;
use App\Http\Controllers\Web\AccountController;
use App\Http\Controllers\Web\InvoiceController;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\PageController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\CouponController;
// Storage file serving route for development server
Route::get('/storage/{path}', function($path) {
    $file = storage_path('app/public/' . $path);
    if (!file_exists($file)) {
        abort(404);
    }
    
    $mimeType = mime_content_type($file);
    return response()->file($file, [
        'Content-Type' => $mimeType,
        'Cache-Control' => 'public, max-age=31536000'
    ]);
})->where('path', '.*');

// Public routes
Route::get('/', [HomeController::class, 'index'])->name('home');

// Authentication routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');

// Social Authentication routes
Route::get('/auth/google', [SocialAuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/admin', [SocialAuthController::class, 'redirectToGoogleAdmin'])->name('auth.google.admin');
Route::get('/auth/google/callback', [SocialAuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');

// Phone verification routes
Route::middleware('auth')->group(function () {
    Route::get('/auth/phone/verify', [SocialAuthController::class, 'showPhoneVerificationForm'])->name('auth.phone.verify.form');
    Route::post('/auth/phone/send-code', [SocialAuthController::class, 'sendPhoneVerificationCode'])->name('auth.phone.send.code');
    Route::post('/auth/phone/verify', [SocialAuthController::class, 'verifyPhone'])->name('auth.phone.verify');
});

// Real-time verification API routes
Route::prefix('api/verify')->group(function () {
    Route::post('/username', [VerificationController::class, 'checkUsername'])->name('api.verify.username');
    Route::post('/email', [VerificationController::class, 'checkEmail'])->name('api.verify.email');
    Route::post('/phone', [VerificationController::class, 'checkPhone'])->name('api.verify.phone');
    Route::post('/pincode', [VerificationController::class, 'verifyPincode'])->name('api.verify.pincode');
    Route::post('/password', [VerificationController::class, 'checkPasswordStrength'])->name('api.verify.password');
});

// Static pages
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');

// Test route for custom sections
Route::get('/test-custom-sections', function () {
    return view('web.test-custom-sections');
})->name('test.custom.sections');

// Catalog - SEO Friendly Routes
Route::get('/search', function() {
    return redirect()->route('products', request()->all());
})->name('search');
Route::get('/search/mobile', [CatalogController::class, 'mobileSearch'])->name('search.mobile');
Route::get('/ajax-search', [CatalogController::class, 'ajaxSearch'])->name('ajax.search');

// SEO-friendly category routes
Route::get('/category/{category:slug}', [CatalogController::class, 'categoryProducts'])->name('category.products');
Route::get('/category/{category:slug}/sort/{sort}', [CatalogController::class, 'categoryProducts'])->name('category.products.sorted');
Route::get('/category/{category:slug}/price/{min}-{max}', [CatalogController::class, 'categoryProducts'])->name('category.products.priced');
Route::get('/category/{category:slug}/sort/{sort}/price/{min}-{max}', [CatalogController::class, 'categoryProducts'])->name('category.products.filtered');

// Search results SEO-friendly routes
Route::get('/search/{query}', [CatalogController::class, 'searchProducts'])->name('search.results');
Route::get('/search/{query}/category/{category:slug}', [CatalogController::class, 'searchProducts'])->name('search.category');

// General products routes (fallback)
Route::get('/products', [CatalogController::class, 'products'])->name('products');
Route::get('/categories', [CatalogController::class, 'allCategories'])->name('categories.all');

// Product
Route::get('/p/{slug}', [ProductController::class, 'show'])->name('product.show');
Route::get('/product-quick-view/{id}', [ProductController::class, 'quickView'])->name('product.quick-view');

// Combo Offers (Bundles)
Route::get('/combos', [\App\Http\Controllers\Web\ComboOfferController::class, 'index'])->name('combos.index');
Route::get('/combo/{slug}', [\App\Http\Controllers\Web\ComboOfferController::class, 'show'])->name('combo.show');

// Limited Time Offers
Route::get('/limited-time-offers', [\App\Http\Controllers\Web\LimitedTimeOfferController::class, 'index'])->name('limited-time-offers');
Route::get('/limited-time-offer/{slug}', [\App\Http\Controllers\Web\LimitedTimeOfferController::class, 'show'])->name('limited-time-offer.show');

// Cart
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/add-bundle/{bundle}', [CartController::class, 'addBundle'])->name('cart.add.bundle');
Route::post('/cart/add-combo/{combo}', [CartController::class, 'addCombo'])->name('cart.add.combo');
Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');
Route::get('/cart', [CartController::class, 'view'])->name('cart.view');

// Wishlist
Route::get('/wishlist', [\App\Http\Controllers\WishlistController::class, 'index'])->name('wishlist.index');
Route::post('/wishlist/add', [\App\Http\Controllers\WishlistController::class, 'add'])->name('wishlist.add');
Route::post('/wishlist/remove', [\App\Http\Controllers\WishlistController::class, 'remove'])->name('wishlist.remove');
Route::post('/wishlist/toggle', [\App\Http\Controllers\WishlistController::class, 'toggle'])->name('wishlist.toggle');
Route::get('/wishlist/count', [\App\Http\Controllers\WishlistController::class, 'count'])->name('wishlist.count');

// Checkout (multi-step)
Route::get('/checkout', [CheckoutController::class, 'summary'])->name('checkout.summary');
Route::post('/checkout/coupon', [CheckoutController::class, 'applyCoupon'])->name('checkout.coupon');
Route::post('/checkout/summary-next', [CheckoutController::class, 'summaryNext'])->name('checkout.summary.next');
Route::get('/checkout/address', [CheckoutController::class, 'address'])->name('checkout.address');
Route::post('/checkout/address-next', [CheckoutController::class, 'addressNext'])->name('checkout.address.next');
Route::get('/checkout/payment', [CheckoutController::class, 'payment'])->name('checkout.payment');
Route::post('/checkout/place-order', [CheckoutController::class, 'placeOrder'])->name('checkout.place');
Route::get('/checkout/success/{order}', [CheckoutController::class, 'success'])->name('checkout.success');
// Utilities
Route::post('/validate-pincode', [CheckoutController::class, 'validatePincode'])->name('checkout.pincode');
Route::post('/slots', [CheckoutController::class, 'slots'])->name('checkout.slots');

// Coupon routes
Route::post('/coupon/apply', [CouponController::class, 'apply'])->name('coupon.apply');
Route::post('/coupon/remove', [CouponController::class, 'remove'])->name('coupon.remove');
Route::post('/coupon/validate', [CouponController::class, 'validate'])->name('coupon.validate');
Route::get('/coupon/available', [CouponController::class, 'available'])->name('coupon.available');

// Test Components Page (for development)
Route::get('/test-components', function() {
    return view('web.test-components');
})->name('test.components');

// Account (protected routes)
Route::middleware('auth')->group(function(){
    Route::get('/account', [AccountController::class, 'index'])->name('account.index');
    Route::get('/account/orders', [AccountController::class, 'orders'])->name('account.orders');
    Route::get('/account/orders/{order}', [AccountController::class, 'orderShow'])->name('account.orders.show');
    Route::post('/account/orders/{order}/cancel', [AccountController::class, 'cancelOrder'])->name('account.orders.cancel');
    Route::get('/account/profile', [AccountController::class, 'profile'])->name('account.profile');
    Route::post('/account/profile', [AccountController::class, 'profileUpdate'])->name('account.profile.update');
    Route::post('/account/address', [AccountController::class, 'addressStore'])->name('account.address.store');
    Route::post('/account/address/{address}/delete', [AccountController::class, 'addressDelete'])->name('account.address.delete');
    Route::get('/account/invoices/{invoice}', [InvoiceController::class, 'download'])->name('account.invoice.download');
});

// Admin routes will be handled by Filament
// Removed custom admin redirect to let Filament handle its own routing

// Admin authentication routes handled by Filament
Route::prefix('admin')->group(function () {
    Route::post('/login', function (Request $request) {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            if ($user->hasRole('admin')) {
                $request->session()->regenerate();
                return redirect('/admin/dashboard');
            } else {
                Auth::logout();
                return back()->withErrors(['email' => 'You do not have permission to access the admin panel.']);
            }
        }
        
        return back()->withErrors(['email' => 'The provided credentials do not match our records.']);
    })->name('admin.login.post');
});

// Include admin routes
if (file_exists(base_path('routes/admin.php'))) {
    require base_path('routes/admin.php');
}

