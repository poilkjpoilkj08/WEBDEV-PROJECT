<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Author;

class AuthorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $authors = [
            [
                'name' => 'J.K. Rowling',
                'email' => 'jk.rowling@authors.com',
                'phone' => '555-0201',
                'bio' => 'Bestselling author of the Harry Potter series. Known for her magical storytelling and world-building expertise.',
                'photo_url' => 'https://via.placeholder.com/300x300?text=J.K.+Rowling',
                'publisher' => 'Bloomsbury Publishing',
                'user_id' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Stephen King',
                'email' => 'stephen.king@authors.com',
                'phone' => '555-0202',
                'bio' => 'Master of horror and suspense fiction. Author of numerous bestselling novels including The Shining and It.',
                'photo_url' => 'https://via.placeholder.com/300x300?text=Stephen+King',
                'publisher' => 'Scribner',
                'user_id' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Agatha Christie',
                'email' => 'agatha.christie@authors.com',
                'phone' => '555-0203',
                'bio' => 'Queen of mystery novels. Famous for her detective stories featuring Hercule Poirot and Miss Marple.',
                'photo_url' => 'https://via.placeholder.com/300x300?text=Agatha+Christie',
                'publisher' => 'HarperCollins',
                'user_id' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'George R.R. Martin',
                'email' => 'grrmartin@authors.com',
                'phone' => '555-0204',
                'bio' => 'Author of the epic fantasy series A Song of Ice and Fire, basis for the Game of Thrones TV series.',
                'photo_url' => 'https://via.placeholder.com/300x300?text=George+R.R.+Martin',
                'publisher' => 'Bantam Books',
                'user_id' => null,
                'is_active' => true,
            ],
        ];

        foreach ($authors as $author) {
            Author::create($author);
        }
    }
}
