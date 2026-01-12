<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
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
    }

    public function show($id)
    {
        $product = Product::with('category', 'images')->findOrFail($id);
        return response()->json($product);
    }

    public function similar($id)
    {
        $product = Product::findOrFail($id);
        $similar = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $id)
            ->with('images')
            ->inRandomOrder()
            ->take(6)
            ->get();
            
        return response()->json($similar);
    }

    // Admin methods
    public function store(Request $request) {
        $validated = $request->validate([
            'name' => 'required',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'slug' => 'required|unique:products',
            'discount_percent' => 'nullable|integer'
        ]);
        
        $product = Product::create($validated);
        
        // Handle images if provided in request (simple placeholder logic for now)
        // In real app, we handle file upload here.

        return response()->json($product, 201);
    }

    public function update(Request $request, $id) {
        $product = Product::findOrFail($id);
        $product->update($request->all());
        return response()->json($product);
    }

    public function destroy($id) {
        Product::destroy($id);
        return response()->json(['message' => 'Product deleted successfully']);
    }
}
