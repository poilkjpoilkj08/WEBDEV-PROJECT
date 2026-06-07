<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\StoreLocation;
use App\Models\Book;

class StoreLocationSeeder extends Seeder
{
    public function run(): void
    {
        $stores = [
            [
                'name'          => 'BookHive Jakarta Pusat',
                'address'       => 'Jl. MH Thamrin No. 1',
                'province'      => 'Jawa Barat',
                'city'          => 'Jakarta',
                'district'      => 'Menteng',
                'postal_code'   => '10230',
                'latitude'      => -6.1944,
                'longitude'     => 106.8229,
                'phone'         => '+62 21 1234 5678',
                'email'         => 'jakarta@bookhive.com',
                'opening_hours' => 'Mon–Sat 09:00–21:00',
                'country'       => 'Indonesia',
                'is_active'     => true,
            ],
            [
                'name'          => 'BookHive Bandung',
                'address'       => 'Jl. Braga No. 10',
                'province'      => 'Jawa Barat',
                'city'          => 'Bandung',
                'district'      => 'Bandung',
                'postal_code'   => '40111',
                'latitude'      => -6.9175,
                'longitude'     => 107.6191,
                'phone'         => '+62 22 9876 5432',
                'email'         => 'bandung@bookhive.com',
                'opening_hours' => 'Mon–Sun 10:00–20:00',
                'country'       => 'Indonesia',
                'is_active'     => true,
            ],
            [
                'name'          => 'BookHive Surabaya',
                'address'       => 'Jl. Pemuda No. 27',
                'province'      => 'Jawa Timur',
                'city'          => 'Surabaya',
                'district'      => 'Genteng',
                'postal_code'   => '60271',
                'latitude'      => -7.2575,
                'longitude'     => 112.7521,
                'phone'         => '+62 31 8765 4321',
                'email'         => 'surabaya@bookhive.com',
                'opening_hours' => 'Mon–Sat 10:00–21:00',
                'country'       => 'Indonesia',
                'is_active'     => true,
            ],
            [
                'name'          => 'BookHive Yogyakarta',
                'address'       => 'Jl. Malioboro No. 55',
                'province'      => 'DI Yogyakarta',
                'city'          => 'Yogyakarta',
                'district'      => 'Kraton',
                'postal_code'   => '55241',
                'latitude'      => -7.7928,
                'longitude'     => 110.3656,
                'phone'         => '+62 274 555 888',
                'email'         => 'yogyakarta@bookhive.com',
                'opening_hours' => 'Daily 09:00–22:00',
                'country'       => 'Indonesia',
                'is_active'     => true,
            ],
            [
                'name'          => 'BookHive Bali',
                'address'       => 'Jl. Sunset Road No. 88',
                'province'      => 'Bali',
                'city'          => 'Denpasar',
                'district'      => 'Denpasar Timur',
                'postal_code'   => '80227',
                'latitude'      => -8.6705,
                'longitude'     => 115.2126,
                'phone'         => '+62 361 777 999',
                'email'         => 'bali@bookhive.com',
                'opening_hours' => 'Daily 10:00–22:00',
                'country'       => 'Indonesia',
                'is_active'     => true,
            ],
        ];

        foreach ($stores as $data) {
            StoreLocation::create($data);
        }

        // Attach every book to every store with random stock
        $books = Book::all();
        $allStores = StoreLocation::all();

        foreach ($books as $book) {
            foreach ($allStores as $store) {
                $book->storeLocations()->attach($store->id, ['stock' => rand(2, 20)]);
            }
        }
    }
}
