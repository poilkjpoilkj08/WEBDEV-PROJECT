<?php

namespace Database\Seeders;

<<<<<<< HEAD
=======
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
>>>>>>> 0baef88919b77deb93dd0969a66df6291b15cee3
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
<<<<<<< HEAD
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            UserRoleSeeder::class,
            BookCategorySeeder::class,
            AuthorSeeder::class,
            BookSeeder::class,
            StoreLocationSeeder::class, 
        ]);
=======
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(UserSeeder::class);
        $this->call(UserRoleSeeder::class);
        $this->call(BookCategorySeeder::class);
        $this->call(AuthorSeeder::class);
        $this->call(BookSeeder::class);
>>>>>>> 0baef88919b77deb93dd0969a66df6291b15cee3
    }
}
