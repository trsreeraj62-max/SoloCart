<?php

namespace App\Http\Controllers\Api;

use App\Models\Banner;
use Illuminate\Http\Request;

class BannerController extends ApiController
{
    /**
     * Get active banners
     */
    /**
     * Get active banners
     */
    public function index()
    {
        try {
            // Apply active scope to filter filtering by is_active and dates
            $banners = Banner::active()->latest()->get();
            return $this->success($banners, "Banners retrieved");
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('API Banner Index Error: ' . $e->getMessage());
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
                'type' => 'in:hero,carousel',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
                'is_active' => 'boolean'
            ]);

            $data = [
                'title' => $validated['title'] ?? null,
                'link' => $validated['link'] ?? null,
                'type' => $validated['type'] ?? 'hero',
                'start_date' => $validated['start_date'] ?? null,
                'end_date' => $validated['end_date'] ?? null,
                'is_active' => $request->has('is_active') ? $validated['is_active'] : true
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
                'title' => 'sometimes|string|max:255',
                'subtitle' => 'nullable|string|max:255',
                'image' => 'sometimes|string', // Wait, file upload logic is missing here in original too? Admin only sends path string? Or file?
                // Correcting update validation for image file
                'image_file' => 'nullable|image|max:5120', 
                'link' => 'nullable|url',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
                'is_active' => 'boolean'
            ]);

            // Handle image logic if file provided
            if ($request->hasFile('image')) {
                 $validated['image_path'] = $request->file('image')->store('banners', 'public');
            }

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
