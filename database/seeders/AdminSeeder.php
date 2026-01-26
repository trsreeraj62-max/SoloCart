<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Demote Old Admin if exists
        $oldAdmin = User::where('email', 'trsreeraj07@gmail.com')->first();
        if ($oldAdmin) {
            $oldAdmin->update(['role' => 'user']);
        }

        // 2. Create or Update New Admin
        $admin = User::updateOrCreate(
            ['email' => 'admin@store.com'],
            [
                'name' => 'SoloCart Admin',
                'role' => 'admin',
                'phone' => '0000000000',
                'email_verified_at' => now(),
                'password' => Hash::make('admin123'),
            ]
        );
    }
}
