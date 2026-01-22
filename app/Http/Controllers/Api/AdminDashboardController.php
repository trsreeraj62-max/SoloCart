<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends ApiController
{
    public function stats()
    {
        try {
            $stats = [
                'total_revenue' => Order::where('status', 'delivered')->sum('total'),
                'total_orders' => Order::count(),
                'pending_orders' => Order::where('status', 'pending')->count(),
                'total_products' => Product::count(),
                'total_users' => User::where('role', 'user')->count(),
                'recent_orders' => Order::with('user')->latest()->take(5)->get(),
                'low_stock_products' => Product::where('stock', '<', 10)->get()
            ];

            return $this->success($stats, 'Dashboard stats retrieved successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to retrieve dashboard stats', 500);
        }
    }
}
