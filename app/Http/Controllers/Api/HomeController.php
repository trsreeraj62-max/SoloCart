<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    public function index()
    {
        // Cache home data for 10 minutes to improve performance
        return Cache::remember('home_data', 600, function () {
            $products = Product::with(['category', 'images'])
                ->active()
                ->get();

            $categoriesQuery = Category::query();
            if (\Illuminate\Support\Facades\Schema::hasColumn('categories', 'status')) {
                $categoriesQuery->where('status', 1);
            }
            $categories = $categoriesQuery->get();

            return response()->json([
                'success' => true,
                'message' => 'Home data retrieved',
                'data' => [
                    'banners' => Banner::all(),
                    'categories' => $categories,
                    'products' => $products->take(20),
                    'featured_products' => $products->shuffle()->take(8),
                    'latest_products' => $products->sortByDesc('created_at')->take(8),
                ]
            ]);
        });
    }

    public function healthCheck()
    {
        return response()->json([
            'status' => 'ok',
            'timestamp' => now(),
            'service' => 'SoloCart Backend'
        ]);
    }
}
