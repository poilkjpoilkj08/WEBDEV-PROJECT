<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        DB::table('product_categories')->insert([
            [
                'name' => 'Electronics',
                'description' => 'Devices and gadgets like smartphones, laptops, and TVs.',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Clothing',
                'description' => 'Apparel and fashion items for all seasons.',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Books',
                'description' => 'Fiction, non-fiction, educational, and more.',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Home & Kitchen',
                'description' => 'Appliances, cookware, and home essentials.',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Sports & Outdoors',
                'description' => 'Equipment and gear for sports and outdoor activities.',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Beauty & Personal Care',
                'description' => 'Skincare, makeup, and personal care products.',
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);
    }
}
