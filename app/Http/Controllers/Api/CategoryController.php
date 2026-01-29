<?php

namespace App\Http\Controllers\Api;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class CategoryController extends ApiController
{
    /**
     * Get all categories
     */
    public function index()
    {
        try {
            // Select only necessary columns
            // Using a more robust query approach
            $categories = Category::query()
                ->select(['id', 'name', 'slug', 'image', 'discount_percent', 'start_at', 'end_at'])
                ->when(Schema::hasColumn('categories', 'status'), function($q) {
                    return $q->where('status', 1);
                })
                ->orderBy('name')
                ->get();
                
            Log::info('Categories retrieved successfully.', ['count' => $categories->count()]);
            return $this->success($categories, "Categories retrieved successfully.");
        } catch (\Exception $e) {
            Log::error('Category Index Error: Failed to retrieve categories.', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
            return $this->error("Failed to retrieve categories due to an internal server error. Please try again later.", 500);
        }
    }
}
