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
    
            $categoryId = $request->input('category_id') ?? $request->input('category');
            if ($categoryId) {
                $query->where('category_id', $categoryId);
            }
            
            $minPrice = $request->input('min_price') ?? $request->input('min');
            if ($minPrice) {
                 $query->where('price', '>=', $minPrice);
            }
            
            $maxPrice = $request->input('max_price') ?? $request->input('max');
            if ($maxPrice) {
                 $query->where('price', '<=', $maxPrice);
            }
    
            $products = $query->paginate(12);
            $categories = Category::all();
    
            if ($request->ajax()) {
                $html = '';
                foreach ($products as $product) {
                    $html .= view('components.product-card', compact('product'))->render();
                }
                return response()->json([
                    'html' => $html,
                    'next_page' => $products->nextPageUrl()
                ]);
            }

            return view('products.index', compact('products', 'categories'));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Product Index Error: ' . $e->getMessage());
            if ($request->ajax()) {
                return response()->json(['error' => 'An error occurred.'], 500);
            }
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
