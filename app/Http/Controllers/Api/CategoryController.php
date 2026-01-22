<?php

namespace App\Http\Controllers\Api;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends ApiController
{
    /**
     * Get all categories
     */
    public function index()
    {
        try {
            // Select only necessary columns to avoid overhead
            $categories = Category::select('id', 'name', 'slug', 'image')->get();
            return $this->success($categories, "Categories retrieved");
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Category Index Error: ' . $e->getMessage());
            return $this->error("Failed to retrieve categories: " . $e->getMessage(), 500);
        }
    }
}
