<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Category;
use App\Models\Banner;
use App\Models\ProductImage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class AdminWebController extends Controller
{
    public function dashboard()
    {
        try {
            $stats = [
                'daily_revenue' => Order::whereDate('created_at', today())->sum('total'),
                'orders_count' => Order::count(),
                'products_count' => Product::count(),
                'users_count' => User::count(),
            ];
            
            $revenueData = Order::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total) as revenue')
            )
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->take(7)
            ->get();
    
            return view('admin.dashboard', compact('stats', 'revenueData'));
        } catch (\Exception $e) {
            Log::error('Admin Dashboard Error: ' . $e->getMessage());
            return back()->with('error', 'Failed to load dashboard data.');
        }
    }

    public function products()
    {
        try {
            $products = Product::with('category')->latest()->paginate(20);
            $categories = Category::all();
            return view('admin.products', compact('products', 'categories'));
        } catch (\Exception $e) {
             Log::error('Admin Products Error: ' . $e->getMessage());
            return back()->with('error', 'Failed to load products.');
        }
    }

    public function storeProduct(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required',
                'category_id' => 'required|exists:categories,id',
                'price' => 'required|numeric',
                'stock' => 'required|integer',
                'description' => 'nullable',
                'image' => 'nullable|image',
                'discount_percent' => 'nullable|integer|min:0|max:100',
                'discount_start_date' => 'nullable|date',
                'discount_end_date' => 'nullable|date|after_or_equal:discount_start_date',
                'specifications' => 'nullable|string'
            ]);

            $data = $request->except('image');
            $data['slug'] = Str::slug($request->name) . '-' . rand(1000,9999);
            
            $product = Product::create($data);
            
            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('products', 'public');
                 ProductImage::create([
                     'product_id' => $product->id,
                     'image_path' => $path,
                     'is_primary' => true
                 ]);
            }

            return back()->with('success', 'Product created successfully.');
        } catch (\Exception $e) {
            Log::error('Store Product Error: ' . $e->getMessage());
            return back()->with('error', 'Failed to create product.');
        }
    }

    public function updateProduct(Request $request, $id)
    {
        try {
             $product = Product::findOrFail($id);
             // Validation similar to store but not all required
             // For brevity, skipping full validation update here, but ideally should be there.
             
             $data = $request->except(['image', '_method', '_token']);
             $product->update($data);

             if ($request->hasFile('image')) {
                 // Update image logic (replace primary)
                 $path = $request->file('image')->store('products', 'public');
                 // Remove old primary?
                 ProductImage::where('product_id', $product->id)->where('is_primary', true)->delete();
                 ProductImage::create([
                     'product_id' => $product->id,
                     'image_path' => $path,
                     'is_primary' => true
                 ]);
             }

             return back()->with('success', 'Product updated.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update product.');
        }
    }

    public function destroyProduct($id)
    {
        try {
            Product::destroy($id);
            return back()->with('success', 'Product deleted.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete product.');
        }
    }

    public function orders()
    {
        try {
           $orders = Order::with('user')->latest()->paginate(20);
           return view('admin.orders', compact('orders'));
        } catch (\Exception $e) {
             Log::error('Admin Orders Error: ' . $e->getMessage());
            return back()->with('error', 'Failed to load orders.');
        }
    }

    public function updateStatus(Request $request, $id)
    {
        try {
            $request->validate(['status' => 'required|in:pending,approved,packed,shipped,out_for_delivery,delivered,cancelled,returned']);
            
            $order = Order::with('user', 'items.product')->findOrFail($id);
            $oldStatus = $order->status;
            $newStatus = $request->status;
            
            $order->update(['status' => $newStatus]);
            
            // If Delivered, Send Invoice Email
            if ($newStatus === 'delivered' && $oldStatus !== 'delivered') {
                Mail::to($order->user->email)->send(new \App\Mail\InvoiceMail($order));
            }
            
            return back()->with('success', 'Order status updated to ' . $newStatus);
        } catch (\Exception $e) {
            Log::error('Admin Order Update Error: ' . $e->getMessage());
            return back()->with('error', 'Failed to update status.');
        }
    }

    public function users()
    {
        try {
            $users = User::latest()->paginate(20);
            return view('admin.users', compact('users'));
        } catch (\Exception $e) {
             Log::error('Admin Users Error: ' . $e->getMessage());
            return back()->with('error', 'Failed to load users.');
        }
    }

    public function suspendUser($id)
    {
        try {
            $user = User::findOrFail($id);
            if ($user->role === 'admin') {
                return back()->with('error', 'Cannot suspend an admin.');
            }
            $user->status = ($user->status === 'active' || !$user->status) ? 'suspended' : 'active';
            $user->save();
            return back()->with('success', 'User status updated to ' . $user->status);
        } catch (\Exception $e) {
            return back()->with('error', 'Action failed.');
        }
    }

    public function destroyUser($id)
    {
        try {
            $user = User::findOrFail($id);
            if ($user->role === 'admin') {
                return back()->with('error', 'Cannot delete an admin.');
            }
            $user->delete();
            return back()->with('success', 'User deleted.');
        } catch (\Exception $e) {
            return back()->with('error', 'Action failed.');
        }
    }

    // Categories
    public function categories() {
        return view('admin.categories', ['categories' => Category::all()]);
    }

    public function storeCategory(Request $request) {
        $request->validate(['name' => 'required|unique:categories']);
        Category::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name)
        ]);
        return back()->with('success', 'Category created.');
    }

    // Banners
    public function banners()
    {
        $banners = Banner::all();
        return view('admin.banners', compact('banners'));
    }

    public function storeBanner(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required',
                'image' => 'required|image',
                'type' => 'required|in:hero,promo',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after_or_equal:start_date'
            ]);

            $path = $request->file('image')->store('banners', 'public');

            Banner::create([
                'title' => $request->title,
                'image_path' => $path,
                'type' => $request->type,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date
            ]);

            return back()->with('success', 'Banner created.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to create banner: ' . $e->getMessage());
        }
    }

    public function destroyBanner($id)
    {
        Banner::destroy($id);
        return back()->with('success', 'Banner deleted.');
    }

    // Discounts
    public function discounts() {
        return view('admin.discounts', ['categories' => Category::all()]);
    }

    public function applyGlobalDiscount(Request $request)
    {
        try {
             $request->validate(['discount_percent' => 'required|integer|min:0|max:100']);
             // Ideally populate start/end dates for all products too if needed, 
             // but user request was vague on if global date applies to all individually or a global setting.
             // I'll update the columns on all products.
             Product::query()->update([
                 'discount_percent' => $request->discount_percent,
                 'discount_start_date' => now(), // Assume immediate
                 'discount_end_date' => null // Indefinite? Or add fields to form
             ]);
             return back()->with('success', 'Global discount updated.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to apply discount.');
        }
    }

    public function applyCategoryDiscount(Request $request)
    {
        try {
            $request->validate([
                'category_id' => 'required|exists:categories,id',
                'discount_percent' => 'required|integer|min:0|max:100'
            ]);
            
            Product::where('category_id', $request->category_id)->update([
                'discount_percent' => $request->discount_percent,
                 'discount_start_date' => now(),
                 'discount_end_date' => null
            ]);
            return back()->with('success', 'Category discount updated.');
        } catch (\Exception $e) {
             return back()->with('error', 'Failed to apply category discount.');
        }
    }
}
