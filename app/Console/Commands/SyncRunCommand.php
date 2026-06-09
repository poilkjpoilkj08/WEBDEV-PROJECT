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
use App\Models\Order;
use App\Models\OrderDetail;

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
        $this->syncOrders();
    }

    protected function syncBooks(): void
    {
        $lastSync = now()->subHours(24)->toIso8601String();

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

    protected function syncOrders(): void
    {
        $lastSync = now()->subHours(24)->toIso8601String();

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->get($this->peerUrl . '/api/sync/orders', [
            'since' => $lastSync,
        ]);

        if ($response->successful()) {
            $orders = $response->json('data', []);
            $synced = 0;

            foreach ($orders as $data) {
                $userId = $data['user_id'];
                $userExists = \App\Models\User::where('id', $userId)->exists();
                
                if (!$userExists) {
                    $userId = 1; // Default to first user for synced orders
                }

                $order = Order::updateOrCreate(
                    ['invoice_number' => $data['invoice_number']],
                    [
                        'user_id' => $userId,
                        'customer_name' => $data['customer_name'],
                        'total_price' => $data['total_price'],
                        'status' => $data['status'],
                        'shipping_status' => $data['shipping_status'],
                        'payment_method' => $data['payment_method'],
                        'paid_at' => $data['paid_at'],
                        'store_id' => $data['store_id'],
                        'shipping_name' => $data['shipping_name'],
                        'shipping_phone' => $data['shipping_phone'],
                        'shipping_address' => $data['shipping_address'],
                        'shipping_city' => $data['shipping_city'],
                        'shipping_province' => $data['shipping_province'],
                        'shipping_postal_code' => $data['shipping_postal_code'],
                        'shipping_country' => $data['shipping_country'],
                        'shipping_method' => $data['shipping_method'],
                        'shipping_cost' => $data['shipping_cost'],
                        'tracking_number' => $data['tracking_number'],
                    ]
                );
                $synced++;

                if (isset($data['order_details']) && is_array($data['order_details'])) {
                    foreach ($data['order_details'] as $detail) {
                        OrderDetail::updateOrCreate(
                            [
                                'order_id' => $order->id,
                                'book_id' => $detail['book_id'],
                            ],
                            [
                                'store_id' => $detail['store_id'],
                                'book_title' => $detail['book_title'],
                                'quantity' => $detail['quantity'],
                                'price' => $detail['price'],
                                'subtotal' => $detail['subtotal'],
                            ]
                        );
                    }
                }
            }

            $this->info("Synced {$synced} orders");
        }
    }
}