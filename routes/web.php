<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductWebController;
use App\Http\Controllers\CartWebController;
use App\Http\Controllers\OrderWebController;
use App\Http\Controllers\AdminWebController;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/products', [ProductWebController::class, 'index'])->name('products.index');
Route::get('/products/{slug}', [ProductWebController::class, 'show'])->name('products.show');

// Static Pages
Route::get('/about', function () { return view('about'); })->name('about');
Route::get('/contact', function () { return view('contact'); })->name('contact');

Route::middleware(['auth'])->group(function () {
    Route::get('/cart', [CartWebController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [CartWebController::class, 'add'])->name('cart.add');
    Route::delete('/cart/remove', [CartWebController::class, 'remove'])->name('cart.remove');
    Route::delete('/cart/remove', [CartWebController::class, 'remove'])->name('cart.remove');
    
    Route::get('/checkout', [OrderWebController::class, 'checkout'])->name('checkout.index');
    Route::get('/checkout/single', [OrderWebController::class, 'checkout'])->name('checkout.single');
    Route::post('/checkout/process', [OrderWebController::class, 'store'])->name('checkout.store');
    Route::get('/checkout/pay/{id}', [OrderWebController::class, 'paymentPage'])->name('checkout.pay');
    Route::post('/checkout/pay/{id}/confirm', [OrderWebController::class, 'confirmPayment'])->name('checkout.confirm');

    Route::get('/orders', [OrderWebController::class, 'index'])->name('orders.index');
    Route::get('/orders/{id}', [OrderWebController::class, 'show'])->name('orders.show');
    
    // Order Actions
    Route::post('/orders/{id}/cancel', [OrderWebController::class, 'cancel'])->name('orders.cancel');
    Route::post('/orders/{id}/return', [OrderWebController::class, 'returnOrder'])->name('orders.return');
    Route::get('/orders/{id}/invoice', [OrderWebController::class, 'downloadInvoice'])->name('orders.invoice');
    
    Route::get('/profile', function () { return view('profile.edit'); })->name('profile.edit');
    Route::post('/profile', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
});

// Auth Pages
Route::get('/login', function () { return view('auth.login'); })->name('login');
Route::post('/login', [App\Http\Controllers\AuthWebController::class, 'login']);
Route::get('/register', function () { return view('auth.register'); })->name('register');
Route::post('/register', [App\Http\Controllers\AuthWebController::class, 'register']);
Route::get('/otp-verify', function () { return view('auth.otp'); })->name('otp.verify');
Route::post('/otp-verify', [App\Http\Controllers\AuthWebController::class, 'verifyOtp']);
Route::post('/logout', [App\Http\Controllers\AuthWebController::class, 'logout'])->name('logout');

// Admin Panel
Route::prefix('admin')->name('admin.')->group(function () {
    // We will add middleware 'can:admin' later or handling it in controller
    Route::get('/login', function () { return view('admin.auth.login'); })->name('login');
    
    Route::middleware(['auth'])->group(function () {
        Route::get('/dashboard', [AdminWebController::class, 'dashboard'])->name('dashboard');
        
        // Products
        Route::get('/products', [AdminWebController::class, 'products'])->name('products.index');
        Route::post('/products', [AdminWebController::class, 'storeProduct'])->name('products.store');
        Route::put('/products/{id}', [AdminWebController::class, 'updateProduct'])->name('products.update');
        Route::delete('/products/{id}', [AdminWebController::class, 'destroyProduct'])->name('products.destroy');
        
        // Categories
        Route::get('/categories', [AdminWebController::class, 'categories'])->name('categories.index');
        Route::post('/categories', [AdminWebController::class, 'storeCategory'])->name('categories.store');
        
        // Orders
        Route::get('/orders', [AdminWebController::class, 'orders'])->name('orders.index');
        Route::post('/orders/{id}/status', [AdminWebController::class, 'updateStatus'])->name('orders.status');
        
        // Users
        Route::get('/users', [AdminWebController::class, 'users'])->name('users.index');
        Route::post('/users/{id}/suspend', [AdminWebController::class, 'suspendUser'])->name('users.suspend');
        Route::delete('/users/{id}', [AdminWebController::class, 'destroyUser'])->name('users.destroy');

        // Banners
        Route::get('/banners', [AdminWebController::class, 'banners'])->name('banners.index');
        Route::post('/banners', [AdminWebController::class, 'storeBanner'])->name('banners.store');
        Route::put('/banners/{id}', [AdminWebController::class, 'updateBanner'])->name('banners.update');
        Route::delete('/banners/{id}', [AdminWebController::class, 'destroyBanner'])->name('banners.destroy');

        // Discounts
        Route::get('/discounts', [AdminWebController::class, 'discounts'])->name('discounts.index');
        Route::post('/discounts/global', [AdminWebController::class, 'applyGlobalDiscount'])->name('discounts.global');
        Route::post('/discounts/category', [AdminWebController::class, 'applyCategoryDiscount'])->name('discounts.category');
    });
});
