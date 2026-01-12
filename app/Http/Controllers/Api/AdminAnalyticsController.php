<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AdminAnalyticsController extends ApiController
{
    /**
     * Get admin dashboard stats
     */
    public function index()
    {
        $stats = [
            'overview' => [
                'total_revenue' => Order::where('status', 'delivered')->sum('total'),
                'pending_orders' => Order::where('status', 'pending')->count(),
                'total_products' => Product::count(),
                'total_users' => User::count(),
            ],
            'recent_orders' => Order::with('user')->latest()->take(5)->get(),
            'revenue_chart' => Order::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total) as revenue')
            )
            ->where('status', 'delivered')
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->take(7)
            ->get()
        ];

        return $this->success($stats, "Analytics retrieved");
    }
}
