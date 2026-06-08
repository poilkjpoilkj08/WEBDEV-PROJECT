<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Author;
use App\Models\Publisher;
use App\Models\BookCategory;
use App\Models\StoreLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SyncController extends Controller
{
    protected function validateToken(): bool
    {
        $authHeader = request()->header('Authorization');
        $token = env('SYNC_TOKEN');

        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return false;
        }

        return trim(str_replace('Bearer ', '', $authHeader)) === $token;
    }

    public function ping()
    {
        if (!$this->validateToken()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json(['status' => 'ok', 'timestamp' => now()->toIso8601String()]);
    }

    public function books(Request $request)
    {
        if (!$this->validateToken()) {
            Log::warning('Sync unauthorized access attempt', ['ip' => $request->ip()]);
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $since = $request->query('since');
        
        $query = Book::query();
        if ($since) {
            $query->where('updated_at', '>', $since);
        }

        $books = $query->get()->map(function ($book) {
            return [
                'id' => $book->id,
                'isbn' => $book->isbn,
                'title' => $book->title,
                'description' => $book->description,
                'price' => $book->price,
                'pages' => $book->pages,
                'language' => $book->language,
                'publication_year' => $book->publication_year,
                'cover_type' => $book->cover_type,
                'status' => $book->status,
                'author_id' => $book->author_id,
                'category_id' => $book->category_id,
                'cover_image_url' => $book->cover_image_url,
                'stock' => $book->stock,
                'store_stocks' => $book->storeLocations()->get()->map(function ($store) {
                    return [
                        'store_id' => $store->id,
                        'stock' => $store->pivot->stock,
                    ];
                })->toArray(),
                'updated_at' => $book->updated_at->toIso8601String(),
            ];
        });

        return response()->json(['data' => $books]);
    }

    public function authors()
    {
        if (!$this->validateToken()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $authors = Author::all(['id', 'name', 'biography', 'birth_date']);
        return response()->json(['data' => $authors]);
    }

    public function publishers()
    {
        if (!$this->validateToken()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $publishers = Publisher::all(['id', 'name']);
        return response()->json(['data' => $publishers]);
    }

    public function categories()
    {
        if (!$this->validateToken()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $categories = BookCategory::all(['id', 'name']);
        return response()->json(['data' => $categories]);
    }

    public function store(Request $request)
    {
        if (!$this->validateToken()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $data = $request->validate([
            'books' => 'sometimes|array',
            'store_stocks' => 'sometimes|array',
        ]);

        if (isset($data['books'])) {
            foreach ($data['books'] as $bookData) {
                Book::updateOrCreate(
                    ['isbn' => $bookData['isbn']],
                    [
                        'title' => $bookData['title'] ?? 'Unknown',
                        'description' => $bookData['description'] ?? null,
                        'price' => $bookData['price'] ?? 0,
                        'stock' => $bookData['stock'] ?? 0,
                        'status' => $bookData['status'] ?? 'available',
                    ]
                );
            }
        }

        if (isset($data['store_stocks'])) {
            foreach ($data['store_stocks'] as $stockData) {
                $book = Book::where('isbn', $stockData['isbn'])->first();
                if ($book) {
                    $book->storeLocations()->syncWithoutDetaching([
                        $stockData['store_id'] => [
                            'stock' => $stockData['stock'],
                        ]
                    ]);
                }
            }
        }

        return response()->json(['success' => true]);
    }
}