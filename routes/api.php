<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\{
    AuthController,
    ProductController,
    CategoryController,
    BannerController,
    CartController,
    CheckoutController,
    OrderController,
    AdminAnalyticsController,
    AdminUserController,
    ContactController,
    AdminDashboardController,
    AdminProductController,
    AdminCategoryController,
    AdminDiscountController,
    AdminContactController,
    AdminOrderController
};

/*
|--------------------------------------------------------------------------
| PUBLIC
|--------------------------------------------------------------------------
*/
Route::get('/health', fn () => response()->json(['status' => 'ok']));

Route::get('/home-data', function() {
    $products = \App\Models\Product::with(['category', 'images'])->active()->get();
    
    return response()->json([
        'success' => true,
        'message' => 'Home data retrieved',
        'data' => [
            'banners' => \App\Models\Banner::all(),
            'categories' => \App\Models\Category::all(),
            'products' => $products->take(20), // General pool
            'featured_products' => $products->shuffle()->take(8),
            'latest_products' => $products->sortByDesc('created_at')->take(8),
        ]
    ]);
});

Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show']);
Route::get('/products/{id}/similar', [ProductController::class, 'similar']);

Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/banners', [BannerController::class, 'index']);
Route::post('/contact', [ContactController::class, 'store']);

// Temporary route for Render free tier migration
Route::get('/system/migrate', function() {
    if (request('key') !== 'render_fix_2026') {
        return response()->json(['error' => 'Unauthorized'], 403);
    }
    
    try {
        \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
        $migrateOutput = \Illuminate\Support\Facades\Artisan::output();
        
        \Illuminate\Support\Facades\Artisan::call('optimize:clear');
        $optimizeOutput = \Illuminate\Support\Facades\Artisan::output();
        
        return response()->json([
            'success' => true,
            'message' => 'System updated successfully',
            'output' => [
                'migration' => $migrateOutput,
                'optimization' => $optimizeOutput
            ]
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ], 500);
    }
});

/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/otp/verify', [AuthController::class, 'verifyOtp']);
Route::post('/otp/resend', [AuthController::class, 'resendOtp']);

/*
|--------------------------------------------------------------------------
| AUTHENTICATED USER
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {

    Route::get('/user', [AuthController::class, 'user']);
    Route::get('/profile', [AuthController::class, 'user']); // Alias for better semantics
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // Profile
    Route::post('/profile/update', [AuthController::class, 'updateProfile']);
    Route::post('/profile/photo', [AuthController::class, 'uploadProfilePhoto']);

    // Cart
    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart/add', [CartController::class, 'add']);
    Route::post('/cart/update', [CartController::class, 'update']);
    Route::put('/cart/{id}', [CartController::class, 'update']);
    Route::post('/cart/remove', [CartController::class, 'remove']);
    Route::delete('/cart/{id}', [CartController::class, 'remove']);
    Route::post('/cart/clear', [CartController::class, 'clear']);

    // Checkout
    Route::post('/checkout/preview', [CheckoutController::class, 'preview']);
    Route::post('/checkout/single', [CheckoutController::class, 'singleProductCheckout']);
    Route::post('/checkout/cart', [CheckoutController::class, 'cartCheckout']);

    // Orders
    Route::get('/orders', [OrderController::class, 'index']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
    Route::post('/orders/{id}/cancel', [OrderController::class, 'cancel']);
    Route::post('/orders/{id}/return', [OrderController::class, 'returnOrder']);
    Route::get('/orders/{id}/invoice', [OrderController::class, 'downloadInvoice']);
});

/*
|--------------------------------------------------------------------------
| ADMIN (Protected by auth:sanctum + admin middleware)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {
    
    // Dashboard Stats
    Route::get('/dashboard-stats', [AdminDashboardController::class, 'stats']);

    // Analytics
    Route::get('/analytics', [AdminAnalyticsController::class, 'index']);
    
    // Orders Management
    // Orders Management
    Route::get('/orders', [AdminOrderController::class, 'index']);
    Route::post('/orders/{id}/status', [AdminOrderController::class, 'updateStatus']);
    
    // Users Management
    Route::get('/users', [AdminUserController::class, 'index']);
    Route::get('/users/{id}', [AdminUserController::class, 'show']);
    Route::post('/users/{id}/toggle-status', [AdminUserController::class, 'toggleStatus']);
    Route::post('/users/{id}/role', [AdminUserController::class, 'updateRole']);
    Route::delete('/users/{id}', [AdminUserController::class, 'destroy']);
    
    // Products Management
    Route::get('/products', [AdminProductController::class, 'index']);
    Route::post('/products', [AdminProductController::class, 'store']);
    Route::put('/products/{id}', [AdminProductController::class, 'update']);
    Route::delete('/products/{id}', [AdminProductController::class, 'destroy']);
    
    // Banners Management
    Route::get('/banners', [BannerController::class, 'adminIndex']);
    Route::post('/banners', [BannerController::class, 'store']);
    Route::put('/banners/{id}', [BannerController::class, 'update']);
    Route::delete('/banners/{id}', [BannerController::class, 'destroy']);

    // Categories Management
    Route::get('/categories', [AdminCategoryController::class, 'index']);
    Route::post('/categories', [AdminCategoryController::class, 'store']);
    Route::put('/categories/{id}', [AdminCategoryController::class, 'update']);
    Route::delete('/categories/{id}', [AdminCategoryController::class, 'destroy']);

    // Discount Management
    Route::post('/discounts/apply', [AdminDiscountController::class, 'apply']);
    Route::post('/discounts/category', [AdminDiscountController::class, 'applyToCategory']);
    Route::post('/discounts/all', [AdminDiscountController::class, 'applyToAll']);
    Route::post('/discounts/remove', [AdminDiscountController::class, 'removeDiscount']);

    // Contact Messages
    // Contact Messages
    Route::get('/contacts', [AdminContactController::class, 'index']);
    Route::get('/contacts/{id}', [AdminContactController::class, 'show']);
    Route::post('/contacts/{id}/reply', [AdminContactController::class, 'reply']);
});
