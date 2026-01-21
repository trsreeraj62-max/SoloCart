<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CleanupUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Delete all users who are NOT the admin
        $deleted = User::where('email', '!=', 'admin@store.com')->delete();
        $this->command->info("Deleted {$deleted} non-admin user(s).");

        // 2. Ensure the admin user exists
        $adminEmail = 'admin@store.com';
        $admin = User::where('email', $adminEmail)->first();

        if (!$admin) {
            User::create([
                'name' => 'Admin User',
                'email' => $adminEmail,
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'phone' => '1234567890', // Default phone
                'email_verified_at' => now(),
            ]);
            $this->command->info('Admin account created successfully.');
        } else {
            $this->command->info('Admin account already exists and was preserved.');
        }
    }
}
