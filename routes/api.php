<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\BannerController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CheckoutController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\AdminAnalyticsController;
use App\Http\Controllers\Api\ContactController;
use App\Models\Product;

// Auth
Route::post('/register', [AuthController::class, 'register']); // Sends OTP
Route::post('/login', [AuthController::class, 'login']); // Sends OTP if needed or returns token
Route::post('/otp/verify', [AuthController::class, 'verifyOtp']);
Route::post('/otp/resend', [AuthController::class, 'resendOtp']);

// Public Data
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toIso8601String(),
        'environment' => app()->environment(),
        'database' => \Illuminate\Support\Facades\DB::connection()->getDatabaseName() ? 'connected' : 'error'
    ]);
});

Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show']);
Route::get('/products/{id}/similar', [ProductController::class, 'similar']);
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/banners', [BannerController::class, 'index']);
Route::post('/contact', [ContactController::class, 'store']);

// Protected
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/user/profile', [AuthController::class, 'updateProfile']);
    Route::post('/user/profile-photo', [AuthController::class, 'uploadProfilePhoto']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Cart
    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart/add', [CartController::class, 'add']);
    Route::post('/cart/update', [CartController::class, 'update']);
    Route::post('/cart/remove', [CartController::class, 'remove']);
    Route::post('/cart/clear', [CartController::class, 'clear']);

    // Checkout
    Route::post('/checkout/single', [CheckoutController::class, 'singleProductCheckout']);
    Route::post('/checkout/cart', [CheckoutController::class, 'cartCheckout']);

    // Orders
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
    Route::post('/orders/{id}/cancel', [OrderController::class, 'cancel']);
    Route::post('/orders/{id}/return', [OrderController::class, 'returnOrder']);
    Route::get('/orders/{id}/invoice', [OrderController::class, 'downloadInvoice']);

    // Admin
    Route::middleware('can:admin')->group(function () {
        Route::get('/admin/analytics', [AdminAnalyticsController::class, 'index']);
        Route::get('/admin/orders', [OrderController::class, 'adminIndex']);
        Route::post('/admin/orders/{id}/status', [OrderController::class, 'updateStatus']);
        Route::post('/admin/products', [ProductController::class, 'store']);
        Route::put('/admin/products/{id}', [ProductController::class, 'update']);
        Route::delete('/admin/products/{id}', [ProductController::class, 'destroy']);

        // Discounts
        Route::post('/admin/discounts/global', [\App\Http\Controllers\Api\DiscountController::class, 'applyGlobal']);
        Route::post('/admin/discounts/category', [\App\Http\Controllers\Api\DiscountController::class, 'applyCategory']);
    });
});
//use App\Models\Product;

Route::get('/home-data', function () {
    return response()->json([
        'status' => true,
        'products' => Product::latest()->take(8)->get()
    ]);
});


Route::get('/health', function () {
    return response()->json(['status' => 'ok']);
});
use Illuminate\Support\Facades\DB;

Route::get('/db-test', function () {
    try {
        DB::connection()->getPdo();
        return response()->json([
            'db' => 'connected'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'db' => 'failed',
            'error' => $e->getMessage()
        ], 500);
    }
});


Route::get('/home-data', function () {
    return response()->json([
        'status' => true,
        'products' => Product::latest()->take(8)->get()
    ]);
});

Route::get('/products', function () {
    return Product::all();
});
Route::get('/home-data', function () {
    return response()->json([
        'status' => true,
        'message' => 'Home API working'
    ]);
});
use Illuminate\Support\Facades\Schema;

Route::get('/products-table-test', function () {
    return response()->json([
        'exists' => Schema::hasTable('products')
    ]);
});
