<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\Book;
use App\Models\Publisher;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Get all unique publisher names from books table
        $publisherNames = Book::whereNotNull('publisher')
            ->distinct()
            ->pluck('publisher')
            ->filter(fn($name) => !empty(trim($name)))
            ->unique();

        // Create publishers if they don't exist
        foreach ($publisherNames as $name) {
            Publisher::firstOrCreate(
                ['name' => trim($name)],
                ['name' => trim($name)]
            );
        }

        // Update books with publisher_id based on publisher name
        foreach ($publisherNames as $name) {
            $publisher = Publisher::where('name', trim($name))->first();
            if ($publisher) {
                Book::where('publisher', $name)->update([
                    'publisher_id' => $publisher->id
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Clear publisher_id values
        Book::update(['publisher_id' => null]);
        
        // Delete all publishers that were synced from books
        Publisher::truncate();
    }
};
