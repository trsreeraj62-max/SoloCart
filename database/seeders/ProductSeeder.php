<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductImage;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::all();

        if ($categories->isEmpty()) {
            $this->call(CategorySeeder::class);
            $categories = Category::all();
        }

        $products = [
            [
                'category_name' => 'Mobile Phones',
                'name' => 'iPhone 15 Pro',
                'description' => 'Experience the power of the A17 Pro chip and the versatility of the Pro camera system.',
                'price' => 999.00,
                'stock' => 50,
                'image' => 'https://images.unsplash.com/photo-1696446701796-da61225697cc?auto=format&fit=crop&q=80&w=300'
            ],
            [
                'category_name' => 'Mobile Phones',
                'name' => 'Samsung Galaxy S24 Ultra',
                'description' => 'The ultimate flagship with a 200MP camera and integrated S Pen.',
                'price' => 1199.00,
                'stock' => 30,
                'image' => 'https://images.unsplash.com/photo-1610945415295-d9bbf067e59c?auto=format&fit=crop&q=80&w=300'
            ],
            [
                'category_name' => 'Laptops',
                'name' => 'MacBook Air M3',
                'description' => 'The world’s most popular laptop is better than ever with the M3 chip.',
                'price' => 1099.00,
                'stock' => 20,
                'image' => 'https://images.unsplash.com/photo-1517336714460-457885b206b1?auto=format&fit=crop&q=80&w=300'
            ],
            [
                'category_name' => 'Laptops',
                'name' => 'Dell XPS 13',
                'description' => 'Stunning bezel-less display and performance in a compact design.',
                'price' => 949.00,
                'stock' => 15,
                'image' => 'https://images.unsplash.com/photo-1593642632823-8f785ba67e45?auto=format&fit=crop&q=80&w=300'
            ],
            [
                'category_name' => 'Headphones',
                'name' => 'Sony WH-1000XM5',
                'description' => 'Industry-leading noise cancellation and superior sound quality.',
                'price' => 349.00,
                'stock' => 40,
                'image' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&q=80&w=300'
            ],
            [
                'category_name' => 'Smart Watches',
                'name' => 'Apple Watch Series 9',
                'description' => 'Smarter, brighter, and more powerful than ever.',
                'price' => 399.00,
                'stock' => 25,
                'image' => 'https://images.unsplash.com/photo-1544117518-30df57809930?auto=format&fit=crop&q=80&w=300'
            ],
            [
                'category_name' => 'Cameras',
                'name' => 'Sony Alpha 7 IV',
                'description' => 'The all-arounder hybrid camera for creators.',
                'price' => 2499.00,
                'stock' => 10,
                'image' => 'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?auto=format&fit=crop&q=80&w=300'
            ],
            [
                'category_name' => 'Gaming',
                'name' => 'PlayStation 5 Slim',
                'description' => 'Experience lightning-fast loading with an ultra-high-speed SSD.',
                'price' => 499.00,
                'stock' => 60,
                'image' => 'https://images.unsplash.com/photo-1606813907291-d86efa9b94db?auto=format&fit=crop&q=80&w=300'
            ],
        ];

        foreach ($products as $pData) {
            $cat = $categories->where('name', $pData['category_name'])->first();
            
            $product = Product::create([
                'category_id' => $cat ? $cat->id : $categories->first()->id,
                'name' => $pData['name'],
                'slug' => Str::slug($pData['name']) . '-' . rand(1000, 9999),
                'description' => $pData['description'],
                'price' => $pData['price'],
                'stock' => $pData['stock'],
            ]);

            // Add image
            ProductImage::create([
                'product_id' => $product->id,
                'image_path' => $pData['image'], // Using external URL as path for demo
                'is_primary' => true
            ]);
        }
    }
}
