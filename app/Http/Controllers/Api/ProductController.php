<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends ApiController
{
    /**
     * Get all products with filters
     */
    public function index(Request $request)
    {
        try {
            $query = Product::with(['category', 'images'])->active();

            if ($request->filled('category_id')) {
                $query->where('category_id', $request->category_id);
            }

            if ($request->filled('search')) {
                $query->where('name', 'like', '%' . $request->search . '%');
            }

            if ($request->filled('min_price')) {
                $query->where('price', '>=', $request->min_price);
            }

            if ($request->filled('max_price')) {
                $query->where('price', '<=', $request->max_price);
            }

            // Return all or paginate
            if ($request->has('all')) {
                $products = $query->latest()->get();
            } else {
                $products = $query->latest()->paginate(20);
            }

            return $this->success($products, "Products retrieved");
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('API Product Index Error: ' . $e->getMessage());
            return $this->error("Failed to load products");
        }
    }

    /**
     * Get single product
     */
    public function show($id)
    {
        $product = Product::with(['category', 'images'])->find($id);

        if (!$product) {
            return $this->error("Product not found", 404);
        }

        return $this->success($product, "Product details retrieved");
    }

    /**
     * Get similar products
     */
    public function similar($id)
    {
        $product = Product::find($id);
        if (!$product) return $this->error("Product not found", 404);

        $similar = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $id)
            ->active()
            ->take(4)
            ->get();

        return $this->success($similar, "Similar products retrieved");
    }

    /**
     * Admin: Store product
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
        ]);

        $data = $request->all();
        $data['slug'] = \Illuminate\Support\Str::slug($request->name) . '-' . rand(100, 999);
        $product = Product::create($data);

        return $this->success($product, "Product created", 201);
    }

    /**
     * Admin: Update product
     */
    public function update(Request $request, $id)
    {
        $product = Product::find($id);
        if (!$product) return $this->error("Product not found", 404);

        $product->update($request->all());

        return $this->success($product, "Product updated");
    }

    /**
     * Admin: Delete product
     */
    public function destroy($id)
    {
        $product = Product::find($id);
        if (!$product) return $this->error("Product not found", 404);

        $product->delete();

        return $this->success([], "Product deleted");
    }
}
