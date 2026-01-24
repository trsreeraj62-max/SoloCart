<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends ApiController
{
    /**
     * Get User Profile
     */
    public function show(Request $request)
    {
        $user = Auth::user();
        return $this->success($this->formatUser($user), "Profile retrieved");
    }

    /**
     * Update User Profile (Name & Image)
     */
    public function update(Request $request)
    {
        try {
            $user = Auth::user();
            
            $request->validate([
                'name' => 'nullable|string|max:255',
                'profile_image' => 'nullable|image|max:2048' // Max 2MB
            ]);

            // Update Name
            if ($request->filled('name')) {
                $user->name = $request->name;
            }

            // Update Image
            if ($request->hasFile('profile_image')) {
                // Delete old image if verified strictly, but usually safe to just overwrite reference
                if ($user->profile_photo && Storage::disk('public')->exists($user->profile_photo)) {
                    Storage::disk('public')->delete($user->profile_photo);
                }
                
                // Store new image: storage/app/public/profiles/unique_id.jpg
                $path = $request->file('profile_image')->store('profiles', 'public');
                $user->profile_photo = $path;
            }

            $user->save();

            return $this->success($this->formatUser($user), "Profile updated successfully");

        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->error("Validation failed", 422, $e->errors());
        } catch (\Exception $e) {
            return $this->error("Profile update failed: " . $e->getMessage(), 500);
        }
    }

    /**
     * Format user response to match requirements
     */
    private function formatUser($user)
    {
        // Refresh to ensure any appends are calculated
        // Append 'profile_image' explicitly with full URL
        return array_merge($user->toArray(), [
            'profile_image' => $user->profile_photo_url, // Uses the model accessor logic
        ]);
    }
}
