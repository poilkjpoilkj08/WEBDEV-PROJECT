<?php
require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);

// Minimal request
$request = \Illuminate\Http\Request::create('/', 'GET');
$response = $kernel->handle($request);

// Now run queries
echo "=== CHECKING LOCAL DATABASE ===\n";
$allOrders = \App\Models\Order::orderByDesc('id')->limit(5)->get();
if ($allOrders->isEmpty()) {
    echo "No orders found in local database!\n";
    echo "\nYou were checking production (subsif13_ticktesting).\n";
    echo "To debug email on production, SSH into deicide.my.id and run:\n";
    echo "  php tinker_check.php\n";
    exit(0);
}

echo "Recent orders in local database:\n";
foreach ($allOrders as $order) {
    echo "  Order #" . $order->id . " - User ID: " . $order->user_id . " - Status: " . $order->status . "\n";
}

// Try the first order instead
$order = $allOrders->first();
$user = $order->user;
echo "\n=== CHECKING ORDER #" . $order->id . " ===\n";
echo "User ID: " . $order->user_id . "\n";
echo "User Email: " . $user->email . "\n";
echo "User Google ID: " . ($user->google_id ?: "NULL - Did NOT login via Google") . "\n";
echo "\nIf Google ID is NULL, email won't be sent (by design).\n";
echo "Email only sends if user logged in via Google.\n";
