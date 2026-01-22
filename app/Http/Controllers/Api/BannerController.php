<?php

namespace App\Http\Controllers\Api;

use App\Models\Banner;
use Illuminate\Http\Request;

class BannerController extends ApiController
{
    /**
     * Get active banners
     */
    public function index()
    {
        try {
            $banners = Banner::where(function($q) {
                $q->whereNull('start_date')->orWhere('start_date', '<=', now());
            })->where(function($q) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', now());
            })->get();

            return $this->success($banners, "Banners retrieved");
        } catch (\Exception $e) {
            return $this->error("Failed to retrieve banners", 500);
        }
    }

    /**
     * Admin: Get all banners (including inactive)
     */
    public function adminIndex()
    {
        try {
            $banners = Banner::latest()->get();
            return $this->success($banners, "All banners retrieved");
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Admin Banners Error: ' . $e->getMessage());
            return $this->error("Failed to retrieve banners", 500);
        }
    }

    /**
     * Admin: Create banner
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'nullable|string|max:255',
                'image' => 'required|image|max:5120', // Max 5MB
                'link' => 'nullable|url',
                'type' => 'in:hero,carousel'
            ]);

            $data = [
                'title' => $validated['title'] ?? null,
                'link' => $validated['link'] ?? null,
                'type' => $validated['type'] ?? 'hero'
            ];

            if ($request->hasFile('image')) {
                $data['image_path'] = $request->file('image')->store('banners', 'public');
            }

            $banner = Banner::create($data);

            return $this->success($banner, "Banner created successfully", 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->error("Validation failed", 422, $e->errors());
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Create Banner Error: ' . $e->getMessage());
            return $this->error("Failed to create banner: " . $e->getMessage(), 500); // Exposed error for debugging
        }
    }

    /**
     * Admin: Update banner
     */
    public function update(Request $request, $id)
    {
        try {
            $banner = Banner::find($id);
            
            if (!$banner) {
                return $this->error("Banner not found", 404);
            }

            $validated = $request->validate([
                'title' => 'sometimes|required|string|max:255',
                'subtitle' => 'nullable|string|max:255',
                'image' => 'sometimes|required|string',
                'link' => 'nullable|url',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
            ]);

            $banner->update($validated);

            return $this->success($banner, "Banner updated successfully");
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->error("Validation failed", 422, $e->errors());
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Update Banner Error: ' . $e->getMessage());
            return $this->error("Failed to update banner", 500);
        }
    }

    /**
     * Admin: Delete banner
     */
    public function destroy($id)
    {
        try {
            $banner = Banner::find($id);
            
            if (!$banner) {
                return $this->error("Banner not found", 404);
            }

            $banner->delete();

            return $this->success([], "Banner deleted successfully");
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Delete Banner Error: ' . $e->getMessage());
            return $this->error("Failed to delete banner", 500);
        }
    }
}
