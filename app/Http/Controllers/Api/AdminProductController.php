<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AdminProductController extends ApiController
{
    /**
     * Get all products for admin
     */
    public function index(Request $request)
    {
        try {
            // Start query without global scopes if necessary, or just standard
            $query = Product::with('category', 'images');

            // Search functionality
            if ($request->filled('search')) {
                $query->where('name', 'like', '%' . $request->search . '%')
                      ->orWhere('description', 'like', '%' . $request->search . '%');
            }

            // Category filter
            if ($request->filled('category_id')) {
                $query->where('category_id', $request->category_id);
            }

            // Pagination
            $products = $query->latest()->paginate(20);

            return $this->success($products, 'Products retrieved successfully');
        } catch (\Exception $e) {
            Log::error('Admin Product Index Error: ' . $e->getMessage());
            return $this->error('Failed to retrieve products', 500);
        }
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'required|numeric|min:0',
                'stock' => 'required|integer|min:0',
                'category_id' => 'required|exists:categories,id',
                'image' => 'nullable|image|max:2048'
            ]);

            $data = $request->except('image');
            $data['slug'] = \Illuminate\Support\Str::slug($request->name) . '-' . uniqid();

            // Handle image upload if present (Assuming standard handling or separate)
            // For now, basic creation
            
            $product = Product::create($data);

            return $this->success($product, 'Product created successfully', 201);
        } catch (\Exception $e) {
            Log::error('Admin Product Store Error: ' . $e->getMessage());
            return $this->error('Failed to create product', 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $product = Product::with('category', 'images')->find($id);

            if (!$product) {
                return $this->error('Product not found', 404);
            }

            return $this->success($product, 'Product retrieved successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to retrieve product', 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $product = Product::find($id);

            if (!$product) {
                return $this->error('Product not found', 404);
            }

            $request->validate([
                'name' => 'string|max:255',
                'price' => 'numeric|min:0',
                'stock' => 'integer|min:0',
                'category_id' => 'exists:categories,id',
            ]);

            $product->update($request->all());

            return $this->success($product, 'Product updated successfully');
        } catch (\Exception $e) {
            Log::error('Admin Product Update Error: ' . $e->getMessage());
            return $this->error('Failed to update product', 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $product = Product::find($id);

            if (!$product) {
                return $this->error('Product not found', 404);
            }

            $product->delete();

            return $this->success(null, 'Product deleted successfully');
        } catch (\Exception $e) {
            Log::error('Admin Product Delete Error: ' . $e->getMessage());
            return $this->error('Failed to delete product', 500);
        }
    }
}
