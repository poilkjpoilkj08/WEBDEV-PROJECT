<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        DB::table('products')->insert([
            // Electronics (1)
            [
                'name' => 'iPhone 15 Pro Max',
                'details' => 'Apple flagship smartphone with A17 Pro chip and titanium design',
                'price' => 20499000,
                'category_id' => 1,
                'stock' => 45,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Samsung Galaxy S24 Ultra',
                'details' => 'Premium Android phone with AI features and S Pen',
                'price' => 18799000,
                'category_id' => 1,
                'stock' => 40,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Clothing (2)
            [
                'name' => 'Uniqlo Oversized T-Shirt',
                'details' => 'Comfortable oversized cotton t-shirt for everyday wear',
                'price' => 199000,
                'category_id' => 2,
                'stock' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Nike Tech Fleece Hoodie',
                'details' => 'Lightweight and warm hoodie with modern athletic fit',
                'price' => 2199000,
                'category_id' => 2,
                'stock' => 60,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Books (3)
            [
                'name' => 'Atomic Habits',
                'details' => 'Bestselling self-improvement book by James Clear',
                'price' => 299000,
                'category_id' => 3,
                'stock' => 150,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Laravel 11 From Scratch',
                'details' => 'Comprehensive guide to building modern Laravel applications',
                'price' => 399000,
                'category_id' => 3,
                'stock' => 70,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Home & Kitchen (4)
            [
                'name' => 'Philips Rice Cooker',
                'details' => 'Durable rice cooker with automatic keep-warm function',
                'price' => 599000,
                'category_id' => 4,
                'stock' => 80,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Miyako Blender',
                'details' => 'Multi-purpose blender for smoothies and food prep',
                'price' => 429000,
                'category_id' => 4,
                'stock' => 65,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Sports & Outdoors (5) ✅ NEW
            [
                'name' => 'Yonex Badminton Racket',
                'details' => 'Lightweight racket for professional and casual play',
                'price' => 899000,
                'category_id' => 5,
                'stock' => 50,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Decathlon Camping Tent',
                'details' => '2-person waterproof tent for outdoor adventures',
                'price' => 1299000,
                'category_id' => 5,
                'stock' => 30,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
