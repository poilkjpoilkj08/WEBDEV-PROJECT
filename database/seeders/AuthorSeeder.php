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
                'name' => 'F. Scott Fitzgerald',
                'email' => 'f.fitzgerald@authors.com',
                'phone' => '555-0205',
                'bio' => 'American novelist and short story writer, widely regarded as one of the greatest American writers of the 20th century.',
                'photo_url' => 'https://via.placeholder.com/300x300?text=F.+Scott+Fitzgerald',
                'publisher' => 'Charles Scribner\'s Sons',
                'user_id' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Jane Austen',
                'email' => 'jane.austen@authors.com',
                'phone' => '555-0206',
                'bio' => 'English novelist known primarily for her six major novels, which interpret, critique and comment upon the British landed gentry.',
                'photo_url' => 'https://via.placeholder.com/300x300?text=Jane+Austen',
                'publisher' => 'Penguin Classics',
                'user_id' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Haruki Murakami',
                'email' => 'haruki.murakami@authors.com',
                'phone' => '555-0207',
                'bio' => 'Japanese writer. His books and stories have been bestsellers in Japan as well as internationally.',
                'photo_url' => 'https://via.placeholder.com/300x300?text=Haruki+Murakami',
                'publisher' => 'Knopf',
                'user_id' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Frank Herbert',
                'email' => 'frank.herbert@authors.com',
                'phone' => '555-0208',
                'bio' => 'American science fiction author best known for the novel Dune and its five sequels.',
                'photo_url' => 'https://via.placeholder.com/300x300?text=Frank+Herbert',
                'publisher' => 'Chilton Books',
                'user_id' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Isaac Asimov',
                'email' => 'isaac.asimov@authors.com',
                'phone' => '555-0209',
                'bio' => 'American writer and professor of biochemistry, known for his works of science fiction and popular science.',
                'photo_url' => 'https://via.placeholder.com/300x300?text=Isaac+Asimov',
                'publisher' => 'Gnome Press',
                'user_id' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Tara Westover',
                'email' => 'tara.westover@authors.com',
                'phone' => '555-0210',
                'bio' => 'American author who grew up in a survivalist family in rural Idaho and later earned a PhD from Cambridge.',
                'photo_url' => 'https://via.placeholder.com/300x300?text=Tara+Westover',
                'publisher' => 'Random House',
                'user_id' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Michelle Obama',
                'email' => 'michelle.obama@authors.com',
                'phone' => '555-0211',
                'bio' => 'American attorney, author and public speaker who served as the First Lady of the United States from 2009 to 2017.',
                'photo_url' => 'https://via.placeholder.com/300x300?text=Michelle+Obama',
                'publisher' => 'Crown Publishing',
                'user_id' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Margaret Atwood',
                'email' => 'margaret.atwood@authors.com',
                'phone' => '555-0212',
                'bio' => 'Canadian poet, novelist, literary critic, essayist, and environmental activist.',
                'photo_url' => 'https://via.placeholder.com/300x300?text=Margaret+Atwood',
                'publisher' => 'McClelland and Stewart',
                'user_id' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Yuval Noah Harari',
                'email' => 'yuval.harari@authors.com',
                'phone' => '555-0213',
                'bio' => 'Israeli public intellectual, historian, and a professor in the Department of History at the Hebrew University of Jerusalem.',
                'photo_url' => 'https://via.placeholder.com/300x300?text=Yuval+Noah+Harari',
                'publisher' => 'Harper',
                'user_id' => null,
                'is_active' => true,
            ],
            [
                'name' => 'James Clear',
                'email' => 'james.clear@authors.com',
                'phone' => '555-0214',
                'bio' => 'American author, entrepreneur, and photographer known for his work on habits and continuous improvement.',
                'photo_url' => 'https://via.placeholder.com/300x300?text=James+Clear',
                'publisher' => 'Avery',
                'user_id' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Morgan Housel',
                'email' => 'morgan.housel@authors.com',
                'phone' => '555-0215',
                'bio' => 'Partner at The Collaborative Fund and a former columnist at The Motley Fool and The Wall Street Journal.',
                'photo_url' => 'https://via.placeholder.com/300x300?text=Morgan+Housel',
                'publisher' => 'Harriman House',
                'user_id' => null,
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
            [
                'name' => 'Matt Haig',
                'email' => 'matt.haig@authors.com',
                'phone' => '555-0205',
                'bio' => 'English novelist and journalist. Author of both fiction and non-fiction, including the bestselling novel The Midnight Library.',
                'photo_url' => 'https://via.placeholder.com/300x300?text=Matt+Haig',
                'publisher' => 'Viking',
                'user_id' => null,
                'is_active' => true,
            ],
        ];

        foreach ($authors as $author) {
            Author::create($author);
        }
    }
}
