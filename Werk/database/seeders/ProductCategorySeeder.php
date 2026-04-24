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
                'description' => 'Devices and gadgets such as smartphones, laptops, and accessories.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Clothing',
                'description' => 'Men and Women Apparel',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Books',
                'description' => 'Fiction, Non-fiction, and Educational Books',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
