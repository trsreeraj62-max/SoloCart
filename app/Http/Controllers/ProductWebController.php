<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;

class ProductWebController extends Controller
{
    public function index(Request $request)
    {
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
    }

    public function show($slugOrId)
    {
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
    }
}
