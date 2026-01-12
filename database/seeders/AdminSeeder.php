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
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@store.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'phone' => '1234567890',
            'email_verified_at' => now(),
        ]);
    }
}
