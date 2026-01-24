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
    AdminOrderController,
    ProfileController
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

// Temporary route for Render free tier migration & admin fix
Route::get('/system/maintenance', function() {
    if (request('key') !== 'render_fix_2026') {
        return response()->json(['error' => 'Unauthorized'], 403);
    }
    
    $output = [];

    // 1. Run Migrations
    if (request()->has('migrate')) {
        try {
            \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
            $output['migration'] = \Illuminate\Support\Facades\Artisan::output();
        } catch (\Exception $e) {
            $output['migration_error'] = $e->getMessage();
        }
    }

    // 2. Clear Cache
    if (request()->has('optimize')) {
        try {
            \Illuminate\Support\Facades\Artisan::call('optimize:clear');
            $output['optimization'] = \Illuminate\Support\Facades\Artisan::output();
        } catch (\Exception $e) {
            $output['optimization_error'] = $e->getMessage();
        }
    }

    // 3. Promote User to Admin
    if (request()->has('promote_id')) {
        $id = request('promote_id');
        $user = \App\Models\User::find($id);
        if ($user) {
            $user->role = 'admin'; // strict assignment
            $user->save();
            $output['admin_promotion'] = "User ID {$id} is now ADMIN.";
        } else {
            $output['admin_promotion'] = "User ID {$id} not found.";
        }
    }

    if (request()->has('promote_email')) {
        $email = request('promote_email');
        $user = \App\Models\User::where('email', $email)->first();
        if ($user) {
            $user->role = 'admin';
            $user->save();
            $output['admin_promotion'] = "User {$email} (ID: {$user->id}) is now ADMIN.";
        } else {
            $output['admin_promotion'] = "User email {$email} not found.";
        }
    }

    // 4. Create Storage Symlink (for local uploads)
    if (request()->has('link_storage')) {
        try {
            \Illuminate\Support\Facades\Artisan::call('storage:link');
            $output['storage_link'] = 'Symlink created successfully';
        } catch (\Exception $e) {
            $output['storage_link_error'] = $e->getMessage();
        }
    }

    // 5. Debug: Check if orders exist
    if (request()->has('debug_orders')) {
        $connection = \Illuminate\Support\Facades\DB::connection();
        
        $output['debug_orders'] = [
            'database_connection' => [
                'name' => $connection->getName(),
                'driver' => $connection->getDriverName(),
                'host' => $connection->getConfig('host'),
                'database' => $connection->getDatabaseName(),
            ],
            'table_status' => [
                'orders_exists' => \Illuminate\Support\Facades\Schema::hasTable('orders'),
                'order_items_exists' => \Illuminate\Support\Facades\Schema::hasTable('order_items'),
                'users_exists' => \Illuminate\Support\Facades\Schema::hasTable('users'),
            ],
            'users_stats' => \App\Models\User::withCount('orders')->get()->map(function($u) {
                return [
                    'id' => $u->id,
                    'email' => $u->email,
                    'role' => $u->role,
                    'orders_count' => $u->orders_count
                ];
            }),
            'total_orders_count' => \App\Models\Order::count(),
            'recent_orders' => \App\Models\Order::with(['user:id,name,email', 'items.product:id,name,price'])
                ->latest()
                ->take(5)
                ->get()
                ->map(function($order) {
                    return [
                        'id' => $order->id,
                        'user_id' => $order->user_id,
                        'user_email' => $order->user ? $order->user->email : 'N/A',
                        'total' => $order->total,
                        'status' => $order->status,
                        'created_at' => $order->created_at->toDateTimeString(),
                        'items_count' => $order->items->count(),
                    ];
                }),
        ];
    }

    // 6. Create Test Order
    if (request()->has('create_test_order')) {
        try {
            // Find or create a test user
            $testUser = \App\Models\User::where('email', 'testbuyer@test.com')->first();
            if (!$testUser) {
                $testUser = \App\Models\User::create([
                    'name' => 'Test Buyer',
                    'email' => 'testbuyer@test.com',
                    'password' => \Illuminate\Support\Facades\Hash::make('password'),
                    'role' => 'user',
                    'email_verified_at' => now(),
                ]);
            }

            // Get first available product
            $product = \App\Models\Product::where('stock', '>', 0)->first();
            
            if (!$product) {
                $output['test_order'] = 'No products available in stock';
            } else {
                $order = \App\Models\Order::create([
                    'user_id' => $testUser->id,
                    'status' => 'pending',
                    'total' => $product->price + 70, // price + shipping + fee
                    'address' => '123 Test Street, Test City, 12345',
                    'payment_method' => 'cod',
                    'payment_status' => 'unpaid',
                ]);

                \App\Models\OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => 1,
                    'price' => $product->price,
                ]);

                $output['test_order'] = [
                    'success' => true,
                    'order_id' => $order->id,
                    'order' => $order->load(['user', 'items.product']),
                    'message' => 'Test order created successfully'
                ];
            }
        } catch (\Exception $e) {
            $output['test_order'] = [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    // 7. Create Test User Account
    if (request()->has('create_user')) {
        $email = request('email', 'trsreeraj07@gmail.com');
        $name = request('name', 'sreeraj');
        $password = request('password', 'sreeraj');
        $role = request('role', 'user'); // 'user' or 'admin'
        
        $existingUser = \App\Models\User::where('email', $email)->first();
        
        if ($existingUser) {
            $output['user_creation'] = "User {$email} already exists (ID: {$existingUser->id}, Role: {$existingUser->role})";
        } else {
            $user = \App\Models\User::create([
                'name' => $name,
                'email' => $email,
                'password' => \Illuminate\Support\Facades\Hash::make($password),
                'role' => $role,
                'email_verified_at' => now(), // Auto-verify for testing
            ]);
            
            $output['user_creation'] = "User created successfully! Email: {$email}, Password: {$password}, Role: {$role}, ID: {$user->id}";
        }
    }

    return response()->json([
        'success' => true,
        'message' => 'System maintenance executed',
        'output' => $output
    ]);
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
    Route::get('/profile', [ProfileController::class, 'show']); 
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // Profile
    Route::post('/profile/update', [ProfileController::class, 'update']);

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
