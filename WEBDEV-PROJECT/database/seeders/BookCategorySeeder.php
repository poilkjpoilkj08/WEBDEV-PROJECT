<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\BookCategory;

class BookCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $bookCategories = [
            [
                'name' => 'Fiction',
                'description' => 'Novels, short stories, and fictional literature'
            ],
            [
                'name' => 'Non-Fiction',
                'description' => 'Biographies, memoirs, and factual books'
            ],
            [
                'name' => 'Science Fiction',
                'description' => 'Sci-fi novels and futuristic literature'
            ],
            [
                'name' => 'Mystery & Thriller',
                'description' => 'Crime novels, detective stories, and suspense'
            ],
            [
                'name' => 'Romance',
                'description' => 'Love stories and romantic novels'
            ],
            [
                'name' => 'Biography',
                'description' => 'Life stories and autobiographies'
            ],
            [
                'name' => 'History',
                'description' => 'Historical accounts and chronicles'
            ],
            [
                'name' => 'Self-Help',
                'description' => 'Personal development and motivational books'
            ],
        ];

        foreach ($bookCategories as $category) {
            BookCategory::create($category);
        }
    }
}
