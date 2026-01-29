<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class AdminProductController extends ApiController
{
    /**
     * Get all products for admin
     */
    public function index(Request $request)
    {
        try {
            // Start query
            $query = Product::with('category', 'images'); // No active scope for admin

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
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'required|numeric|min:0',
                'stock' => 'required|integer|min:0',
                'category_id' => 'required|exists:categories,id',
                'image' => 'nullable|image|max:5048', // 5MB max
                'specifications' => 'nullable|string',
                'is_active' => 'boolean',
                'discount_percent' => 'nullable|integer|min:0|max:100',
                'start_at' => 'nullable|date',
                'end_at' => 'nullable|date|after_or_equal:start_at',
            ]);

            // Create Product
            $productData = $request->only([
                'name', 
                'description', 
                'price', 
                'stock', 
                'category_id', 
                'specifications',
                'discount_percent',
                'start_at',
                'end_at'
            ]);
            
            $productData['slug'] = \Illuminate\Support\Str::slug($request->name) . '-' . uniqid();
            
            // Default to true if not provided (though DB defaults to true)
            $productData['is_active'] = $request->has('is_active') ? $request->is_active : true;
            
            // INHERIT CATEGORY DISCOUNT LOGIC (If no specific discount provided)
            if (!isset($productData['discount_percent']) || $productData['discount_percent'] == 0) {
                $category = \App\Models\Category::find($request->category_id);
                $now = now();
                $hasCatDiscount = $category && $category->discount_percent > 0 && 
                                 ($category->start_at && $category->start_at <= $now) &&
                                 ($category->end_at && $category->end_at >= $now);

                if ($hasCatDiscount) {
                    $productData['discount_percent'] = $category->discount_percent;
                    $productData['start_at'] = $category->start_at;
                    $productData['end_at'] = $category->end_at;
                    $productData['discount_type'] = 'percentage';
                } else {
                    $productData['discount_percent'] = 0;
                }
            }

            // ADMIN GIVING IS THE CORRECT (FINAL) PRICE
            // If any discount is active for this new product, we back-calculate the base price
            // so that: price * (1 - disc/100) = admin_price
            if (isset($productData['discount_percent']) && $productData['discount_percent'] > 0) {
                $adminPrice = (float) $productData['price'];
                $discountFactor = 1 - ($productData['discount_percent'] / 100);
                if ($discountFactor > 0) {
                    $productData['price'] = round($adminPrice / $discountFactor, 2);
                    Log::info("Admin Product Store: Back-calculated base price for {$productData['name']} from {$adminPrice} to {$productData['price']} due to {$productData['discount_percent']}% discount.");
                }
            }
            
            $product = Product::create($productData);

            // Handle Image Upload
            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('products', 'public');
                
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $path,
                    'is_primary' => true
                ]);
            }

            // Reload to include images
            $product->load('images', 'category');

            // Clear home cache for immediate updates
            \Illuminate\Support\Facades\Cache::forget('home_data');

            return $this->success($product, 'Product created successfully', 201);

        } catch (ValidationException $e) {
            return $this->error('Validation failed', 422, $e->errors());
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

            // Allow 'image' to be a file or string
            $rules = [
                'name' => 'string|max:255',
                'price' => 'numeric|min:0',
                'stock' => 'integer|min:0',
                'category_id' => 'exists:categories,id',
                'is_active' => 'boolean',
                'discount_percent' => 'nullable|integer|min:0|max:100',
                'start_at' => 'nullable|date',
                'end_at' => 'nullable|date|after_or_equal:start_at',
            ];

            if ($request->hasFile('image')) {
                $rules['image'] = 'image|max:5048';
            } else {
                $rules['image'] = 'nullable|string';
            }

            $request->validate($rules);

            $data = $request->except(['image', 'slug']);
            
            // Ensure discount_percent is never null (DB column is not nullable)
            if (array_key_exists('discount_percent', $data) && is_null($data['discount_percent'])) {
                $data['discount_percent'] = 0;
            }

            $product->update($data);

            // Handle Image Update
            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('products', 'public');
                Log::info("Admin Product Image Uploaded to: " . $path);
                
                // Optional: Unset previous primary
                ProductImage::where('product_id', $product->id)->update(['is_primary' => false]);

                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $path,
                    'is_primary' => true
                ]);
            }

            $product->load('images', 'category');

            // Clear home cache for immediate updates
            \Illuminate\Support\Facades\Cache::forget('home_data');

            return $this->success($product, 'Product updated successfully');

        } catch (ValidationException $e) {
             return $this->error('Validation failed', 422, $e->errors());
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

            // Optionally delete images from storage
            foreach($product->images as $image) {
                Storage::disk('public')->delete($image->image_path);
            }
            $product->images()->delete();

            $product->delete();

            // Clear home cache for immediate updates
            \Illuminate\Support\Facades\Cache::forget('home_data');

            return $this->success(null, 'Product deleted successfully');
        } catch (\Exception $e) {
            Log::error('Admin Product Delete Error: ' . $e->getMessage());
            return $this->error('Failed to delete product', 500);
        }
    }
}
