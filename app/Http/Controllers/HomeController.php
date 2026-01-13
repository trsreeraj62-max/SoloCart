<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Banner;
use App\Models\Category;
use App\Models\Product;

class HomeController extends Controller
{
    public function index()
    {
        try {
            $banners = Banner::all();
            $categories = Category::has('products')->get();
            $latestProducts = Product::with(['category', 'images'])
                ->where('stock', '>', 0)
                ->latest()
                ->take(8)
                ->get();
            
            $featuredProducts = Product::with(['category', 'images'])
                ->where('stock', '>', 0)
                ->inRandomOrder()
                ->take(4)
                ->get();
    
            return view('home', compact('banners', 'categories', 'latestProducts', 'featuredProducts'));
        } catch (\Exception $e) {
             \Illuminate\Support\Facades\Log::error('Home Error: ' . $e->getMessage());
             return view('home', [
                 'banners' => [],
                 'categories' => [],
                 'latestProducts' => [],
                 'featuredProducts' => []
             ])->with('error', 'Unable to load some content.');
        }
    }
}
