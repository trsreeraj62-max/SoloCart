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

            \Illuminate\Support\Facades\Cache::forget('home_data');

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

            // Allow 'image' to be a file (upload) or string (existing url/path)
            $rules = [
                'title' => 'sometimes|string|max:255',
                'subtitle' => 'nullable|string|max:255',
                'link' => 'nullable|url',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
                'is_active' => 'boolean'
            ];

            if ($request->hasFile('image')) {
                $rules['image'] = 'image|max:5120';
            } else {
                $rules['image'] = 'nullable|string';
            }

            $validated = $request->validate($rules);

            // Handle Image Upload
            if ($request->hasFile('image')) {
                $validated['image_path'] = $request->file('image')->store('banners', 'public');
                // Remove 'image' from validated so it doesn't try to update a non-existent column if 'image' isn't in fillable, 
                // but our model has 'image' in fillable which creates confusion. 
                // We should prioritize image_path.
                unset($validated['image']);
            } elseif (isset($validated['image']) && is_string($validated['image'])) {
                // If it's a string, it might be the existing path or URL. 
                // If it's a full URL, we might want to extract the relative path if it matches our storage, 
                // but for now, we just trust the storage logic or assume it is the path.
                // However, the DB column is image_path. 'image' alias exists in model fillable but we should use image_path.
                 $validated['image_path'] = $validated['image']; // logic to keep existing if string passed
                 unset($validated['image']);
            }

            $banner->update($validated);

            \Illuminate\Support\Facades\Cache::forget('home_data');

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

            \Illuminate\Support\Facades\Cache::forget('home_data');

            return $this->success([], "Banner deleted successfully");
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Delete Banner Error: ' . $e->getMessage());
            return $this->error("Failed to delete banner", 500);
        }
    }
}
