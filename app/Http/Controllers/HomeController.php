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
        $banners = Banner::all();
        $categories = Category::all();
        $featuredProducts = Product::with(['category', 'images'])->where('stock', '>', 0)->latest()->take(8)->get();
        
        // Simple recommendation logic
        $recommendedProducts = Product::with(['category', 'images'])
            ->where('stock', '>', 0)
            ->inRandomOrder()
            ->take(4)
            ->get();

        return view('home', compact('banners', 'categories', 'featuredProducts', 'recommendedProducts'));
    }
}
