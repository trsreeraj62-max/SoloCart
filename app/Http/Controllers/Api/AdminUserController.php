<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AdminUserController extends ApiController
{
    /**
     * Get all users (admin only)
     */
    public function index(Request $request)
    {
        try {
            $query = User::query();
            
            // Filter by role if provided
            if ($request->filled('role')) {
                $query->where('role', $request->role);
            }
            
            // Search by name or email
            if ($request->filled('search')) {
                $query->where(function($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->search . '%')
                      ->orWhere('email', 'like', '%' . $request->search . '%');
                });
            }
            
            $users = $query->latest()->paginate(20);
            
            return $this->success($users, "Users retrieved successfully");
        } catch (\Exception $e) {
            Log::error('Admin Users Index Error: ' . $e->getMessage());
            return $this->error("Failed to retrieve users", 500);
        }
    }

    /**
     * Get single user details
     */
    public function show($id)
    {
        try {
            $user = User::with(['orders'])->find($id);
            
            if (!$user) {
                return $this->error("User not found", 404);
            }
            
            return $this->success($user, "User details retrieved");
        } catch (\Exception $e) {
            Log::error('Admin User Show Error: ' . $e->getMessage());
            return $this->error("Failed to retrieve user details", 500);
        }
    }

    /**
     * Toggle user status (active/inactive)
     */
    public function toggleStatus($id)
    {
        try {
            $user = User::find($id);
            
            if (!$user) {
                return $this->error("User not found", 404);
            }
            
            // Prevent admin from deactivating themselves
            if ($user->id === auth()->id()) {
                return $this->error("You cannot deactivate your own account", 403);
            }
            
            // Toggle status field (assuming you have a 'status' or 'is_active' field)
            // If not, you can add this field to users table migration
            $user->status = $user->status === 'active' ? 'inactive' : 'active';
            $user->save();
            
            return $this->success($user, "User status updated to " . $user->status);
        } catch (\Exception $e) {
            Log::error('Toggle User Status Error: ' . $e->getMessage());
            return $this->error("Failed to update user status", 500);
        }
    }

    /**
     * Delete user (soft delete recommended)
     */
    public function destroy($id)
    {
        try {
            $user = User::find($id);
            
            if (!$user) {
                return $this->error("User not found", 404);
            }
            
            // Prevent admin from deleting themselves
            if ($user->id === auth()->id()) {
                return $this->error("You cannot delete your own account", 403);
            }
            
            // Prevent deleting other admins
            if ($user->role === 'admin') {
                return $this->error("Cannot delete admin users", 403);
            }
            
            $user->delete();
            
            return $this->success([], "User deleted successfully");
        } catch (\Exception $e) {
            Log::error('Delete User Error: ' . $e->getMessage());
            return $this->error("Failed to delete user", 500);
        }
    }

    /**
     * Update user role
     */
    public function updateRole(Request $request, $id)
    {
        try {
            $request->validate([
                'role' => 'required|in:user,admin'
            ]);
            
            $user = User::find($id);
            
            if (!$user) {
                return $this->error("User not found", 404);
            }
            
            // Prevent admin from changing their own role
            if ($user->id === auth()->id()) {
                return $this->error("You cannot change your own role", 403);
            }
            
            $user->role = $request->role;
            $user->save();
            
            return $this->success($user, "User role updated to " . $request->role);
        } catch (\Exception $e) {
            Log::error('Update User Role Error: ' . $e->getMessage());
            return $this->error("Failed to update user role", 500);
        }
    }
}
