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
            $categories = Category::all();
            return $this->success($categories, "Categories retrieved");
        } catch (\Exception $e) {
            return $this->error("Failed to retrieve categories", 500);
        }
    }
}
