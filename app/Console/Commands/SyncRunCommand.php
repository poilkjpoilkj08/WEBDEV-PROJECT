<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Book;
use App\Models\Author;
use App\Models\Publisher;
use App\Models\BookCategory;
use App\Models\StoreLocation;

class SyncRunCommand extends Command
{
    protected $signature = 'sync:run {--dry-run : Run without writing data}';
    protected $description = 'Run 2-way sync with remote peer';

    protected string $peerUrl;
    protected string $token;

    public function handle(): int
    {
        $this->peerUrl = config('app.sync_peer_url') ?? env('SYNC_PEER_URL');
        $this->token = env('SYNC_TOKEN');

        if (!$this->peerUrl || !$this->token) {
            $this->error('SYNC_PEER_URL and SYNC_TOKEN must be configured in .env');
            return self::FAILURE;
        }

        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->info('Running in dry-run mode - no data will be written');
        }

        $this->info('Sync starting with peer: ' . $this->peerUrl);

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->token,
                'Accept' => 'application/json',
            ])->withOptions([
                'timeout' => 30,
            ])->get($this->peerUrl . '/api/sync/ping');

            if ($response->successful()) {
                $this->info('Connection successful - peer is reachable');

                if (!$dryRun) {
                    $this->performSync();
                }

                Log::channel('single')->info('Sync completed', [
                    'timestamp' => now()->toIso8601String(),
                    'dry_run' => $dryRun,
                ]);

                return self::SUCCESS;
            }

            $this->error('Peer returned status: ' . $response->status());
            return self::FAILURE;

        } catch (\Exception $e) {
            $this->error('Connection failed: ' . $e->getMessage());
            return self::FAILURE;
        }
    }

    protected function performSync(): void
    {
        $this->syncBooks();
        $this->syncAuthors();
        $this->syncPublishers();
        $this->syncCategories();
    }

    protected function syncBooks(): void
    {
        $lastSync = now()->subMinute()->toIso8601String();

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->get($this->peerUrl . '/api/sync/books', [
            'since' => $lastSync,
        ]);

        if ($response->successful()) {
            $books = $response->json('data', []);
            $synced = 0;

            foreach ($books as $data) {
                $book = Book::updateOrCreate(
                    ['isbn' => $data['isbn']],
                    [
                        'title' => $data['title'] ?? 'Unknown',
                        'description' => $data['description'] ?? null,
                        'price' => $data['price'] ?? 0,
                        'pages' => $data['pages'] ?? null,
                        'language' => $data['language'] ?? null,
                        'publication_year' => $data['publication_year'] ?? null,
                        'cover_type' => $data['cover_type'] ?? 'hardcover',
                        'status' => $data['status'] ?? 'available',
                        'author_id' => $data['author_id'] ?? null,
                        'category_id' => $data['category_id'] ?? null,
                        'cover_image_url' => $data['cover_image_url'] ?? null,
                        'stock' => $data['stock'] ?? 0,
                    ]
                );
                $synced++;

                if (isset($data['store_stocks']) && is_array($data['store_stocks'])) {
                    foreach ($data['store_stocks'] as $storeStock) {
                        if (isset($storeStock['store_id']) && isset($storeStock['stock'])) {
                            $book->storeLocations()->syncWithoutDetaching([
                                $storeStock['store_id'] => [
                                    'stock' => $storeStock['stock'],
                                ]
                            ]);
                        }
                    }
                }
            }

            $this->info("Synced {$synced} books");
        }
    }

    protected function syncAuthors(): void
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->get($this->peerUrl . '/api/sync/authors');

        if ($response->successful()) {
            $authors = $response->json('data', []);

            foreach ($authors as $data) {
                Author::updateOrCreate(
                    ['name' => $data['name']],
                    [
                        'name' => $data['name'],
                        'biography' => $data['biography'] ?? null,
                        'birth_date' => $data['birth_date'] ?? null,
                    ]
                );
            }
        }
    }

    protected function syncPublishers(): void
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->get($this->peerUrl . '/api/sync/publishers');

        if ($response->successful()) {
            $publishers = $response->json('data', []);

            foreach ($publishers as $data) {
                Publisher::updateOrCreate(
                    ['name' => $data['name']],
                    ['name' => $data['name']]
                );
            }
        }
    }

    protected function syncCategories(): void
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->get($this->peerUrl . '/api/sync/categories');

        if ($response->successful()) {
            $categories = $response->json('data', []);

            foreach ($categories as $data) {
                BookCategory::updateOrCreate(
                    ['name' => $data['name']],
                    ['name' => $data['name']]
                );
            }
        }
    }
}