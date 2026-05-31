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
                'name'          => 'Jakarta Pusat',
                'address'       => 'Jl. MH Thamrin No. 1',
                'city'          => 'Jakarta',
                'latitude'      => -6.1944,
                'longitude'     => 106.8229,
                'phone'         => '+62 21 1234 5678',
                'opening_hours' => 'Mon–Sat 09:00–21:00',
            ],
            [
                'name'          => 'Bandung',
                'address'       => 'Jl. Braga No. 10',
                'city'          => 'Bandung',
                'latitude'      => -6.9175,
                'longitude'     => 107.6191,
                'phone'         => '+62 22 9876 5432',
                'opening_hours' => 'Mon–Sun 10:00–20:00',
            ],
            [
                'name'          => 'Surabaya',
                'address'       => 'Jl. Pemuda No. 27',
                'city'          => 'Surabaya',
                'latitude'      => -7.2575,
                'longitude'     => 112.7521,
                'phone'         => '+62 31 8765 4321',
                'opening_hours' => 'Mon–Sat 10:00–21:00',
            ],
            [
                'name'          => 'Yogyakarta',
                'address'       => 'Jl. Malioboro No. 55',
                'city'          => 'Yogyakarta',
                'latitude'      => -7.7928,
                'longitude'     => 110.3656,
                'phone'         => '+62 274 555 888',
                'opening_hours' => 'Daily 09:00–22:00',
            ],
            [
                'name'          => 'Bali',
                'address'       => 'Jl. Sunset Road No. 88',
                'city'          => 'Denpasar',
                'latitude'      => -8.6705,
                'longitude'     => 115.2126,
                'phone'         => '+62 361 777 999',
                'opening_hours' => 'Daily 10:00–22:00',
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
