<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Mobile Phones', 'slug' => 'mobile-phones'],
            ['name' => 'Laptops', 'slug' => 'laptops'],
            ['name' => 'Headphones', 'slug' => 'headphones'],
            ['name' => 'Smart Watches', 'slug' => 'smart-watches'],
            ['name' => 'Cameras', 'slug' => 'cameras'],
            ['name' => 'Gaming', 'slug' => 'gaming'],
        ];

        foreach ($categories as $cat) {
            Category::create($cat);
        }
    }
}
