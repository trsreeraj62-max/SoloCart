<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Product::with('category', 'images');
    
            // Search
            if ($request->filled('search')) {
                $query->where('name', 'like', '%' . $request->search . '%');
            }
    
            // Category Filter
            if ($request->filled('category_id')) {
                $query->where('category_id', $request->category_id);
            }
    
            // Price Filter
            if ($request->filled('min_price')) {
                $query->where('price', '>=', $request->min_price);
            }
            if ($request->filled('max_price')) {
                $query->where('price', '<=', $request->max_price);
            }
    
            $products = $query->paginate(12);
    
            return response()->json($products);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch products', 'message' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        try {
            $product = Product::with('category', 'images')->findOrFail($id);
            return response()->json($product);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Product not found', 'message' => $e->getMessage()], 404);
        }
    }

    public function similar($id)
    {
        try {
            $product = Product::findOrFail($id);
            $similar = Product::where('category_id', $product->category_id)
                ->where('id', '!=', $id)
                ->with('images')
                ->inRandomOrder()
                ->take(6)
                ->get();
                
            return response()->json($similar);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch similar products', 'message' => $e->getMessage()], 500);
        }
    }

    // Admin methods
    public function store(Request $request) {
        try {
            $validated = $request->validate([
                'name' => 'required',
                'category_id' => 'required|exists:categories,id',
                'price' => 'required|numeric',
                'stock' => 'required|integer',
                'slug' => 'required|unique:products',
                'discount_percent' => 'nullable|integer'
            ]);
            
            $product = Product::create($validated);
            
            return response()->json($product, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to create product', 'message' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id) {
        try {
            $product = Product::findOrFail($id);
            $product->update($request->all());
            return response()->json($product);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update product', 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy($id) {
        try {
            Product::destroy($id);
            return response()->json(['message' => 'Product deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete product', 'message' => $e->getMessage()], 500);
        }
    }
}
