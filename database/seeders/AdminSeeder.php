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
        // Use firstOrCreate to ensure we don't duplicate or error if exists
        User::firstOrCreate(
            ['email' => 'admin@store.com'], 
            [
                'name' => 'Admin User',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'phone' => '1234567890',
                'email_verified_at' => now(),
            ]
        );
    }
}
