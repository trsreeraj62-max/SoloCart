<?php

namespace Database\Seeders;

use App\Models\Banner;
use Illuminate\Database\Seeder;

class BannerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 5 Banners
        $banners = [
            [
                'title' => 'Summer Sale',
                'image_path' => 'https://placehold.co/1200x400/orange/white?text=Summer+Sale',
                'type' => 'hero',
                'start_date' => now()->subDays(1),
                'end_date' => now()->addDays(30),
            ],
            [
                'title' => 'New Arrivals',
                'image_path' => 'https://placehold.co/1200x400/blue/white?text=New+Arrivals',
                'type' => 'hero',
                'start_date' => now(),
                'end_date' => null, // Indefinite
            ],
            [
                'title' => 'Electronics Deal',
                'image_path' => 'https://placehold.co/800x400/red/white?text=Electronics',
                'type' => 'carousel',
                'start_date' => now()->subDays(5),
                'end_date' => now()->addDays(10),
            ],
            [
                'title' => 'Fashion Trends',
                'image_path' => 'https://placehold.co/800x400/green/white?text=Fashion',
                'type' => 'carousel',
                'start_date' => null,
                'end_date' => null, // Always active
            ],
            [
                'title' => 'Home Decor Special',
                'image_path' => 'https://placehold.co/800x400/purple/white?text=Home+Decor',
                'type' => 'carousel',
                'start_date' => now(),
                'end_date' => now()->addYear(),
            ],
        ];

        foreach ($banners as $banner) {
            Banner::create($banner);
        }
    }
}
