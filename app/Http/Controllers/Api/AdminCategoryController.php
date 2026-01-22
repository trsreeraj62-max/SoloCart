<?php

namespace App\Http\Controllers\Api;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class AdminCategoryController extends ApiController
{
    /**
     * Get all categories for admin
     */
    public function index()
    {
        try {
            $categories = Category::withCount('products')->latest()->get();
            return $this->success($categories, "Categories retrieved");
        } catch (\Exception $e) {
            Log::error('Admin Category Index Error: ' . $e->getMessage());
            return $this->error("Failed to retrieve categories", 500);
        }
    }

    /**
     * Store a newly created category
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:categories,name',
                'description' => 'nullable|string',
                'image' => 'nullable|image|max:2048'
            ]);

            $data = $request->only(['name', 'description']);
            $data['slug'] = \Illuminate\Support\Str::slug($request->name);

            // Handle Image
            if ($request->hasFile('image')) {
                $data['image'] = $request->file('image')->store('categories', 'public');
            }

            $category = Category::create($data);

            return $this->success($category, "Category created successfully", 201);

        } catch (ValidationException $e) {
            return $this->error("Validation failed", 422, $e->errors());
        } catch (\Exception $e) {
            Log::error('Admin Category Store Error: ' . $e->getMessage());
            return $this->error("Failed to create category", 500);
        }
    }

    /**
     * Update the specified category
     */
    public function update(Request $request, $id)
    {
        try {
            $category = Category::find($id);

            if (!$category) {
                return $this->error("Category not found", 404);
            }

            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:categories,name,' . $id,
                'description' => 'nullable|string',
                'image' => 'nullable|image|max:2048'
            ]);

            $data = $request->only(['name', 'description']);
            if ($request->filled('name')) {
                $data['slug'] = \Illuminate\Support\Str::slug($request->name);
            }

            // Handle Image
            if ($request->hasFile('image')) {
                // Delete old image
                if ($category->image) {
                    Storage::disk('public')->delete($category->image);
                }
                $data['image'] = $request->file('image')->store('categories', 'public');
            }

            $category->update($data);

            return $this->success($category, "Category updated successfully");

        } catch (ValidationException $e) {
            return $this->error("Validation failed", 422, $e->errors());
        } catch (\Exception $e) {
            Log::error('Admin Category Update Error: ' . $e->getMessage());
            return $this->error("Failed to update category", 500);
        }
    }

    /**
     * Remove the specified category
     */
    public function destroy($id)
    {
        try {
            $category = Category::find($id);

            if (!$category) {
                return $this->error("Category not found", 404);
            }

            // Check if has products
            if ($category->products()->count() > 0) {
                return $this->error("Cannot delete category with associated products. Delete products first.", 400);
            }

            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }

            $category->delete();

            return $this->success(null, "Category deleted successfully");
        } catch (\Exception $e) {
            Log::error('Admin Category Delete Error: ' . $e->getMessage());
            return $this->error("Failed to delete category", 500);
        }
    }
}
