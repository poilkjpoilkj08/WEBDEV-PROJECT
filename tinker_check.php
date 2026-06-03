<?php
require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);

// Minimal request
$request = \Illuminate\Http\Request::create('/', 'GET');
$response = $kernel->handle($request);

// Now run queries
$order = \App\Models\Order::find(48);
if (!$order) {
    echo "Order 48 not found!\n";
    exit(1);
}

$user = $order->user;
echo "=== ORDER 48 DEBUG ===\n";
echo "Order ID: " . $order->id . "\n";
echo "User ID: " . $order->user_id . "\n";
echo "User Email: " . $user->email . "\n";
echo "User Google ID: " . ($user->google_id ?: "NULL - Did NOT login via Google") . "\n";
echo "\nIf Google ID is NULL, email won't be sent (by design).\n";
echo "User needs to log in via Google for email to be sent.\n";
