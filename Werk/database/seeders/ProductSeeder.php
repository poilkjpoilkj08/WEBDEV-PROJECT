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
            [
                'name' => 'MacBook Pro M3',
                'details' => 'Apple laptop powered by M3 chip for high performance',
                'price' => 33999000,
                'category_id' => 1,
                'stock' => 20,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Sony WH-1000XM5',
                'details' => 'Industry-leading noise cancelling wireless headphones',
                'price' => 5999000,
                'category_id' => 1,
                'stock' => 55,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Uniqlo Oversized T-Shirt',
                'details' => 'Comfortable oversized cotton t-shirt for everyday wear',
                'price' => 199000,
                'category_id' => 2,
                'stock' => 120,
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
            [
                'name' => 'Levi’s 501 Original Jeans',
                'details' => 'Classic straight fit denim jeans',
                'price' => 1199000,
                'category_id' => 2,
                'stock' => 80,
                'created_at' => now(),
                'updated_at' => now(),
            ],
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
                'name' => 'The Psychology of Money',
                'details' => 'Timeless lessons on wealth, greed, and happiness',
                'price' => 249000,
                'category_id' => 3,
                'stock' => 130,
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
        ]);
    }
}
