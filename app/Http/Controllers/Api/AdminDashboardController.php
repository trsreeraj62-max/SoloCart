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
            $validStatuses = ['approved', 'processing', 'shipped', 'delivered'];

            // 1. Total Revenue (Valid orders only)
            $totalRevenue = Order::whereIn('status', $validStatuses)->sum('total');

            // 2. Recent Orders (with total_amount alias for frontend compatibility)
            $recentOrders = Order::with('user')->latest()->take(5)->get()
                ->map(function ($order) {
                    $order->total_amount = $order->total; // Alias for frontend
                    return $order;
                });

            // 3. Revenue Chart (Last 30 Days Trajectory)
            $revenueTrajectory = Order::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total) as revenue')
            )
            ->whereIn('status', $validStatuses)
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date', 'asc') // Chronological order
            ->get();

            $revenueChart = [
                'labels' => $revenueTrajectory->pluck('date'),
                'data' => $revenueTrajectory->pluck('revenue')
            ];

            // 4. Category Distribution (Based on items sold in valid orders)
            $categoryDist = DB::table('order_items')
                ->join('orders', 'order_items.order_id', '=', 'orders.id')
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->join('categories', 'products.category_id', '=', 'categories.id')
                ->whereIn('orders.status', $validStatuses)
                ->select('categories.name', DB::raw('COUNT(order_items.id) as item_count'))
                ->groupBy('categories.name')
                ->orderByDesc('item_count')
                ->get();

            $categoryChart = [
                'labels' => $categoryDist->pluck('name'),
                'data' => $categoryDist->pluck('item_count')
            ];

            $stats = [
                'total_revenue' => $totalRevenue,
                'total_orders' => Order::count(),
                'pending_orders' => Order::where('status', 'pending')->count(),
                'total_products' => Product::count(),
                'total_users' => User::where('role', 'user')->count(),
                'active_products' => Product::where('stock', '>', 0)->count(),
                'recent_orders' => $recentOrders,
                'low_stock_products' => Product::where('stock', '<', 10)->get(),
                'revenue_chart' => $revenueChart,
                'category_chart' => $categoryChart
            ];

            return $this->success($stats, 'Dashboard stats retrieved successfully');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Dashboard Stats Error: ' . $e->getMessage());
            return $this->error('Failed to retrieve dashboard stats', 500);
        }
    }
}
