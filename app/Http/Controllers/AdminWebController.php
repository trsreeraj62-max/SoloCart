<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Category;
use Illuminate\Support\Facades\DB;

class AdminWebController extends Controller
{
    public function dashboard()
    {
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
        ->orderBy('date', 'asc') // proper order for chart
        ->take(7)
        ->get();

        return view('admin.dashboard', compact('stats', 'revenueData'));
    }

    public function products()
    {
        $products = Product::with('category')->latest()->paginate(20);
        return view('admin.products', compact('products'));
    }

    public function orders()
    {
        $orders = Order::with('user')->latest()->paginate(20);
        return view('admin.orders', compact('orders'));
    }

    public function users()
    {
        $users = User::latest()->paginate(20);
        return view('admin.users', compact('users'));
    }
}
