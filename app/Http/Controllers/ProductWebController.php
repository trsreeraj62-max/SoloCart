<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;

class ProductWebController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Product::with(['category', 'images']);
    
            if ($request->filled('search')) {
                $query->where('name', 'like', '%' . $request->search . '%');
            }
    
            if ($request->filled('category_id')) {
                $query->where('category_id', $request->category_id);
            }
            
            if ($request->filled('min_price')) {
                 $query->where('price', '>=', $request->min_price);
            }
            if ($request->filled('max_price')) {
                 $query->where('price', '<=', $request->max_price);
            }
    
            $products = $query->paginate(12);
            $categories = Category::all();
    
            return view('products.index', compact('products', 'categories'));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Product Index Error: ' . $e->getMessage());
            return back()->with('error', 'Unable to load products. Please try again.');
        }
    }

    public function show($slugOrId)
    {
        try {
            $product = Product::with(['category', 'images'])->where('slug', $slugOrId)->first();
            if (!$product) {
                $product = Product::with(['category', 'images'])->findOrFail($slugOrId);
            }
            
            $similarProducts = Product::where('category_id', $product->category_id)
                ->where('id', '!=', $product->id)
                ->with(['images'])
                ->take(4)
                ->get();
    
            return view('products.show', compact('product', 'similarProducts'));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Product Show Error: ' . $e->getMessage());
            return redirect()->route('products.index')->with('error', 'Product not found or unavailable.');
        }
    }
}
